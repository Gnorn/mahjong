# mahjong
A Mahjong tournament software

Copyright 2016 Simon Picard

This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

---

QUICK OVERVIEW: this allows you to run a mahjong tournament (or other 4-player games) by importing a table map, creating players, inputing results and penalties, and getting a ranking. Dynamic placement and ranking (depending on the result of players during the previous rounds for placement, and allowing for advanced calculation methods for ranking) is also available.

If something is not clear in this README file send me an email at sim.pic@free.fr :)

---

REQUIRED: Apache2, PHP5, SQLite3. If like me you're running this on debian and don't have those, you should be good to go with
sudo apt-get install apache2 php5 libapache2-mod-php5 php5-sqlite

If you're using another distribution you probably can manage to get it running. If you don't know what all this is about, read the "VERY EASY START" section at the end of this file!

---

INSTALLATION: this is a web based application. You can run it on a web server (local or distant) with PHP5 and SQLite3. You should just put the files on the server and access them from a browser. There is a public part, which is at the root of the folder, and a private part, in the "admin" folder (for now the public part is empty, but it will eventually have the same functions as the admin part without the editing capabilities). If you intend to run this on a public server, you should secure the admin folder (I use a .htaccess file).

---

SECURITY DISCLAIMER: one of the functions of this program, the dynamic tournament system, allows the user to run arbitrary PHP code. The option is deactivated by default, but the program is probably not well secured due to my lack of programming skills. Before you use it, and especially before you activate the dynamic tournament system, you should acknowledge that I'm not responsible for any damage, and you should secure the access to the "admin" folder!

---

HOW TO USE: when you first access the program, it will create a database which will contain tournaments info. You will be presented with an empty list of tournaments, click on "Create new tournament..." You will then be asked for your tournament info:
-Name of the tournament: should be self-explanatory
-Description: optional, a short description of your tournament
-Tournament map type: static for a static placement with a ranking that takes into account the cumulated score of each player, dynamic for dynamic placement and advanced ranking (see section "Dynamic tournament")
-Tournament map: a map that lists all the games and which player will play against which (see section "Tournament map").

Once you created a tournament, you can go to the tournament's page. You will have several options:
-Players: see the list of players, click on a player's name to see their individual results, click on "Add/edit player(s)" to edit the player's list
-Rankings: see the rankings
Rounds: see all the rounds of the tournament. Click on a round to input the player's scores. There is also an option to edit the tournament map
-Back to index: go back to the list of tournaments
-Download DB: download a copy of the database

The details should be pretty self-explanatory!

---

TOURNAMENT MAP: this is what contains the list of the games that will be played. It is in the CSV format. Each line contains the info of a game in the following format :

Round_number,Table_number,Player1,Player1's_score,Player2,Player2's_score,Player3,Player3's_score,Player4,Player4's_score

The score information is used to pre-fill the game results, for example if you're importing an existing tournament or are doing some seeding. If that's not the case, leave it at 0. Here's an example of a tournament map :
1,1,1,0,2,0,3,0,4,0
1,2,5,0,6,0,7,0,8,0
2,1,1,0,2,0,5,0,6,0
2,2,3,0,4,0,7,0,8,0

In this simplistic tournament, there are 8 players, 2 rounds and 2 tables.
The first line describes table 1 of round 1, with players 1, 2, 3 and 4, all starting at 0.
The second line describes table 2 of round 1, with players 5, 6, 7 and 8, all starting at 0.
The third line describes table 1 of round 2, with players 1, 2, 5 and 6, all starting at 0.
The fourth line describes table 2 of round 2, with players 3, 4, 7 and 8, all starting at 0.

If you want to have a clearer view of this file, just copy the tournament map in a plain text file and save it as *.csv . You can then edit it with a spreadsheet editor (LibreOffice, Excel, etc.)

You can also generate maps using the placement.sh script that is included.

---

DYNAMIC TOURNAMENT: a dynamic tournament is a tournament where the players' placement depend on their result in the tournament, and the ranking may be more complex than just the cumulative scores of the players. This may be used for a swiss-system tournament, or for a tournament with playoffs. This requires the user to execute PHP code and thus poses a security threat if it is not run in a secure environment, so the option is disabled by default. To enable it, edit the prefs.php in the admin folder and comment this line : “$dynamic = "disabled";”.

