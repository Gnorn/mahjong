# mahjong
A Mahjong tournament software

Quick overview before a more detailed readme is available: this allows you to run a mahjong tournament (or other 4-player games) by importing a table map, creating players, inputing results and penalties, and getting a ranking. Dynamic placement and ranking (depending on the result of players during the previous rounds for placement, and allowing for advanced calculation methods for ranking) is also available.

Required : Apache2, PHP5, SQLite3. If like me you're running this on debian and don't have those, you should be good to go with sudo apt-get install apache2 php5 libapache2-mod-php5 php5-sqlite . If you're using another distribution you probably can manage to get it running. If you don't know what all this is about, read the following :

VERY EASY START :
You can run this in a virtual machine (VM) on your computer. It's like a virtual computer running inside your computer. In order to do that, do the followings :
1) Download and install VirtualBox here: https://www.virtualbox.org
2) Download the Mahjong tournament software VM here: http://www.chuuren.fr/vrac/mahjong-tournament.ova
3) Open VirtualBox, go to File > Import Appliance
4) Choose the mahjong-tournament.ova file
5) When asked what to import, leave the basic options checked
6) You'll end up with a virtual machine called "Mahjong Tournament Software", select it and click the start button
7) The machine will start. The interface is text-only. Wait for the following to show up :
Debian GNU/Linux 8 debian tty1
debian login : _
8) Login to the machine by typing first the login "mahjong", pressing enter, then when asked the password "tournament" (nothing will show as you type, it's normal)
9) Type the following command : "sudo ifconfig", then press enter
10) Type the password "tournament"
11) Find the IP address associated to the interface "eth0", it should be in the section "inet addr" (four numbers separated by a point)
12) Keep VirtualBox running and go to your browser, then type http://the_ip_address_you_just_found
13) Run your tournament :)
14) To turn off the virtual machine, just close the window, and when asked choose the "Send shutdown signal" option