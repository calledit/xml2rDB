<?php
//The Code works but it is not supper nicly written i just wan't to push it out there

//This is The Variable where we will put the DB structure
$dbStru = NULL;

$RELATIONS = true;

$options = getopt("lnf:");
if(isset($options['l'])){
	$ISLIVE = true;
}
if(isset($options['n'])){
	$RELATIONS = false;
}
//Xml file and table name is somthing we need
require_once ('Common.php');

if(!isset($options['f'])){
	echo "missing xml file argument \n";
	exit;
}
$XMLFile = $options['f'];


//Read the xml file with our data and create a XML Schema for it
require_once ('xmlClassifier.php');



//Create DB Querys that create the tabels in our mysql DB
require_once ('array2Querys.php');


file_put_contents($TABLE.'_DBshema.php_serialize',serialize($dbStru));



$Queries = array2Queries($dbStru, $RELATIONS);

foreach ($Queries as $key => &$value) {
	if(isset($ISLIVE) && $ISLIVE){
		mysqli_query($SQL_Handle, $value);
		echo("executed query ".mysqli_error($SQL_Handle)."\n");
	}else{
		echo($value."\n\n");
	}
}

?>
