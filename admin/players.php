<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Mahjong Tournament Software</title>
<link href="Style.css" rel="stylesheet" type="text/css" />
</head>
<body>


<?php

// Check if it should create a player or display the form to do so

if (!isset($_POST['create'])) {

  $ID = $_GET['TournamentId'];
  if (isset($_POST['bulk'])) {
    $ID = $_POST['TournamentId'];
    try {
 
      // Create (connect to) SQLite database in file
      $file_db = new PDO('sqlite:../tournaments.sqlite3');
      // Set errormode to exceptions
      $file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


      // Prepare INSERT statement
      for ($i=1;$i<=$_POST['bulknumber'];$i++) {
        $insert = "INSERT INTO _".$ID."_Players (name) VALUES ('PLAYER".$i."')";
        $stmt = $file_db->prepare($insert);
        $stmt->execute();
      }


      // Close file db connection
      $file_db = null;

    }
    catch(PDOException $e) {
      // Print PDOException message
      echo $e->getMessage();
    }


    $ID = $_POST['TournamentId'];
    echo "<b>Bulk added ".$_POST['bulknumber']." players !</b>\n";
  }

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
<form action=\"players.php\" method=\"post\">
<tr><th>ID</th><th>Player</th></tr>";

$max=0;

foreach ($Players as $key=>$value) {
  echo "<tr><td>$key</td><td><input type=\"text\" name=\"name[$key]\" value=\"$value\"></td></tr>\n";
  $max++;
}

?>

<tr><td></td><td><input type="text" name="name[<?php echo $max+1; ?>]"></td></tr>
</table>
<input type="submit" name="create" value="Create new player/update names">
<input type="hidden" name="TournamentId" value="<?php echo $ID; ?>">
</form>
<p>
<form method="post" action="players.php">
Bulk add <input type="text" name="bulknumber" size="4"> players... <input type="submit" name="bulk" value="Bulk add">
<input type="hidden" name="TournamentId" value="<?php echo $ID; ?>">
</form>
<?php

} else {

  // Defines var with player info
  $name = $_POST['name'];
  $TournamentId = $_POST['TournamentId'];
  $ID = $TournamentId;

  try {
 
    // Display result table
    echo "Updated player table:<br />\n<table>\n<tr><th>ID</th><th>Player</th></tr>\n";

    // Create (connect to) SQLite database in file
    $file_db = new PDO('sqlite:../tournaments.sqlite3');
    // Set errormode to exceptions
    $file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get players list
    $result = $file_db->query('SELECT * FROM _'.$TournamentId.'_Players');
    foreach ($result as $key=>$value) {
      $Players[$key] = $value['name'];
      $dbkey=$key+1;
      $valueOK = SQLite3::escapeString($name[$dbkey]);
      $update = "UPDATE _".$TournamentId."_Players SET name='$valueOK' WHERE id=$dbkey";
      $oldname=$value['name'];
      $file_db->exec($update);
      echo "<tr><td>$dbkey</td><td>$valueOK (was $oldname)</td></tr>\n";
    }
    


    // Prepare INSERT statement
    $name= $name[$dbkey+1];
    echo $name;
    $nameOK = SQLite3::escapeString($name);
    if ($nameOK != "") {
      $insert = "INSERT INTO _".$TournamentId."_Players (name) VALUES ('$nameOK')";
      $stmt = $file_db->prepare($insert);
      $stmt->execute();

      $PlayerId = $file_db->lastInsertId();
      echo "<tr><td><b>$PlayerId</b></td><td><b>$nameOK</b></td></tr>\n";
    }
    // Close file db connection
    $file_db = null;

  }
  catch(PDOException $e) {
    // Print PDOException message
    echo $e->getMessage();
  }

echo "</table>";

}
?>
<p>
<a href="index.php">Back to general index</a> - <a href="tournament.php?id=<?php echo $ID; ?>">Back to tournament index</a> - <a href="deleteplayer.php?id=<?php echo $ID; ?>">Delete players</a>

</body>
</html>