<?

include 'include/dbfunctions.php';
include 'include/check_login.php';

// abrir la base mysq
//mysql_connect($server,$username,$password);
//@mysql_select_db($database) or die( "Unable to select database");
db_connect();

if(!isset($_SESSION["username"])) exit ();

$u=$_SESSION["username"];
$q="SELECT *  FROM `books` WHERE  `username` = \"".$u."\" ORDER BY  `updated_at` DESC;";
$result = mysql_query($q) or die(mysql_error());

$i=0;$j=0;$book=array();
while($r = mysql_fetch_array($result)){
	
	if ($i==0) $currentbook=$r['code'];
	$book[$i]['code']=$r['code'];
	$book[$i]['short']=$r['short'];
	$book[$i]['name']=$r['name'];
	$book[$i]['is_published']=$r['is_published'];
	$book[$i]['is_published']=False;
	$book[$i]['status']=$r['status'];
	$book[$i]['created_at']=date("M d, Y", strtotime($r['created_at']));
	$book[$i]['updated_at']=date("M d, Y", strtotime($r['updated_at'])); // June 08, 2012
	$book[$i]['updated_at2']=date("m/d/y", strtotime($r['updated_at'])); // 06/08/12
	$book[$i]['updated_at3']=date("c", strtotime($r['updated_at'])); // 2012-06-07T12:12:12Z
	
	$i++;

// procesar tags de todas las notas de un libro y llevar arreglo asociativo con la cuenta. 
// los tags son de todos los libros...
	
}

$varbooks="    var _books = ".json_encode($book).";\n";
$varusername="var _username ='".$_SESSION["username"]."';\n";
$varcurrentbook="var _current_book_code = '".$currentbook."';\n";

?>

