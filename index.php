<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Mahjong Tournament Software</title>
<link href="Style.css" rel="stylesheet" type="text/css" />
</head>
<body>

<b>Tournaments index</b>

<p>

<?php
 
  // Set default timezone
  // date_default_timezone_set('UTC');
 
  try {
 
    // Create (connect to) SQLite database in file
    $file_db = new PDO('sqlite:tournaments.sqlite3');
    // Set errormode to exceptions
    $file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 
 
    // Create tournaments table
 
    $file_db->exec("CREATE TABLE IF NOT EXISTS Tournaments (id INTEGER PRIMARY KEY, name TEXT, description TEXT, maptype TEXT, map TEXT)");

    // Select tournaments
    $result = $file_db->query('SELECT * FROM Tournaments');

?>
<table class="TableIndex">
<tr><th>Tournament name</th><th colspan=2>Description</th></tr>
<?php 

    // Loop thru all data from Tournaments table 
    // and display tournaments
    foreach ($result as $m) {
      $id = $m['id'];
      $name = $m['name'];
      $description = $m['description'];

      echo "<tr onclick=\"document.location = 'tournament.php?id=$id';\"><td class=\"ColIndex1\">$name</td><td class=\"ColIndex2\">$description</td></tr>";
    }

?></table><?php

    // Close file db connection
    $file_db = null;

  }
  catch(PDOException $e) {
    // Print PDOException message
    echo $e->getMessage();
  }
?>

</body>
</html>