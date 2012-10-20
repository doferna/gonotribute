<?php

require_once("config.php");

/**
 * Conecta con la base y retorna el id de conexión.
 */
function db_connect() {
	
	global $CONFIG;
	
	$con = mysql_connect($CONFIG["dbserver"],$CONFIG["dbusername"],$CONFIG["dbpassword"]);
	@mysql_select_db($CONFIG["database"],$con) or die( "Unable to select database");
	return $con;
}

/**
 * Ejecuta una consulta y retorna los resultados en un array.
 */
function db_getData($con, $sql) {
	$result = mysql_query($sql,$con) or die(mysql_error());
	$R = array();
	while($r = mysql_fetch_array($result)){
		$R[] = $r;
	}
	
	return $R;
}

/**
 * Ejecuta una consulta y retorna el primer registro.
 */
function db_getRecord($con, $sql) {
	
	$result = mysql_query($sql,$con) or die(mysql_error());
	$R = array();
	if($r = mysql_fetch_array($result)){
		return $r;
	} 
	
	return FALSE;
}


?>
