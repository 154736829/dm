<?php
header("Content-type: text/html;charset=UTF-8");
/*生成文件类别静态页面
 * 
 */
$local_path = dirname(_FILE_);//当前文件所在文件夹
$html_name='list'; //类别静态页面名字
$dir_path="./aftersift";//读取文件的文件夹
$content_string = '';//静态页面内容
$return_url = "http://localhost:802/index.php";//返回的页面

$dir_handle=opendir($dir_path);

//根据文件名生成文件列表静态网站
while(($file_name=readdir($dir_handle))!=null){
		if($file_name == "." || $file_name == "..") continue;
		$content_string.= "<a href = \"../".$file_name."_1.html\">".$file_name."<a/><br/>";
}
closedir($dir_handle);

$html_content = set_html($html_name,$content_string,$return_url);
produce_static_html($html_name,$html_content,'../static_html/list/');

/*生成静态页面内容
*
*/
function set_html($html_name,$file_contents,$return_url){
	//静态页面内容
	$html_content="<html>".
	"<head>".
	"<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">".
	"<title>".$html_name."</title>".
	"</head>".
	"<body>
                        <div align=\"center\">LOGO</div>
			<div align=\"center\">".$file_contents."</div>
                        <div align=\"center\"><a href=\"".$return_url."\">返回</a></div>".
	"</body>".
	"</html>";
        return $html_content;
}

/*生成静态页面
 * 
 */
function produce_static_html($html_name,$html_content,$html_dir){
        //生成静态页面
	$static_html = $html_name.".html";
	file_put_contents($html_dir.$static_html,$html_content);
	return;
}