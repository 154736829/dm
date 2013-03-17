<?php
$dispatch = new dispatch();
$dispatch->dispatching();

/**调度器
*	$file : 调度的文件
*	$class : 调度的类
*	$action : 调度的函数
*/
class dispatch{
	function dispatching(){
		$file = isset($_GET['f'])?($_GET['f']):'default';
		$action = isset($_GET['a'])?($_GET['a']):'index';
                
		$class = "control_$file";
		$filePath = DIR."/control/control_$file.php";
		
		if(file_exists($filePath)){
			include_once($filePath);
		} else { 
			exit (文件不存在);
		}
		
		$dispatchClass = new $class();
		if(!method_exists($dispatchClass,$action)){exit (函数不存在);}
		$dispatchClass ->{$action}();
	}
}