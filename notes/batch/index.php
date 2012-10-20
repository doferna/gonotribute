<?

// cmd UPDATE INSERT DELETE etc.


//$fh = fopen("/tmp/notesbatch.txt", 'w') or die("can't open file");
//$s = print_r($_POST, TRUE);

$putdata = fopen("php://input", "r");


while ($data = fread($putdata, 1024)) {
  //fwrite($fh, $data);
  $p=$p.$data ;
}

$code=substr($p,6,32);
$data=substr($p,43);
$data=urldecode($data);

//$data = str_replace(array("%22", "%5B", "%3A","%5D","%2C","%7B","%7D","%26","%3B"), 
 //                        array("\"","[", ":", "]",",","{","}","&",";" ), $data);


//echo "data=".$data."\n";
//fwrite($fh,"\ndata=".$data."\n");

$j=json_decode($data,true);
$jl=count($j);
//fwrite ($fh,"jl=".$jl."\n");
 
include '../../include/dbfunctions.php';

$link=db_connect();

for ($ii=0;$ii<$jl;$ii++) {

//fwrite ($fh,"cmd= $ii".$j[$ii]['cmd']."\n");


if ($j[$ii]['cmd']=="INSERT") {
	$c=htmlspecialchars_decode($j[$ii]['content']);
	$c=mysql_real_escape_string($c,$link);
	
	// si no hay bookcode buscarlo del seccode...
	$str="INSERT INTO notes (code, seccode, content, pos) VALUES ('%s','%s','%s',%s)	";
	$query = sprintf($str, $j[$ii]["code"],$j[$ii]["sec_code"], $c, $j[$ii]["pos"]+0);
	//fwrite ($fh,"j0=".$j[$ii]['cmd']."\n");  fwrite ($fh,"q=$query\n");
	mysql_query($query);
}


if ($j[$ii]['cmd']=="DELETE") {
	// hay que mandar al trash somehow
	$str="DELETE FROM notes WHERE code='%s';";
	$q=sprintf($str,$j[$ii]["code"]);
	//fwrite ($fh,"j0=".$j[$ii]['cmd']."\n");  fwrite ($fh,"q=$q\n");
	mysql_query($q);
}

// hay que buscar una seccion en el bookcode y cambiarle la seccion...

if ($j[$ii]['cmd']=="MOVE" and array_key_exists('book_code',$j[$ii])) {

 $str = "SELECT * FROM `sections` WHERE `bookcode`= \"%s\"";
 $q = sprintf($str,$j[$ii]['book_code']);
 $r=db_getRecord($link, $q);
 //fwrite ($fh,"$q\n"); 
 $str="UPDATE `notes` SET `seccode` = \"%s\" WHERE `code` = \"%s\" ; ";
 $q=sprintf($str,$r["code"],$j[$ii]["code"]);
 //fwrite ($fh,"$q\n");

/*
	$str="UPDATE `notes` SET `bookcode` = \"%s\" WHERE `code` = \"%s\" ; ";
	$q=sprintf($str,$j[$ii]["book_code"],$j[$ii]["code"]);
*/	
	//fwrite ($fh,"j0=".$j[$ii]['cmd']."\n");  fwrite ($fh,"q=$q\n");
	mysql_query($q);
}

if ($j[$ii]['cmd']=="SEC_DELETE" and array_key_exists('code',$j[$ii]) ) {
	// borra todo la section y sus notas...
	
}


if ($j[$ii]['cmd']=="SEC_MOVE" and array_key_exists('book_code',$j[$ii]) and array_key_exists('code',$j[$ii]) ) {
	$str="UPDATE `sections` SET `bookcode`=\"%s\" WHERE `sections`.`code`=\"%s\";";
	$q=sprintf($str,$j[$ii]["book_code"],$j[$ii]["code"]);
	//fwrite ($fh,"jsm=".$j[$ii]['cmd']."\n");  fwrite ($fh,"q0=$q\n");
	mysql_query($q);
}

if ($j[$ii]['cmd']=="SEC_UPDATE" and array_key_exists('name',$j[$ii])) {
  $str="UPDATE `sections` SET `name`=\"%s\" WHERE `sections`.`code`=\"%s\";";
	$q=sprintf($str,$j[$ii]["name"],$j[$ii]["code"]);
	//fwrite ($fh,"j0=".$j[$ii]['cmd']."\n");  fwrite ($fh,"q0=$q\n");
	mysql_query($q);
}

if ($j[$ii]['cmd']=="SEC_DELETE_HEADER" and array_key_exists('code',$j[$ii])) {
	$str="UPDATE `sections` SET `name`=\"\" WHERE `code`=\"%s\"; ";
	$q=sprintf($str,$j[$ii]["code"]);
	//fwrite ($fh,"jdh=".$j[$ii]['cmd']."\n");  fwrite ($fh,"q0=$q\n");
	mysql_query($q);
}

if ($j[$ii]['cmd']=="UPDATE") {

// http://www.php.net/manual/en/function.htmlentities.php
// http://www.php.net/manual/en/function.htmlspecialchars-decode.php

$c=htmlspecialchars_decode($j[$ii]['content']);
$c=mysql_real_escape_string($c,$link);

$coma="";$q0="";
if ($c!="") {$q0=" `content` = \"".$c."\" "; $coma=", ";}

$co=$j[$ii]['comment'];
if (array_key_exists('comment',$j[$ii])) {$q0=$q0.$coma." `comment` = \"".$co."\" ";$coma=", ";}

$co=$j[$ii]['tags'];
if (array_key_exists('tags',$j[$ii])) {$q0=$q0.$coma." `tags` = \"".$co."\" ";$coma=", ";}


$co=$j[$ii]['pos']+0;
if (array_key_exists('pos',$j[$ii])) {$q0=$q0.$coma." `pos` = \"".$co."\" ";$coma=", ";}

$co=$j[$ii]['sec_code'];
if (array_key_exists('sec_code',$j[$ii])) {$q0=$q0.$coma." `seccode` = \"".$co."\" ";$coma=", ";}

$q="UPDATE `notes` SET ".$q0." WHERE `notes`.`code` = \"".$j[$ii]['code']."\";";

mysql_query($q);


} //end UPDATE

// fwrite ($fh,"j=--".$j[$ii]['cmd']."--\n");fwrite ($fh,"q=$q\n");

} //end for 

//print_r($j);
//fwrite ($fh,"\n".print_r($j,TRUE)."\n");fclose($fh);

fclose($putdata);

?>
