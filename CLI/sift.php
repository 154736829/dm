<?php
	set_time_limit(0);
	$file=dirname(_FILE_).'/sift_rule.txt';//匹配规则的文件
        $rules = array(); 
        
	$preg_rule_handle=fopen($file,'r');

        //匹配规则
        while($preg_string=fgets($preg_rule_handle)){
            array_push($rules, $preg_string);
        }
        //进行匹配
        while($line=fgets(STDIN)){
            foreach($rules as $key=>$value){
                $preg_rule = explode(":",$value);
                if(preg_match("/".trim($preg_rule[1])."/ies",$line)){
                        file_put_contents("./aftersift/".$preg_rule[0],$line,FILE_APPEND);
                }
            }
	}
	
	fclose($preg_rule_handle);
	echo "success";