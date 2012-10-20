<?php

function isAjax(){
   return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
   strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}


  /* set the cache limiter to 'private' */
    session_cache_limiter('private');
    $cache_limiter = session_cache_limiter();
    /* set the cache expire to 43200 minutes */
    session_cache_expire(43200);
    $cache_expire = session_cache_expire();
    /*
Debian sets up a <crontab /etc/cron.d/php5> which deletes all files, 
including those in subdirectories, which exceed the gc_maxlifetime 
specified in the <php.ini> file only.

That is, on Debian (and likely variants like Ubuntu) modifying the session 
expiration settings (like gc_maxlifetime) does *NOTHING*.  You *HAVE* to 
modify the global <php.ini>.  Not even a <.htaccess> file will help you.
*/


session_start();
//print_r($_SESSION);

if(!isset($_SESSION["username"])) {
	
	if(isAjax()) {
		echo "<script type='text/javascript' language='JavaScript'> window.location='login.php'; </script>";
	} else {
		header("Location: login.php");
		exit();
	}
	header("Location: login.php");
	//exit();
	return;
}


/*

// http://stackoverflow.com/questions/520237/how-do-i-expire-a-php-session-after-30-minutes

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    // last request was more than 30 minates ago
    session_unset();     // unset $_SESSION variable for the runtime 
    session_destroy();   // destroy session data in storage
}
$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp

if (!isset($_SESSION['CREATED'])) {
    $_SESSION['CREATED'] = time();
} else if (time() - $_SESSION['CREATED'] > 1800) {
    // session started more than 30 minates ago
    session_regenerate_id(true);    // change session ID for the current session an invalidate old session ID
    $_SESSION['CREATED'] = time();  // update creation time
}

http://prajapatinilesh.wordpress.com/2009/01/14/manually-set-php-session-timeout-php-session/

*/
?>
