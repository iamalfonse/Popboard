<?php
	include("config.php");
	
	$start = $_GET['start'];
	$limit = $_GET['limit'];
	
	$dblink = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
			
	$SQLString = "SELECT username, blog_title, blog_message, user_id, id, post_date FROM users INNER JOIN posts USING (user_id) ORDER BY id DESC LIMIT $start, $limit";
	$QueryResult = @mysqli_query($dblink, $SQLString);
	$Row = mysqli_fetch_assoc($QueryResult);
	$num_rows = mysqli_num_rows($QueryResult);
	//$postusername = ucfirst($Row['username']);

	//get username for reference
	$username = strtolower($_COOKIE['login_cookie']);

	/* Open connection to the database */
	$q   = "SELECT user_id FROM users WHERE username='$username'";
    $r  = mysqli_query($dblink, $q);
	$Rows = mysqli_fetch_assoc($r);
	
	//$SQLString = "SELECT * FROM post";
	//$QueryResult = @mysqli_query($dblink, $SQLString);
	//$Row = mysqli_fetch_assoc($QueryResult);
	
	if($num_rows == 0){
		$start = $start-3;
		return $start;
	}else {
		
		get_posts($Rows, $Row, $QueryResult);
		
	}
?>



