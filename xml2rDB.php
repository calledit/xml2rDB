<?php
//The Code works but it is not supper nicly written i just wan't to push it out there

//This is The Variable where we will put the DB structure
$dbStru = NULL;

//Read the xml file with our data and create a XML Schema for it
require_once ('xmlClassifier.php');


//Create DB Querys that create the tabels in our mysql DB
require_once ('array2Querys.php');


$TABLE = 'dictionary';
echo file_put_contents($TABLE.'_DBshema.php_serialize',serialize($dbStru));


/*Debug
$Queries = array2Queries($dbStru);

foreach ($Queries as $key => &$value) {
  echo($value."\n\n");
}
*/
?>