
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="/stylesheets/style1.css" rel="stylesheet" type="text/css" />
<link href='http://fonts.googleapis.com/css?family=Brawler' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
<script type="text/javascript" src="/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="/js/cleartext.js"></script>
<title>Dashboard</title>
</head>
<body class='comments'>

<div id="container">

	<div id="top">
    	<div id="logomenu">
        	<h1><a href="/">Logo</a></h1>
<?php
			include("config.php");

			if (isset( $_COOKIE['login_cookie'] )) {
				$have_user_id = true; 
				$user_id = ucfirst($_COOKIE['login_cookie']); 
			?>
			
			<div class='tab'>
				<p><a href='/posts'>Blog Wall</a></p>
			</div><!--.tab-->
			
			<?
			} else {
			?>
			<form id='signinform' method='post' action='/'>
            	<input id='usernameinput' type='text' name='username' value='Username' onFocus='clearText(this)' onBlur='clearText(this)' />
            	<input type='password' name='passwd' />
            	<input type='submit' name='login' value='Sign In' />
			</form>
			<?
			}
?>
            
        </div><!--#logomenu-->
    </div><!--#top-->
	
<?php

if (isset( $_COOKIE['login_cookie'] )) { 
	$have_user_id = true; 
	$user_id = ucfirst($_COOKIE['login_cookie']);
	?>

<div id="bottom">
	<?php include("left.php"); ?>
		
	<div id='right'>
	    <!--<h3 class='sectiontitle'>Blog Post</h3><hr />-->
	

	<?php 
} else {
	echo "<div id='bottom'>
        <h3>Blog Post</h3><hr />";
}
?>
	
	
        
