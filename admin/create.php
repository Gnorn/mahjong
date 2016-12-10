<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Mahjong Tournament Software</title>
<link href="Style.css" rel="stylesheet" type="text/css" />
</head>
<body>


<?php

// Check if it should create a tournament or display the form to do so

if (!isset($_POST['create'])) {

// Display a form with the necessary info to create the tournament

// Include prefs vars

include "prefs.php";

?>
<table>
<form action="create.php" method="post">
<tr><td>Name of the tournament:</td><td><input type="text" name="name"></td></tr>
<tr><td>Description:</td><td><input type="text" name="description"></td></tr>
<tr><td>Tournament map type:</td><td><label for="static"><input id="static" type="radio" name="maptype" value="static" checked> Static</static> - <label for="dynamic"><input id="dynamic" type="radio" name="maptype" value="dynamic"<?php if ($dynamic =="disabled") echo "disabled";?>> Dynamic</label></td></tr>
<tr><td>Tournament map:</td><td><textarea rows="20" cols="100" name="map"></textarea></td></tr>
</table>
<input type="submit" name="create" value="Create">
</form>
<p>
<a href="index.php">Back to index</a>
<?php

} else {

  // Defines var with tournament info
   $name = $_POST['name'];
   $description = $_POST['description'];
   $map = $_POST['map'];
   $maptype = $_POST['maptype'];

  try {
 
    // Create (connect to) SQLite database in file
    $file_db = new PDO('sqlite:../tournaments.sqlite3');
    // Set errormode to exceptions
    $file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare INSERT statement
    $nameOK = SQLite3::escapeString($name);
    $descriptionOK = SQLite3::escapeString($description);
    $mapOK = SQLite3::escapeString($map);
    $maptypeOK = SQLite3::escapeString($maptype);
    $insert = "INSERT INTO Tournaments (name, description, maptype, map) VALUES ('$nameOK', '$descriptionOK', '$maptypeOK', '$mapOK')";
    $stmt = $file_db->prepare($insert);
    $stmt->execute();

    $TournamentId = $file_db->lastInsertId();

    // Creates tables for this tournament

    $file_db->exec("CREATE TABLE _".$TournamentId."_Players (id INTEGER PRIMARY KEY, name TEXT)");
    $file_db->exec("CREATE TABLE _".$TournamentId."_Games (id INTEGER PRIMARY KEY, tablenumber INT, round INT, player1 INT, player2 INT, player3 INT, player4 INT, score1 INT, score2 INT, score3 INT, score4 INT)");
    $file_db->exec("CREATE TABLE _".$TournamentId."_Penalties (id INTEGER PRIMARY KEY, round INTEGER, player INTEGER, value INT)");

    // Close file db connection
    $file_db = null;

  }
  catch(PDOException $e) {
    // Print PDOException message
    echo $e->getMessage();
  }

   echo "Tournament created with the following info:<br />
	<table>
	<tr><td>Tournament ID :</td><td>$TournamentId</td></tr>
	<tr><td>Name of the tournament:</td><td>$name</td></tr>
	<tr><td>Description:</td><td>$description</td></tr>
	<tr><td>Map type:</td><td>$maptype</td></tr>
	<tr><td>Tournament map:</td><td><textarea rows=\"20\" cols=\"100\">$map</textarea></td></tr>
	</table>
	<p>
	<a href=\"index.php\">Back to index</a> - <a href=\"tournament.php?id=$TournamentId\">Go to tournament page</a>";

}
?>


</body>
</html>