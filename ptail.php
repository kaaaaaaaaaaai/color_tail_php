<?php
$filename = isset($argv[1])?$argv[1]:exit(1);
$lastFileTime = 0;
$fileOffset = filesize($filename);//-100;
while(true){
	clearstatcache(true, $filename);
	$fileSize = filesize($filename);
	$fileTime = filemtime($filename);
	if($lastFileTime !== $fileTime){
		$lastFileTime = $fileTime;
		 $contents = file_get_contents($filename, false, null, $fileOffset, $fileSize);
		
		$lastFileTime = $fileTime;
		$fileOffset = $fileSize;
	
		$target = "/(o(.*?))gita/";
		echo $contents;
		preg_match_all($target, $contents, $matches);
		
		$matches = array_values(array_unique($matches[0]));

		print_r($matches);
		foreach($matches as $m){
			echo $m;
			$patterns[] = "/{$m}/";
			$replaces[] = "\e[31m{$m}\e[m";
		}
		print_r($patterns);
		echo preg_replace($patterns, $replaces, $contents);
	}
}

