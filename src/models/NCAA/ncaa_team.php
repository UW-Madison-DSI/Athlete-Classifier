<?php
/******************************************************************************\
|                                                                              |
|                               ncaa_team.php                                  |
|                                                                              |
|******************************************************************************|
|                                                                              |
|        This defines a class for fetching, storing, and saving teams.         |
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

class NCAATeam extends Team {

	//
	// attributes
	//

	public static $table = 'ncaa_teams';
	public static $baseUrl = ESPN_API_URL . '/sports/football/leagues/college-football/seasons/2024/teams';

	//
	// methods
	//

	/**
	 * Get this team's database table.
	 *
	 * @return string
	 */
	public function table() {
		return self::$table;
	}

	/**
	 * Get this team's base url.
	 *
	 * @return string
	 */
	public function baseUrl() {
		return self::$baseUrl;
	}
}