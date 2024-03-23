<?php
/******************************************************************************\
|                                                                              |
|                                fetch_team.php                                |
|                                                                              |
|******************************************************************************|
|                                                                              |
|        This is a utility for fetching, storing, and saving a team.           |
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
// methods
//

/**
 * Fetch a team and save it to the file system and database.
 *
 * @param string $league - The league of the team to fetch.
 * @param string $id - The id of the team to fetch.
 * @param int $year - The year of the team to fetch.
 * @param string $teamsDir - The directory name to save team data.
 * @param object $db - The database to store team data.
 * @return App\Models\Team
 */
function fetchTeam(string $league, string $id, int $year, string $teamsDir, $db) { 

	// create team
	//
	$team = new Team([
		'league' => $league,
		'id' => $id,
		'year' => $year
	]);

	// fetch team
	//
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

//
// main
//

// check command line arguments
//
if (count($argv) < 3) {
	echo "Usage: php fetch_team.php <LEAGUE> <TEAMID> [<YEAR>]\n";
	echo "Leagues: NFL, NCAA \n";
	exit();
}

// parse league
//
$league = $argv[1];
$leagueDir = '../../data/' . $league;

// parse team id
//
$teamId = intval($argv[2]);

// parse year
//
if (count($argv) >= 4) {
	$year = intval($argv[3]);
} else {
	$year = date("Y");
}

// create new league directory
//
if (!file_exists($leagueDir)) {
	mkdir($leagueDir);
}

// create new teams directory
//
$teamsDir = $leagueDir . '/Teams';
if (!file_exists($teamsDir)) {
	mkdir($teamsDir);
}

// fetch team by id
//
echo "Fetching " . $league . " team " . $teamId . " from " . $year . ".\n";
fetchTeam($league, $teamId, $year, $teamsDir, $db);