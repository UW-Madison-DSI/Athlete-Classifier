<?php
/******************************************************************************\
|                                                                              |
|                                   team.php                                   |
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

include "config.php";
include "models/model.php";

class Team extends Model {

	//
	// attributes
	//

	public static $table = 'teams';
	public static $baseUrl = ESPN_API_URL . '/sports/football/leagues/nfl/seasons/2024/teams';

	//
	// querying methods
	//

	/**
	 * Get this athlete's url.
	 *
	 * @return string
	 */
	public function url() {
		return self::$baseUrl . '/' . $this->attributes['id'];
	}

	/**
	 * Get this athlete's table.
	 *
	 * @return string
	 */
	public function table() {
		return self::$table;
	}

	//
	// saving methods
	//

	/**
	 * Get this athlete's data to a directory.
	 *
	 * @return null
	 */
	public function save(string $dirname = '') {

		// find names
		//
		$name = $this->get('displayName');
		$filename = ($dirname? $dirname . '/' : '') . $name . '.json';

		// make new team directory
		//
		if ($dirname && !file_exists($dirname)) {
			mkdir($dirname);
		}

		// write team data
		//
		$this->saveAs($filename);

		// write team logo
		//
		if ($this->has('logos')) {
			$logos = $this->get('logos');
			$href = $logos[0]->href;
			$extension = strtolower(pathinfo($href, PATHINFO_EXTENSION));
			$imagename = ($dirname? $dirname . '/' : '') . $name . '.' . $extension;
			$image = file_get_contents($href);
			file_put_contents($imagename, $image);
		}
	}

	//
	// conversion methods
	//

	/**
	 * Convert this team to an array.
	 *
	 * @return bool - success
	 */
	public function toArray() {
		return [
			'id' => $this->get('id'),
			'location' => $this->get('location'),

			// name attributes
			//
			'name' => $this->get('displayName'),
			'short_name' => $this->get('shortDisplayName'),

			// team colors
			//
			'color' => $this->get('color'),
			'alternate_color' => $this->get('alternateColor'),

			// team attributes
			//
			'is_active' => $this->get('isActive') == 'true'? 1 : 0,
			'is_all_star' => $this->get('isAllStar') == 'true'? 1 : 0,

			// venue attributes
			//
			'venue_name' => $this->has('venue')? ($this->get('venue')->fullName ?? NULL) : NULL,
			'venue_city' => $this->has('venue')? ($this->get('venue')->address->city ?? NULL) : NULL,
			'venue_state' => $this->has('venue')? ($this->get('venue')->address->state ?? NULL) : NULL,
			'venue_zip_code' => $this->has('venue')? ($this->get('venue')->address->zipCode ?? NULL) : NULL,
			'venue_capacity' => $this->has('venue')? $this->get('venue')->capacity : NULL,
			'venue_grass' => $this->has('venue')? ($this->get('venue')->grass == 'true'? 1 : 0) : 0,
			'venue_indoor' => $this->has('venue')? ($this->get('venue')->indoor == 'true'? 1 : 0) : 0,
		];
	}
}