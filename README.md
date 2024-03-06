<p align="center">
  <div align="center">
    <img src="./images/logos/espn-wordmark.svg" alt="Logo" style="width:50%">
    <br />
    <img src="./images/logos/nfl-logo.svg" alt="Logo" style="width:25%">
     <img src="./images/logos/ncaa-logo.svg" alt="Logo" style="width:25%">
  </div>
</p>

# Athlete Categorizer

The Athlete Categorizer is a simple utility for downloading, organizing, and categorizing NFL and NCAA athelete data using the ESPN API.  It can be used to perform various types of analysis of player data, including ethnographic analysis.

The utility performs the following functions:

### 1. Downloading 
The utility downloads the data using the ESPN API and organizes the JSON and image data into a convenient folder structure.

### 2. Indexing
While downloading the data, the utility stores it into a relational database for easy querying and analysis. 

### 3. Categorizing
The utility performs an ethnographic / demographic categorization of players based upon their headshots.

## Requirements

### 1. PHP, Python
The utility relies upon the PHP and Python scripting languages.

### 2. SQL Database
The data is stored in a SQL database for easy querying and analysis.  You will need MySQL, MariaDB, or an equivalent.

### 3. DeepFace
The [DeepFace](https://github.com/serengil/deepface) utility is used to perform an image based ethnographic / demographic categorization of players.

## Installation

### 1. Database

Create a database called "athletics".  Then, run [database/athletics.sql](database/athletics.sql) to create the required table structure.

### 2. DeepFace
Please install the DeepFace utility using the instructions provided [here](https://github.com/serengil/deepface).

## Running
There are two main script utilities: 

### 1. Athletes

These scripts fetch and categorize player data and headshots. Since there are about many thousands of players in the database, they are divided up into folders based upon the first letter of the player's last name.  The complete NFL dataset is about 12,000 players with headshots is over 3GB in size. The NCAA dataset is about 200,000 players.

```
cd src
php fetch_nfl_athletes.php
php fetch_ncaa_athletes.php
```

### 2. Teams

These scripts 'fetch_teams.php' script fetches team data and logos.

```
cd src
php fetch_nfl_teams.php
php fetch_ncaa_teams.php
```

## Data
Below are sample athlete and team datasets produced from running this software (March 2024):

1. [nfl_athletes.csv](data/nfl_athletes.csv)
2. [nfl_teams.csv](data/nfl_teams.csv)

<!-- LICENSE -->
## License

Distributed under the MIT License which allows urestricted use of the software. See [LICENSE.txt](LICENSE.txt) for more information.

<!-- CONTACT -->
## Acknowledgements
This software was made possible by the following organizations:

1. [University of Wisconsin](http://wisc.edu) [Data Science Institute](http://datascience.wisc.edu)

Thank you to the Data Science Institute for support.

2. [ESPN](http://espn.com)

Thank you to ESPN for providing access to their data through their API.  This software was created without involvement from or any association with the ESPN organization.

<!-- CONTACT -->
## Contact

Abe Megahed - (mailto:amegahed@wisc.edu)
