<?php
/******************************************************************************\
|                                                                              |
|                              fetch_athlete.php                               |
|                                                                              |
|******************************************************************************|
|                                                                              |
|        This defines a utility for fetching an NFL athlete.                   |
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

// create and fetch athlete
//
$athlete = (new Athlete([
	'id' => 16296
]))->fetch();

// save athlete in file system and database
//
$athlete->save();
$athlete->store($db);

// save demographics in file system and database
//
$athlete->saveDemographics();
$athlete->storeDemographics($db);