<?php
if (isset( $_COOKIE['login_cookie'] )) { 
	//DATABASE PARAMS
	include("config.php");
	$dblink = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

	$have_user_id = true; 
	$user_id = mysqli_real_escape_string($dblink, strtolower($_COOKIE['login_cookie']));   
	
	$query    = "SELECT * FROM users WHERE username='$user_id'";
    $results  = mysqli_query($dblink, $query);
	$Row = mysqli_fetch_assoc($results);
	
	//--------------------------------------	
	$have_error    = false;
	$have_gravatar = false;
	$login_success = false;
	$errmsg        = "";
	
	//if user hits submit on the gravatar form---------------------------------
	if (isset($_POST['submitUserInfo'])) {
		
		/* Now we want to use empty() instead of just isset() */
		if (!empty($_POST['gravatar'])) {
		    $have_gravatar = true;
		    //$form_gravatar = $_POST['gravatar'];
		}
		
		if ($have_gravatar) {
			

			/* Connect to the database and try to find 
			 * a match for the username and password. 
			 */
			$gravatarValue = mysqli_real_escape_string($dblink, $_POST['gravatar']);
			

			 $dblink = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	         if (!$dblink) {
	             $errmsg = "*** Failure!  Unable to connect to database!" . mysqli_connect_errno(); 
	             $have_error = true;
	             echo $errmsg;
	         } else {
	            /* Connection is okay... go ahead and try to add decision to db. */

	            //get email value to cross check who the user is
	            $email = $Row['email'];

	            if($gravatarValue == 'gravatar'){//if user wants to use a gravatar image
	            	$query2 = "UPDATE users SET gravatar='1' WHERE email='".$email."';";
	            	$results2  = mysqli_query($dblink, $query2);
	            	//print_r $results;
	            }else if($gravatarValue == 'normal'){
	            	$query2 = "UPDATE users SET gravatar='0' WHERE email='".$email."';";
	            	$results2  = mysqli_query($dblink, $query2);
	            }else {
	            	echo "Oh no! Something went wrong. Please refresh the page and try again.";
	            }
	            //close db link
	            mysqli_close($dblink);
			 }
		} else {
			
			$have_error = true;
			$errmsg = "Please choose an option for your user image!";
		}//end gravatar update
	}



	//if user hits submit on the profile form---------------------------------
	if (isset($_POST['firstname']) || isset($_POST['lastname']) || isset($_POST['bio'])) {
		
		/* Connect to the database and try to find 
		 * the user
		 */
		$dblink = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

		$firstnameValue = mysqli_real_escape_string($dblink, $_POST['firstname']);
		$lastnameValue = mysqli_real_escape_string($dblink, $_POST['lastname']);
		$bioValue = mysqli_real_escape_string($dblink, $_POST['bio']);

			
			if (!$dblink) {
			 $errmsg = "*** Failure!  Unable to connect to database!" . mysqli_connect_errno(); 
			 $have_error = true;
			 echo $errmsg;
			} else {
			/* Connection is okay... go ahead and try to add decision to db. */

			//get email value to cross check who the user is
			$email = $Row['email'];

			$query2 = "UPDATE users SET firstname='$firstnameValue', lastname='$lastnameValue', bio='$bioValue' WHERE email='".$email."';";
			$results2  = mysqli_query($dblink, $query2);

			//close db link
			mysqli_close($dblink);

			header('Location: /profile/'.$user_id);
			}
		
	}//end profile update
	
	
	
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<link href="/stylesheets/style1.css" rel="stylesheet" type="text/css" />
<link href='http://fonts.googleapis.com/css?family=Brawler' rel='stylesheet' type='text/css'>
<title>My Profile</title>


</head>
<body>

<div id="container">
	
	<div id="top">
    	<div id="logomenu">
        	<h1><a href="/">Logo</a></h1>
			<div class="tab">
				<p><a href="/posts">Blog Wall</a></p>
			</div><!--.tab-->
			
        </div><!--#logomenu-->
		
    </div><!--#top-->
    <div id="bottom">
		<?php include("left.php"); ?>
	
    	<div id="right">
    		<?
	    		$userprofile= mysqli_real_escape_string($dblink, $_GET['username']);
	    		if($userprofile && ($userprofile != $user_id )){ //if you're looking at someone else's profile
	    			//show other user's profile
	    			echo "<h1>$userprofile's Profile</h1>";
	    		}else{ //show your own profile
    		?>
    		<div class="right-content">
	        	<h3 class="sectiontitle">My Profile</h3>
	        	<div class="clear"></div>
	        </div><!--.right-content -->
        	<div id="myprofile">
        		<?php get_profile_pic($Row['email']); ?>
				
				<div class="userinfo">
					<p><strong>Username:</strong> <?= $Row['displayname'] ?></p>
					<p><strong>E-mail:</strong> <?= $Row['email'] ?></p>
					<p><strong>Joined on:</strong> <?= $Row['joindate']; ?></p>
					<form action="/profile" method="post">
						<div id="update-profile">
							<p class="profile"><strong>Profile</strong></p>
							<p>First Name</p>
							<input id="fname" class="userinput" type="text" name="firstname" value="<?= stripslashes($Row['firstname']); ?>"/>
							<p>Last Name</p>
							<input id="lname" class="userinput" type="text" name="lastname" value="<?= stripslashes($Row['lastname']); ?>"/><br />
							<p>Bio</p>
							<textarea id="profileinfo" class="userinput" name="bio"><?= stripslashes($Row['bio']); ?></textarea>
						</div>
						<div id="update-gravatar">
							<p class="user-image"><strong>User Image</strong></p>
							<?php 

								if($Row['gravatar'] == 1){
									//user has gravatar already set so check the radio button
									echo '<input type="radio" name="gravatar" value="gravatar" checked /> Use Gravatar Image<br />';
								}else {
									//user doesn't have gravatar set
									echo '<input type="radio" name="gravatar" value="gravatar" /> Use Gravatar Image<br />';
								}
							?>
							
							<input type="radio" name="gravatar" value="normal" /> None<br />
						</div>

							<input type="submit" name="submitUserInfo" class="submitbtn" value="Submit">
					</form>
				</div><!-- .userinfo -->
				
			</div><!--#myprofile-->

			<?
				}

			?>
    	</div><!--#right-->
	</div><!-- #bottom -->

</div><!--#container-->

<?php
} else {//if not logged in
	//echo "<a href='index.php' />Go back to index</a>";
	header("Location: /"); 
}
	
	
?>