<?php
		//DATABASE PARAMS
		//include("config.php");
		$dblink = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

		/* This will tell us to display an error message */
		$have_error = false;
		$errmsg     = "";
		
		$postid_num = mysqli_real_escape_string($dblink, $_GET['post_id']);
		
			
		//Check Post and add +1 view to the table column views
		$SQLString00 = "SELECT views FROM posts WHERE id = $postid_num ";
		$QueryResult00 = @mysqli_query($dblink, $SQLString00);
		$Row00 = mysqli_fetch_assoc($QueryResult00);

		if($Row00['views']== '' || $Row00 == null){
			//add +1 to views if there's no value yet
			$SQLString0 = "UPDATE posts SET views = 1 WHERE id = $postid_num ";
			$QueryResult0 = @mysqli_query($dblink, $SQLString0);
		}else{
			//update and add +1 to views if it's not null
			$SQLString0 = "UPDATE posts SET views = views+1 WHERE id = $postid_num ";
			$QueryResult0 = @mysqli_query($dblink, $SQLString0);
		}

			
	if(isset($_POST['submitcomment']) && $_POST['comment'] != ''){
			$dblink = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

			$postid_num = mysqli_real_escape_string($dblink, $_POST['postid_num']);
			//echo $postid_num;
			
			//================ SHOW PREVIOUS AND GET URL ID NUMBER ===============//
			$SQLString = "SELECT username, email, blog_title, blog_message, user_id, id, post_date FROM users INNER JOIN posts USING (user_id) WHERE id = '$postid_num'";
			$QueryResult = @mysqli_query($dblink, $SQLString);
			$Row = mysqli_fetch_assoc($QueryResult);
			$postusername = ucfirst($Row['username']);

			$blogtitle = get_cleaner_url($Row);
			
				echo "<div class='postitem'>";
				echo "<div class='blogtop'>";
				get_post_pic($Row['email']);
				echo "<h3 class='blogtitle'>{$Row['blog_title']}</h3>";
				echo "<p class='username'>Posted by: $postusername on {$Row['post_date']}</p></div>\n";
				echo "<div class='blogbottom'><p class='blogmessage'>{$Row['blog_message']}</p></div>\n";
				echo "</div>";

				if (isset( $_COOKIE['login_cookie'] )) { 
					$have_user_id = true; 
					$user_idname = $_COOKIE['login_cookie'];	

				echo "<div id='addcomment'>
						<form method='post' action='/post/$postid_num/$blogtitle'>
							<p id='addcommentp'>Add Comment:<br />
							<textarea id='textareainput' name='comment'></textarea></p><br />
							<input type='hidden' name='postid_num' value='$postid_num' >
							<input type='submit' name='submitcomment' class='button1 submitbtn' value='Submit'/>
					  	</form>
					  </div>";
				} else {
					echo "<p class='logintocomment' >To post comments, please Sign In.</p>";
				}
			
			//================ PROCESS COMMENT INPUT ===============//
			$comment = mysqli_real_escape_string($dblink, $_POST['comment']);
			$postid_num = mysqli_real_escape_string($dblink, $_POST['postid_num']);
			
			//get id of post to insert into comments
			$SQLString4 = "SELECT id FROM posts WHERE id = $postid_num";
			$QueryResult4 = @mysqli_query($dblink, $SQLString4);
			$Row4 = mysqli_fetch_assoc($QueryResult4);
			$urlid = $Row4['id'];
			
			//get user id of logged in person to insert into comments
			$SQLString5 = "SELECT user_id FROM users WHERE username = '$user_idname'";
			$QueryResult5 = @mysqli_query($dblink, $SQLString5);
			$Row5 = mysqli_fetch_assoc($QueryResult5);
			$user_id = $Row5['user_id'];
			
			$comment_date = date("M d, Y");
			
			//Insert comment from logged in user using their Id into the comments table using the post id
			$SQLString2 = "INSERT INTO comments(usrname, post_id, user_id, comment_post, comment_date) VALUES ('$user_idname', '$postid_num', '$user_id', '$comment', '$comment_date')";
			$QueryResult2 = @mysqli_query($dblink, $SQLString2);
			
			
			//output comments at the end
			
		
	}else {
			
			
				//================ OUTPUT COMMENTS ALREADY STORED BEFORE INPUTTING ANY COMMENTS ===============//
				$dblink = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
				$SQLString = "SELECT username, email, blog_title, blog_message, user_id, id, post_date FROM users INNER JOIN posts USING (user_id) WHERE id = '$postid_num'";
				$QueryResult = @mysqli_query($dblink, $SQLString);
				$Row = mysqli_fetch_assoc($QueryResult);
				$postusername = ucfirst($Row['username']);

				$blogtitle = get_cleaner_url($Row);

					echo "<div class='postitem'>";
					echo "<div class='blogtop'>";
					get_post_pic($Row['email']);
					echo "<h3 class='blogtitle'>{$Row['blog_title']}</h3>";
					echo "<p class='username'>Posted by: $postusername on {$Row['post_date']}</p></div>\n";
					echo "<div class='blogbottom'><p class='blogmessage'>{$Row['blog_message']}</p></div>\n";
					echo "</div>";

					if (isset( $_COOKIE['login_cookie'] )) { 
						$have_user_id = true; 
						$user_idname = $_COOKIE['login_cookie'];	

					//if user submits but there is no comment, output error
					if(isset($_POST['submitcomment']) && $_POST['comment'] == ''){
						echo "<p><span class='red'>* required</span></p>";
					}

					echo "<div id='addcomment'>
						  	<form method='post' action='/post/$postid_num/$blogtitle'>
								<p id='addcommentp'>Add Comment:<br />
								<textarea id='textareainput' name='comment'></textarea></p><br />
								<input type='hidden' name='postid_num' value='$postid_num' >
								<input type='submit' name='submitcomment' class='button1 submitbtn' value='Submit'/>
						  	</form>
						 </div>";
					} else {
						echo "<p class='logintocomment' >To post comments, please Sign In.</p>";
						
					}
			
			
			//================ OUTPUT COMMENTS ===============//
			
			
			
		//} else {
		//	echo "<div class='btn1'><p><a href='posturl.php'>Back to URL listing</a></p></div>";
		//}
			
			
			
	}//end of else/ if(isset($_POST['submitcomment']))
	
	$postid_num = $_GET['post_id'];
	
	//================ OUTPUT COMMENTS ===============//
	$SQLString3 = "SELECT * FROM comments WHERE post_id = '$postid_num'";
	$QueryResult3 = @mysqli_query($dblink, $SQLString3);
	$Row3 = mysqli_fetch_assoc($QueryResult3);
	$numrows = mysqli_num_rows($QueryResult3);
	$commentusername = ucfirst($Row3['usrname']);
	
	//if there's at least one comment, display comments
if($numrows >= 1){
	echo "<div id='commentstop'><h3>Comments</h3></div>";
	echo "<div id='commentsarea'>";
	do {
		$SQLString2 = "SELECT id, usrname, email FROM users INNER JOIN comments USING (user_id) WHERE id = '{$Row3['id']}'";
		$QueryResult2 = @mysqli_query($dblink, $SQLString2);
		$Row2 = mysqli_fetch_assoc($QueryResult2);
		$commentusername2 = ucfirst($Row2['usrname']);
		
		echo "<span class='commentpic'>";
		echo get_comment_pic($Row2['email']);
		echo "<h4>Posted by: <span>$commentusername2</span> on {$Row3['comment_date']}</h4></span>";
		echo "<img class='commentarrow' src='/images/comment_arrow.jpg' /><p class='comment'>{$Row3['comment_post']}</p>";
		$Row3 = mysqli_fetch_assoc($QueryResult3);
	} while ($Row3);
	echo "</div>";
	
	if (isset( $_COOKIE['login_cookie'] )) { 
	}else{
		echo "<div class='button1'>\n<p><a href='postblog.php'>Back to Blog Wall</a></p>\n</div><!--.button1-->";
	}
	
}else {
	//don't show anything if there are no comments
	if (isset( $_COOKIE['login_cookie'] )) { 
	}else{
		echo "<div class='button1'>\n<p><a href='postblog.php'>Back to Blog Wall</a></p>\n</div><!--.button1-->";
	}
}	
	

echo "</div>";


		?>