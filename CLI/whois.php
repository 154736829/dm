<?php
set_time_limit(0);

//检测最后一次查询的域名
if(file_exists(".tmp")) {
	$lastWord = trim(file_get_contents(".tmp"));
	$flag = true;
} else {
	$lastWord = "";
	$flag = false;
}

// 对每行日志进行处理
while($line=fgets(STDIN)) 
{ 
	$line = trim($line);
	//匹配最后一次查询的域名
	if($flag) {
		if($lastWord == $line) {
			$flag = false;
		}
		continue;
	}
	file_put_contents(".tmp", $line);

	list($usec, $sec) = explode(" ", microtime());
	$start = ((float)$usec + (float)$sec);

	try 
	{
		$com = "$line.com";
		if(whois_net($com)) {
			file_put_contents("./com.txt", "$com\n",FILE_APPEND);
		} else {
			echo "$com\n";
		}
		$com = "$line.net";
		if(whois_net($com)) {
			file_put_contents("./net.txt", "$com\n",FILE_APPEND);
		} else {
			echo "$com\n";
		}
		$com = "$line.cn";
		if(whois_net($com)) {
			file_put_contents("./cn.txt", "$com\n",FILE_APPEND);
		} else {
			echo "$com\n";
		}
		$com = "$line.com.cn";
		if(whois_net($com)) {
			file_put_contents("./cn.txt", "$com\n",FILE_APPEND);
		} else {
			echo "$com\n";
		}

	}
	catch (Exception $e)
	{
		continue;
	}
	
    list($usec, $sec) = explode(" ", microtime());
    $end = ((float)$usec + (float)$sec);
    $t = $end - $start;	
    echo "time:$t\n";	
    if($t > 10 ) {
        sleep(300);
    }   
}
//把process进程文件内容改为0
file_put_contents($argv[1], '0');
exit;

/**转换IP并获取站点信息
 * 
 * $url 站点
 * return $data 站点数据
 */
function change_ip($url){
    $ip_long = array(
    array('607649792', '608174079'), //36.56.0.0-36.63.255.255
    array('1038614528', '1039007743'), //61.232.0.0-61.237.255.255
    array('1783627776', '1784676351'), //106.80.0.0-106.95.255.255
    array('2035023872', '2035154943'), //121.76.0.0-121.77.255.255
    array('2078801920', '2079064063'), //123.232.0.0-123.235.255.255
    array('-1950089216', '-1948778497'), //139.196.0.0-139.215.255.255
    array('-1425539072', '-1425014785'), //171.8.0.0-171.15.255.255
    array('-1236271104', '-1235419137'), //182.80.0.0-182.92.255.255
    array('-770113536', '-768606209'), //210.25.0.0-210.47.255.255
    array('-569376768', '-564133889'), //222.16.0.0-222.95.255.255
    );
    $rand_key = mt_rand(0, 9);
    $ip= long2ip(mt_rand($ip_long[$rand_key][0], $ip_long[$rand_key][1]));
    
    $ch=  curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:8.8.8.8', 'CLIENT-IP:'.$ip));
    curl_setopt($ch, CURLOPT_REFERER, "http://www.ename.cn");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}


/** 检查域名是否被注册
 * 
 * $domain  域名
 */
function whois_net($domain)
{
	$url = "http://panda.www.net.cn/cgi-bin/check_muitl.cgi?domain={$domain}";
	$result = change_ip($url);

	if(strpos($result,"|210|")) return true;
	else if(strpos($result,"|211|")) {
		return false;
	} else {
		//$result = file_get_contents($url);
		if(strpos($result,"|210|")) return true;
		else return false;
	}
}
