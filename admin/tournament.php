<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Mahjong Tournament Software</title>
<link href="Style.css" rel="stylesheet" type="text/css" />
</head>
<body>

<script type="text/javascript">

function display(id) {

  document.getElementById("Players").style.display = "none";
  document.getElementById("Rankings").style.display = "none";
  document.getElementById("Rounds").style.display = "none";
  document.getElementById(id).style.display = "block";

}

function toggleranking(id) {
  if (document.getElementById(id).style.display == "none") {
    document.getElementById(id).style.display = "block";
  } else {
    document.getElementById(id).style.display = "none";
  }
}
</script>

<?php

  // Set variables from GET
  $ID = $_GET['id'];

  // Set default timezone
  // date_default_timezone_set('UTC');
 
  try {
 
    // Create (connect to) SQLite database in file
    $file_db = new PDO('sqlite:../tournaments.sqlite3');
    // Set errormode to exceptions
    $file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 

    // Get tournament infos
    $result = $file_db->query('SELECT * FROM Tournaments WHERE id='.$ID);
    foreach ($result as $TournamentInfo) {
      $TournamentName = $TournamentInfo['name'];
      $TournamentDescription = $TournamentInfo['description'];
      $TournamentMap = $TournamentInfo['map'];
      $TournamentMapType = $TournamentInfo['maptype'];
    }

    echo "<div class=\"Title\"><b>Tournoi $TournamentName</b><br />
	$TournamentDescription<p></div>

	<div class\"Menu\"><table><tr class=\"Menu\"><td>Menu:</td><td><a href=\"javascript:void(0)\" onclick=\"display('Players');\">Players</a></td><td><a href=\"javascript:void(0)\" onclick=\"display('Rankings');\">Rankings</a></td><td><a href=\"javascript:void(0)\" onclick=\"display('Rounds');\">Rounds</a></td><td><a href=\"index.php\">Back to index</a></td><td><a href=\"../tournaments.sqlite3\">Download DB</a></tr></table></div>	

	<p>

	<div id=\"Players\" style=\"display:none\"><b>Players:</b>
	<table>
	<tr><th>ID</th><th>Name</th><th>Score</th><th>Penalty</th></tr>";

    // Get players list
    $MaxPlayer=0;
    $result = $file_db->query('SELECT * FROM _'.$ID.'_Players');
    foreach ($result as $Player) {
      $Players[$Player['id']] = $Player['name'];

      $score = 0;
      $resultBis = $file_db->query('SELECT * FROM _'.$ID.'_Games WHERE player1='.$Player['id']);
      foreach ($resultBis as $game) {
        $score = $score + $game['score1'];
      }
      $resultBis = $file_db->query('SELECT * FROM _'.$ID.'_Games WHERE player2='.$Player['id']);
      foreach ($resultBis as $game) {
        $score = $score + $game['score2'];
      }
      $resultBis = $file_db->query('SELECT * FROM _'.$ID.'_Games WHERE player3='.$Player['id']);
      foreach ($resultBis as $game) {
        $score = $score + $game['score3'];
      }
      $resultBis = $file_db->query('SELECT * FROM _'.$ID.'_Games WHERE player4='.$Player['id']);
      foreach ($resultBis as $game) {
        $score = $score + $game['score4'];
      }
      $Scores[$Player['id']] = $score;
//      $Scores[$Player['id']]['id'] = $Player['id'];

      $penalty = 0;
      $resultBis = $file_db->query('SELECT * FROM _'.$ID.'_Penalties WHERE player='.$Player['id']);
      foreach ($resultBis as $penal) {
        $penalty = $penalty + $penal['value'];
      }
      $Penalties[$Player['id']] = $penalty;


      echo "<tr onclick=\"document.location = 'player.php?TournamentId=".$ID."&player=".$Player['id']."';\"><td>".$Player['id']."</td><td>".$Player['name']."</td><td>".$score."</td><td>".$penalty."</td></tr>\n";
      $MaxPlayer++;
    }

    echo "<tr onclick=\"document.location = 'players.php?TournamentId=$ID';\"><td colspan=4>Add/edit player(s)</td></tr>
	</table></div>";

   // Take care of a static tournament

   if ($TournamentMapType == "static") {
    // Display rankings
    echo "<div id=\"Rankings\" style=\"display:none\"><b>Rankings:</b><br />
	<table>
	<tr><th>Ranking</th><th>Player</th><th>Score</th></tr>";
    $PlayersBis = $Players;
    foreach ($Scores as $key=>$value) {
      $ScorePenal[$key] = $value + $Penalties[$key];
    }

    array_multisort($ScorePenal, SORT_DESC, $PlayersBis);
    for ($count = 0; $count < $Player['id']; $count++) {
      $countUP = $count + 1;
      echo "<tr><td>#$countUP</td><td>".$PlayersBis[$count]."</td><td>".$ScorePenal[$count]."</td></tr>";
    }
    echo "</table></div>";    


    // Display rounds
    echo "<div id=\"Rounds\" style=\"display:none\"><b>Rounds:</b>
	<table><tr onclick=\"document.location = 'round.php?id=".$ID."&round=1';\"><td>Round 1:</td>";

    // Parse tournament map
    $Map = [];
    foreach(preg_split("/((\r?\n)|(\r\n?))/", $TournamentMap) as $line){
      $Map[] = str_getcsv($line);
    }

    $current = 1;

    foreach ($Map as $game) {

      if ($game[0] > $current) {
        $current = $game[0];
        echo "</tr><tr onclick=\"document.location = 'round.php?id=".$ID."&round=".$current."';\"><td>Round $current:</td>";
      }
      echo "<td>Table ".$game[1]."
	<table class=\"Game\">
	<tr class=\"Game\"><td>".$game[2].". ".$Players[$game[2]]."</td></tr>
	<tr class=\"Game\"><td>".$game[4].". ".$Players[$game[4]]."</td></tr>
	<tr class=\"Game\"><td>".$game[6].". ".$Players[$game[6]]."</td></tr>
	<tr class=\"Game\"><td>".$game[8].". ".$Players[$game[8]]."</td></tr>
	</table></td>";

    }

    echo "</tr></table>";

   // Take care of a dynamic tournament
   } else {

    include("dynamic.php");

    // Display rankings
    echo "<div id=\"Rankings\" style=\"display:none\"><b>Rankings:</b><br />\n";

    foreach ($RankByRound as $key=>$value) {
      echo "<b><a href=\"javascript:void(0)\" onclick=\"toggleranking('Rankings$key');\">Round #$key</a></b><br \>
	<div id=\"Rankings$key\" style=\"display:none\"><table>
	<tr><th>Rank</th><th>Player</th><th>Overall Score</th></tr>\n";
      foreach ($value as $keybis=>$valuebis) {
        echo "<tr><td>$keybis</td><td>".$Players[$valuebis]."</td><td>".$OverallScoreByRound[$key][$valuebis]."</td></tr>";
      }
      echo "</table></div>";
    }

    echo "</div>";

    // Display rounds
    echo "<div id=\"Rounds\" style=\"display:none\"><b>Rounds:</b>
	<table>";
    $CurrentRound = 1;
    foreach ($MapRounds as $MapRound) {
      echo "<tr onclick=\"document.location = 'round.php?id=".$ID."&round=".$CurrentRound."';\"><td>Round $CurrentRound:</td>";
      $CurrentGame = 1;
      foreach ($MapRound as $MapGame) {
        eval("\$MapGame1 = ".$MapGame[1].";");
        eval("\$MapGame2 = ".$MapGame[2].";");
        eval("\$MapGame3 = ".$MapGame[3].";");
        eval("\$MapGame4 = ".$MapGame[4].";");
        echo "<td>Table $CurrentGame
	<table class=\"Game\">
	<tr class=\"Game\"><td>".$MapGame1.". ".$Players[$MapGame1]."</td></tr>
	<tr class=\"Game\"><td>".$MapGame2.". ".$Players[$MapGame2]."</td></tr>
	<tr class=\"Game\"><td>".$MapGame3.". ".$Players[$MapGame3]."</td></tr>
	<tr class=\"Game\"><td>".$MapGame4.". ".$Players[$MapGame4]."</td></tr>
	</table></td>";
        $CurrentGame++;
      }
      echo "</tr>";
      $CurrentRound++;
    }

    echo "</table>\n";

   }


    // Close file db connection
    $file_db = null;

  }
  catch(PDOException $e) {
    // Print PDOException message
    echo $e->getMessage();
  }
?>

<p>

<a href="editmap.php?TournamentId=<?php echo $ID; ?>">Edit tournament map</a>
</div>

</body>
</html>