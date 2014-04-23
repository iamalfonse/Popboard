<?php

include("config.php");
//$dblink = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

if (isset( $_COOKIE['login_cookie'] )) { 
	$have_user_id = true; 
	$user_id = mysqli_real_escape_string($dblink, ucfirst($_COOKIE['login_cookie']));   
	$username = mysqli_real_escape_string($dblink, strtolower($_COOKIE['login_cookie']));
	$session_hash = mysqli_real_escape_string($dblink, $_COOKIE['hsh']);

	/* Open connection to the database */
	$q   = "SELECT user_id, email, session_hash FROM users WHERE username='$username'";
	$r  = mysqli_query($dblink, $q);
	$Rows = mysqli_fetch_assoc($r);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="/stylesheets/style1.css" rel="stylesheet" type="text/css" />
<link href='http://fonts.googleapis.com/css?family=Brawler' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
<script type="text/javascript" src="/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="/js/cleartext.js"></script>

<title>Blog Wall</title>
</head>
<body class='posts'>

<div id="container">
	<div id="top">
    	<div id="logomenu">
        	<h1><a href="postblog.php">Logo</a></h1>
    		<div class="tab">
				<p><a href="postblog.php">Blog Wall</a></p>
			</div><!--.tab-->
        </div><!--#logomenu-->
    </div><!--#top-->
	<div id="bottom">

		<?php include("left.php"); ?>
	
	    <div id="right">
	    	<div id="XP">
	    		<?
				
				//get number of likes by the user
				$SQLString2 = "SELECT post_user_id FROM likes WHERE post_user_id='{$Rows['user_id']}';";
				$QueryResult = @mysqli_query($dblink, $SQLString2);
				//$Row = mysqli_fetch_assoc($QueryResult);
				$num_rows = mysqli_num_rows($QueryResult);

	    		calculateXP($num_rows);
				//calculateXP(40);
	    		?>
	    	</div>
		
<?php

		/* This will tell us to display an error message */
		$have_error = false;
		$errmsg     = "";

		if (isset($_POST['submitpost'])) { //============= IF USER SUBMITS BLOG POST ===============================

		        /* Open connection to the database */
		        //$dblink = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

		        /* Process the new user registration */
		    	$blogtitle  = mysqli_real_escape_string($dblink, $_POST['blogtitle']);
				$blogmessage = mysqli_real_escape_string($dblink, $_POST['blogmessage']);	

		        if (!$dblink) {
		            $errmsg = "*** Failure!  Unable to connect to database!" . mysqli_connect_errno(); 
		            $have_error = true;

		        } else {
		            /* Connection is okay... go ahead and try to add blog to db. */
		            
		                /* Insert new row... */
						$username = strtolower($_COOKIE['login_cookie']);
						$query2    = "SELECT user_id, session_hash FROM users WHERE username='$username'";
			            $qresult2  = mysqli_query($dblink, $query2);
						$Rows2 = mysqli_fetch_assoc($qresult2);
						
						$post_date = date("M d, Y");
						
						//make sure it's the correct user posting it by checking their session_hash (security check)
						if($session_hash == $Rows2['session_hash']){
							//Post user id and url to db
			                $q2 = "INSERT INTO posts(user_id, blog_title, blog_message, post_date) VALUES('$Rows2[user_id]','$blogtitle', '$blogmessage', '$post_date')";
			                $r2 = mysqli_query($dblink, $q2);
						}
						

		                $SQLString = "SELECT username, blog_title, blog_message, user_id, id, post_date FROM users INNER JOIN posts USING (user_id) ORDER BY id DESC LIMIT 0, 3";
						$QueryResult = @mysqli_query($dblink, $SQLString);
						$Row = mysqli_fetch_assoc($QueryResult);
						$num_rows = mysqli_num_rows($QueryResult);

		                //output posts
						get_posts($Rows,$Row, $QueryResult);
		            

		            /* Close our connection to the database */
		            //mysqli_close($dblink);

		  		} /* end od dblink connect */
		}else if(isset($_POST['editsubmitblog'])){  //============= IF USER EDITS BLOG POST =================================
			
			
	        /* Open connection to the database */
	        $dblink = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

	        $username = strtolower($_COOKIE['login_cookie']);
			$query2    = "SELECT session_hash FROM users WHERE username='$username'";
            $qresult2  = mysqli_query($dblink, $query2);
			$Rows2 = mysqli_fetch_assoc($qresult2);

	        $blogtitle  = mysqli_real_escape_string($dblink, $_POST['blogtitle']);
			$blogmessage = mysqli_real_escape_string($dblink, $_POST['blogmessage']);
			
			$date = date_create();
			$updated_date = date_format($date, 'Y-m-d H:i:s');
			
			//grabbed from hidden input from editpost.php
			$hiddenpost_id = mysqli_real_escape_string($dblink, $_POST['hiddenpost_id']);


			//make sure it's the correct user posting it by checking their session_hash (security check)
			if($session_hash == $Rows2['session_hash']){
				//Post user id and url to db
				//----------UPDATES Post title, message and updates date
	            $q1 = "UPDATE posts SET blog_title='$blogtitle', blog_message='$blogmessage', post_updated='$updated_date' WHERE id = '$hiddenpost_id'" ; //VALUES('$blogtitle', '$blogmessage', '$post_date')";
	            $r1 = mysqli_query($dblink, $q1);
	        }else {
	        	//echo "<h1>Didn't Update</h1>";
	        }


			$SQLString = "SELECT username, blog_title, blog_message, user_id, id, post_date FROM users INNER JOIN posts USING (user_id) ORDER BY id DESC LIMIT 0, 3";
			$QueryResult = @mysqli_query($dblink, $SQLString);
			$Row = mysqli_fetch_assoc($QueryResult);
			$num_rows = mysqli_num_rows($QueryResult);

            //output posts
			get_posts($Rows,$Row, $QueryResult);



		}else { //=========================== IF IN BLOG WALL ================================================
			
			$dblink = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
			
			$SQLString = "SELECT username, blog_title, blog_message, user_id, id, post_date FROM users INNER JOIN posts USING (user_id) ORDER BY id DESC LIMIT 0, 3";
			$QueryResult = @mysqli_query($dblink, $SQLString);
			$Row = mysqli_fetch_assoc($QueryResult);
			$num_rows = mysqli_num_rows($QueryResult);
			
			if($num_rows == 0){
				//no posts
			}else {
				
				//get_posts() from config.php
				get_posts($Rows, $Row, $QueryResult);
				
			}
		}

		//------Show errors if any-------//
		if($have_error == true){
			echo $errmsg;
		}

	echo "</div><!-- #right -->";


}else { /*================================= ELSE SHOW ALL THIS IF NOT LOGGED IN ===================================*/				
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="stylesheets/style1.css" rel="stylesheet" type="text/css" />
<link href='http://fonts.googleapis.com/css?family=Brawler' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
<script src="js/cleartext.js" type="text/javascript"></script>
<title>Popboard | Share Your Stories</title>
</head>
<body class="posts">

<div id="container">

	<div id="top">
    	<div id="logomenu">
        	<h1><a href="index.php">Logo</a></h1>
            <!--<form id="signinform" method="post" action="index.php">
            	<input class="userinput" type="text" name="username" value="Username" onFocus="clearText(this)" onBlur="clearText(this)" />
                <input class="userinput" type="password" name="passwd" />
                <input type="submit" name="login" value="Sign In" />
					
            </form>-->
        </div><!--#logomenu-->
    </div><!--#top-->
	
	
    <div id="bottom">
    	<div class='button1'><p><a href='index.php'>Back to homepage</a></p></div><!--.button1-->
        <h3>Blog Wall</h3>
		<hr />

<?php 
	
		$dblink = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
		$TableName = "posts";
		$SQLString = "SELECT username, blog_title, blog_message, user_id, id, post_date FROM users INNER JOIN posts USING (user_id) ORDER BY id DESC LIMIT 0, 3";
		$QueryResult = @mysqli_query($dblink, $SQLString);
		$Row = mysqli_fetch_assoc($QueryResult);
		$num_rows = mysqli_num_rows($QueryResult);
		
		
		
		if($num_rows == 0){
			//don't show anything
		}else {
				
			get_posts($Rows, $Row, $QueryResult);
		
		}//End if($num_rows == 0)

		
}
?>	<p class='load-more'>Load More</p>
	</div><!--#bottom-->
</div><!--#container-->
</body>
</html>