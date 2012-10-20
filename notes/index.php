<?php

//
// htmlspecialchars_decode($jsonVariable);
// http://192.168.11.1/cutnote/console.htm

//$fh = fopen("/tmp/notes.txt", 'w') or die("can't open file");
//$s = print_r($_GET, TRUE);fwrite($fh, $s);


$keyword=$_GET['keyword'];
//$keyword='pagina html';
//$keyword='anda el newnote';

include '../include/dbfunctions.php';
include '../include/check_login.php';
    
    if (!isset($_SESSION["username"])) exit ();
    $username=$_SESSION["username"];

$tag=$_GET['tag'];

if ($tag!='') {

  // abrir la base mysql
  $con=db_connect();
  $q1="SELECT notes.*,sections.bookcode as sbook FROM books, sections, notes where `notes`.`seccode`=`sections`.`code` and `sections`.`bookcode`=`books`.`code` and  `notes`.`tags` like '%".$tag."%' ORDER BY  `notes`.`updated_at` DESC;";
	$result1 = mysql_query($q1) or die(mysql_error());
	//fwrite ($fh,"q1=$q1\n"); 	
	
	$i=0;$j=0;$sections=array();$notes=array();$secid=array();
  while($r1 = mysql_fetch_array($result1)){
	
	$s = print_r($r1, TRUE);   
	//fwrite($fh, "$j r1=$s\n");
	
	// hay que traerlas cada uno con su seccion... 
	// manteniendo el array de seccode
	
	    $notes[$j]['code']=$r1['code'];
      $notes[$j]['name']=$r1['name'];
      $notes[$j]['pos']=$r1['pos'];
      $notes[$j]['secCode']=$r1['seccode'];
      $notes[$j]['bookCode']=$r1['sbook'];
      $notes[$j]['content']=$r1['content'];
      $notes[$j]['comment']=$r1['comment'];
      $notes[$j]['ref_title']=$r1['ref_title'];
      $notes[$j]['ref_url']=$r1['ref_url'];
      $notes[$j]['tags']=$r1['tags'];
      
      $notes[$j]['created_at']=date("M d, Y", strtotime($r1['created_at']));
      $notes[$j]['updated_at']=date("M d, Y", strtotime($r1['updated_at']));
      $notes[$j]['updated_at2']=date("m/d/y", strtotime($r1['updated_at']));
      $notes[$j]['updated_at3']=date("c", strtotime($r['updated_at']));

      $notes[$j]['username']=$r1['username'];
      //print_r($r1);
      
      if (array_key_exists($r1['seccode'],$secid)) {
      	$ii=$secid[$r1['seccode']];
        $sections['sections'][$ii]['notes'][]=$notes[$j];
      } else
      {
      	$sections['sections'][$i]['code']=$r1['seccode'];
      	// sacar el nombre de la secction...
	      $sections['sections'][$i]['name']='';	      
	      $sections['sections'][$i]['pos']=0;
	      $sections['sections'][$i]['notes'][]=$notes[$j];
	      $secid[$r1['seccode']]=$i++;
      }
      $j++;
  }
  
  $s = print_r($sections, TRUE);   
	//fwrite($fh, "sections=$s\n");
  echo json_encode($sections);
  exit();	
	
}


