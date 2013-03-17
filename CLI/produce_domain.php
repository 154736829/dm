<?php
set_time_limit(0);

fwrite(STDOUT,"请输入构建域名长度(整数形式):\n");
$flag = false;
while($length = trim(fgets(STDIN))){
	if(!$flag){
		if(is_numeric($length) && $length>0) {break;}
		else {fwrite(STDOUT,"请输入正确格式(整数形式):\n");}
		continue;
	}
}

$letterArr = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","1","2","3","4","5","6","7","8","9","0");

//检查上次匹配的最后一个域名
if(file_exists('lastword.txt')){
	$handle = fopen('lastword.txt','r');
    $lastWord = fgets($handle);
    fclose($handle);
	
	fwrite(STDOUT,"上一个创建域名是".$lastWord."是否重新构建(y/n):\n");
	
	while($flag=trim(fgets(STDIN))){
		if($flag==y) {system("rm ./word_*.txt");system("rm ./lastword.txt"); genword($letterArr,$length); break;}
		else if($flag==n) {exit('exit');}
		else { fwrite(STDOUT,"请输入y或者n:\n");}
	}
}

genword($letterArr,$length);
fwrite(STDOUT,"域名构建完成\n");

/**
 *根据$letterArr中的字母生成各种组合
 *
 * $letterArr   字母及数字构成的数组
 * $depth       生成的字符最大长度
 * $word        初始字符
 * $currDepth   上一个字符串的长度
 * $n           当前要添加的字符的位置
 */
function genword($letterArr, $depth, $word = '', $currDepth = 0,$n = 0) {
	if($currDepth == $depth) return ;
        
	for ($num=$n;$num<count($letterArr); $num++) {
            $newWord = "{$word}{$letterArr[$num]}";
            //获取最后更新的字符
            $handle=fopen('lastword.txt','w');
            fwrite($handle, $newWord);
            fclose($handle);
            
            file_put_contents("./word_{$currDepth}.txt", "$newWord\n", FILE_APPEND);
            genword($letterArr,$depth,$newWord,$currDepth+1,$num);
        }
	return 0;
}

