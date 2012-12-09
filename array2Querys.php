<?php


function array2Queries(&$ArrStruct) {
  $QueryArray = array();
	foreach ($ArrStruct as $key => &$value) {
		$QueryArray[] = table2Query($key, $value);
	}
	return($QueryArray);
}

function table2Query($TableName, &$Collumns) {
	$QueryT = "CREATE TABLE IF NOT EXISTS `".$TableName."` (\n" .
			"  `_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Internal Reference ID',\n";
	$ExtraKeys = array();
	foreach ($Collumns as $key => &$value) {
		if($value->Key !== false)
			$ExtraKeys[] = $key;
		$QueryT .= "  `".$key."` ".$value->Type.(($value->Len == 0)? '':'('.$value->Len.')').($value->Signed? '':' unsigned').($value->Null? ' DEFAULT NULL':'').(($value->Comment == '')? '':' COMMENT \''.$value->Comment.'\'').",\n";
	}

	
	$QueryT .= "  PRIMARY KEY (`_id`)";
	foreach ($ExtraKeys as &$value) {
		$QueryT .= ",\n  KEY `".$TableName.'_'.$value."` (`".$value."`)";
	}
	
	$QueryT .="\n) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	return($QueryT);
}

?>