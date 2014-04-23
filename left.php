<?php
	$username = strtolower($_COOKIE['login_cookie']);
	
	/* Open connection to the database */
    $dblink = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	$q   = "SELECT user_id, username, email FROM users WHERE username='$username'";
    $r  = mysqli_query($dblink, $q);
	$Rows = mysqli_fetch_assoc($r);
?>

<div id="left">
	<?php get_profile_pic($Rows['email']); ?>
	<p class="leftusername">Logged in as:<br/><?php echo $user_id; ?></p>
	
	<div class="clear"></div>
	<p class="leftbuttons profile"><a href="/profile/<?= $Rows['username']?>"><span></span>My Profile</a>
	<p class="leftbuttons createpost"><a href="/createpost"><span></span>Create Post</a></p>
	<p class="leftbuttons myposts"><a href="/myposts"><span></span>My Posts</a></p>
	<p class="leftbuttons logout"><a id="logoutbtn" href="/logout"><span></span>Logout</a></p>
</div><!--#left-->