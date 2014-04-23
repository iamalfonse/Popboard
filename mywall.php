<?php
	
	include("config.php");
	$dblink = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

	if (isset( $_COOKIE['login_cookie'] )) { 
		$have_user_id = true; 
		$user_id = mysqli_real_escape_string($dblink, $_COOKIE['login_cookie']);   


		//echo $user_id;

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
<title>Dashboard</title>
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
	    	<div class="right-content">
		        <h3 class="sectiontitle">My Wall</h3>
				<div id="sortby">
					<p>Sort by: </p>
					<p class="sortbtn"><a href="/myposts/newest">Most Recent</a></p>
					<p class="sortbtn" ><a href="/myposts/oldest">Oldest</a></p>
				</div>
				<div class="clear"></div>
			</div>
		
        
<?php
		//DATABASE PARAMS
		//include("config.php");

		/* This will tell us to display an error message */
		$have_error = false;
		$errmsg     = "";

		//get sort by
		$sortby = mysqli_real_escape_string($dblink, $_GET['sortby']);
		

		if($sortby == 'newest'|| $sortby == '') { //sort by newest
			$SQLString = "SELECT username, blog_title, blog_message, user_id, id, post_date FROM users INNER JOIN posts USING (user_id) WHERE username = '$user_id' ORDER BY id DESC";
		}else if($sortby == 'oldest'){ //sort by oldest
			$SQLString = "SELECT username, blog_title, blog_message, user_id, id, post_date FROM users INNER JOIN posts USING (user_id) WHERE username = '$user_id' ORDER BY id ASC";
		}
		
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
				$SQLString2 = "SELECT id, username FROM users INNER JOIN posts USING (user_id) WHERE id = '{$Row['id']}' ORDER BY id DESC";
				$QueryResult2 = @mysqli_query($dblink, $SQLString2);
				$Row2 = mysqli_fetch_assoc($QueryResult2);
				$postusername2 = ucfirst($Row2['username']);

				$blogtitle = get_cleaner_url($Row);
				?>

				<div class='postitem'>
					<div class='blogtop'><h3 class='blogtitle'><a href='/post/<?=$Row['id']?>/<?=$blogtitle?>'><?= $Row['blog_title'] ?></a></h3>
						<p class='username'>Posted by: <?= $postusername2 ?> on <?= $Row['post_date']?></p>
						<p class='editpost'><a href='/editpost/<?= $Row['id'] ?>/<?=$blogtitle?>'>Edit Post</a></p>
						<div class='deletebtn delete'>
							<p><a href='deletepost/<?= $Row['id']?>/<?=$blogtitle?>'>Delete Post</a></p>
						</div>
					</div>
				    <div class='blogbottom'>
				    	<p class='blogmessage'><?= $Row['blog_message'] ?></p>
						<div class='button2'>
							<p><a href='/post/<?= $Row['id']?>/<?=$blogtitle?>'>View Comments</a></p>
						</div>
						<div class='clear'></div>
					</div>
				</div>

				<?
				$Row = mysqli_fetch_assoc($QueryResult);
			} while ($Row);
			
		}//end of if($num_rows == 0)
?>
		
	</div><!-- #bottom -->
<?
	}else { //else statement for if LOGGED IN
			
		
		//*** DON'T SHOW ANYTHING IF NOT LOGGED IN ***//
	
	}//end of if LOGGED IN		

		//------Show errors if any-------//

		if($have_error == true){
			echo $errmsg;
		}



?>
</div><!--#bottom-->
</div><!--#container-->
</body>
</html>