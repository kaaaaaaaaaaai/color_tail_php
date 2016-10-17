<?php
/*
 *              _        _ _
 * _ __       | |_ __ _(_) |
 *| '_ \ _____| __/ _` | | |
 *| |_) |_____| || (_| | | |
 *| .__/       \__\__,_|_|_|
 *|_|
 *
 */
const colors = [
	"black"	=>	"0",
	"red"	=>	"1",
	"green"	=>	"2",
	"yellow"=>	"3",
	"blue"	=>	"4",
	"magenta"=>	"5",
	"cyan"	=>	"6",
	"white"	=> 	"7",
];

const paintPlace = [
	"characters" => "3",
	"background" => "4",
];

//get filePath by arguments
$filename = isset($argv[1])?$argv[1]:exit(1);

//load setting file
$settings  = json_decode(file_get_contents('config.json'), true);

//get last update time & full file size
$lastFileTime = filemtime($filename);
$fileOffset = filesize($filename);

while(true){

	//cache delete
	clearstatcache(true, $filename);

	//get file size & last update time
	$fileSize = filesize($filename);
	$fileTime = filemtime($filename);

	if($lastFileTime !== $fileTime){

		$lastFileTime = $fileTime;
		$contents = file_get_contents($filename, false, null, $fileOffset, $fileSize);
		
		$lastFileTime = $fileTime;
		$fileOffset = $fileSize;

		foreach ($settings["data"] as $data) {

			preg_match_all($data["target"], $contents, $matches);


			if (empty($matches[0])) {
				echo $contents;
				continue;
			}

			//重複削除とkey振り直し
			$matches = array_values(array_unique($matches[0]));

			$patterns = [];
			$replaces = [];
			foreach ($matches as $m) {
				echo $m;
				$patterns[] = "/{$m}/";
				$colorCode = colorScheme($data["paint"], $data["color"]);
				$replaces[] = "\e[{$colorCode}m{$m}\e[m";
			}

			$contents = preg_replace($patterns, $replaces, $contents);
		}
		echo $contents;
	}
}

function colorScheme($place, $color){

	if(!array_key_exists($color, colors) && !array_key_exists($place, paintPlace)){
		return null;
	}
	return paintPlace[$place].colors[$color];
}