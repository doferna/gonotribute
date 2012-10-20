<?php

  // export en atom este book:
	  
    $bc='d262bbb06eafa6119f6cc88d287f8de3';
    $bc='1baa5982edc224e0bb40573c13eee751'; //varios asterisk
    
    $bc=$_GET['bc'];
    
    // http://php.net/manual/en/book.simplexml.php
    // http://www.php.net/manual/en/simplexml.examples-basic.php
    
    // http://stackoverflow.com/questions/3720408/atom-namespace-with-php-simplexml?rq=1
    // echo $xml -> asXML();
    
    include '../include/dbfunctions.php';
    include '../include/check_login.php';
    
    if (!isset($_SESSION["username"])) exit ();
    $username=$_SESSION["username"];

    //$fh = fopen("/tmp/atom.txt", 'w') or die("can't open file");
    
    $username="doferna";
    
    include "atom-base.php";
    include "SimpleDOM.php";
    
    $basexml = simpledom_load_string($xmlbase);
    $sec1xml = simpledom_load_string($xmlsection);
    $note1xml = simpledom_load_string($xmlnote);

//    $new_node= $basexml->appendChild($sec1xml->cloneNode(true));    
//    $new_node= $basexml->appendChild($note1xml->cloneNode(true)); 
    
//    $basexml -> entry[1] -> title = 'titulo1';
//    $basexml -> entry[1]-> category[0]->attributes()->term='termterm';
    
    
    
    // abrir la base mysql
    $con=db_connect();

$sections=array();$notes=array();

$q="SELECT *  FROM `books` WHERE code=\"$bc\";";
$result = mysql_query($q) or die(mysql_error());
while($r = mysql_fetch_array($result)){
    $basexml -> title = $r['name'];
    $basexml -> author -> name = $username;
    $basexml -> author -> email  = $username;
    $updated=date('c', strtotime($r['updated_at']));
    $basexml -> updated  = $updated;
}

// SELECT MAX(updated_at) AS updated_at FROM notes WHERE seccode="f41d8cd98f00b204e9800998ecf8427f";
// SELECT MAX(updated_at) AS updated_at FROM sections WHERE bookcode="f41d8cd98f00b204e9800998ecf8427f";

$i=-1;
$q="SELECT *  FROM `sections` WHERE bookcode=\"$bc\";";
$result = mysql_query($q) or die(mysql_error());


while($r = mysql_fetch_array($result)){
	
	$new_node= $basexml->appendChild($sec1xml->cloneNode(true)); $i++;
	$basexml -> entry[$i] -> id = $r['code'];
	$basexml -> entry[$i] -> title = $r['name'];
  $updated=date('c', strtotime($r['updated_at']));
  $basexml -> entry[$i]-> updated  = $updated;
	
	

  $q1="SELECT * FROM `notes` WHERE seccode=\"".$r['code']."\" ORDER BY  `pos` ;";
  $result1 = mysql_query($q1) or die(mysql_error());
  while($r1 = mysql_fetch_array($result1)){
  
     $new_node= $basexml->appendChild($note1xml->cloneNode(true)); $i++;
	   $basexml -> entry[$i] -> id = $r1['code'];
		 $basexml -> entry[$i] -> title = substr(strip_tags($r1['content']),0,50);
		 $basexml -> entry[$i]-> category[0]->attributes()->term=$r1['seccode'];
 		 $basexml -> entry[$i]-> content =$r1['content'];
     $updated=date('c', strtotime($r1['updated_at']));
     $basexml -> entry[$i]-> updated  = $updated;
     
     // $r1['comment'];$r1['ref_title'];$r1['ref_url'];
    }
  }

/*
header('Content-Type: text/xml');
header('Content-Disposition: attachment; filename='.$basexml->title.'".xml"'); 
header('Content-Transfer-Encoding: binary');
*/

$xml= $basexml -> asXML();
downloader($xml, $basexml->title.'.xml', 'application/xml');
mysql_close($con);



function downloader($data, $filename = true, $content = 'application/x-octet-stream')
   {
    // If headers have already been sent, there is no point for this function.
    if(headers_sent()) return false;
    // If $filename is set to true (or left as default), treat $data as a filepath.
    if($filename === true)
     {
      if(!file_exists($data)) return false;
      $data = file_get_contents($data);
     }
    if(strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") !== false)
     {
      header('Content-Disposition: attachment; filename="'.$filename.'"');
      header('Expires: 0');
      header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
      header('Content-Transfer-Encoding: binary');
      header('Content-Type: '.$content);
      header('Pragma: public');
      header('Content-Length: '.strlen($data));
     }
    else
     {
      header('Content-Disposition: attachment; filename="'.$filename.'"');
      header('Content-Transfer-Encoding: binary');
      header('Content-Type: '.$content);
      header('Expires: 0');
      header('Pragma: no-cache');
      header('Content-Length: '.strlen($data));
     }
    // Send file to browser, and terminate script to prevent corruption of data.
    exit($data);
   }
  
?>
