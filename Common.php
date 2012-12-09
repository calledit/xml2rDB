<?php
$TABLE = 'dictionary';
$XMLFile = 'folkets_sv_en_public.xml';

$data_db = "DAtabaseName";
$SQL_user = "MysqlUser";
$SQL_pass = "MysqlPassword";
$SQL_host = "MysqlHost";


$SQL_Handle = 'NO_DB_CONNECTION';
if(isset($ISLIVE) && $ISLIVE){
  $SQL_Handle = @mysql_connect($SQL_host,$SQL_user,$SQL_pass);
	if (!$SQL_Handle)die('Database connection failed');

	//use the normal database
	mysql_select_db($data_db,$SQL_Handle);

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
	public $Key = false;
	public $Path = '';
}


function MySql_update($Values, $Definers = NULL, $Table = 'Write Table Name Here'){
	global $SQL_Handle;
	
	if(count($Values) == 0)
		return (true);
	//initialaze the query string
	$query = 'UPDATE '.$Table.' SET ';
	
	//Add each value that sould be saved
	foreach ($Values as $key => $value)
		$query .= '`' . mysql_real_escape_string($key) . '`'  . ' = \'' . mysql_real_escape_string($value) . '\', ';
	$query = substr($query, 0, -2); //Remove the last ', '	
	
	$queryUpdEx = '';	
	if(isset($Definers) && count($Definers) != 0){//if we have definers: add the where clause.
		$queryUpdEx = " \nwhere \n";//lets phrase the definers
		foreach ($Definers as $key => $value)
			if(isset($value))
				$queryUpdEx .= '`' . mysql_real_escape_string($key) . '`'  . ' = ' . floatval($value) . ' && ';
		
		$queryUpdEx = substr($queryUpdEx, 0, -4); // Remove the last " && "
	}
	
	
	$qr = $query.$queryUpdEx;
	$query_res = mysql_query($qr,$SQL_Handle);
	

		
	
	return($query_res);
}


?>