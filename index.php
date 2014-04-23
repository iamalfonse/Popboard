<?php
/**
 * login.php
 *
 * Display a login form. Also process submission of login credentials.
 *
 **/

if (isset( $_COOKIE['login_cookie'] )) { 
		$have_user_id = true; 
		$user_id = ucfirst($_COOKIE['login_cookie']);   
		//echo $user_id;

		header('Location: /posts');
		exit;
}

/* Declare the parameters for accessing the database again. */
//DATABASE PARAMS
include("config.php");

//$dblink = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

$have_error    = false;
$have_username = false;
$have_passwd   = false;
$login_success = false;
$errmsg        = "";





/* Check to see if the login form has been submitted.  We'll test 
 * to see if the 'submit' name (ie. the submit button element) is
 * present in the POST superglobal array.
 */
if (isset($_POST['login'])) {
	
	/* Now we want to use empty() instead of just isset() */
	if (!empty($_POST['username'])) {
	    $have_username = true;
	    $form_username = mysqli_real_escape_string($dblink, strtolower($_POST['username']));
	}
	
	if (!empty($_POST['passwd'])) {
		$have_passwd = true;
		$form_passwd = mysqli_real_escape_string($dblink, $_POST['passwd']);
	}
	
	if ($have_username && $have_passwd) {
		/* Connect to the database and try to find 
		 * a match for the username and password. 
		 */
		
		 
         if (!$dblink) {
             $errmsg = "*** Failure!  Unable to connect to database!" . mysqli_connect_errno(); 
             $have_error = true;

         } else {
            /* Connection is okay... go ahead and try to add user to db. */

            // Sanitize inputs before doing mysql query
            $password = sha1($form_passwd);

            /* Try to find a row in the user's table with both a matching username and
             * encrypted password. If we have a match then that is a successful authentication.
             */

            $query = "SELECT user_id FROM users WHERE username='$form_username' AND passwd='$password' AND active='1'";

            $results  = mysqli_query($dblink, $query);

            /* Getting the number of rows will tell us whether this was a success or not.
             * If number of rows = 0, then there were no matches, and therefore failed.
             * If we get back exactly one row, then the user gave us the right combination
             * of username and password.
             */
            $num_rows = mysqli_num_rows($results);


            /* Close our connection to the database -- we're done with that now.*/
           // mysqli_close($dblink);

            if ($num_rows == '1') {
	            /* Successful login! */
	            $login_success = true;

	            /* add random hash to make sure that 
				 * the user has rights to delete, edit,add posts,etc.
				 * if this hash matches in the database.
				 */
				$randomHash = md5(rand(0,1000).$form_username);
				$query2 = "UPDATE users SET session_hash = '$randomHash' WHERE username = '$form_username'";
	            $results2  = mysqli_query($dblink, $query2);
	
				/* Set a COOKIE */
				$cookie_duration = 3600 * 24 * 365; // 1 year 
				$login_ref = $form_username;
				$retval = setcookie("login_cookie", $login_ref, time() + $cookie_duration);
				$retval2 = setcookie("hsh", $randomHash, time() + $cookie_duration);
			
				header("Location: /posts"); 
				exit;
	        } else {
		        $have_error = true;
		        $errmsg = "Incorrect username and/or password. Please try again.";
		    }
		    
		 }
	} else {
		$have_error = true;
		$errmsg = "Please provide both your username and password to login!";
	}
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="/stylesheets/style1.css" rel="stylesheet" type="text/css" />
<link href='http://fonts.googleapis.com/css?family=Brawler' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
<script src="/js/cleartext.js" type="text/javascript"></script>

<title>Popboard</title>

</head>
<body>

<div id="container">

	<div id="top">
		<div id="top-container">
	    	<div id="logomenu">
	        	<h1><a href="/">Popboard</a></h1>
				<!--<div class="tab">
					<p><a href="postblog.php">Blog Wall</a></p>
				</div>--><!--.tab-->
	            
	        </div><!--#logomenu-->
    	</div>
    </div><!--#top-->
	
    <div id="bottom">
    	<div id="home-banner">
    		<h2 class="welcome">Welcome to Popboard</h2>
    		<p class="welcome">Share your stories.</p>
    		<img src="/images/homebanner.jpg" alt="Welcome to Popboard - Share your stories"/>
    	</div>
        <div id="signupfront">
        	<?php
                if ($have_error) {
                    echo "<p class='error'>$errmsg</p>\n";
                }
            ?>
        	<form id="signinform" method="post" action="/">
	        	<p class="signupfront-username">Username <input class="userinput" type="text" name="username" placeholder="Username" onFocus="clearText(this)" onBlur="clearText(this)" /></p>
	            <p class="signupfront-password">Password <input class="userinput" type="password" name="passwd" placeholder="Password"/></p>
	            <p class="signupfront-submit"><input class="submitbtn" type="submit" name="login" value="Sign In" /></p>
	        	<p>If you would like to try Popboard, use this login info:</p>
				<p>Username: <strong>test</strong></p>
				<p>Password: <strong>test</strong></p>
	        </form>
	        <hr />
			<h3><span>New to Popboard?</span> Sign up for free!</h3>
			<p class="signup"><a href="/register">Sign Up for Popboard</a></p>

		</div>
	
    </div><!--#bottom-->
</div><!--#container-->







</body>
</html>