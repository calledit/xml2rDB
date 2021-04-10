# xml 2 relational DB
=======

## Convert xml data to MySQL 

Takes a XML file as a input analyses it and converts it into a relational mysql database.

Example using https://folkets-lexikon.csc.kth.se/folkets/folkets_sv_en_public.xml

```bash

#Change settings for database conenction
vi db.conf.php

#analyse folkets_sv_en_public.xml and create tables in database
php xml2rDB.php -l -f "../folkets_sv_en_public.xml"

#fill the created database with values from the xml file
php InsertData.php -f ../folkets_sv_en_public.xml

```
Resulting database shema:

![resulting databse shema](https://raw.githubusercontent.com/calledit/xml2rDB/master/shema_dictionary.png)
