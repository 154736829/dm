<?php
set_time_limit(0);

/*
* $lineNum 文档行数
* $content 文档内容
* $emptyProcessID 空闲进程编号
* $limit 进程限制数
*/
$lineNum = 0;
$content = "";
$emptyProcessID = array();
$limit = isset($argv[1])?intval($argv[1]):5;
if(createProcess($limit)!=1) exit("创建文件失败\n");

//运行多进程
while($line = fgets(STDIN)){
	$lineNum++;
	$content.=$line;
	//如果数据达到一定数量，转交子进程运行
	if($lineNum==50){
		if(count($emptyProcessID)==0) $emptyProcessID = getEmptyProcess($limit);
		workProcess($emptyProcessID[0],$content);
		array_shift($emptyProcessID);
		$content = "";
	}
}
if($content !="") workProcess(0,$content);

//确保进程全部运行完毕
while(count($emptyProcessID)!=$limit){
	$emptyProcessID = getEmptyProcess($limit);
	$emptyProcessID = array_unique($emptyProcessID);
}
while(file_get_contents('./process/p0')==0){
	closeAllProcess(); 
	exit("完结\n");
}

/**创建进程
 * $limit 进程限制数
 */
function createProcess($limit){
	for($n=0;$n<=$limit;$n++){
		file_put_contents("./process/p{$n}",0);
	}
	return 1;
}

/**获取空闲进程
 * $limit 进程限制数
 */
function getEmptyProcess($limit){
	$emptyProcess = array();
	while(true){
		for($processID=1;$processID<=$limit;$processID++){
			$processPath = "./process/p{$processID}";
			if(file_get_contents("./process/p{$processID}")==0) array_push($emptyProcess, $processID);
		}
		if(count($emptyProcess)==0) {sleep(1);}
		else{ return $emptyProcess;}
	}
}

/**运行子进程
 * $processID 进程ID编号
 * $content 要处理的内容
 */
function workProcess($processID,$content){
	$process = "./process/p{$processID}";
	file_put_contents($process,$content);
	system("cat {$process} | nohup php whois.php {$process} >>./output.log 2>>./err.log &");
	return 1;
}

/**关闭进程
 *
*/
function closeAllProcess(){
	system("rm ./process/p*");
	return 1;
}