The format of the tournament map is similar to that of a static tournament, but the player's number may not be plainly expressed, instead being calculated based on the players' results. There is also an extra line after each round that tells the system how to establish ranking.

In order to determine the players' IDs you can use the following variables (although you will mostly use the first one), and even intertwine them:
-$RankByRound[Round][Rank] : if for example you call $RankByRound[3][1] you will get the ID of the highest ranked player after round 3. If you call $RankByRound[4][2] you will get the ID of the second highest ranked player after round 4
-$ScoreByRound[$Player][$Round] : the score player $Player got at round $Round
-$OverallScoreByRound[$Player][$Round] : the overall score of player $Player after round $Round (including previous weighting/calculations)
-basically any php code: maths operators, conditional structures, etc. Your tournament map may become hard to read, though.

Ranking is determined by an extra line after each round that has the following format:
Round_number,r,code_used_to_determine_ranking
The code used to determine ranking will make use of the AttributeRank function that works as such:
AttributeRank(best rank to attribute, worst rank to attribute, pool's best rank from previous deal, pool's worst rank from previous deal, score formula)
-best rank to attribute : that's the best rank you'll attribute with this call
-worst rank to attribute : that's the worst rank you'll attribute with this call
-pool's best rank from previous deal : that's the rank of the highest ranked player you'll consider for the pool to which you'll attribute ranks
-pool's worst rank from previous deal : that's the rank of the lowset ranked player you'll consider for the pool to which you'll attribute ranks
-score formula : how you'll calculate the score that'll be used for ranking (you can use the same variables as we used for placement).

You can call the AttributeRank function as many times as is necessary to establish a ranking. It has to be followed each time by a semi-colon (“;”), and special characters have to be escaped. Note that if your tournaments follows a static structure at a given round, you can just replace all of this by the word “static”. Here is an example of a tournament with 4 deals and 8 players (you can save it as *.csv an open it in a spreadsheet editor). In the first 3 deals the tournament follows a static structure for both placement and ranking. The last game is a playoff where the 4 highest ranked players fight at the same table for ranks 1 to 4 and only get to keep 50% of their previous overall scores, whereas the 4 lowest ranked players fight for ranks 5 to 8 and keep all of their previous overall score :

1,1,1,0,2,0,3,0,4,0
1,2,5,0,6,0,7,0,8,0
1,r,"static"
2,1,1,0,6,0,3,0,8,0
2,2,5,0,2,0,7,0,4,0
2,r,"static"
3,1,1,0,2,0,7,0,8,0
3,2,5,0,6,0,3,0,4,0
3,r,"static"
4,1,"$RankByRound[3][1]",0,"$RankByRound[3][2]",0,"$RankByRound[3][3]",0,"$RankByRound[3][4]",0
4,2,"$RankByRound[3][5]",0,"$RankByRound[3][6]",0,"$RankByRound[3][7]",0,"$RankByRound[3][8]",0
4,r,"AttributeRank(1,4,1,4,\"\\$ThisScore+round(\\$PreviousOverallScore*0.5,1)\");AttributeRank(5,8,5,8,\"\\$ThisScore+\\$PreviousOverallScore\");"

---

VERY EASY START:
You can run this in a virtual machine (VM) on your computer. It's like a virtual computer running inside your computer. Note that you won't necessarily get the last version of the program. In order to do that, do the followings :
1) Download and install VirtualBox here: https://www.virtualbox.org
2) Download the Mahjong tournament software VM here: http://www.chuuren.fr/vrac/mahjong-tournament.ova
3) Open VirtualBox, go to File > Import Appliance
4) Choose the mahjong-tournament.ova file
5) When asked what to import, leave the basic options checked
6) You'll end up with a virtual machine called "Mahjong Tournament Software", select it and click the start button
7) The machine will start. The interface is text-only. Wait for the following to show up :
Debian GNU/Linux 8 debian tty1
debian login : _
8) Login to the machine by typing first the login "mahjong", pressing enter, then when asked type the password "tournament" (nothing will show as you type, it's normal)
9) Type the following command : "sudo ifconfig", then press enter
10) When asked, type the password "tournament"
11) Find the IP address associated to the interface "eth0", it should be in the section "inet addr" (four numbers separated by a point, for example 192.168.1.10)
12) Keep VirtualBox running and go to your browser, then open http://the_ip_address_you_just_found
13) Run your tournament :)
14) To turn off the virtual machine, just close the window, and when asked choose the "Send shutdown signal" option