<?php
/******************************************************************************\
|                                                                              |
|                            fetch_ncaa_athletes.php                           |
|                                                                              |
|******************************************************************************|
|                                                                              |
|        This defines a utility for fetching NCAA athletes.                     |
|                                                                              |
|        Author(s): Abe Megahed                                                |
|                                                                              |
|        This file is subject to the terms and conditions defined in           |
|        'LICENSE.txt', which is part of this source code distribution.        |
|                                                                              |
|******************************************************************************|
|  Copyright (C) 2024 Data Science Institute, Univeristy of Wisconsin-Madison  |
\******************************************************************************/

include "models/ncaa/ncaa_athlete.php";
include 'utilities/database.php';

//
// variables
//

$leagueDir = '../NCAA';
$athletesDir = $leagueDir . '/Athletes';
$start = 3300;

//
// methods
//

/**
 * Fetch an athlete and save it to the file system and database.
 *
 * @param  integer $id - The id of the athelete to fetch.
 * @param  string $athletesDir - The directory name to save athelete data.
 * @return App\Models\Athlete
 */
function fetchNCAAAthlete($id, $athletesDir, $db) {

	// fetch athelete info
	//
	$athlete = new NCAAAthlete([
		'id' => $id
	]);
	$athlete->fetch();
	if (!$athlete) {
		return;
	}

	// find first letter of athlete's last name
	//
	$lastName = $athlete->get('lastName');
	if ($lastName == '' || $lastName == '`') {
		return;
	}
	$firstLetter = substr($lastName, 0, 1);
	if ($firstLetter == '[' || $firstLetter == ' ' || $firstLetter == '-') {
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
 * @param string $athletesDir - The directory name to save athelete data.
 * @param string $db - The database to store athelete data.
 * @param int $start - The first athelete to save.
 */
function fetchNCAAAthletes($leagueDir, $athletesDir, $db, $start = 0) { 
	$athletes = json_decode(file_get_contents(NCAAAthlete::$baseUrl));
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
			$data = json_decode(file_get_contents(NCAAAthlete::$baseUrl . '?page=' . $i));
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
							fetchNCAAAthlete($id, $athletesDir, $db);
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

fetchNCAAAthletes($leagueDir, $athletesDir, $db, $start);