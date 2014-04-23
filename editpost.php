<?php

	if (isset( $_COOKIE['login_cookie'] )) { 
		$have_user_id = true; 
		$user_id = ucfirst($_COOKIE['login_cookie']); 
		
		
		//echo $user_id;
		
		//DATABASE PARAMS
		include("config.php");
		$dblink = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
		
		$post_id = mysqli_real_escape_string($dblink, $_GET['post_id']);

		

		$SQLString = "SELECT username, blog_title, blog_message FROM users INNER JOIN posts USING (user_id) WHERE id = '$post_id'";
		$QueryResult = @mysqli_query($dblink, $SQLString);
		$Row = mysqli_fetch_assoc($QueryResult);
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<!-- TinyMCE -->
<script type="text/javascript" src="/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
	tinyMCE.init({
		mode : "textareas",
		theme : "advanced",
		theme_advanced_buttons1 : "bold,italic,underline, | ,strikethrough,justifyleft,justifycenter,justifyright,justifyfull,bullist,numlist,undo,redo,link,unlink, | ,image,",
		theme_advanced_buttons2 : "",
	    theme_advanced_buttons3 : "",
	    theme_advanced_toolbar_location : "top",
	    theme_advanced_toolbar_align : "left",
	    theme_advanced_statusbar_location : "bottom"

	});
</script>
<!-- /TinyMCE -->

<link href="/stylesheets/style1.css" rel="stylesheet" type="text/css" />
<link href='http://fonts.googleapis.com/css?family=Brawler' rel='stylesheet' type='text/css'>
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
        	<h3 class="sectiontitle">Edit Post</h3>
			<div class="clear"></div>
		</div><!--.right-content -->
		<div id="postcomment"> 
			<form method="post" action="/posts"> 
				<p class='inputtitle'>Title<br /><input type="text" name="blogtitle" maxlength="50" class='inputtitle' value="<?php echo stripslashes($Row['blog_title']); ?>" /></p> 
				<p class='inputmessage'>Message<br /><textarea name="blogmessage" rows="5" cols="50" class='inputmessage'> 
				<?php echo stripslashes($Row['blog_message']); ?>
	 			</textarea></p> 
				<input type="hidden" name="hiddenpost_id" value="<?php echo $post_id ?>">
				<p><input type="submit" name="editsubmitblog" value="Post Blog" class="submitbtn"/></p> 
			</form> 
		</div><!--#postcomment-->
</div><!--#right-->
</div><!-- #bottom -->

</div><!--#container-->

<?php
	} else {
		//echo "<a href='index.php' />Go back to index</a>";
		header("Location: index.php"); 
	}
	
	
?>


</body>
</html>