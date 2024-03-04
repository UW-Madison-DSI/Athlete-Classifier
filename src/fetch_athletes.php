<?php
/******************************************************************************\
|                                                                              |
|                              fetch_athletes.php                              |
|                                                                              |
|******************************************************************************|
|                                                                              |
|        This defines a utility for fetching NFL athletes.                     |
|                                                                              |
|        Author(s): Abe Megahed                                                |
|                                                                              |
|        This file is subject to the terms and conditions defined in           |
|        'LICENSE.txt', which is part of this source code distribution.        |
|                                                                              |
|******************************************************************************|
|  Copyright (C) 2024 Data Science Institute, Univeristy of Wisconsin-Madison  |
\******************************************************************************/

include "models/athlete.php";
include 'utilities/database.php';

//
// variables
//

$athletesDir = '../Athletes';
$start = 0;

//
// methods
//

function fetchAthlete($id, $athletesDir, $db) {

	// fetch athelete info
	//
	$athlete = new Athlete([
		'id' => $id
	]);
	$athlete->fetch();
	if (!$athlete) {
		return;
	}

	// create letter directory, if necessary
	//
	$lastName = $athlete->get('lastName');
	$firstLetter = substr($lastName, 0, 1);
	if ($firstLetter == '[') {
		return;
	}
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

	// store athlete in file system and database
	//
	echo "saving athlete to " . $athleteDir . "\n";
	$athlete->save($athleteDir);
	$athlete->store($db);

	// store demographics in file system and database
	//
	$athlete->saveDemographics($athleteDir);
	$athlete->storeDemographics($db, $athleteDir);
}

function fetchAthletes($athletesDir, $db, $start = 0) { 
	$athletes = json_decode(file_get_contents(Athlete::$baseUrl));
	if (!$athletes) {
		return;
	}
	
	echo "athletes count = " . $athletes->count . "\n";
	echo "page size = " . $athletes->pageSize . "\n";
	echo "page count = " . $athletes->pageCount . "\n";

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
			$data = json_decode(file_get_contents(Athlete::$baseUrl . '?page=' . $i));
			if ($data) {
				for ($j = 0; $j < count($data->items); $j++) {
					$index = ($i * $athletes->pageSize) + $j;

					// check if athlete is after start index
					//
					if ($index > $start) {

						// fetch athlete by id
						//
						$item = $data->items[$j];
						if ($item) {
							$ref = $item->{'$ref'};
							$id = basename(substr($ref, 0, strpos($ref, "?")));
							fetchAthlete($id, $athletesDir, $db);
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

fetchAthletes($athletesDir, $db, $start);