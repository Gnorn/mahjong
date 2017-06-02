#!/bin/bash

#Print array with printf '%s\n' "${TestPlayer1[@]}"

if [ -z ${3+x} ]
then
  echo -e "Usage: ./placement.sh number_of_rounds number_of_players max_number_of_tries_for_each_round\n-number_of_rounds is the number of rounds you'll be playing\n-number_of_players is the number of players\n-max_number_of_tries_for_each_round is the number of times the script is allowed to try to generate a better version of the round it's working on (will stop if perfect)\n\nThis script generates tournament map in a format that is suitable for the Mahjong tournament software it is shipped with (CSV with information regarding round, table and players' IDs and scores, for more details see README"
  exit 1
fi

Rounds=$1
Players=$2
MaxTries=$3
Tables=$(($Players/4))

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
  PlayersArray=( $(gshuf -i 1-$Players) )
  while (( $RoundScore != 0 )) && (( $Try != 0 ))
  do
    ThisRoundScore=0
    unset ThisRoundMap
    OldPlayersArray=("${PlayersArray[@]}")


    Exchange1=$(( RANDOM % $Players))
    Exchange2=$(( RANDOM % $Players))
    ExchangePlayer1=${PlayersArray[$Exchange1]}
    ExchangePlayer2=${PlayersArray[$Exchange2]}

    PlayersArray[$Exchange1]=$ExchangePlayer2
    PlayersArray[$Exchange2]=$ExchangePlayer1

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
         ThisRoundScore=$(($ThisRoundScore+1))
        fi
        if [[ $player == $ThisPlayer3 ]]
        then
         ThisRoundScore=$(($ThisRoundScore+1))
        fi
        if [[ $player == $ThisPlayer4 ]]
        then
         ThisRoundScore=$(($ThisRoundScore+1))
        fi
      done

      for player in ${TestPlayer2[*]}
      do
        if [[ $player == $ThisPlayer3 ]]
        then
         ThisRoundScore=$(($ThisRoundScore+1))
        fi
        if [[ $player == $ThisPlayer4 ]]
        then
         ThisRoundScore=$(($ThisRoundScore+1))
        fi
      done



      for player in ${TestPlayer3[*]}
      do
        if [[ $player == $ThisPlayer4 ]]
        then
         ThisRoundScore=$(($ThisRoundScore+1))
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

    else
       PlayersArray=("${OldPlayersArray[@]}")
    fi

    Try=$(($Try-1))
    
  done

  for (( Player=1; Player<=$Players; Player++ ))
  do
    eval "Player$Player+=(\${NewArrayPlayer$Player[@]})"
    eval "Player$Player=(\$(for l in \${Player$Player[@]}; do echo \$l; done | sort -u))"
  done


  echo "Round $Round done (score: $RoundScore) after $(( $MaxTries - $Try )) tries"
  TournamentMap="$TournamentMap$RoundMap"
  TournamentScore=$(( $TournamentScore+$RoundScore ))
done
echo -e $TournamentMap

echo "Tournament score : $TournamentScore"

if ! [ -z ${4+x} ]
then
  for (( CheckPlayer=1; CheckPlayer<=$Players; CheckPlayer++ ))
  do
    echo "Player $CheckPlayer played with:"
    eval "printf '%s\n' \"\${Player$CheckPlayer[@]}\""
  done
fi