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
  $ID = $_GET['id'];
  $Round = $_GET['round'];
  $Table = $_GET['table'];

  // Set default timezone
  // date_default_timezone_set('UTC');
 
  try {
 
    // Create (connect to) SQLite database in file
    $file_db = new PDO('sqlite:tournaments.sqlite3');
    // Set errormode to exceptions
    $file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    // Get players list
    $MaxPlayer=0;
    $result = $file_db->query('SELECT * FROM _'.$ID.'_Players');
    foreach ($result as $Player) {
      $Players[$Player['id']] = $Player['name'];
      $MaxPlayer++;
    }
 

    // Get round infos
    $result = $file_db->query('SELECT * FROM Tournaments WHERE id='.$ID);
    foreach ($result as $TournamentInfo) {
      $TournamentName = $TournamentInfo['name'];
      $TournamentMap = $TournamentInfo['map'];
      $TournamentMapType = $TournamentInfo['maptype'];
    }

    // Get dynamic info if needed
    if ($TournamentMapType == "dynamic") {
      include("dynamic.php");
    }

      // Get list of penalties
      $result = $file_db->query('SELECT * FROM _'.$ID.'_Penalties WHERE round='.$Round);
      foreach ($result as $penalty) {
        $Penalties[$penalty['player']] = $penalty['value'];
      }


    echo "<b>Tournoi $TournamentName</b><br />
	<p>
	<b>Round $Round:</b>
	<form>
	<table>
	<tr><th>Table</th><th>Results</th></tr>";




    // Parse tournament map
    $Map = [];
    foreach(preg_split("/((\r?\n)|(\r\n?))/", $TournamentMap) as $line){
      $Map[] = str_getcsv($line);
    }
 
    foreach ($Map as $game) {

      if ($game[0] == $Round && $game[1] !="r") {
        // Check if game already exists and sets points accordingly
        $result = $file_db->query('SELECT * FROM _'.$ID.'_Games WHERE tablenumber='.$game[1].' AND round='.$game[0]);
        $rows = 0;
        foreach ($result as $Test) {
          $Points1 = $Test['score1'];
          $Points2 = $Test['score2'];
          $Points3 = $Test['score3'];
          $Points4 = $Test['score4'];
          $rows++;
        }
        if ($rows == 0) {
          $Points1 = $game[3];
          $Points2 = $game[5];
          $Points3 = $game[7];
          $Points4 = $game[9];
        }

        eval("\$Player1 = ".$game[2].";");
        eval("\$Player2 = ".$game[4].";");
        eval("\$Player3 = ".$game[6].";");
        eval("\$Player4 = ".$game[8].";");

        echo "<tr><td>".$game[1]."</td><td><table>
	<tr><th class=\"ColInputScore1\">Player</th><th>Score</th><th>Penalty</th></tr>
	<tr><td>".$Players[$Player1]."</td><td><input type=\"text\" size=\"5\" name=\"points1[".$game[1]."]\" value=\"".$Points1."\"></td><td><input size=\"5\" type=\"text\" name=\"penalty[".$Player1."]\" value=\"".$Penalties[$Player1]."\"></td></tr>
	<tr><td>".$Players[$Player2]."</td><td><input size=\"5\" type=\"text\" name=\"points2[".$game[1]."]\" value=\"".$Points2."\"></td><td><input size=\"5\" type=\"text\" name=\"penalty[".$Player2."]\" value=\"".$Penalties[$Player2]."\"></td></tr>
	<tr><td>".$Players[$Player3]."</td><td><input size=\"5\" type=\"text\" name=\"points3[".$game[1]."]\" value=\"".$Points3."\"></td><td><input size=\"5\" type=\"text\" name=\"penalty[".$Player3."]\" value=\"".$Penalties[$Player3]."\"></td></tr>
	<tr><td>".$Players[$Player4]."</td><td><input size=\"5\" type=\"text\" name=\"points4[".$game[1]."]\" value=\"".$Points4."\"><td><input size=\"5\" type=\"text\" name=\"penalty[".$Player4."]\" value=\"".$Penalties[$Player4]."\"></td></td></tr>
	</table>\n";
      }

    }

    // Close file db connection
    $file_db = null;

  }
  catch(PDOException $e) {
    // Print PDOException message
    echo $e->getMessage();
  }


?>

</table>


</form>

<?php
} else {

  // Set variables from POST
  $ID = $_POST['id'];
  $Round = $_POST['round'];
  $Table = $_POST['table'];

  // Set default timezone
  // date_default_timezone_set('UTC');
 
  try {
 
    // Create (connect to) SQLite database in file
    $file_db = new PDO('sqlite:tournaments.sqlite3');
    // Set errormode to exceptions
    $file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    // Get players list
    $MaxPlayer=0;
    $result = $file_db->query('SELECT * FROM _'.$ID.'_Players');
    foreach ($result as $Player) {
      $Players[$Player['id']] = $Player['name'];
      $MaxPlayer++;
    }

    // Get round infos
    $result = $file_db->query('SELECT * FROM Tournaments WHERE id='.$ID);
    foreach ($result as $TournamentInfo) {
      $TournamentName = $TournamentInfo['name'];
      $TournamentMap = $TournamentInfo['map'];
      $TournamentMapType = $TournamentInfo['maptype'];
    }

    // Get dynamic info if needed
    if ($TournamentMapType == "dynamic") {
      include("dynamic.php");
    }


    echo "<b>Tournoi $TournamentName</b><br />
	<p>
	<b>Round $Round:</b>
	<table>
	<tr><th>Table</th><th>Results</th></tr>";

    // Parse tournament map
    $Map = [];
    foreach(preg_split("/((\r?\n)|(\r\n?))/", $TournamentMap) as $line){
      $Map[] = str_getcsv($line);
    }

 
    foreach ($Map as $game) {

      if ($game[0] == $Round && $game[1] !="r") {
        $Points1 = $_POST['points1'][$game[1]];
        $Points2 = $_POST['points2'][$game[1]];
        $Points3 = $_POST['points3'][$game[1]];
        $Points4 = $_POST['points4'][$game[1]];


        eval("\$Player1 = ".$game[2].";");
        eval("\$Player2 = ".$game[4].";");
        eval("\$Player3 = ".$game[6].";");
        eval("\$Player4 = ".$game[8].";");


        echo "<tr><td>".$game[1]."</td><td><table>
	<tr><th class=\"ColInputScore1\">Player</th><th>Score</th><th>Penalty</th></tr>
	<tr><td>".$Players[$Player1].": </td><td>$Points1</td><td>".$_POST['penalty'][$Player1]."</td></tr>
	<tr><td>".$Players[$Player2].": </td><td>$Points2</td><td>".$_POST['penalty'][$Player2]."</td></tr>
	<tr><td>".$Players[$Player3].": </td><td>$Points3</td><td>".$_POST['penalty'][$Player3]."</td></tr>
	<tr><td>".$Players[$Player4].": </td><td>$Points4</td><td>".$_POST['penalty'][$Player4]."</td></tr>
	</table>\n";

        // Check if game already exists and create or update it
        $result = $file_db->query('SELECT * FROM _'.$ID.'_Games WHERE tablenumber='.$game[1].' AND round='.$game[0]);
        $rows = 0;
        foreach ($result as $ZOB) {
          $rows++;
        }
        
      }

    }

    // Close file db connection
    $file_db = null;

  }
  catch(PDOException $e) {
    // Print PDOException message
    echo $e->getMessage();
  }

}

?>
<p>
<a href="index.php">Back to general index</a> - <a href="tournament.php?id=<?php echo$ID; ?>">Back to tournament index</a>

</body>
</html>