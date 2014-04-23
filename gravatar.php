
<?php
include("config.php");

$have_error    = false;
$have_gravatar = false;
$login_success = false;
$errmsg        = "";


/* Check to see if the gravatar form has been submitted.  We'll test 
 * to see if the 'submit' name (ie. the submit button element) is
 * present in the POST superglobal array.
 */

//if user hits submit on the gravatar form
if (isset($_POST['gravatarupdate'])) {
	
	/* Now we want to use empty() instead of just isset() */
	if (!empty($_POST['gravatar'])) {
	    $have_gravatar = true;
	    $form_gravatar = $_POST['gravatar'];
	}
	
	if ($have_gravatar) {

		/* Connect to the database and try to find 
		 * a match for the username and password. 
		 */

		$gravatarValue = $_POST['gravatar'];
		

		 $dblink = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
         if (!$dblink) {
             $errmsg = "*** Failure!  Unable to connect to database!" . mysqli_connect_errno(); 
             $have_error = true;
             echo $errmsg;
         } else {
            /* Connection is okay... go ahead and try to add decision to db. */

            /* Try to find a row in the user's table with both a matching username and
             * encrypted password. If we have a match then that is a successful authentication.
             */
            //get user id to cross check who the user is
            if (isset( $_COOKIE['login_cookie'] )) { 
				$have_user_id = true;
				$user_name = $_COOKIE['login_cookie'];
				echo $user_name;
			}

            $query0 = "SELECT user_id FROM users WHERE username=";

            if($gravatarValue == 'gravatar'){//if user wants to use a gravatar mage
            	
            	$query = "INSERT INTO Users(gravatar) VALUES ('1') WHERE username=$user_name;";
            	$results  = mysqli_query($dblink, $query);
            }else if($gravatarValue == 'normal'){
            	$query = "INSERT INTO Users(gravatar) VALUES ('0') WHERE username=$user_name;";
            	$results  = mysqli_query($dblink, $query);
            }else {
            	echo "something went wrong";
            }

            //$query = "SELECT gravatar FROM users WHERE username='$form_username' ";
            //$results  = mysqli_query($dblink, $query);

            /* Getting the number of rows will tell us whether this was a success or not.
             * If number of rows = 0, then there were no matches, and therefore failed.
             * If we get back exactly one row, then the user gave us the right combination
             * of username and password.
             */
            //$num_rows = mysqli_num_rows($results);

            /* Close our connection to the database -- we're done with that now.*/
            mysqli_close($dblink);

            
		    
		 }
	} else {
		$have_error = true;
		$errmsg = "Please choose an option for your user image!";
	}
}

?>