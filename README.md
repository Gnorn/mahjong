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

---

REQUIRED: Apache2, PHP5, SQLite3. If like me you're running this on debian and don't have those, you should be good to go with
sudo apt-get install apache2 php5 libapache2-mod-php5 php5-sqlite
If you're using another distribution you probably can manage to get it running. If you don't know what all this is about, read the "VERY EASY START" section.

---

INSTALLATION: this is a web based application. You can run it on a web server with PHP5 and SQLite3 (local or distant). You should just put the files on the server and access them from a browser. There is a public part, which is at the root of the folder, and a private part, in the folder admin (for now the public part is empty, but it will have the same functions as the admin part without the editing capabilities). If you intend to run this on a public server, you should secure the admin folder (I use a .htaccess file).

---

SECURITY DISCLAIMER: one of the functions of this program, the dynamic tournament system, allows the user to run arbitrary PHP code. The option is deactivated by default, but the program is probably not well secured due to my lack of programming skills. Before you use it, and especially before you activate the dynamic tournament system, you should acknowledge that I'm not responsible for any damage, and you should secure the access to the "admin" folder!

---

HOW TO USE: when you first access the program, it will create a database which will contain tournaments info. You will be presented with an empty list of tournaments, click on "Create new tournament..." You will then be asked for

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