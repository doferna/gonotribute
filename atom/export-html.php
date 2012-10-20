<?php

	  // export en atom este book:
	  
    $bc=$_GET['bc'];
    
    // http://php.net/manual/en/book.simplexml.php
    // http://www.php.net/manual/en/simplexml.examples-basic.php
    // http://stackoverflow.com/questions/3720408/atom-namespace-with-php-simplexml?rq=1
    // echo $xml -> asXML();
    
    
    include '../include/dbfunctions.php';
    include '../include/check_login.php';
    
    if (!isset($_SESSION["username"])) exit ();
    $username=$_SESSION["username"];
        
    $con=db_connect();

$section=array();$note=array();

$q="SELECT *  FROM `books` WHERE code=\"$bc\";";
$result = mysql_query($q) or die(mysql_error());
if ($r = mysql_fetch_array($result)){
    $title = $r['name'];
    $author = $username;
    $email = $username;
    $updated=date('c', strtotime($r['updated_at']));
    }
 else exit();
    
    
echo "<html><head>\n
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\n
    <link type=\"text/css\" rel=\"stylesheet\" href=\"../themes/show.css\">\n
    <title>$title</title></head>\n
<body bgcolor=\"#FFFFFF\" onload=\"\">\n
    <div id=\"pubContent\">\n";

 echo "<div style=\"font-size: 24px; font-weight: bold; margin: 0 0 10px 0;\">$title</div>\n\n
       <div id=\"pubHeader\"><div id=\"pubHeaderSub\">Last edited $updated<br></div></div>\n\n";


$i=-1;
$q="SELECT *  FROM `sections` WHERE bookcode=\"$bc\" ORDER BY `name` ;";
$result = mysql_query($q) or die(mysql_error());
    
while($r = mysql_fetch_array($result)){
	
	$seccode = $r['code'];
	$title = $r['name'];
  $updated=date('c', strtotime($r['updated_at']));
  $basexml -> entry[$i]-> updated  = $updated;
  
  if ($title != '') {
  	echo "<div class=\"pubSectionHeader\"><font size=\"-0\">$title</font></div><br>\n\n";
  }
	
	$q1="SELECT * FROM `notes` WHERE seccode=\"".$r['code']."\" ORDER BY  `pos` ;";
  $result1 = mysql_query($q1) or die(mysql_error());
  while($r1 = mysql_fetch_array($result1)){
  
	   $code = $r1['code'];
		 $title = $r1['name'];
 		 $content =$r1['content'];
     $updated=date('c', strtotime($r1['updated_at']));
     
    echo "<div class=\"PubNote\"><div class=\"PubNoteContentArea\">\n
     <div style=\"margin-top:0px;margin-right:0px;margin-bottom:0px;margin-left:0px;background-color:rgb(255,255,255)\">\n
     $content\n\n
     </div></div></div>\n\n";
     
     // $r1['comment'];$r1['ref_title'];$r1['ref_url'];
    }
  }

echo "</div></body></html>";      
      
?>