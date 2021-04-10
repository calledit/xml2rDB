<?php

$ISLIVE = true;
require_once ('Common.php');

$options = getopt("lf:");
if(!isset($options['f'])){
	echo "missing xml file argument \n";
	exit;
}
$XMLFile = $options['f'];

//Loads The data from the xml file
$element = simplexml_load_file($XMLFile);

//$TABLE = $element->getName();
//Load the XML Schema That was created using xml2rDB.php
require_once ('DataSchema.php');

//var_dump($Schema);





$LevelName = array();

InsertDataToDB($element);



function InsertDataToDB(&$element,$level = 0,$Curent_Table = false,$OwnerRow = NULL, $collum_name = array()){
  global $LevelName, $Schema, $SQL_Handle;
	if($Curent_Table === false){
		if(isset($Schema[$element->getName()])){
			$Curent_Table = $element->getName();
		}else {
			throw new Exception('The xml file does not seam to match the current schema(object "'.$element->getName().'" can not be found in the current schema schema). Load another file or change Schema/make a new schema and DB.');
		}
	}
		
	
	$LevelName[] = $element->getName();
	
	
	$Table_Holder = implode('_',$LevelName);
	
	$Curent_RowId = $OwnerRow;
	
	if(isset($Schema[$Table_Holder])){
		
		$OwnerTableCollumn = $Curent_Table;
		$Curent_Table = $Table_Holder;
		
		
		//echo('Insert to table: '.$Curent_Table."");
		//echo('INSERT INTO `'.$Curent_Table.'` '."\n");
		$val = '() VALUES ()';
		if(isset($OwnerRow)){
			/*$poped = array_pop($LevelName);
			$OwnerTableCollumn = implode('_',$LevelName);
			$LevelName[] = $poped;*/
			
			$val = 'SET `'.$OwnerTableCollumn.'ID` = '.$OwnerRow;
		}
		if(!mysqli_query($SQL_Handle, 'INSERT INTO `'.$Curent_Table.'` '.$val))
				throw new Exception('Could not insert new row to the table: '.$Curent_Table."\nMYSQL: ".mysqli_error($SQL_Handle)."\n".$val);
		
		$Curent_RowId = mysqli_insert_id($SQL_Handle);
		
		//$Curent_RowId = rand(1000,10000);
		
		$collum_name = array();
		
		
		//echo(' ('.$Curent_RowId.")".$OwIdText."\n");
	}else{
		$collum_name[] = $element->getName();
	}
	
	
	//echo(."\n");
	$AtribsArr = array();
	foreach($element->attributes() as $AtrKey => $AtrVal){
			
		
		$collum_name[] = $AtrKey;
		
		$AtribsArr[implode('_',$collum_name)] = utf8_decode($AtrVal->__toString());
		
		array_pop($collum_name);
	}
	
	if(!MySql_update($AtribsArr, array('_id' => $Curent_RowId), $Curent_Table))
		throw new Exception('Could not update row where _id = '.$Curent_RowId.' in the table: '.$Curent_Table."\nMYSQL: ".mysqli_error($SQL_Handle)."\n");
		
	
	
	foreach($element as $key => $TmHold){
		InsertDataToDB($TmHold, $level + 1, $Curent_Table, $Curent_RowId, $collum_name);	
	}
	
	array_pop($collum_name);
	array_pop($LevelName);
}

?>
