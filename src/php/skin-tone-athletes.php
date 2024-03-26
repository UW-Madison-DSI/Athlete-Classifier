<?php
/******************************************************************************\
|                                                                              |
|                            skin-tone-athletes.php                            |
|                                                                              |
|******************************************************************************|
|                                                                              |
|        This is a utility for classifying the skin tone of athletes.          |
|                                                                              |
|        Author(s): Abe Megahed                                                |
|                                                                              |
|        This file is subject to the terms and conditions defined in           |
|        'LICENSE.txt', which is part of this source code distribution.        |
|                                                                              |
|******************************************************************************|
|  Copyright (C) 2024 Data Science Institute, Univeristy of Wisconsin-Madison  |
\******************************************************************************/

//
// methods
//

function skinToneDirectory($directory) {
	$html = '';
	$basename = basename($directory);

	// iterate over all athletes
	//
	$iterator = new DirectoryIterator($directory);
	foreach ($iterator as $file) {
		if (!$file->isFile()) {
			$pathname = $file->getPath();
			$dirname = $file->getFilename();

			# analyze athlete directory
			#
			if ($dirname != '.' && $dirname != '..' && $dirname != $basename) {
				$path = $pathname . '/' . $dirname;
				skinToneAthlete($path);
			}
		}
	}

	return $html;
}

function skinToneAthlete($directory) {
	$iterator = new DirectoryIterator($directory);
	foreach ($iterator as $file) {
		if ($file->isFile()) {
			$filename = $file->getFilename();
			if (str_ends_with($filename, '.png') &&
				!str_ends_with($filename, 'Report.png') && 
				!str_ends_with($filename, '(rgb).png')) {
				$path = $file->getPath() . '/' . $file->getFilename();
				echo "Analyzing skin tone of athlete " . $file->getFilename() . "\n";

				// run classifier
				//
				$command = "python3 ../python/skin_tone.py " . '"' . $path . '"';
				shell_exec($command);
			}
		}
	}
}

//
// main
//

if (count($argv) != 2) {
	echo "Usage: php skin-tone-athletes.php <source-directory-name>";
	exit();
}

$path = $argv[1];
echo "Analyzing skin tones of " . $path . "\n";

// create web page
//
skinToneDirectory($path);