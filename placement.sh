#!/bin/bash

#Print array with printf '%s\n' "${TestPlayer1[@]}"

if [ -z ${3+x} ]
then
  echo -e "Usage: ./placement.sh number_of_rounds number_of_players max_number_of_tries_for_each_round [output_file]\n-number_of_rounds is the number of rounds you'll be playing\n-number_of_players is the number of players entering the tournament\n-max_number_of_tries_for_each_round is the number of times the script is allowed to try to generate a better version of the round it's working on (will stop if perfect)\n-output_file is the file to which the tournament map will be saved\n\nThis script generates tournament map in a format that is suitable for the Mahjong tournament software it is shipped with (CSV with information regarding round, table and players' IDs and scores, for more details see README.md file). It tries to get as few rematches as possible. To do that, for each round of the tournament it will randomly generate a big number of candidates, check how many rematches they induce based on the previous rounds, and keep the best. It will automatically stop if it finds a perfect round (which doesn't induce any rematch), otherwise it will stop when it reaches the maximum number of tries you allowed (higher number of tries for higher quality maps, lower number of tries for lower computational time). You can judge of the quality of a map by its \"Tournament score\", which is just the total number of rematches during the tournament."
  exit 1
fi

Rounds=$1
Players=$2
MaxTries=$3
Tables=$(($Players/4))

if ! [ -z ${4+x} ]
then
  ExitFile=$4
fi

echo "Looking for a solution for $Rounds rounds and $Players players ($Tables tables)..."

# Round 1 is set by numbered order

for (( Table=1; Table<=$Tables; Table++ ))
do
  TableMap="1,$Table,$(( ($Table-1)*4+1 )),0,$(( ($Table-1)*4+2 )),0,$(( ($Table-1)*4+3 )),0,$(( ($Table-1)*4+4 )),0"
  TournamentMap="$TournamentMap\n$TableMap"

  eval "Player$(( ($Table-1)*4+1 ))=($(( ($Table-1)*4+2 )) $(( ($Table-1)*4+3 )) $(( ($Table-1)*4+4 )))"
  eval "Player$(( ($Table-1)*4+2 ))=($(( ($Table-1)*4+1 )) $(( ($Table-1)*4+3 )) $(( ($Table-1)*4+4 )))"
  eval "Player$(( ($Table-1)*4+3 ))=($(( ($Table-1)*4+1 )) $(( ($Table-1)*4+2 )) $(( ($Table-1)*4+4 )))"
  eval "Player$(( ($Table-1)*4+4 ))=($(( ($Table-1)*4+1 )) $(( ($Table-1)*4+2 )) $(( ($Table-1)*4+3 )))"
done

echo "Round 1 done."

# From then on players are randomly assigned

for (( Round=2; Round<=$Rounds; Round++))
do
  RoundScore=1000
  Try=$MaxTries
  while (( $RoundScore != 0 )) && (( $Try != 0 ))
  do
    ThisRoundScore=0
    unset ThisRoundMap
    PlayersArray=( $(gshuf -i 1-$Players) )
    for (( Table=1; Table<=$Tables; Table++ ))
    do
      TableMap="$Round,$Table"
      for (( Player=1; Player<=4; Player++ ))
      do
        ThisPlayer=${PlayersArray[$Player-1+4*($Table-1)]}
        TableMap="$TableMap,$ThisPlayer,0"
        eval "ThisPlayer$Player=$ThisPlayer"
      done
      ThisRoundMap="$ThisRoundMap\n$TableMap"
      eval "TestPlayer1=(\${Player$ThisPlayer1[@]})"
      eval "TestPlayer2=(\${Player$ThisPlayer2[@]})"
      eval "TestPlayer3=(\${Player$ThisPlayer3[@]})"
      eval "TestPlayer4=(\${Player$ThisPlayer4[@]})"

      for player in ${TestPlayer1[*]}
      do
        if [[ $player == $ThisPlayer2 ]]
        then
         ThisRoundScore=$(( ThisRoundScore+1 ))
        fi
        if [[ $player == $ThisPlayer3 ]]
        then
         ThisRoundScore=$(( ThisRoundScore+1 ))
        fi
        if [[ $player == $ThisPlayer4 ]]
        then
         ThisRoundScore=$(( ThisRoundScore+1 ))
        fi
      done

      for player in ${TestPlayer2[*]}
      do
        if [[ $player == $ThisPlayer3 ]]
        then
         ThisRoundScore=$(( ThisRoundScore+1 ))
        fi
        if [[ $player == $ThisPlayer4 ]]
        then
         ThisRoundScore=$(( ThisRoundScore+1 ))
        fi
      done

      for player in ${TestPlayer3[*]}
      do
        if [[ $player == ThisPlayer4 ]]
        then
         ThisRoundScore=$(( ThisRoundScore+1 ))
        fi
      done

    eval "NewTestArrayPlayer$ThisPlayer1=($ThisPlayer2 $ThisPlayer3 $ThisPlayer4)"
    eval "NewTestArrayPlayer$ThisPlayer2=($ThisPlayer1 $ThisPlayer3 $ThisPlayer4)"
    eval "NewTestArrayPlayer$ThisPlayer3=($ThisPlayer1 $ThisPlayer2 $ThisPlayer4)"
    eval "NewTestArrayPlayer$ThisPlayer4=($ThisPlayer1 $ThisPlayer2 $ThisPlayer3)"

    done

    if (( $ThisRoundScore < $RoundScore ))
    then
      RoundScore=$ThisRoundScore
      RoundMap=$ThisRoundMap
      for (( Player=1; Player<=$Players; Player++ ))
      do
        eval "NewArrayPlayer$Player=(\${NewTestArrayPlayer$Player[@]})"
      done
    fi

    Try=$(($Try-1))
    
  done

  for (( Player=1; Player<=$Players; Player++ ))
  do
    eval "Player$Player+=(\${NewArrayPlayer$Player[@]})"
    eval "Player$Player=(\$(for l in \${Player$Player[@]}; do echo \$l; done | sort -u))"
  done

  echo "Round $Round done (score: $RoundScore)."
  TournamentMap="$TournamentMap$RoundMap"
  TournamentScore=$(( $TournamentScore+$RoundScore ))
done
echo -e "\nTournament map:$TournamentMap"

if ! [ -z ${ExitFile+x} ]
then
  echo "(Saved to $ExitFile)"
  echo -e $TournamentMap > $ExitFile
fi

echo -e "\nTournament score: $TournamentScore"

#echo "Player1 a joue avec :"
#printf '%s\n' "${Player1[@]}"