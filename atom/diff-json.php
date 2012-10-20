<?php

// https://simperium.com/docs/reference/http/
// api-key = af9a15a62f1444a7b31c1426644bb796
// 

//$fh = fopen("/tmp/notes.txt", 'w') or die("can't open file");
//$s = print_r($_GET, TRUE);fwrite($fh, $s);


//include '../simperiumapi.php';

include '../include/dbfunctions.php';

include 'htmldiff-htmldiff/html_diff.php'; 
include 'nuxodin-dmp/diff_match_patch.php';

$book=$_GET['bc'];
//$book="654e9e683adb98bb901973559423ddd1";
//$book="f9526d95007a1e1a9acb14adcb6187af";

// abrir la base mysql
$con=db_connect();

$sections=array(); $notes=array();

$q="SELECT *  FROM `json` WHERE id=\"$book\" ORDER BY `v`;";
$result = mysql_query($q) or die(mysql_error());

$i=0;$j=0;
while($r = mysql_fetch_array($result)){

  // v=1-5
  $json[$r['v']]=$r['json'];
  
  // convertir secciones y notes a asociative array para poder comparar...
  // loopear secciones y loopear notes y guardar el content
  // las comillas dentro del content joden...
  
  $js=json_decode($r['json'],true);
  
  //var_dump($js);
  //$cn[$i]=$js['sections'][2]['notes'][0]['content'] ;
  
  for ($j=0;$j<count($js['sections']);$j++) 
      for ($k=0;$k<count($js['sections'][$j]['notes']);$k++) {
           //echo "s=".$js['sections'][$j]['notes'][$k]['content']."\n";
           $cn[$js['sections'][$j]['notes'][$k]['code']][$i]=$js['sections'][$j]['notes'][$k]['content'];
           $cnup[$js['sections'][$j]['notes'][$k]['code']][$i]=$js['sections'][$j]['notes'][$k]['updated_at2'];
           $cn1[$js['sections'][$j]['notes'][$k]['code']]=1;
          }
 $i++;           
}

//print_r($cn);echo "<hr /><br /> \n\n";
// para cada code, comparar de version 0 hasta i o de las n ultimas
// en realidad hay que comparar toda el book 

echo "<div style=\"margin: 10px 25px 15px 10px\">\n";

foreach ($cn1 as $code => $value) {
	//echo "<br />\n<b>code=$code</b><br />\n\n";
	//echo "<br /><hr>\n";
	$notechanged=0;
  for ($j=1;$j<$i;$j++) {

//  echo"$code j=$j ".$cn[$code][$j-1]."\n";
//echo html_diff($cn[0],$cn[2],false);


 $dmp = new diff_match_patch();
 
 $c1=strip_tags($cn[$code][$j-1]);
 $c2=strip_tags($cn[$code][$j]);
 $c1=preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $c1);
 $c2=preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $c2);
 
 $d = $dmp->diff_main($c1,$c2);
 //print_r($d); 
 
 $diff=$dmp -> diff_prettyHtml($d); 
 if ($diff!='') {
 	  if (!$notechanged) { 
 	  	$notechanged=1; 
 	  	echo "<div class=\"m\" style=\"font-size: 83%; font-family: arial,sans-serif\"><hr>";
 	  }
    echo "<b>v$j ".$cnup[$code][$j] ."</b><br \>\n".$diff."<br \>\n";
  }
}
if ($notechanged) echo "</div>\n";

}

echo "</div>\n";
// comparar una nota en cada version... 
// hacer algun arreglo para extraer note[id][version] y compararlas
// $js['sections'][2]['notes'][0]['code']
//print_r($json);
//$body=json_encode($sections);

?>
