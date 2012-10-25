<?php
/*


*/
    
    include '../include/dbfunctions.php';
    include '../include/check_login.php';
    
    $uploaddir = '/tmp/';
    $uploadfile = $uploaddir . basename($_FILES['atomfile']['name']);
    if (!move_uploaded_file($_FILES['atomfile']['tmp_name'], $uploadfile))  exit (); 
  
    
    $feedURL = 'migracion.xml';
    $feedURL = 'asterisk.xml';
    $feedURL = 'xxx.xml';
    $feedURL = $uploadfile;
    
    if (!isset($_SESSION["username"])) exit ();
    $username=$_SESSION["username"];
    
    // read feed into SimpleXML object
    $sxml = simplexml_load_file($feedURL);
    
    //$fh = fopen("/tmp/atom.txt", 'w') or die("can't open file");
    
    $t=(array) $sxml->title;
    $book['title']=$t[0];
    $t=(array) $sxml->updated;
    $book['updated']=$t[0];
    
    // array get_object_vars ( object $object )
    // print_r($book);
    
  // create book in database
    $book=array( 
        'code' => md5(uniqid (rand(), true)).date("Hmis"),
        'short'=> md5(uniqid (rand(), true)).date("Hmis"),
        'name' => $book['title'],
        "is_published"  => 0,
        "status"  => 0,
        "created_at" => $book['updated'],
        "updated_at" => $book['updated']
                                              
    );
	
	$con = db_connect();
	
	$str="INSERT INTO  books (  code, short, name, is_published, status, created_at, updated_at, username) 
		VALUES ('%s','%s','%s',%s,%s,'%s','%s','%s');";
	$query = sprintf($str, $book["code"], $book["short"], $book["name"], $book["is_published"], $book["status"], $book["created_at"], $book["updated_at"], $username);
  //fwrite($fh, "$query\n");
  mysql_query($query);

	
    // echo "tot= $total\n"; 
    // iterate over entries in category
    // print each entry's details
    $section=array();
    $ni=1;$si=1;
    foreach ($sxml->entry as $entry) {
      
      $entry = (array) $entry;
      $id= $entry['id'];
      $title = $entry['title'];
      $content = $entry['content'];
      //$published = $entry->published;
      $updated = $entry['updated'];
      // category puede ser un object or array de objects
      $category= (array) $entry['category'];
      //echo "category=";print_r($category);
      //print_r($entry);
      if (is_object($category[0])) {
      	// esto siempre es una nota
      	 $cs=array();$cs[0] = get_object_vars($category[0]); $cs[1]=get_object_vars($category[1]); 
      	 if ($cs[1]['@attributes']['term']!="http://schemas.google.com/notebook/2008/kind#note") {
      	 $scheme=$cs[1]['@attributes']['scheme'];
      	 $term=$cs[1]['@attributes']['term'];
      	 $label=$cs[1]['@attributes']['term'];
      	} else {
      	 $scheme=$cs[0]['@attributes']['scheme'];
      	 $term=$cs[0]['@attributes']['term'];
      	 $label=$cs[0]['@attributes']['term'];
      }
      	 //echo "nota= $id $term \nseccode= ".$section[$term]['code']."-".$section[$term]['title']."\n";
         //echo "$title\n\n";
         
         // insert note
				$c=htmlspecialchars_decode($content);
				$c=mysql_real_escape_string($c,$con);
				$ncode=md5(uniqid (rand(), true)).date("Hmis");
				$str="INSERT INTO notes (code, seccode, updated_at, created_at, content, pos) VALUES ('%s','%s','%s','%s','%s',%s)	;";
				$query = sprintf($str, $ncode,$section[$term]["code"],$updated, $updated, $c, $ni++);
  			//fwrite ($fh,"$query\n");
				mysql_query($query);

      	 
      	 }
      elseif (is_array($category['@attributes'])) {
      	// esto es siempre una section 
      	$cs =$category['@attributes'];
      	$scheme=$cs['scheme'];
      	$term=$cs['term'];
      	$label=$cs['label'];
      	$section[$id]['code']=md5(uniqid (rand(), true));
      	$section[$id]['title']=$title; 
      	//echo "--seccode= $id ".$section[$id]['code']."\n";
      	// echo "section= $id $term\n";
      	// echo "content= ".substr($content,1,40)."\n";
      	// echo "title=$title\n\n";
      	// insert section 
      	$str="INSERT INTO  sections (code, bookcode, name, pos) VALUES ('%s','%s','%s',%s);";
				$query = sprintf($str, $section[$id]["code"],$book["code"], $section[$id]["title"], $si++);
  			//fwrite($fh, "$query\n");
		    mysql_query($query);

        
              }
        else {
        echo "guacho= $id $term $scheme\n";    	
       }
      
    } //foreach
    
  //fclose($fh);
	mysql_close($con);
	
 header("Location: ../index.php");
  
?>
