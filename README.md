xml2rDB
=======

Convert xml data to MySQL 

The code here is far from butifull... you are welcome to clean it up, i don't feel like it.

1. Edit the Commons.php file so that the sql config is correct and the main tabel name and xmlfile is like you want it
2. then run php5 xml2rDB.php
3. Wait for it to spit out some sql querys it wont run them you have to run them yourself in Php my admin or mysql cli
4. Run php5 InsertData.php which will connect to you database and insert the data from your xml file