// search 
if ($keyword!='') {
	//fwrite ($fh,"key=$keyword\n");
	
  // abrir la base mysql
  $con=db_connect();
  
  //$q1="SELECT notes.*,sections.bookcode as sbook FROM books, sections, notes where `notes`.`seccode`=`sections`.`code` and `sections`.`bookcode`=`books`.`code` and  `notes`.`content` like '%".$keyword."%' ORDER BY  `notes`.`updated_at` DESC;";
  
  $q1="SELECT notes.*,sections.bookcode as sbook FROM books, sections, notes where `notes`.`seccode`=`sections`.`code` and `sections`.`bookcode`=`books`.`code` and  ( `notes`.`content` like '%".$keyword."%' OR  `notes`.`tags` LIKE  '%".$keyword."%' ) ORDER BY  `notes`.`updated_at` DESC;";
  
  //$q1="SELECT *  FROM  `notes`  WHERE  `content` LIKE  '%".$keyword."%' ORDER BY  `updated_at` DESC;";
  
	$result1 = mysql_query($q1) or die(mysql_error());
	
	//fwrite ($fh,"q1=$q1\n"); 	
	
	$i=0;$j=0;$sections=array();$notes=array();$secid=array();
  while($r1 = mysql_fetch_array($result1)){
	
	$s = print_r($r1, TRUE);   
	//fwrite($fh, "$j r1=$s\n");
	
	// meterlas en section ficticia? o traer cada section de cada notas agrupadas...
	
	// hay que traerlas cada uno con su seccion... 
	// manteniendo el array de seccode
	
	    $notes[$j]['code']=$r1['code'];
      $notes[$j]['name']=$r1['name'];
      $notes[$j]['pos']=$r1['pos'];
      $notes[$j]['secCode']=$r1['seccode'];
      $notes[$j]['bookCode']=$r1['sbook'];
      $notes[$j]['content']=$r1['content'];
      $notes[$j]['comment']=$r1['comment'];
      $notes[$j]['ref_title']=$r1['ref_title'];
      $notes[$j]['ref_url']=$r1['ref_url'];
      $notes[$j]['tags']=$r1['tags'];
      
      $notes[$j]['created_at']=date("M d, Y", strtotime($r1['created_at']));
      $notes[$j]['updated_at']=date("M d, Y", strtotime($r1['updated_at']));
      $notes[$j]['updated_at2']=date("m/d/y", strtotime($r1['updated_at']));
      $notes[$j]['updated_at3']=date("c", strtotime($r['updated_at']));

      $notes[$j]['username']=$r1['username'];
      
      //print_r($r1);
      
      if (array_key_exists($r1['seccode'],$secid)) {
      	$ii=$secid[$r1['seccode']];
        $sections['sections'][$ii]['notes'][]=$notes[$j];
      } else
      {
      	$sections['sections'][$i]['code']=$r1['seccode'];
      	// sacar el nombre de la secction...
	      $sections['sections'][$i]['name']='';	      
	      $sections['sections'][$i]['pos']=0;
	      $sections['sections'][$i]['notes'][]=$notes[$j];
	      $secid[$r1['seccode']]=$i++;
      }
      $j++;
  }
  
  /*
  $sections['sections'][0]['code']=$notes[0]['secCode'];
	$sections['sections'][0]['name']='';
	$sections['sections'][0]['pos']=0;
  $sections['sections'][0]['notes']=$notes;
  */
  
  $s = print_r($sections, TRUE);   
	//fwrite($fh, "sections=$s\n");
	
  echo json_encode($sections);
  exit();	
}


$book=$_GET['bc'];
// abrir la base mysql
$con=db_connect();


$sections=array(); $notes=array();

//$q="SELECT *  FROM `sections` WHERE bookcode=\"$book\" ORDER BY `pos` , `name`;";
$q="SELECT *  FROM `sections` WHERE bookcode=\"$book\" ORDER BY `name`;";
$result = mysql_query($q) or die(mysql_error());

$i=0;$j=0;
while($r = mysql_fetch_array($result)){
	$sections['sections'][$i]['code']=$r['code'];
	$sections['sections'][$i]['name']=$r['name'];
	$sections['sections'][$i]['pos']=$r['pos'];
	//print_r($sections);
  $q1="SELECT * FROM `notes` WHERE seccode=\"".$r['code']."\" ORDER BY  `pos` ;";
  $result1 = mysql_query($q1) or die(mysql_error());
  while($r1 = mysql_fetch_array($result1)){
  	
  	//echo "<br><br>\nr1=";print_r($r1);echo "<br><br>\n";
  	
      $notes[$j]['code']=$r1['code'];
      $notes[$j]['name']=$r1['name'];
      $notes[$j]['pos']=$r1['pos'];
      $notes[$j]['secCode']=$r1['seccode'];
      $notes[$j]['content']=$r1['content'];
      $notes[$j]['comment']=$r1['comment'];
      $notes[$j]['ref_title']=$r1['ref_title'];
      $notes[$j]['ref_url']=$r1['ref_url'];
      $notes[$j]['tags']=$r1['tags'];
      
      $notes[$j]['created_at']=date("M d, Y", strtotime($r1['created_at']));
      $notes[$j]['updated_at']=date("M d, Y", strtotime($r1['updated_at']));
      $notes[$j]['updated_at2']=date("m/d/y", strtotime($r1['updated_at']));
      $notes[$j]['updated_at3']=date("c", strtotime($r['updated_at']));
      $notes[$j]['username']=$username;
     //print_r($r1);
      $j++;
  }
  //echo "<br><br>\n";print_r($notes);echo "<br><br>\n";
  $sections['sections'][$i]['notes']=$notes; $notes=array(); $j=0;
  $i++;
}
//echo "<br><br>\n";
echo json_encode($sections);

//fclose($fh);

?>
