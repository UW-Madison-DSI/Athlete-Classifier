<?php
/******************************************************************************\
|                                                                              |
|                                fetch_team.php                                |
|                                                                              |
|******************************************************************************|
|                                                                              |
|        This defines a utility for fetching an NFL team.                      |
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

// create and fetch team
//
$team = (new Team([
	'id' => 1
]))->fetch();

// save team in file system and database
//
$team->save();
$team->store($db);