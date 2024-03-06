<?php
/******************************************************************************\
|                                                                              |
|                                fetch_team.php                                |
|                                                                              |
|******************************************************************************|
|                                                                              |
|        This defines a utility for fetching NCAA teams.                        |
|                                                                              |
|        Author(s): Abe Megahed                                                |
|                                                                              |
|        This file is subject to the terms and conditions defined in           |
|        'LICENSE.txt', which is part of this source code distribution.        |
|                                                                              |
|******************************************************************************|
|  Copyright (C) 2024 Data Science Institute, Univeristy of Wisconsin-Madison  |
\******************************************************************************/

include "models/ncaa/ncaa_team.php";
include 'utilities/database.php';

//
// variables
//

$leagueDir = '../NCAA';
$teamsDir = $leagueDir . '/Teams';

//
// methods
//

/**
 * Fetch a team and save it to the file system and database.
 *
 * @param  integer $id - The id of the team to fetch.
 * @param  string $teamsDir - The directory name to save team data.
 * @param  object $db - The database to store team data.
 * @return App\Models\Team
 */
function fetchNCAATeam($id, $teamsDir, $db) { 

	// fetch team
	//
	$team = new NCAATeam([
		'id' => $id
	]);
	$team->fetch();

	// save team to storage
	//
	$teamDir = $teamsDir . '/' . $team->get('displayName');
	echo "saving team to " . $teamDir . "\n";
	$team->save($teamDir);

	// store team in database
	//
	$team->store($db);

	return $team;
}

/**
 * Fetch all teams and save them to the file system and database.
 *
 * @param  integer $id - The id of the athelete to fetch.
 * @param  string $teamsDir - The directory name to save athelete data.
 * @return App\Models\Athlete
 */
function fetchNCAATeams($leagueDir, $teamsDir, $db) { 

	// fetch teams
	//
	$teams = json_decode(file_get_contents(NCAATeam::$baseUrl . '?limit=1000'));
	if (!$teams) {
		return;
	}

	// ouput team metadata
	//
	echo "teams count = " . $teams->count . "\n";

	// create new league directory
	//
	if (!file_exists($leagueDir)) {
		mkdir($leagueDir);
	}

	// create new teams directory
	//
	if (!file_exists($teamsDir)) {
		mkdir($teamsDir);
	}

	// iterate over teams
	//
	for ($i = 0; $i < count($teams->items); $i++) {
		$item = $teams->items[$i];

		// parse team id from ref
		//
		$ref = $item->{'$ref'};
		$id = basename(substr($ref, 0, strpos($ref, "?")));

		// fetch individual team by id
		//
		fetchNCAATeam($id, $teamsDir, $db);
	}
}

//
// main
//

fetchNCAATeams($leagueDir, $teamsDir, $db);