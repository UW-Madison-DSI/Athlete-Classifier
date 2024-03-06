<?php
/******************************************************************************\
|                                                                              |
|                               nfl_athlete.php                                |
|                                                                              |
|******************************************************************************|
|                                                                              |
|        This defines a class for fetching, storing, and saving athletes.      |
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

class NFLAthlete extends Athlete {

	//
	// attributes
	//

	public static $table = 'nfl_athletes';
	public static $baseUrl = ESPN_API_URL . '/sports/football/leagues/nfl/athletes';

	//
	// methods
	//

	/**
	 * Get this athlete's database table.
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