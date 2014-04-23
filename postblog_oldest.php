<?php

include("config.php");

if (isset( $_COOKIE['login_cookie'] )) { 
		$have_user_id = true; 
		$user_id = ucfirst($_COOKIE['login_cookie']);   


		//echo $user_id;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="stylesheets/style1.css" rel="stylesheet" type="text/css" />
<link href='http://fonts.googleapis.com/css?family=Brawler' rel='stylesheet' type='text/css'>
<title>Dashboard</title>
</head>
<body>

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
		<div id="left">
			<?php get_profile_pic(); ?>
			<p class="leftusername">Logged in as:<br/><?php echo $user_id; ?></p>
			<div class="clear"></div>
			<p class="leftbuttons"><a href="myprofile.php">My Profile</a>
			<p class="leftbuttons"><a href="dashboard.php">Dashboard</a></p>
			<p class="leftbuttons"><a href="mywall.php">My Wall</a></p>
			<p class="leftbuttons"><a id="logoutbtn" href="logout.php">Logout</a></p>
		</div><!--#left-->
	
	
	    <div id="right">
	        <h3 class="sectiontitle">Blog Wall</h3>
			<div id="sortby">
			<p>Sort by: </p><p class="sortbtn"><a href="postblog.php">Most Recent</a></p><p class="sortbtn" ><a href="postblog_oldest.php">Oldest</a></p>
			</div>
			<hr />
		
        
<?php
		//DATABASE PARAMS
		//include("config.php");


		/* This will tell us to display an error message */
		$have_error = false;
		$errmsg     = "";

		 if (isset($_POST['submitblog'])) {

		     /* Process the new user registration */
		     $blogtitle  = addslashes($_POST['blogtitle']);
			 $blogmessage = addslashes($_POST['blogmessage']);


		         /* Open connection to the database */
		         $dblink = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
		         if (!$dblink) {
		             $errmsg = "*** Failure!  Unable to connect to database!" . mysqli_connect_errno(); 
		             $have_error = true;

		         } else {
		            /* Connection is okay... go ahead and try to add blog to db. */

		             /* Does this title already exist? */
		             $query    = "SELECT blog_title FROM post WHERE blog_title='$blogtitle'";
		             $results  = mysqli_query($dblink, $query);
					 $Row0 = mysqli_fetch_assoc($results);
		             //$num_rows = mysqli_num_rows($results);

		             if ($blogtitle == $Row0['blog_title']) {
		                 /* This url is already posted */
		                 $have_error = true;
		                 $errmsg = "This title is already posted.  Please select another.";   
		             } else { 
		                 /* Insert new row... */
						 $username = $_COOKIE['login_cookie'];
						 $query2    = "SELECT user_id FROM users WHERE username='$username'";
			             $user_id  = mysqli_query($dblink, $query2);
						 $fetchid = mysqli_fetch_assoc($user_id);
						
						
						$post_date = date("M d, Y");
						
						//echo "WTF!!!!";
						//Post user id and url to db
		                 $q2 = "INSERT INTO post(user_id, blog_title, blog_message, post_date) VALUES('$fetchid[user_id]','$blogtitle', '$blogmessage', '$post_date')";
		                 $r2 = mysqli_query($dblink, $q2);




						$SQLString = "SELECT username, blog_title, blog_message, user_id, id, post_date FROM users INNER JOIN post USING (user_id) ORDER BY id";
						$QueryResult = @mysqli_query($dblink, $SQLString);
						$Row = mysqli_fetch_assoc($QueryResult);
						$num_rows = mysqli_num_rows($QueryResult);
							
						//echo $num_rows;
						//$SQLString = "SELECT * FROM post";
						//$QueryResult = @mysqli_query($dblink, $SQLString);
						//$Row = mysqli_fetch_assoc($QueryResult);
						
						if($num_rows == 0){
							
						}else {
							do {
								$SQLString2 = "SELECT id, username FROM users INNER JOIN post USING (user_id) WHERE id = '{$Row['id']}'";
								$QueryResult2 = @mysqli_query($dblink, $SQLString2);
								$Row2 = mysqli_fetch_assoc($QueryResult2);
								$postusername2 = ucfirst($Row2['username']);
								
								echo "<div class='postitem'>";
								echo "<div class='blogtop'><h3 class='blogtitle'><a href='commentblog.php?post_id={$Row['id']}'>{$Row['blog_title']}</a></h3>";
								echo "<p class='username'>Posted by: $postusername2 on {$Row['post_date']}</p></div>\n";
								echo "<div class='blogbottom'><p class='blogmessage'>{$Row['blog_message']}</p>\n";
								echo "<div class='button2'><p><a href='commentblog.php?post_id={$Row['id']}'>View Comments</a></p></div>\n</div>";
								echo "</div>";

								$Row = mysqli_fetch_assoc($QueryResult);
							} while ($Row);
						
						}//end of if($num_rows == 0){
		             }

		             /* Close our connection to the database */
		             mysqli_close($dblink);

		  } /* end if (isset(...) */
		}else {
			
			$dblink = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
			
			$SQLString = "SELECT username, blog_title, blog_message, user_id, id, post_date FROM users INNER JOIN post USING (user_id) ORDER BY id";
			$QueryResult = @mysqli_query($dblink, $SQLString);
			$Row = mysqli_fetch_assoc($QueryResult);
			$num_rows = mysqli_num_rows($QueryResult);
			$postusername = ucfirst($Row['username']);
			
			//echo $num_rows;
			
			//$SQLString = "SELECT * FROM post";
			//$QueryResult = @mysqli_query($dblink, $SQLString);
			//$Row = mysqli_fetch_assoc($QueryResult);
			
			if($num_rows == 0){
							
			}else {
				//$postusername = ucfirst($Row['username']);
				do {
					$SQLString2 = "SELECT id, username FROM users INNER JOIN post USING (user_id) WHERE id = '{$Row['id']}'";
					$QueryResult2 = @mysqli_query($dblink, $SQLString2);
					$Row2 = mysqli_fetch_assoc($QueryResult2);
					$postusername2 = ucfirst($Row2['username']);
					
					echo "<div class='postitem'>";
					echo "<div class='blogtop'><h3 class='blogtitle'><a href='commentblog.php?post_id={$Row['id']}'>{$Row['blog_title']}</a></h3>";
					echo "<p class='username'>Posted by: $postusername2 on {$Row['post_date']}</p></div>\n";
					echo "<div class='blogbottom'><p class='blogmessage'>{$Row['blog_message']}</p>\n";
					echo "<div class='button2'><p><a href='commentblog.php?post_id={$Row['id']}'>View Comments</a></p></div>\n</div>";
					echo "</div>";

					$Row = mysqli_fetch_assoc($QueryResult);
				} while ($Row);
				
			}//end of if($num_rows == 0)
		}

//------Show errors if any-------//

		if($have_error == true){
			echo $errmsg;
		}

	echo "</div><!-- #bottom -->";


		?>

		


<?php
			}else {
				
				
/*================================= ELSE SHOW ALL THIS IF NOT LOGGED IN ===================================*/				
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="stylesheets/style1.css" rel="stylesheet" type="text/css" />
<link href='http://fonts.googleapis.com/css?family=Brawler' rel='stylesheet' type='text/css'>
<script src="cleartext.js" type="text/javascript"></script>
<title>Dashboard</title>
</head>
<body>

<div id="container">

	<div id="top">
    	<div id="logomenu">
        	<h1><a href="index.php">Logo</a></h1>
            <form id="signinform" method="post" action="index.php">
            	<input class="userinput" type="text" name="username" value="Username" onFocus="clearText(this)" onBlur="clearText(this)" />
                <input class="userinput" type="password" name="passwd" />
                <input type="submit" name="login" value="Sign In" />
					
            </form>
        </div><!--#logomenu-->
    </div><!--#top-->
	
	
    <div id="bottom">
        <h3>Blog Wall</h3>
		<hr />



<?php 
		//DATABASE PARAMS
		include("config.php");
	
		$dblink = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
		$TableName = "post";
		$SQLString = "SELECT username, blog_title, blog_message, user_id, id, post_date FROM users INNER JOIN post USING (user_id) ORDER BY id";
		$QueryResult = @mysqli_query($dblink, $SQLString);
		$Row = mysqli_fetch_assoc($QueryResult);
		$num_rows = mysqli_num_rows($QueryResult);
		
		//echo "WTF!!!!" . $num_rows;
		
		if($num_rows == 0){
			//don't show anything
		}else {
				
			do {
				$SQLString2 = "SELECT id, username FROM users INNER JOIN post USING (user_id) WHERE id = '{$Row['id']}'";
				$QueryResult2 = @mysqli_query($dblink, $SQLString2);
				$Row2 = mysqli_fetch_assoc($QueryResult2);
				$postusername2 = ucfirst($Row2['username']);
				
				echo "<div class='postitem'>";
				echo "<div class='blogtop'><h3 class='blogtitle'><a href='commentblog.php?post_id={$Row['id']}'>{$Row['blog_title']}</a></h3>";
				echo "<p class='username'>Posted by: $postusername2 on {$Row['post_date']}</p></div>\n";
				echo "<div class='blogbottom'><p class='blogmessage'>{$Row['blog_message']}</p>\n";
				echo "<div class='button2'><p><a href='commentblog.php?post_id={$Row['id']}'>View Comments</a></p></div>\n</div>";
				echo "</div>";

				$Row = mysqli_fetch_assoc($QueryResult);
			} while ($Row);
		
		}//End if($num_rows == 0)

		echo "<div class='button1'>\n<p><a href='index.php'>Back to homepage</a></p>\n</div><!--.button1-->";
}

?>
</div><!--#bottom-->
</div><!--#container-->
</body>
</html>