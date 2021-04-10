<?php
include('db.conf.php');

$TABLE = 'datafile';
$XMLFile = 'NOFILE';

$SQL_Handle = 'NO_DB_CONNECTION';
if(isset($ISLIVE) && $ISLIVE){
  $SQL_Handle = @mysqli_connect($SQL_host,$SQL_user,$SQL_pass);
	if (!$SQL_Handle)die('Database connection failed');

	//use the normal database
	mysqli_select_db($SQL_Handle, $data_db);
	$SQL_Handle->autocommit(false);
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
}
class Database  {}

class Table  {}

class ValueClassifier  {
	public $MaxLen = 0;
	public $IsInt = true;
	public $IsDoubble = true;
	public $Path = '';
}

class Collumn  {
	public $Len = 0;
	public $Type = 'int';
	public $Signed = true;
	public $Null = true;
	public $Comment = '';
	public $Restriction = '';
	public $Key = false;
	public $Path = '';
}

function xml_name_2sql_name($str){
	return(str_replace('-', '_', $str));
}

function MySql_update($Values, $Definers = NULL, $Table = 'Write Table Name Here'){
	global $SQL_Handle;
	
	if(count($Values) == 0)
		return (true);
	//initialaze the query string
	$query = 'UPDATE '.$Table.' SET ';
	
	//Add each value that sould be saved
	foreach ($Values as $key => $value){
		$query .= '`' . mysqli_real_escape_string($SQL_Handle, $key) . '`'  . ' = \'' . mysqli_real_escape_string($SQL_Handle, $value) . '\', ';
	}
	$query = substr($query, 0, -2); //Remove the last ', '	
	
	$queryUpdEx = '';	
	if(isset($Definers) && count($Definers) != 0){//if we have definers: add the where clause.
		$queryUpdEx = " \nwhere \n";//lets phrase the definers
		foreach ($Definers as $key => $value)
			if(isset($value)){
				$queryUpdEx .= '`' . mysqli_real_escape_string($SQL_Handle, $key) . '`'  . ' = \'' . mysqli_real_escape_string($SQL_Handle, $value) . '\' && ';
			}
		
		$queryUpdEx = substr($queryUpdEx, 0, -4); // Remove the last " && "
	}
	
	
	$qr = $query.$queryUpdEx;
	$query_res = mysqli_query($SQL_Handle, $qr);
	

		
	
	return($query_res);
}


?>
