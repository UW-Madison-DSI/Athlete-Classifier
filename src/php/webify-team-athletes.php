<?php
/******************************************************************************\
|                                                                              |
|                           webify-team-athletes.php                           |
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

$teamTemplate = file_get_contents('../templates/team.html');
$stylesTemplate = file_get_contents('../templates/team.css');
$playerTemplate = file_get_contents('../templates/player.tpl');

$monkTones = [
	'Monk01' => '#f6ede4',
	'Monk02' => '#f3e7db',
	'Monk03' => '#f7ead0',
	'Monk04' => '#eadaba',
	'Monk05' => '#d7bd96',
	'Monk06' => '#a07e56',
	'Monk07' => '#825c43',
	'Monk08' => '#604134',
	'Monk09' => '#3a312a',
	'Monk10' => '#292420'
];

//
// methods
//

function webifyTeam($directory) {
	$html = '';

	// iterate over all teams
	//
	$iterator = new DirectoryIterator($directory);
	foreach ($iterator as $file) {
		if (!$file->isFile()) {
			$path = $file->getPath();
			$filename = $file->getFilename();
			if (!str_starts_with($filename, '.')) {
				$html .= webifyAthlete($filename, $path);
			}
		}
	}

	return $html;
}

function webifyAthlete($athlete, $directory) {
	$demography = json_decode(file_get_contents($directory . '/' . $athlete . '/' . $athlete . ' Demography.json'));
	$skinTone = json_decode(file_get_contents($directory . '/' . $athlete . '/' . $athlete . ' Skin Tone.json'));

	echo "Webifying " . $athlete . " in " . $directory . "\n";

	// compute substitutions
	//
	$src = $athlete . '/' . $athlete . '.png';
	$report = $athlete . '/' . $athlete . ' Report.png';

	// compute skin tone info
	//
	global $monkTones;
	// $faceSkinTone = $skinTone->faces[0]->skin_tone;
	$toneLabel = $skinTone->faces[0]->tone_label;
	$faceSkinTone = $monkTones[$toneLabel];
	$dominantColor1 = $skinTone->faces[0]->dominant_colors[0]->color ?? 'black';
	$dominantColor2 = $skinTone->faces[0]->dominant_colors[1]->color ?? 'black';
	$dominantColor3 = $skinTone->faces[0]->dominant_colors[2]->color ?? 'black';
	$dominantStrength1 = round(floatval($skinTone->faces[0]->dominant_colors[0]->percent) * 100);
	$dominantStrength2 = round(floatval($skinTone->faces[0]->dominant_colors[1]->percent) * 100);

	// compute race info
	//
	$dominantRace = $demography[0]->dominant_race;
	$asianRace = round($demography[0]->race->asian);
	$indianRace = round($demography[0]->race->indian);
	$blackRace = round($demography[0]->race->black);
	$whiteRace = round($demography[0]->race->white);
	$middleEasternRace = round($demography[0]->race->{'middle eastern'});
	$latinoHispanicRace = round($demography[0]->race->{'latino hispanic'});

	// fix issue with failure on dark faces
	//
	if ($demography[0]->face_confidence == 0) {
		$dominantRace = 'black';
	}

	// adjust wording
	//
	if ($dominantRace == 'middle eastern') {
		$dominantRace = 'mideastern';
	}
	if ($dominantRace == 'latino hispanic') {
		$dominantRace = 'latino';
	}
	$dominantRace = ucwords($dominantRace);

	// format web content
	//
	global $playerTemplate;
	$html = $playerTemplate;
	$html = str_replace('<%= href %>', $src, $html);
	$html = str_replace('<%= icon %>', str_replace("'", "\'", $src), $html);
	$html = str_replace('<%= name %>', $athlete, $html);
	$html = str_replace('<%= report %>', $report, $html);

	// format skin tones
	//
	$html = str_replace('<%= skin_tone %>', $faceSkinTone, $html);
	$html = str_replace('<%= tone_label %>', $toneLabel, $html);
	$html = str_replace('<%= dominant_color1 %>', $dominantColor1, $html);
	$html = str_replace('<%= dominant_strength1 %>', $dominantStrength1, $html);
	$html = str_replace('<%= dominant_color2 %>', $dominantColor2, $html);
	$html = str_replace('<%= dominant_strength2 %>', $dominantStrength2, $html);

	// format race info
	//
	$html = str_replace('<%= dominant_race %>', $dominantRace, $html);
	$html = str_replace('<%= asian_race %>', $asianRace, $html);
	$html = str_replace('<%= indian_race %>', $indianRace, $html);
	$html = str_replace('<%= black_race %>', $blackRace, $html);
	$html = str_replace('<%= white_race %>', $whiteRace, $html);
	$html = str_replace('<%= middle_eastern_race %>', $middleEasternRace, $html);
	$html = str_replace('<%= latino_hispanic_race %>', $latinoHispanicRace, $html);

	return $html;
}

//
// main
//

if (count($argv) != 2) {
	echo "Usage: php webify-team-athletes.php <source-directory-name>";
	exit();
}

$path = $argv[1];
$name = basename($argv[1]);
$teamLogo = $name . '.png';

// create web page
//
$html = $teamTemplate;
$html = str_replace('<%= logo %>', $teamLogo, $html);
$html = str_replace('<%= title %>', $name, $html);
$html = str_replace('<%= items %>', webifyTeam($path), $html);
file_put_contents($argv[1] . '/' . 'index.html', $html);
file_put_contents($argv[1] . '/' . 'styles.css', $stylesTemplate);