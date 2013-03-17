<?php
/**连接万网查看域名信息
*
**/
class control_search{
	/*查询域名数据
	* $host     web提交的域名
	* $TOPDM    顶级域名后缀
	*/
	function search(){
		$host = stripslashes(isset($_GET["value"])? trim($_GET["value"]) : 'error');
		if($host == 'error') exit('无法接收输入的域名');
		global $TOPDM;
		$TOPDM= array('com','net','org','cn','ws','cc','biz','us','in','eu','uk','name','name','info','tw');
		//获取域名
		$Tdomain = GetTdomain($host);
        
		//查看是否注册
		$exists = check_register($Tdomain);
		if($exists ==false){ 
			echo $Tdomain."已经被注册。<br/>";
			}else {
			echo $Tdomain."可以注册。<br/>";
		}

		//查看域名IP
		$ip=gethostbynamel($Tdomain);
		if($ip==true){
			echo($Tdomain."的IP是:<br/>");
			for($i = 0;$i<count($ip);$i++){
			echo ($ip[$i]." <br/>");
			}
		}else{
			echo "无法查找IP<br/>";
		}
			
		//连接Whois并输出信息
		$whois = ConWhois($Tdomain);
	}
}
    
	/** 解析域名
    *   $host 域名
    *   return  $Tdomain 解析后的域名
    */
	function GetTdomain($host){
		global $TOPDM;
		$rmHttp = preg_match("/^(\bhttp\b:\/\/)/",$host);//检测是否含http://
		if($rmHttp == 0){
			$host="http://".$host;
		}
		
		$website = parse_url($host);
		$website = strtolower($website['host']) ;
		
		$str = '';
		foreach ( $TOPDM as $t ) {
			$str .= ($str ? '|' : '') . $t;
		}
		
		$matchstr = "[^\.]+\.(?:(" . $str . ")|\w{2}|((" . $str . ")\.\w{2}))$"; //获取域名主体
		if (preg_match ( "/" . $matchstr . "/ies", $website, $matchs )) {
			$Tdomain = $matchs ['0'];
		} else {
			$Tdomain = $website;
		}
		
		$match_domain = preg_match("/(".$str.")$/",$Tdomain);//检查$Tdomain是否有顶级域名
		if($match_domain=='0') exit($Tdomain."非正规域名<br/>");
		
		return $Tdomain;
	}

	/** 查询域名是否被注册
	 *  
	 *  $Tdomain 顶级域名
	 */
	function check_register($Tdomain)
	{
		$url = "http://panda.www.net.cn/cgi-bin/check_muitl.cgi?domain={$Tdomain}";
		$result=file_get_contents($url);
		
		if(strpos($result,"|210|")) return true;
		else if(strpos($result,"|211|")) {
			return false;
		} else {
			echo "error";
			return;
		}
	}
        
	/** 根据顶级域名选择whois站点连接
	 * 
	 * $Tdomain 顶级域名
	 */
	function ConWhois($Tdomain){
		$domainParse = array();
		preg_match("/^(\w+)((\.\w+){1,})/", $Tdomain , $domainParse);//拆分域名
		switch ($domainParse[2]){
			case '.com':
			case '.net':
			case '.org':
			case '.biz':
			case '.info':
			case '.name':
				$server = 'whois.verisign-grs.com';
				GetWhois($server,$Tdomain);
				break;
			case '.edu.cn':
				$server = 'whois.educause.net';
				GetWhois($server,$Tdomain);
				break;
			case '.cn':
				$server = 'whois.cnnic.net.cn';
				GetWhois($server,$Tdomain);
				break;
			case '.tw':
				$server = 'whois.twnic.net.tw';
				GetWhois($server,$Tdomain);
				break;
			case '.kr':
				$server = 'whois.krnic.net';
				GetWhois($server,$Tdomain);
				break;
			default:
				echo "无法查找信息。";
				break;
		}
	}
	
	/**查询域名的whois信息
	 * 
	 * $server  whois站点
	 * $Tdomain 顶级域名
	 */
	function GetWhois($server,$Tdomain){
		$Tdomain="$Tdomain\r\n";
		$fp = fsockopen($server, 43, $errno, $errstr, 10);//打开SOCK连接
		fwrite($fp, $Tdomain);
		while (!feof($fp)){
			$data .= fgets($fp)."<br>\n";//获取页面返回数据
		}
		fclose($fp);
		echo "Whois信息：<br/>";
		echo $data;
	}
	