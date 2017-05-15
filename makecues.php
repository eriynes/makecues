<?php 
	$dir    = exec("pwd");
	$total_files = count(glob("$dir/*"));
	$data = "";
	$previous = "TBR";
	$count = 1;
	echo "\n\n-------------------------------------------[[[BEGINNING TO PROCESS]]]--------------------------------------------------\n\n";
	foreach(glob($dir.'/*') as $file) {
		$file_ext	= strtolower(end(explode('.',$file)));
		if($file_ext==='bin'){
			$file_name	= end(explode('/',$file)); 
			preg_match('/(.*)\(Track (\d+)/',$file_name,$matches);
			if(!empty($matches)){
				$file_part = $matches[1];
				$track_number = sprintf('%02d',$matches[2]);
			} else {
				$file_part = $file_name;
				$track_number = "01";
			}
			if ($file_part==$previous){
				$data .= "FILE \"$file_name\" BINARY\r\n  TRACK $track_number AUDIO\r\n    INDEX 00 00:00:00\r\n    INDEX 01 00:02:00\r\n";
			}	else {	
				if(preg_match('/bin/',$previous)){
					$file2write = str_replace("bin","cue",$previous);
				} else {
					$file2write = $previous.".cue";
				}
				if(!file_exists($file2write)) {
					file_put_contents($file2write,$data);
					echo "Wrote File $file2write successfully\n";
				}	
				
				$previous = $file_part;
				$data = "FILE \"$file_name\" BINARY\r\n  TRACK $track_number MODE2/2352\r\n    INDEX 01 00:00:00\r\n";
			}	
		}
		$count++;
		if($count==$total_files){
			if(preg_match('/bin/',$previous)){
				$file2write = str_replace("bin","cue",$previous);
			} else {
				$file2write = $previous.".cue";
			}
			if(!file_exists($file2write)) {
				file_put_contents($file2write,$data);
				echo "Wrote File $file2write successfully\n";
			}	
		}
	}
	exec("rm TBR.cue");
	echo "\n\n-------------------------------------------[[[DONE]]]--------------------------------------------------\n\n";

?>
