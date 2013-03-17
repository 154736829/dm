<?php
	//$argv[1] 处理的文档
    $handle=fopen($argv[1], 'r');
	
	//筛选有价值域名
    while($line = trim(fgets($handle))){
        preg_match('/([a-z0-9]+)/', $line,$domain);
        $open_py = fopen('./py.txt','r');
        while($pick = trim(fgets($open_py))){
            preg_match('/([a-z]+)/', $pick,$py);
            if(preg_match("/^(".$py[1].")+[0-9]{1,3}$/",$domain[1])){
            file_put_contents('./pick.txt', $line, FILE_APPEND);
            }
        }
        fclose($open_py);
    }
	
    fclose($handle);
    file_put_contents($argv[1], '0');
    return 0;

    