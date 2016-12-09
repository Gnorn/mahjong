<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Mahjong Tournament Software</title>
<link href="Style.css" rel="stylesheet" type="text/css" />
</head>
<body>


<?php

// Check if it should edit the map or display the form to do so


if (!isset($_POST['create'])) {

// Set variables from GET
$ID = $_GET['TournamentId'];

// Include prefs vars

include "prefs.php";

  try {
 
    // Create (connect to) SQLite database in file
    $file_db = new PDO('sqlite:../tournaments.sqlite3');
    // Set errormode to exceptions
    $file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 

    // Get tournament infos
    $result = $file_db->query('SELECT * FROM Tournaments WHERE id='.$ID);
    foreach ($result as $TournamentInfo) {
      $TournamentName = $TournamentInfo['name'];
      $TournamentMap = $TournamentInfo['map'];
      $TournamentMaptype = $TournamentInfo['maptype'];
    }
    // Close file db connection
    $file_db = null;

  }
  catch(PDOException $e) {
    // Print PDOException message
    echo $e->getMessage();
  }

// Display a form with the necessary info to create the tournament

?>
<b>Edit map for tournament <u><?php echo $TournamentName; ?></u> :</b>
<table>
<form action="editmap.php" method="post">
<tr><td>Tournament map type:</td><td><label for="static"><input id="static" type="radio" name="maptype" value="static" <?php if($TournamentMaptype == "static") echo "checked"; ?>> Static</static> - <label for="dynamic"><input id="dynamic" type="radio" name="maptype" value="dynamic"<?php if ($dynamic =="disabled") echo "disabled"; if($TournamentMaptype == "dynamic") echo "checked"; ?>> Dynamic</label></td></tr>
<tr><td>Tournament map:</td><td><textarea rows="20" cols="100" name="map"><?php echo $TournamentMap; ?></textarea></td></tr>
</table>
<input type="hidden" name="id" value="<?php echo $ID; ?>">
<input type="submit" name="create" value="Create">
</form>

<?php

} else {

  $ID = $_POST['id'];

  // Defines var with tournament info
   $map = $_POST['map'];
   $maptype = $_POST['maptype'];

  try {
 
    // Create (connect to) SQLite database in file
    $file_db = new PDO('sqlite:../tournaments.sqlite3');
    // Set errormode to exceptions
    $file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Update table


    $mapOK = SQLite3::escapeString($map);
    $maptypeOK = SQLite3::escapeString($maptype);

    $update = "UPDATE Tournaments SET map = '$mapOK', maptype = '$maptypeOK' WHERE id = $ID";
    $file_db->exec($update);

    // Close file db connection
    $file_db = null;

  }
  catch(PDOException $e) {
    // Print PDOException message
    echo $e->getMessage();
  }

   echo "Map Tournament updated with the following info:<br />
	<table>
	<tr><td>Map type:</td><td>$maptype</td></tr>
	<tr><td>Tournament map:</td><td><textarea rows=\"20\" cols=\"100\">$map</textarea></td></tr>
	</table>";

}
?>
<p>
<a href="index.php">Back to general index</a> - <a href="tournament.php?id=<?php echo$ID; ?>">Back to tournament index</a>

</body>
</html>