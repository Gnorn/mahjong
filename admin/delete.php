<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Mahjong Tournament Software</title>
<link href="Style.css" rel="stylesheet" type="text/css" />
</head>
<body>


<?php

// Check if it should create a tournament or display the confirmation to do so

if (!isset($_POST['delete'])) {

echo "<form method=\"post\" action=\"delete.php\">";

  $ID = $_GET['id'];
 
  // Set default timezone
  // date_default_timezone_set('UTC');
 
  try {
 
    // Create (connect to) SQLite database in file
    $file_db = new PDO('sqlite:../tournaments.sqlite3');
    // Set errormode to exceptions
    $file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Select tournaments
    $result = $file_db->query('SELECT * FROM Tournaments WHERE ID='.$ID);

    // Loop thru all data from Tournaments table 
    // and display tournaments
    foreach ($result as $tournament) {

      echo "<b>Delete tournament \"".$tournament['name']."\" ?</b>\n<input type=\"hidden\" name=\"id\" value=\"$ID\"><br />\n";
    }

    // Close file db connection
    $file_db = null;

  }
  catch(PDOException $e) {
    // Print PDOException message
    echo $e->getMessage();
  }

  echo "<input type=\"submit\" name=\"delete\" value=\"Delete\">\n</form>";

} else {

  $ID = $_POST['id'];
 
  // Set default timezone
  // date_default_timezone_set('UTC');
 
  try {
 
    // Create (connect to) SQLite database in file
    $file_db = new PDO('sqlite:../tournaments.sqlite3');
    // Set errormode to exceptions
    $file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Select tournaments
    $result = $file_db->query('SELECT * FROM Tournaments WHERE ID='.$ID);

    foreach ($result as $tournament) {
      echo "<b>Deleted tournament \"".$tournament['name']."\" !</b><br />\n";
    }

    $delete = "DELETE FROM Tournaments WHERE ID=$ID";
    $file_db->exec($delete);


    // Close file db connection
    $file_db = null;

  }
  catch(PDOException $e) {
    // Print PDOException message
    echo $e->getMessage();
  }

  try {
 
    // Create (connect to) SQLite database in file
    $file_db = new PDO('sqlite:../tournaments.sqlite3');
    // Set errormode to exceptions
    $file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $dropgames = "DROP TABLE _".$ID."_Games";
    $file_db->exec($dropgames);
    $dropplayers = "DROP TABLE _".$ID."_Players";
    $file_db->exec($dropplayers);
    $droppenalties = "DROP TABLE _".$ID."_Penalties";
    $file_db->exec($droppenalties);


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
<a href="index.php">Back to general index</a>

</body>
</html>