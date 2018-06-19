<?php

// Define AttributeRank function

function AttributeRank($BestRank, $WorstRank, $BestPool, $WorstPool, $ScoreFormula) {

  global $Players;
  global $CurrentRound;
  global $RankByRound;
  global $ScoreByRound;
  global $OverallScoreByThisRound;
  global $OverallScoreByRound;


  $PoolScore = [];
  $PlayerIDList = [];
  $OverallScoreByThisRoundInter = [];
  for ($CurrentPlayerInPool = $BestPool; $CurrentPlayerInPool <= $WorstPool;$CurrentPlayerInPool++)
  {

    $CurrentPlayer = $RankByRound[$CurrentRound-1][$CurrentPlayerInPool];
    $ThisScore = $ScoreByRound[$CurrentPlayer][$CurrentRound];
    $PreviousScore = $ScoreByRound[$CurrentPlayer][$CurrentRound - 1];
    $PreviousOverallScore = $OverallScoreByRound[$CurrentRound - 1][$CurrentPlayer];
    eval("\$Score = $ScoreFormula;");

    $OverallScoreByThisRound[$CurrentPlayer] = $Score;
    $OverallScoreByThisRoundInter[$CurrentPlayer] = $Score;
    $PoolScore[$CurrentPlayer] = $Score;
    $PlayerIDList[$CurrentPlayerInPool] = $CurrentPlayer;
  }

  $OverallScoreByThisRoundRanked = $OverallScoreByThisRoundInter;
  array_multisort($OverallScoreByThisRoundRanked, SORT_DESC, $PlayerIDList);

  array_unshift($OverallScoreByThisRoundRanked, "phoney");
  unset($OverallScoreByThisRound[0]);
  array_unshift($PlayerIDList, "phoney");
  unset($PlayerIDList[0]);

  $CurrentPlayerIDList = 1;
  for ($CurrentPlayerInPool = $BestPool; $CurrentPlayerInPool <= $WorstPool;$CurrentPlayerInPool++) {
    if ($CurrentPlayerInPool >= $BestPool && $CurrentPlayerInPool <= $WorstPool)
    $RankByRound[$CurrentRound][$CurrentPlayerInPool] = $PlayerIDList[$CurrentPlayerIDList];
    $CurrentPlayerIDList++;

  }

}


// Parse tournament map
$MapLocal = [];
foreach(preg_split("/((\r?\n)|(\r\n?))/", $TournamentMap) as $line){
  $MapLocal[] = str_getcsv($line);
}


$current = 1;

foreach ($MapLocal as $gameLocal) {

  if ($gameLocal[0] > $current) {
    $current = $gameLocal[0];
  }


  if ($gameLocal[1] == "r") {
    $RankMap[$current] = $gameLocal[2];
  } else {
    $MapRounds[$gameLocal[0]][$gameLocal[1]][1]=$gameLocal[2];
    $MapRounds[$gameLocal[0]][$gameLocal[1]][2]=$gameLocal[4];
    $MapRounds[$gameLocal[0]][$gameLocal[1]][3]=$gameLocal[6];
    $MapRounds[$gameLocal[0]][$gameLocal[1]][4]=$gameLocal[8];
  }


}

$MaxRound = $current;
// Get each player's score by round (with penalty)
for ($CurrentPlayer = 1; $CurrentPlayer <= $MaxPlayer; $CurrentPlayer++) {

  $result = $file_db->query('SELECT round, score1 AS score FROM _'.$ID.'_Games WHERE player1='.$CurrentPlayer.' UNION ALL SELECT round, score2 AS score FROM _'.$ID.'_Games WHERE player2='.$CurrentPlayer.' UNION ALL SELECT round, score3 AS score FROM _'.$ID.'_Games WHERE player3='.$CurrentPlayer.' UNION ALL SELECT round, score4 AS score FROM _'.$ID.'_Games WHERE player4='.$CurrentPlayer);

  $IntermediateScores = [];
  foreach ($result as $gameLocal) {

    $IntermediateScores[$gameLocal['round']] = $gameLocal['score'];
  }

  $IntermediatePenalties = [];
  $result = $file_db->query('SELECT * FROM _'.$ID.'_Penalties WHERE player='.$CurrentPlayer);
  foreach ($result as $penal) {
    $IntermediatePenalties[$penal['round']] = $penal['value'];
  }
  for ($CurrentRound = 1; $CurrentRound <= $MaxRound; $CurrentRound++) {
    $ScoreByRound[$CurrentPlayer][$CurrentRound] = $IntermediateScores[$CurrentRound] + $IntermediatePenalties[$CurrentRound];
  }

}


// Loop through rounds and establish ranking

for ($CurrentRound = 1; $CurrentRound <= $MaxRound; $CurrentRound++) {
  if ($RankMap[$CurrentRound] == "static") {

    // Establish ranking for a static-like round

    $OverallScoreByThisRound = [];
    for ($CurrentPlayer = 1; $CurrentPlayer <= $MaxPlayer; $CurrentPlayer++) {
      $OverallScoreByThisRound[$CurrentPlayer] = $OverallScoreByRound[$CurrentRound - 1][$CurrentPlayer] + $ScoreByRound[$CurrentPlayer][$CurrentRound];
    }
  $OverallScoreByRound[$CurrentRound] = $OverallScoreByThisRound;

  $PlayerIDList = range(1, $MaxPlayer);
  $OverallScoreByRound[$CurrentRound] = $OverallScoreByThisRound;
  $OverallScoreByThisRoundRanked = $OverallScoreByThisRound;

  array_multisort($OverallScoreByThisRoundRanked, SORT_DESC, $PlayerIDList);

  array_unshift($OverallScoreByThisRoundRanked, "phoney");
  unset($OverallScoreByThisRoundRanked[0]);
  array_unshift($PlayerIDList, "phoney");
  unset($PlayerIDList[0]);

  $RankByRound[$CurrentRound] = $PlayerIDList;

  } else {

  // Establish ranking for a dynamic round
  $OverallScoreByThisRound = [];
  $RankingFormula = stripslashes($RankMap[$CurrentRound]);
  eval($RankingFormula);
  }
  $OverallScoreByRound[$CurrentRound] = $OverallScoreByThisRound;

}

?>
