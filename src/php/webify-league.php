<?php
/******************************************************************************\
|                                                                              |
|                              webify-league.php                               |
|                                                                              |
|******************************************************************************|
|                                                                              |
|        This is a utility creating a web page of team athletes.               |
|                                                                              |
|        Author(s): Abe Megahed                                                |
|                                                                              |
|        This file is subject to the terms and conditions defined in           |
|        'LICENSE.txt', which is part of this source code distribution.        |
|                                                                              |
|******************************************************************************|
|  Copyright (C) 2024 Data Science Institute, Univeristy of Wisconsin-Madison  |
\******************************************************************************/

$leagueTemplate = file_get_contents('../templates/league.html');
$stylesTemplate = file_get_contents('../templates/league.css');
$teamTemplate = file_get_contents('../templates/team.tpl');

//
// methods
//

function webifyLeague($directory) {
	$html = '';

	// iterate over all teams
	//
	$iterator = new DirectoryIterator($directory);
	foreach ($iterator as $file) {
		if (!$file->isFile()) {
			echo "Adding " . $file->getFilename() . "\n";

			$path = $file->getPath();
			$filename = $file->getFilename();
			if (!str_starts_with($filename, '.')) {
				$html .= webifyTeam($filename, $path);
			}
		}
	}

	return $html;
}

function webifyTeam($team, $directory) {
	echo "Webifying " . $team . " in " . $directory . "\n";

	// compute substitutions
	//
	$src = 'Teams/' . $team . '/' . $team . '.png';
	$href = 'Teams/' . $team . '/index.html';

	// format web content
	//
	global $teamTemplate;
	$html = $teamTemplate;
	$html = str_replace('<%= src %>', str_replace("'", "\'", $src), $html);
	$html = str_replace('<%= name %>', $team, $html);
	$html = str_replace('<%= href %>', $href, $html);

	return $html;
}

//
// main
//

if (count($argv) != 2) {
	echo "Usage: php webify-league.php <source-directory-name>";
	exit();
}

$path = $argv[1];
$name = basename($argv[1]);
$leagueLogo = $name . '.svg';

echo "path = " . $path . "\n";

// create web page
//
$html = $leagueTemplate;
$html = str_replace('<%= logo %>', 'images/' . $leagueLogo, $html);
$html = str_replace('<%= title %>', $name, $html);
$html = str_replace('<%= items %>', webifyLeague($path . '/Teams'), $html);
file_put_contents($path . '/index.html', $html);
file_put_contents($path . '/styles.css', $stylesTemplate);