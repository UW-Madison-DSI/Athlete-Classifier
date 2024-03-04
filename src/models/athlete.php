<?php
/******************************************************************************\
|                                                                              |
|                                 athlete.php                                  |
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

include "config.php";
include "models/model.php";

class Athlete extends Model {

	//
	// attributes
	//

	public static $table = 'athletes';
	public static $baseUrl = ESPN_API_URL . '/sports/football/leagues/nfl/athletes';

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
	 * Get this athlete's database table.
	 *
	 * @return string
	 */
	public function table() {
		return self::$table;
	}

	/**
	 * Get this athlete's image name.
	 *
	 * @return string
	 */
	public function getImageName() {
		$fullName = $this->get('fullName');
		$href = $this->get('headshot')->href;
		$extension = strtolower(pathinfo($href, PATHINFO_EXTENSION));
		return $fullName . '.' . $extension;
	}

	/**
	 * Get this athlete's head shot.
	 *
	 * @return string
	 */
	public function getHeadShot() {
		return file_get_contents($this->get('headshot')->href);
	}

	//
	// saving methods
	//

	/**
	 * Save this athlete's data to a directory.
	 *
	 * @return null
	 */
	public function save(string $dirname = '') {

		// get athlete name
		//
		$fullName = $this->get('fullName');

		// make new team directory
		//
		if ($dirname && !file_exists($dirname)) {
			mkdir($dirname);
		}

		// write athlete data
		//
		$filename = ($dirname? $dirname . '/' : '') . $fullName . '.json';
		$this->saveAs($filename);

		// write athlete head shot
		//
		$image = $this->getHeadShot();
		$imagename = $this->getImageName();
		$imagepath = ($dirname? $dirname . '/' : '') . $imagename;
		file_put_contents($imagepath, $image);
	}

	/**
	 * Save this athlete's demographic data to a directory.
	 *
	 * @return null
	 */
	public function saveDemographics(string $dirname = '') {
		echo "classifying " . $this->get('fullName') . "\n";

		// get image path
		//
		$imagepath = ($dirname? $dirname . '/' : '') . $this->getImageName();

		// run classifier
		//
		$command = "python3 classify.py " . '"' . $imagepath . '"';
		shell_exec($command);
	}

	/**
	 * Store this athlete's demographic data to a database.
	 *
	 * @return null
	 */
	public function storeDemographics($db, string $dirname = '') {
		$fullName = $this->get('fullName');

		// read and store race info
		//
		$filename = ($dirname? $dirname . '/' : '') . $fullName . ' Demography.json';
		$demography = json_decode(file_get_contents($filename))[0];
		$precision = 3;

		// add new row to database
		//
		update($db, 'athletes', $this->get('id'), [

			// racial attributes
			//
			'race_asian' => round(floatval($demography->race->asian), $precision),
			'race_indian' => round(floatval($demography->race->indian), $precision),
			'race_black' => round(floatval($demography->race->black), $precision),
			'race_white' => round(floatval($demography->race->white), $precision),
			'race_middle_eastern' => round(floatval($demography->race->{'middle eastern'}), $precision),
			'race_latino_hispanic' => round(floatval($demography->race->{'latino hispanic'}), $precision),
			'dominant_race' => "'" . $demography->dominant_race . "'",
			'face_confidence' => $demography->face_confidence
		]);
	}

	//
	// conversion methods
	//

	/**
	 * Convert this athlete to an array.
	 *
	 * @return array
	 */
	public function toArray() {
		return [
			'id' => $this->get('id'),
			'type' => $this->get('type'),

			// name attributes
			//
			'first_name' => $this->get('firstName'),
			'last_name' => $this->get('lastName'),
			'full_name' => $this->get('fullName'),
			'short_name' => $this->get('shortName'),

			// athlete attributes
			//
			'weight' => $this->get('weight'),
			'height' => $this->get('height'),
			'age' => $this->get('age'),
			'date_of_birth' => $this->has('dateOfBirth')? date('Y-m-d', strtotime($this->get('dateOfBirth'))) : NULL,
			'debut_year' => $this->get('debutYear'),
			'birth_city' => $this->has('birthPlace')? ($this->get('birthPlace')->city ?? NULL) : NULL,
			'birth_state' => $this->has('birthPlace')? ($this->get('birthPlace')->state ?? NULL) : NULL,
			'birth_country' => $this->has('birthPlace')? ($this->get('birthPlace')->country ?? 'USA') : NULL,
			'jersey' => $this->get('jersey'),
			'position' => $this->has('position')? $this->get('position')->name : NULL,
			'team' => basename(substr($this->get('team')->{'$ref'}, 0, strpos($this->get('team')->{'$ref'}, "?"))) ?? NULL,
			'experience' => $this->has('experience')? $this->get('experience')->years : 0,
			'status' => $this->has('status')? $this->get('status')->name : NULL
		];
	}
}