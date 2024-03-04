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
|  Copyright (C) 2022 Data Science Institute, Univeristy of Wisconsin-Madison  |
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
		$ref = $item->{'$ref'};
		$id = basename(substr($ref, 0, strpos($ref, "?")));

		// fetch new team
		//
		$team = new Team([
			'id' => $id
		]);
		$team->fetch();

		// store team in database
		//
		$team->store($db);

		// save team to storage
		//
		$teamDir = $teamsDir . '/' . $team->get('displayName');
		echo "saving team to " . $teamDir . "\n";
		$team->save($teamDir);
	}
}

//
// main
//

fetchTeams($teamsDir, $db);