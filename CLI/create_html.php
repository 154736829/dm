<?php
header("Content-type: text/html;charset=UTF-8");

$limit = 1000; //每页限制域名数
$n=0; //读取文件的域名数
$page_contents = ''; //文件内容
$dir_path="./aftersift"; //放置筛选后文件的文件夹
$files = array();//筛选后的文件
$contents = array();

//获取域名筛选后的文件名
$dir_handle=opendir($dir_path);
 while(($file_name=readdir($dir_handle))!=null){
		if($file_name == "." || $file_name == ".."){continue;}
		array_push($files,$file_name);
 }
 $files_count = count($files);
 closedir($dir_handle);
//每个文件的静态页面生成
for($i=0;$i<$files_count;$i++){
    $file = $dir_path."/".$files[$i];
    
    $file_handle =  fopen($file, 'r');
    //获取文件内容
    while($line = fgets($file_handle)){
            $n++;
            $page_contents.= $line;
            if($n%10==0) {
                $page_contents .= $line.'<br/>';
            }
            //域名数达到1000
            if($n==$limit){
                array_push($contents, $page_contents);
                $n=0;
                $page_contents = '';
            }
    }
    //生成剩余域名的静态页面
    if($page_contents!=null){
        array_push($contents, $page_contents);
    }
    set_html($files[$i],$contents);
    fclose($file_handle);
    
    $page_contents = '';
    $contents = array();
}
/*静态页面设置
 * 
 * $html_name html文件的名字
 * $contents 放入全部静态页面的内容
 * 
 */
function set_html($html_name,$contents){
    $html_dir="../static_html/"; //放置静态页面的文件夹
    $pages = count($contents); //总页面数
    
    for($n=0;$n<$pages;$n++){
        $current_page=$n+1;
        //总页数为1时
        if($pages==1){
            $change_page='<a href=\'./list/list.html\'>返回</a>';
        }
        //总页数为2时
        if($pages==2){
            if($current_page==1){
                $next_page = $current_page+1;
                $change_page = "<a href=\"".$html_name."_".$next_page.".html\">下一页</a>".
                                "<a href=\"./list/list.html\">返回</a>";
            }else{
                $front_page = $current_page-1;
                $change_page = "<a href=\"".$html_name."_".$front_page.".html\">上一页</a>".
                                "<a href=\"./list/list.html\">返回</a>";
            }
        }
        //总页数为3以上
        if($pages>=3){
            if ($current_page==1){
                $next_page = $current_page+1;
                $change_page="<a href=\"".$html_name."_".$next_page.".html\">下一页</a>".
					"<a href=\"".$html_name."_".$pages.".html\">末页</a>".
					"<a href=\"./list/list.html\">返回</a>";
            } else if ($current_page==$pages){
                $front_page = $current_page-1;
                $change_page="<a href=\"".$html_name."_1.html\">首页</a>".
					"<a href=\"".$html_name."_".$front_page.".html\">上一页</a>".
					"<a href=\"./list/list.html\">返回</a>";
            }  else {
                $front_page=$current_page-1;
		$next_page=$current_page+1;
		$change_page="<a href=\"".$html_name."_1.html\">首页</a>".
					"<a href=\"".$html_name."_".$front_page.".html\">上一页</a>".
					"<a href=\"".$html_name."_".$next_page.".html\">下一页</a>".
					"<a href=\"".$html_name."_".$pages.".html\">末页</a>".
					"<a href=\"./list/list.html\">返回</a>";
            }
        }
        $html_content=html_content($html_name,$contents[$n],$current_page,$change_page);
        produce_static_html($html_name,$html_content,$current_page,$html_dir);
    }
   return;
}
/*生成静态页面内容
 * 
 * $html_name html文件名字
 * $content 放入html文件的内容
 * $current_page 当前页数
 * $change_page 变换页面
 * 
 */
function html_content($html_name,$content,$current_page,$change_page){
    $html_content="<html>".
    "<head>".
    "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">".
    "<title>".$html_name."_".$current_page."</title>".
    "</head>".
    "<body>
            <center>".$content.
            "</center>".
            "<div align=\"center\">".$change_page."</div>
    </body>".
    "</html>";
    return $html_content;
}
/*生成静态页面
 * 
 * $html_name html文件名字
 * $html_content html文件内容
 * $current_page 当前页数
 * $html_dir 存放html的文件夹
 * 
 */
function produce_static_html($html_name,$html_content,$current_page,$html_dir){
	$static_html = $html_name."_".$current_page.".html";
	file_put_contents($html_dir.$static_html,$html_content);
	return;
}