<?php


function array2Queries(&$ArrStruct, $RELATIONS) {
  $QueryArray = array();
	foreach ($ArrStruct as $key => &$value) {
		$r = table2Query($key, $value);
		$QueryArray[] = $r[0];
		if($r[1] != "" && $RELATIONS){
			$QueryArray[] = $r[1];
		}
	}
	return($QueryArray);
}

function table2Query($TableName, &$Collumns) {
	$QueryT = "CREATE TABLE IF NOT EXISTS `".$TableName."` (\n" .
			"  `_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Internal Reference ID',\n";
	$ExtraKeys = array();
	$restriction = "";
	foreach ($Collumns as $key => &$value) {
		if($value->Key !== false){
			$ExtraKeys[] = $key;
		}
		$QueryT .= "  `".$key."` ".$value->Type.(($value->Len == 0)? '':'('.$value->Len.')').($value->Signed? '':' unsigned').($value->Null? ' DEFAULT NULL':'').(($value->Comment == '')? '':' COMMENT \''.$value->Comment.'\'').",\n";
		if($value->Restriction != ""){
			$restriction .= "ALTER TABLE `".$TableName."` ADD FOREIGN KEY (`".$key."`) REFERENCES `".$value->Restriction."`(`_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;";
		}
	}

	
	$QueryT .= "  PRIMARY KEY (`_id`)";
	foreach ($ExtraKeys as &$value) {
		$QueryT .= ",\n  KEY `".$TableName.'_'.$value."` (`".$value."`)";
	}
	
	$QueryT .="\n) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;";
	return(array($QueryT, $restriction));
}

?>
