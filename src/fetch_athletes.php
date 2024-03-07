<?php
/******************************************************************************\
|                                                                              |
|                              fetch_athletes.php                              |
|                                                                              |
|******************************************************************************|
|                                                                              |
|        This defines a utility for fetching athletes.                         |
|                                                                              |
|        Author(s): Abe Megahed                                                |
|                                                                              |
|        This file is subject to the terms and conditions defined in           |
|        'LICENSE.txt', which is part of this source code distribution.        |
|                                                                              |
|******************************************************************************|
|  Copyright (C) 2024 Data Science Institute, Univeristy of Wisconsin-Madison  |
\******************************************************************************/

include "models/nfl/nfl_athlete.php";
include "models/ncaa/ncaa_athlete.php";
include 'utilities/database.php';

//
// methods
//

/**
 * Fetch an athlete and save it to the file system and database.
 *
 * @param  string $league - The league of the athlete to fetch.
 * @param  integer $id - The id of the athelete to fetch.
 * @param  string $athletesDir - The directory name to save athelete data.
 * @param  object $db - The database to store athlete data.
 * @return App\Models\Athlete
 */
function fetchAthlete($league, $id, $athletesDir, $db) {

	// create athlete
	//
	switch ($league) {
		case 'NFL':
			$athlete = new NFLAthlete([
				'id' => $id
			]);
			break;
		case 'NCAA':
			$athlete = new NCAAAthlete([
				'id' => $id
			]);
			break;
	}
	if (!$athlete) {
		return;
	}

	// fetch athelete info
	//
	$athlete->fetch();

	// find first letter of athlete's last name
	//
	$lastName = $athlete->get('lastName');
	$firstLetter = substr($lastName, 0, 1);
	if ($firstLetter == '[') {
		return;
	}

	// create letter directory, if necessary
	//
	$letterDir = ($athletesDir? $athletesDir . '/' : '') . $firstLetter;
	if (!file_exists($letterDir)) {
		mkdir($letterDir);
	}

	// skip athlete if they already exist
	//
	$fullName = $athlete->get('fullName');
	$athleteDir = $letterDir . '/' . $fullName;
	if (file_exists($athleteDir)) {
		echo "skipping " . $fullName . ", already exists. \n";
		return;
	}

	// skip athlete if no headshot
	//
	if (!$athlete->has('headshot')) {
		echo "skipping " . $fullName . ", no headshot. \n";
		return;
	}

	// save athlete in file system
	//
	echo "saving athlete to " . $athleteDir . "\n";
	$athlete->save($athleteDir);

	// store athlete in database
	//
	$athlete->store($db);

	// save athlete demographics in file system
	//
	$athlete->saveDemographics($athleteDir);

	// store athlete demographics in database
	//
	$athlete->storeDemographics($db, $athleteDir);

	// return new athlete
	//
	return $athlete;
}

/**
 * Fetch all athletes and save them to the file system and database.
 *
 * @param string $league - The league of the teams to fetch.
 * @param string $leagueDir - The directory name of the league.
 * @param string $athletesDir - The directory name to save athelete data.
 * @param object $db - The database to store athelete data.
 * @param int $start - The first athelete to save.
 */
function fetchAthletes(string $league, string $leagueDir, string $athletesDir, $db, int $start = 0) { 
	
	// fetch teams
	//
	switch ($league) {
		case 'NFL':
			$athletes = json_decode(file_get_contents(NFLAthlete::$baseUrl));
			break;
		case 'NCAA':
			$athletes = json_decode(file_get_contents(NCAAAthlete::$baseUrl));
			break;
	}
	if (!$athletes) {
		return;
	}
	
	// show athlete metadata
	//
	echo "athletes count = " . $athletes->count . "\n";
	echo "page size = " . $athletes->pageSize . "\n";
	echo "page count = " . $athletes->pageCount . "\n";

	// create new league directory
	//
	if (!file_exists($leagueDir)) {
		mkdir($leagueDir);
	}

	// create new athletes directory
	//
	if (!file_exists($athletesDir)) {
		mkdir($athletesDir);
	}

	// iterate over athlete pages
	//
	for ($i = 0; $i < $athletes->pageCount; $i++) {
		$index = ($i * $athletes->pageSize);
		if ($index >= $start) {
			$data = json_decode(file_get_contents(NFLAthlete::$baseUrl . '?page=' . $i));
			if ($data) {
				for ($j = 0; $j < count($data->items); $j++) {
					$index = ($i * $athletes->pageSize) + $j;

					// check if athlete is after start index
					//
					if ($index > $start) {
						$item = $data->items[$j];
						if ($item) {

							// parse athlete id from ref
							//
							$ref = $item->{'$ref'};
							$id = basename(substr($ref, 0, strpos($ref, "?")));

							// fetch individual athlete by id
							//
							fetchAthlete($league, $id, $athletesDir, $db);
						}
					}
				}
			}
		}
	}
}

//
// main
//

// check command line arguments
//
if (count($argv) < 2) {
	echo "Usage: php fetch_athletes.php <LEAGUE> \n";
	echo "Leagues: NFL, NCAA \n";
	exit();
}

// parse league
//
$league = $argv[1];
$leagueDir = '../' . $league;
$athletesDir = $leagueDir . '/Athletes';
$start = 0;

// fetch all athletes in league
//
fetchAthletes($league, $leagueDir, $athletesDir, $db, $start);