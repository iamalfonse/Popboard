<?php
//echo "awesome";

include("config.php");
$dblink = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

		$have_user_id = true; 
 
		$user_name = mysqli_real_escape_string($dblink, strtolower($_COOKIE['login_cookie']));
		$user_hash = mysqli_real_escape_string($dblink, $_COOKIE['hsh']);
		
		//print_r($user_name);
		$have_error = false;
		$errmsg     = "";
		
		//grab the post id from the blog post
		$postid = mysqli_real_escape_string($dblink, $_GET['postid']);

		//get the user id and session_hash to cross check if the user has liked the post before
		$SQLString= "SELECT user_id, session_hash FROM users WHERE username = '$user_name'";
		$QueryResult = @mysqli_query($dblink, $SQLString);
		$Row = mysqli_fetch_assoc($QueryResult);

		//get the correct post to like
		$SQLString2 = "SELECT * FROM likes WHERE user_id='{$Row['user_id']}' AND likes.post_id = '$postid';";
		$QueryResult2 = @mysqli_query($dblink, $SQLString2);
		$Row2 = mysqli_fetch_assoc($QueryResult2);
		$num_rows2 = mysqli_num_rows($QueryResult2);

		//get the correct user of post
		$SQLString5 = "SELECT id, user_id FROM posts WHERE posts.id='$postid';";
		$QueryResult5 = @mysqli_query($dblink, $SQLString5);
		$Row5 = mysqli_fetch_assoc($QueryResult5);

		//echo $user_hash." ".$Row['session_hash'];
		
		if(!$Row2['user_id'] && ($user_hash == $Row['session_hash'])){//if there's no record and session_hash matches (security check)
			$SQLString3 = "INSERT INTO likes(user_id,post_id, post_user_id) VALUES ('{$Row['user_id']}','$postid','{$Row5['user_id']}')";
			$QueryResult3 = @mysqli_query($dblink, $SQLString3);

			$SQLString4 = "SELECT * FROM likes WHERE likes.post_id = '$postid'";
			$QueryResult4 = @mysqli_query($dblink, $SQLString4);
			$Row4 = mysqli_fetch_assoc($QueryResult4);
			$num_rows4 = mysqli_num_rows($QueryResult4);

			echo "<span class='liked'>Likes</span> ".$num_rows4."";
		}else {
			
			//you already liked a post so delete your like
			$SQLString3 = "DELETE FROM likes WHERE user_id='{$Row['user_id']}' AND likes.post_id = '$postid';";
			$QueryResult3 = @mysqli_query($dblink, $SQLString3);

			$SQLString4 = "SELECT * FROM likes WHERE likes.post_id = '$postid'";
			$QueryResult4 = @mysqli_query($dblink, $SQLString4);
			$Row4 = mysqli_fetch_assoc($QueryResult4);
			$num_rows4 = mysqli_num_rows($QueryResult4);

			echo "<span>Likes</span> ".$num_rows4."";
			
		}//end of if($num_rows == 0)





?>