<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Mahjong Tournament Software</title>
<link href="Style.css" rel="stylesheet" type="text/css" />
</head>
<body>


<?php

// Check if it should delete a player or display the form to do so

if (!isset($_POST['delete'])) {

  $ID = $_GET['id'];
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


    // Close file db connection
    $file_db = null;

  }
  catch(PDOException $e) {
    // Print PDOException message
    echo $e->getMessage();
  }


// Display a form with the necessary info to create the player


echo "<table>
<form action=\"deleteplayer.php\" method=\"post\">
<tr><th>ID</th><th>Player</th></tr>";

foreach ($Players as $key=>$value) {
  echo "<tr><td>$key</td><td><label for=\"player$key\"><input id=\"player$key\" type=\"checkbox\" name=\"players[]\" value=\"$key\">$value</label></td></tr>\n";
}

?>

</table>
<input type="submit" name="delete" value="Delete players">
<input type="hidden" name="ID" value="<?php echo $ID; ?>">
</form>
<?php

} else {

  // Defines var with player info
  $players = $_POST['players'];
  $TournamentId = $_POST['ID'];
  $ID = $TournamentId;


  try {
 
    // Create (connect to) SQLite database in file
    $file_db = new PDO('sqlite:../tournaments.sqlite3');
    // Set errormode to exceptions
    $file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    foreach ($players as $player) {
      $delete = "DELETE FROM _".$TournamentId."_Players WHERE ID=$player";
      $file_db->exec($delete);
    }

    echo "Player(s) deleted.";
    
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
<a href="index.php">Back to general index</a> - <a href="tournament.php?id=<?php echo $ID; ?>">Back to tournament index</a> - <a href="players.php?TournamentId=<?php echo $ID; ?>">Edit players</a>

</body>
</html>