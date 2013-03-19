<?php

//Some class definitions and Mysql connection config 
require_once ('Common.php');

//Load our XML file
$element = simplexml_load_file($XMLFile);

//To get the name correctly in the structure
$RootName = $element->getName();

$stHel = new Database();
$input = new stdClass();
$input->$RootName = $element;
$LevelName = array();

//unset the original xml data copy to save memmory
unset($element);

//Go throgh the array and create a intermidiate format that is easy to handle
goTroughArray($input,$stHel);

//Go throgh the The Database object and decide what value type fit best to each value 
ClassifyValues($stHel);

//Remove lists with only one object and save the object directly instead
Optimize4Database($stHel->$RootName);



//Flatten The array mysql i flat and uses references to have hirarcys
$dbStru = ToDatabase($stHel);



//Now we are done below we have function definitions

//echo (simplexml_import_dom($stHel)->asXML());
//echo(json_encode($stHel));



//print_r($dbStru);

function Optimize4Database(&$ArrStruct){
	foreach($ArrStruct as $key => &$value){
		
		/*if(is_array($value))
			$val = $value[0];
		else
			$val = $value;*/
		
		
		if(is_object($value) && get_class($value) == 'Table'){
			Optimize4Database($value);
			$mvArr = array();
			foreach($value as $keymv => $valuemv){
				$tm = $key.'_'.$keymv;
				$mvArr[$tm] = $valuemv;
			}
			unset($ArrStruct->$key);
			foreach($mvArr as $keymv => $valuemv){
				$ArrStruct->$keymv = $valuemv;
			}
			
		}else if(is_array($value))
			Optimize4Database($value[0]);
	}
}

function ToDatabase($ArrStruct,$name = array()){
	$tables = array();
	/*$XmlNodeName = '';
	if(count($name) >= 1)
		$XmlNodeName = $name[count($name)-1];*/
	
	$curTable = implode('_',$name);
	$ParentTable = implode('_',array_slice($name,0,count($name)-1));
	foreach($ArrStruct as $key => $value){
		if(is_array($value))
			$value = $value[0];
		
		if(is_object($value) && get_class($value) == 'Table'){
			
			array_push($name,$key);
			$tables = array_merge($tables,ToDatabase($value, $name));
			array_pop($name);
		}else{
			if(!isset($tables[$curTable])){
				$tables[$curTable] = array();//'_id' => 'int'
				if($ParentTable != ''){
					$tables[$curTable][$ParentTable.'ID'] =  new Collumn();
					$tables[$curTable][$ParentTable.'ID']->Signed = false;
					$tables[$curTable][$ParentTable.'ID']->Null = false;
					$tables[$curTable][$ParentTable.'ID']->Key = true;
					$tables[$curTable][$ParentTable.'ID']->Type = 'int';
					$tables[$curTable][$ParentTable.'ID']->Comment = 'Pointer To _id of Owner row in the table: '.$ParentTable;
				}
			}
			$tables[$curTable][$key] = $value;
		}
	}
	
	if(isset($tables[$curTable])){
		//$tables[$name][] = 'int';
		
	}
	
	return($tables);
}


function ClassifyValues(&$ArrStruct){
	
	foreach($ArrStruct as $key => &$value){
		
		if(is_object($value) && get_class($value) == 'ValueClassifier'){
			
			$ToVal = new Collumn();
			
			$ToVal->Path = $value->Path;
			
			if($value->IsDoubble && !$value->IsInt)
				$ToVal->Type = 'doubble';
			else if($value->IsInt)
				$ToVal->Type = 'int';
			else{
				$ToVal->Len = $value->MaxLen;
				if($value->MaxLen > 8)
					$ToVal->Len = pow(2,ceil(log($value->MaxLen,2)))-1;
				$ToVal->Type = 'varchar';
			}
			$value = $ToVal;
			
			
		}else
			ClassifyValues($value);
	}
}


function goTroughArray(&$Arr,&$Xstruct,$level = 0){
	global $LevelName;
	$localArr = array();
	foreach($Arr as $key => $TmHold){
		
		$LevelName[] = $key;
		
		//echo(implode('_',$LevelName)."(element)\n");
		
		$curObj = &$Xstruct->$key;
		
		
		
		if(!isset($localArr[$key]))
			$localArr[$key] = 1;
		else
			$localArr[$key]++;
		
		if($localArr[$key] > 1){
			if(!is_array($curObj))
				$Xstruct->$key = array($curObj);
		}

		
		if(!isset($curObj))
			$curObj = new Table();

		foreach($TmHold->attributes() as $AtrKey => $AtrVal){
			
			$LevelName[] = $AtrKey;
			
			if(is_array($curObj) && isset($curObj[0]->$AtrKey))
				$ValType = $curObj[0]->$AtrKey;
			else if(isset($curObj->$AtrKey))
				$ValType = $curObj->$AtrKey;
			else{
				$ValType = new ValueClassifier();
				$ValType->Path = implode('_',$LevelName);
			}
			
			
			$ValStr = $AtrVal->__toString();
			
			
			
			
			if($ValType->IsInt && strval(intval($ValStr)) !== $ValStr)
				$ValType->IsInt = false;
				
			
			if($ValType->IsDoubble && strval(doubleval($ValStr)) !== $ValStr)
				$ValType->IsDoubble = false;

			
			$ValType->MaxLen = max(strlen($ValStr),$ValType->MaxLen);
			
			if(is_array($curObj)){
				$curObj[0]->$AtrKey = $ValType;
				/*
				if(!isset($curObj[0]->$AtrKey))
					$curObj[0]->$AtrKey = array();

				array_push($curObj[0]->$AtrKey, $AtrVal->__toString());

				*/
			}else{
				$curObj->$AtrKey = $ValType;
				/*
				if(!isset($curObj->$AtrKey))
					$curObj->$AtrKey = array();

				array_push($curObj->$AtrKey, $AtrVal->__toString());

				*/
			}
			//echo(implode('/',$LevelName)."(attr)\n");
			
			array_pop($LevelName);
		}
		
		//echo(implode('/',$LevelName)."\n");
		
		if(is_array($curObj))
			goTroughArray($TmHold,$curObj[0],$level + 1);
		else
			goTroughArray($TmHold,$curObj,$level + 1);	
		array_pop($LevelName);
	}
}

//echo($level."_1".memory_get_usage(true)."\n");




?>
