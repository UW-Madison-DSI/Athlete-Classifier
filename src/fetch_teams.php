<?php
/******************************************************************************\
|                                                                              |
|                                fetch_team.php                                |
|                                                                              |
|******************************************************************************|
|                                                                              |
|        This defines a utility for fetching NFL teams.                        |
|                                                                              |
|        Author(s): Abe Megahed                                                |
|                                                                              |
|        This file is subject to the terms and conditions defined in           |
|        'LICENSE.txt', which is part of this source code distribution.        |
|                                                                              |
|******************************************************************************|
|  Copyright (C) 2024 Data Science Institute, Univeristy of Wisconsin-Madison  |
\******************************************************************************/

include "models/team.php";
include 'utilities/database.php';

//
// variables
//

$teamsDir = '../Teams';

//
// methods
//

/**
 * Fetch a single team and save it to the file system and database
 *
 * @param  integer $id - The id of the team to fetch.
 * @param  string $teamsDir - The directory name to store team data.
 * @param  object $db - The database to store team data.
 * @return App\Models\Team
 */
function fetchTeam($id, $teamsDir, $db) { 

	// fetch team
	//
	$team = new Team([
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
 * Fetch a single team and save it to the file system and database
 *
 * @param  integer $id - The id of the athelete to fetch.
 * @param  string $teamsDir - The directory name to store athelete data.
 * @return App\Models\Athlete
 */
function fetchTeams($teamsDir, $db) { 
	$teams = json_decode(file_get_contents(Team::$baseUrl . '?limit=1000'));
	if (!$teams) {
		return;
	}
	
	echo "teams count = " . $teams->count . "\n";

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

		// fetch team by id
		//
		fetchTeam($id, $teamsDir, $db);
	}
}

//
// main
//

fetchTeams($teamsDir, $db);