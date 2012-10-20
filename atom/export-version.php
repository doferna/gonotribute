<?php

/*
hay que hacer funcion ajax y revisar que el INSERT final de ok, porque falla
por ejemplo con el sql eschema

*/

//$fh = fopen("/tmp/notes.txt", 'w') or die("can't open file");
//$s = print_r($_GET, TRUE);fwrite($fh, $s);



 include '../include/dbfunctions.php';
 include '../include/check_login.php';
    
    if (!isset($_SESSION["username"])) exit ();
    $username=$_SESSION["username"];


$book=$_GET['bc'];
//$book="654e9e683adb98bb901973559423ddd1";
//$book="f9526d95007a1e1a9acb14adcb6187af";

// abrir la base mysql
$con=db_connect();
mysql_set_charset("UTF8", $con);

  $sql = "SELECT MAX(v) FROM json WHERE  `id` =  '".$book."'";
	$U = db_getRecord($con, $sql);
  $version=$U['MAX(v)']+1;
  $sql = "SELECT json, updated_at FROM json WHERE  `id` =  '".$book."' and `v` = '".$U['MAX(v)']."' ";
  $U1 = db_getRecord($con, $sql);
  
$sections=array(); $notes=array();

$q="SELECT *  FROM `sections` WHERE bookcode=\"$book\" ORDER BY `name`;";
$result = mysql_query($q) or die(mysql_error());

$i=0;$j=0;
while($r = mysql_fetch_array($result)){
	$sections['sections'][$i]['code']=$r['code'];
	$sections['sections'][$i]['name']=$r['name'];
	$sections['sections'][$i]['pos']=$r['pos'];
  $q1="SELECT * FROM `notes` WHERE seccode=\"".$r['code']."\" ORDER BY  `pos` ;";
  $result1 = mysql_query($q1) or die(mysql_error());
  while($r1 = mysql_fetch_array($result1)){
  	
  	
      $notes[$j]['code']=$r1['code'];
      $notes[$j]['name']=$r1['name'];
      $notes[$j]['pos']=$r1['pos'];
      $notes[$j]['secCode']=$r1['seccode'];
      $notes[$j]['content']=$r1['content'];
      $notes[$j]['comment']=$r1['comment'];
      $notes[$j]['ref_title']=$r1['ref_title'];
      $notes[$j]['ref_url']=$r1['ref_url'];
      $notes[$j]['tags']=$r1['tags'];
      
     // tiene que venir el updated_at o updated_at2 de algun modo...
     // "updated_at":"June 11, 2012","updated_at2":"06/11/12","updated_at3":"2012-06-11T10:23:28Z"
      //$notes[$j]['updated_at2']="06/11/12";
      $notes[$j]['updated_at2']=$r1['updated_at'];
      // last edited
      $notes[$j]['updated_at']="June 11, 2012";
      $notes[$j]['created_at']="2012-06-11T10:23:28Z";
      $notes[$j]['username']=$username;
      $j++;
  }
  $sections['sections'][$i]['notes']=$notes; $notes=array(); $j=0;
  $i++;
}
 $msg="v".$U['MAX(v)']." UNCHANGED since ".$U1['updated_at'].". Nothing saved.";
// save if changed
if (json_encode($sections)!=$U1['json']) { 

  $msg="v$version successfully updated!";
  $body=mysql_real_escape_string(json_encode($sections),$con);
	$str="INSERT INTO json (id, v, json, username) VALUES ('%s',%s,'%s','%s')	;";
	$query = sprintf($str, $book ,$version, $body, $username);
	//echo "$query\n";
	$result=mysql_query($query);   //if (!$result) {   die('Invalid query: ' . mysql_error()); };	
	if (!$result) $msg="error while saving";
}
	
	echo json_encode($msg);
	

?>
