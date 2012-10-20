<?php
/*


*/

include '../include/dbfunctions.php';
include '../include/check_login.php';

//$fh = fopen("/tmp/ppp.txt", 'w') or die("can't open file");
//$s = print_r($_POST, TRUE);  fwrite($fh, $s);
  
if(!isset($_SESSION["username"])) exit ();  

// no se usa
if ($_SERVER['REQUEST_METHOD'] == "PUT") {
$p='';
$putdata = fopen("php://input", "r");
while ($data = fread($putdata, 1024)) {
  //fwrite($fh, $data);
  $p=$p.$data ;
}
  //fwrite($fh,"p = $p \n");
}

//
if ($_POST["delete"]=="yes") 
{
	$con = db_connect();

$query="DELETE FROM `books` WHERE `code`=\"".$_POST["code"]."\";";
mysql_query($query,$con);
	 //fwrite($fh,"delete ".$query);
	 $data=array('code' =>$_POST["code"],
	            'name' =>$_POST["name"]
	            );
	echo json_encode($data);
}
elseif ($_POST["createsection"]=="yes") 
{
	$con = db_connect();

$str="
		INSERT INTO  sections (code, bookcode, name, pos) 
		VALUES ('%s','%s','%s',%s);
	";

	$section=array( 
      'code' => md5($_POST["name"].date("Hmis")."hola"),
      'bc' => $_POST["bc"],
      'name'=> $_POST["name"],
      'pos' => 1 
       );

$query = sprintf($str, $section["code"],$section["bc"], $section["name"], $section["pos"]);

mysql_query($query,$con);
	 //fwrite($fh,"createsection ".$query);
	 $data=array('code' =>$section["code"],
	            'name' =>$section["name"]
	            );
	echo json_encode($data);
}

// cambia el nombre de un book con code y name
elseif(isset($_POST["name"]) and isset($_POST["code"]) ) {

	$con = db_connect();

$query="UPDATE `books` SET `name` = \"".$_POST["name"]."\" WHERE `code`=\"".$_POST["code"]."\";";
mysql_query($query,$con);
//fwrite($fh, $query);
	
	$data=array('code' =>$_POST["code"],
	            'name' =>$_POST["name"]
	            );
	echo json_encode($data);
}


// solo name crea un nuevo book
elseif(isset($_POST["name"]) and !isset($_POST["code"]) ) {
	
	$user=$_SESSION["username"];
	$data=array( 
        'code' => md5($_POST["name"].date("Hmis").uniqid (rand(), true)),
        'short'=> md5(uniqid (rand(), true)),
        'name' => $_POST["name"],
        "is_published"  => 0,
        "status"  => 0,
        "created_at" => "NOW()",
        "updated_at" => "NOW()",
        "username" => $user
                                              
    );
	
	$con = db_connect();
	
	$str="
		INSERT INTO  books (  code, short, name, is_published, status, created_at, updated_at, username) 
		VALUES ('%s','%s','%s',%s,%s,%s,%s,'%s')
	";
	$query = sprintf($str, $data["code"], $data["short"], $data["name"], $data["is_published"], $data["status"], $data["created_at"], $data["updated_at"], $data["username"]);
	mysql_query($query,$con);

	//print_r($query);
  //fwrite($fh, $query);

	$str="
		INSERT INTO  sections (code, bookcode, name, pos, created_at) 
		VALUES ('%s','%s','%s',%s, %s)
	";

	$section=array( 
      'code' => md5($_POST["name"].date("Hmis")."hola"),
      'name'=> "",
      'pos' => 1 
       );
        

	$query = sprintf($str, $section["code"],$data["code"], $section["name"], $section["pos"], "NOW()" );
  //fwrite($fh, $query); 
 // $fh = fopen("/tmp/ppp.txt", 'w') or die("can't open file");
 // $s = print_r($_POST, TRUE);
 // fwrite($fh, $s);
  mysql_query($query,$con);
	mysql_close($con);

  //fclose($fh);
	echo json_encode($data);
	
}
?>