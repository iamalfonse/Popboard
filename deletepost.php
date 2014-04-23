<?php
		include("config.php");
		$dblink = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

		/* This will tell us to display an error message */
		$have_error = false;
		$errmsg     = "";

		$username = mysqli_real_escape_string($dblink, strtolower($_COOKIE['login_cookie']));
		$session_hash = $_COOKIE['hsh'];

		$postid_num = mysqli_real_escape_string($dblink, $_GET['post_id']);
		
		//================ DELETE POST AND GET URL ID NUMBER ===============//
		$q   = "SELECT session_hash FROM users WHERE username='$username'";
	    $r  = mysqli_query($dblink, $q);
		$Rows = mysqli_fetch_assoc($r);
		
		
		if($session_hash == $Rows['session_hash']){
			//-------DELETE THE POST WITH PROPER ID ----------------
			$SQLString = "DELETE FROM posts WHERE id = '$postid_num'";
			$QueryResult = @mysqli_query($dblink, $SQLString);
			
			//------- DELETE THE COMMENTS ASSOCIATED WITH THAT POST USING POST_ID ----------//
			$SQLString = "DELETE FROM comments WHERE post_id = '$postid_num'";
			$QueryResult = @mysqli_query($dblink, $SQLString);

			//------- DELETE THE LIKES ASSOCIATED WITH THAT POST USING POST_ID ----------//
			$SQLString = "DELETE FROM likes WHERE post_id = '$postid_num'";
			$QueryResult = @mysqli_query($dblink, $SQLString);
		}	
			
		
		//$Row = mysqli_fetch_assoc($QueryResult);
		//$postusername = ucfirst($Row['username']);
		
		header("Location: /myposts"); 
		
		//echo $postid_num;
		
		//output comments at the end




		?>