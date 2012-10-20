<?php
require_once("include/dbfunctions.php");
require_once("include/bcrypt.php");


require 'include/openid.php';
try {
    # Change to your domain name. see include/config.php
    $openid = new LightOpenID($CONFIG['url']);
    $openid->required = array('contact/email');
    if(!$openid->mode) {
        if(isset($_GET['login'])) {
            $openid->identity = 'https://www.google.com/accounts/o8/id';
            header('Location: ' . $openid->authUrl());
        }
    } elseif($openid->mode == 'cancel') {
        $login_error =  'User has canceled authentication!';
    } elseif($openid->validate()) {
    $openid_identity = $openid->identity;
    $data = $openid->getAttributes();
    $email = $data['contact/email'];
    $_POST['user']=$email;
    //$namePerson = $data['namePerson'];
    //$first = $data['namePerson/first'];
    //$last = $data['namePerson/last'];
} else {
    $login_error =  "The user has not logged in";
}
    
} catch(ErrorException $e) {
    echo $e->getMessage();
}

if (isset($_POST["user"])) {
  
	  $con = db_connect();
	  $q = "SELECT * FROM users WHERE login = '%s'"; 
	  $sql = sprintf($q,$_POST["user"]); 
	  $U = db_getRecord($con, $sql);
	  
	  if (($U["login"]=='') and ($email!='')) {
	  	$q = "INSERT INTO users (login,password) VALUES ('%s','%s');";
	  	$sql= sprintf($q,$email,$openid_identity);
	  	mysql_query($sql,$con) or die(mysql_error());
	  }
	  
	  if (($openid_identity!='') and ($email!='')) {
	  	 loginsession($email);
	  }
  
	//  bcrypt password
$hash  = BCrypt::hash($_POST['password']);

	if (($U["password"]!='') and ($U['password']==$hash)) {
	
	  loginsession($_POST['user']);
	  
	} else {
		$login_error = "The user or password is incorrect. Please try again.";
	}

} else {
	
	
}


function loginsession($username) {
				
	  /* set the cache limiter to 'private' */
    session_cache_limiter('private');
    $cache_limiter = session_cache_limiter();

    /* set the cache expire to 43200 minutes */
    session_cache_expire(43200);
    $cache_expire = session_cache_expire();

// http://stackoverflow.com/questions/8419332/proper-session-hijacking-prevention-in-php

/*
Debian sets up a <crontab /etc/cron.d/php5> which deletes all files, 
including those in subdirectories, which exceed the gc_maxlifetime 
specified in the <php.ini> file only.

That is, on Debian (and likely variants like Ubuntu) modifying the session 
expiration settings (like gc_maxlifetime) does *NOTHING*.  You *HAVE* to 
modify the global <php.ini>.  Not even a <.htaccess> file will help you.
*/

// **PREVENTING SESSION HIJACKING**
// Prevents javascript XSS attacks aimed to steal the session ID
ini_set('session.cookie_httponly', 1);
// Adds entropy into the randomization of the session ID, as PHP's random number  generator has some known flaws
ini_set('session.entropy_file', '/dev/urandom');
// Uses a strong hash
ini_set('session.hash_function', 'whirlpool');
// **PREVENTING SESSION FIXATION** Session ID cannot be passed through URLs
ini_set('session.use_only_cookies', 1);
// Uses a secure connection (HTTPS) if possible
//ini_set('session.cookie_secure', 1);

ini_set('session.cookie_lifetime', 2592000); 
ini_set('session.gc_maxlifetime', 2592000);
		session_start();
	//	$_SESSION["username"] = $username;

// If the user is already logged
if (isset($_SESSION['username'])) {
    // If the IP or the navigator doesn't match with the one stored in the session
    // there's probably a session hijacking going on
    if ($_SESSION['ip'] !== getIp() || $_SESSION['user_agent_id'] !== getUserAgentId()) {
        // Then it destroys the session
        session_unset();
        session_destroy();

        // Creates a new one
        session_regenerate_id(true); // Prevent's session fixation
        //session_id(sha1(uniqid(microtime()))); // Sets a random ID for the session
    }
} else {
      session_regenerate_id(true); // Prevent's session fixation
      //session_id(sha1(uniqid(microtime()))); // Sets a random ID for the session
    // Set the default values for the session
    //setSessionDefaults();
    $_SESSION['ip'] = getIp(); // Saves the user's IP
    $_SESSION['user_agent_id'] = getUserAgentId(); // Saves the user's navigator
    $_SESSION["username"] = $username;
}

		header("Location: index.php");
		return;

}


function getUserAgentId() {
	$b=$_SERVER['HTTP_USER_AGENT'];
	return md5($b);
	}
function getIp() {
	$b=$_SERVER['REMOTE_ADDR']." ".$_SERVER['HTTP_X_FORWARDED_FOR'];
	return md5($b);	
	}
?>


<html>
	<head>
		 <title></title>
	</head>
	
	<body>
		<div class="ui-widget stylized myform" align="center">
 
			<div class="ui-corner-all ui-widget-content" style="width: 300px;">
				<h2>Login</h2>
				<form method="post" action="" >
					
					<table>
						<tr>
							<td>User:</td>
							<td><input name="user" class="small" type="text" id="UserName"/></td>
						</tr>
						<tr>
							<td>Password:</td>
							<td><input name="password" class="small" type="password" id="UserPass"/>						
							</td>
						</tr>
					</table>
					
					<?php if($login_error): ?>
					
						<spam style="color:red;"><?= $login_error ?></spam>
					
					<?php endif; ?>
						
					
					<br>
					<div id="botones" class="noprint">
						<button id="btn_login" type="submit">Login</button>
					</div>
					<br />
				</form>
				<br />
				
				<form action="?login" method="post">
             <button>Login with Google</button>
</form>
			</div>
		
		</div>
	</body>
</html>
