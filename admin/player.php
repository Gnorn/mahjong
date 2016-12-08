<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Mahjong Tournament Software</title>
<link href="Style.css" rel="stylesheet" type="text/css" />
</head>
<body>


<?php

$ID = $_GET['TournamentId'];
$PlayerID = $_GET['player'];

try {
 
  // Create (connect to) SQLite database in file
  $file_db = new PDO('sqlite:../tournaments.sqlite3');
  // Set errormode to exceptions
  $file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Get players list
  $result = $file_db->query('SELECT * FROM _'.$ID.'_Players');
  foreach ($result as $Player) {
    $Players[$Player['id']] = $Player['name'];
  }

  // Get games with this player
  $result = $file_db->query('SELECT * FROM _'.$ID.'_Games WHERE player1='.$PlayerID);
  foreach ($result as $Game) {
    $Games[$Game['id']]['tablenumber'] = $Game['tablenumber'];
    $Games[$Game['id']]['round'] = $Game['round'];
    $Games[$Game['id']]['score'] = $Game['score1'];
  }

  $result = $file_db->query('SELECT * FROM _'.$ID.'_Games WHERE player2='.$PlayerID);
  foreach ($result as $Game) {
    $Games[$Game['id']]['tablenumber'] = $Game['tablenumber'];
    $Games[$Game['id']]['round'] = $Game['round'];
    $Games[$Game['id']]['score'] = $Game['score2'];
  }

  $result = $file_db->query('SELECT * FROM _'.$ID.'_Games WHERE player3='.$PlayerID);
  foreach ($result as $Game) {
    $Games[$Game['id']]['tablenumber'] = $Game['tablenumber'];
    $Games[$Game['id']]['round'] = $Game['round'];
    $Games[$Game['id']]['score'] = $Game['score3'];
  }

  $result = $file_db->query('SELECT * FROM _'.$ID.'_Games WHERE player4='.$PlayerID);
  foreach ($result as $Game) {
    $Games[$Game['id']]['tablenumber'] = $Game['tablenumber'];
    $Games[$Game['id']]['round'] = $Game['round'];
    $Games[$Game['id']]['score'] = $Game['score4'];
  }

  // Get penalties
  $TotalPenalty = 0;
  $result = $file_db->query('SELECT * FROM _'.$ID.'_Penalties WHERE player='.$PlayerID);
  foreach ($result as $penalty) {
    $Penalties[$penalty['round']]=$penalty['value'];
    $TotalPenalty = $TotalPenalty + $penalty['value'];
  }

  // Close file db connection
  $file_db = null;
    

}
catch(PDOException $e) {
  // Print PDOException message
  echo $e->getMessage();
}

foreach ($Games as $Game) {
  $SortGames[]=$Game['round'];
}

array_multisort($SortGames, SORT_ASC, $Games);

echo "<b>".$PlayerID.". ".$Players[$PlayerID]."</b>";
$count = 1;
echo "<table><tr><th>Round</th><th>Table</th><th>Score</th><th>Penalties</th></tr>";
$total = 0;
foreach ($Games as $Game) {
  echo "<tr onclick=\"document.location = 'round.php?id=$ID&round=".$Game['round']."';\"><td>".$Game['round']."</td><td>".$Game['tablenumber']."</td><td>".$Game['score']."</td><td>".$Penalties[$Game['round']]."</td></tr>";
  $total = $total + $Game['score'];
}
echo "<tr><td colspan=2><i>Total:</i></td><td>$total</td><td>$TotalPenalty</td></tr>";
?>
</table>

<p>
<a href="index.php">Back to general index</a> - <a href="tournament.php?id=<?php echo$ID; ?>">Back to tournament index</a>

</body>
</html>