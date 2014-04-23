<?php
// ========================= DEPRECATED =========================================//

/**
 * login.php
 *
 * Display a login form. Also process submission of login credentials.
 *
 **/

/* Declare the parameters for accessing the database again. */
//DATABASE PARAMS
include("config.php");

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
	    $form_username = mysql_real_escape_string($_POST['username']);
	}
	
	if (!empty($_POST['passwd'])) {
		$have_passwd = true;
		$form_passwd = mysql_real_escape_string($_POST['passwd']);
	}
	
	if ($have_username && $have_passwd) {
		/* Connect to the database and try to find 
		 * a match for the username and password. 
		 */
		
		 $dblink = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
         if (!$dblink) {
             $errmsg = "*** Failure!  Unable to connect to database!" . mysqli_connect_errno(); 
             $have_error = true;

         } else {
            /* Connection is okay... go ahead and try to add user to db. */

            /* Try to find a row in the user's table with both a matching username and
             * encrypted password. If we have a match then that is a successful authentication.
             */
            $query = "SELECT user_id FROM users WHERE username='$form_username' AND passwd=sha1('$form_passwd') AND active='1'";
            $results  = mysqli_query($dblink, $query);

            /* Getting the number of rows will tell us whether this was a success or not.
             * If number of rows = 0, then there were no matches, and therefore failed.
             * If we get back exactly one row, then the user gave us the right combination
             * of username and password.
             */
            $num_rows = mysqli_num_rows($results);

            /* Close our connection to the database -- we're done with that now.*/
            mysqli_close($dblink);

            if ($num_rows == 1) {
	            /* Successful login!  */
	            $login_success = true;
	
	
				/* Set a COOKIE */
				$cookie_duration = 3600 * 24 * 7; // 1 week 
				$login_ref = $form_username;
				$retval = setcookie("login_cookie", $login_ref, time() + $cookie_duration 
				);
			
	
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
<html>
  <head>
     <title>Login</title>
  </head>
 
  <body>
    <h1>Login</h1>

<?php
if ($have_error) {
	print("$errmsg\n");
	print("<br />\n");
}
?>
	
<?php
    if ($login_success ==  true) {
	
		/* Checks to see if cookie is set */
		if (isset( $_COOKIE['login_cookie'] )) { 
			$have_user_id = true; 
			$user_id = ucfirst($_COOKIE['login_cookie']);   
			
			echo $user_id;
		}
?>
      Login Successful!  Welcome back <?php echo $form_username; ?>
      <br />
      <a href="index.php">Click here for main page.</a>

<?php	
	} else {
?>
    <form action="login.php" method="post"> 
        Username : <input type="text" name="username" size="20" />
        <br /><br />
        Password : <input type="password" name="passwd"   size="20" />
        <br /><br />
        <input type="submit" name="login" value="Login" />
    </form>
    <p>Don't have an account? <a href="index.php">Register here</a> </p>
<?php
   } /* end if ($login_success) */
?>

  </body>

</html>