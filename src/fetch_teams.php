<?php
/******************************************************************************\
|                                                                              |
|                                fetch_teams.php                               |
|                                                                              |
|******************************************************************************|
|                                                                              |
|        This is a utility for fetching, storing, and saving teams.            |
|                                                                              |
|        Author(s): Abe Megahed                                                |
|                                                                              |
|        This file is subject to the terms and conditions defined in           |
|        'LICENSE.txt', which is part of this source code distribution.        |
|                                                                              |
|******************************************************************************|
|  Copyright (C) 2024 Data Science Institute, Univeristy of Wisconsin-Madison  |
\******************************************************************************/

include "models/nfl/nfl_team.php";
include "models/ncaa/ncaa_team.php";
include 'utilities/database.php';

//
// methods
//

/**
 * Fetch a team and save it to the file system and database.
 *
 * @param  string $league - The league of the team to fetch.
 * @param  string $id - The id of the team to fetch.
 * @param  string $teamsDir - The directory name to save team data.
 * @param  object $db - The database to store team data.
 * @return App\Models\Team
 */
function fetchTeam(string $league, string $id, string $teamsDir, $db) { 

	// create team
	//
	switch ($league) {
		case 'NFL':
			$team = new NFLTeam([
				'id' => $id
			]);
			break;
		case 'NCAA':
			$team = new NCAATeam([
				'id' => $id
			]);
			break;
	}
	if (!$team) {
		return;
	}

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

/**
 * Fetch all teams and save them to the file system and database.
 *
 * @param string $league - The league of the teams to fetch.
 * @param string $leagueDir - The directory name of the league.
 * @param string $teamsDir - The directory name to save teams data.
 * @param object $db - The database to store teams data.
 * @return App\Models\Athlete
 */
function fetchTeams(string $league, string $leagueDir, string $teamsDir, $db) { 

	// fetch teams
	//
	switch ($league) {
		case 'NFL':
			$teams = json_decode(file_get_contents(NFLTeam::$baseUrl . '?limit=1000'));
			break;
		case 'NCAA':
			$teams = json_decode(file_get_contents(NCAATeam::$baseUrl . '?limit=1000'));
			break;
	}
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
		fetchTeam($league, $id, $teamsDir, $db);
	}
}

//
// main
//

// check command line arguments
//
if (count($argv) < 2) {
	echo "Usage: php fetch_teams.php <LEAGUE> \n";
	echo "Leagues: NFL, NCAA \n";
	exit();
}

// parse league
//
$league = $argv[1];
$leagueDir = '../' . $league;
$teamsDir = $leagueDir . '/Teams';

// fetch all teams in league
//
fetchTeams($league, $leagueDir, $teamsDir, $db);