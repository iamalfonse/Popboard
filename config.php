<?php

// Remove magic quotes if enabled
if (get_magic_quotes_gpc()) {
    $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    while (list($key, $val) = each($process)) {
        foreach ($val as $k => $v) {
            unset($process[$key][$k]);
            if (is_array($v)) {
                $process[$key][stripslashes($k)] = $v;
                $process[] = &$process[$key][stripslashes($k)];
            } else {
                $process[$key][stripslashes($k)] = stripslashes($v);
            }
        }
    }
    unset($process);
}

/* Database params */
$dbhost = "localhost";
$dbuser = "hallofh3_pop";
$dbpass = "emnalphil02";
$dbname = "hallofh3_popboard";
$dblink = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

//set charset for using with mysqli_real_escape_string();
mysqli_set_charset($dblink, "utf8");

function get_posts($Rows, $Row, $QueryResult){

	//need to do query for $Row before you call this function
	//example:
	// $SQLString = "SELECT username, blog_title, blog_message, user_id, id, post_date FROM users INNER JOIN post USING (user_id) ORDER BY id DESC LIMIT 0, 3";
	// $QueryResult = @mysqli_query($dblink, $SQLString);
	// $Row = mysqli_fetch_assoc($QueryResult);

	/* Database params */
	$dbhost = "localhost";
	$dbuser = "hallofh3_pop";
	$dbpass = "emnalphil02";
	$dbname = "hallofh3_popboard";
	$dblink = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
		
	do {
		$blogtitle = get_cleaner_url($Row);

		//get the correct username of the post
		$SQLString2 = "SELECT id, username, email, views FROM users INNER JOIN posts USING (user_id) WHERE id = '{$Row['id']}'";
		$QueryResult2 = @mysqli_query($dblink, $SQLString2);
		$Row2 = mysqli_fetch_assoc($QueryResult2);
		$postusername2 = ucfirst(stripslashes($Row2['username']));

		//get the number of comments for a post
		$SQLString3 = "SELECT count(posts.id), comments.post_id FROM posts INNER JOIN comments WHERE comments.post_id = '{$Row['id']}' AND comments.post_id = posts.id";
		$QueryResult3 = @mysqli_query($dblink, $SQLString3);
		$Row3 = mysqli_fetch_assoc($QueryResult3);

		//get the number of likes for a post
		$SQLString4 = "SELECT count(likes.post_id), likes.user_id FROM likes WHERE likes.post_id = '{$Row['id']}'";
		$QueryResult4 = @mysqli_query($dblink, $SQLString4);
		$Row4 = mysqli_fetch_assoc($QueryResult4);

		//used to check if user has already liked a post
		$SQLString5 = "SELECT * FROM likes WHERE likes.user_id = '{$Rows['user_id']}' AND likes.post_id = '{$Row['id']}'";
		$QueryResult5 = @mysqli_query($dblink, $SQLString5);
		$Row5 = mysqli_fetch_assoc($QueryResult5);
?>
		<div class='postitem'>
			<div class='blogtop'>
				<? get_post_pic($Row2['email']); ?>
				<h3 class='blogtitle' id='<?= $Row['id'] ?>'><a href='/post/<?= $Row['id']?>/<?= $blogtitle?>'><?= $Row['blog_title'] ?></a></h3>
				<p class='username'>Posted by: <?= $postusername2 ?> on <?= $Row['post_date'] ?></p>
			</div>
			<div class='blogbottom'>
				<p class='blogmessage'><?= $Row['blog_message'] ?></p>

				<div class='counters'>
					<? //show likes and determine if user liked the post already
					if($Row5['user_id'] == $Rows['user_id']){ 
						echo "<div class='likes'><span class='liked'>Likes</span> {$Row4['count(likes.post_id)']}</div>";
					}else{
						echo "<div class='likes'><span>Likes</span> {$Row4['count(likes.post_id)']}</div>";
					}

					//show number of views
					if($Row2['views'] == ''){
						echo "<div class='views'><span>Views</span> 0</div>";
					}else{
						echo "<div class='views'><span>Views</span> {$Row2['views']}</div>";
					} 
					?>
				</div>
		
					<? //show number of comments
					if($Row3['count(posts.id)'] == 1){
						echo "<div class='button2'><p><a href='/post/{$Row['id']}/$blogtitle'>{$Row3['count(posts.id)']} Comment</a></p></div>";
					}else{
						echo "<div class='button2'><p><a href='/post/{$Row['id']}/$blogtitle'>{$Row3['count(posts.id)']} Comments</a></p></div>";
					}
					?>
				<div class='clear'></div>
			</div>
		</div>
<?
			$Row = mysqli_fetch_assoc($QueryResult);
	} while ($Row);

}

function get_cleaner_url($Row){
	//get rid of spaces and special characters from the blog title to make it cleaner url
	$blogtitle = trim(strtolower($Row['blog_title'])); //remove spaces before and after
	$blogtitle = preg_replace('/[^A-Za-z0-9_\s]+/', '', $blogtitle); //get rid of non-url chars
	$blogtitle = preg_replace('/\s/', '-', $blogtitle); //replace spaces with a dash -
	$blogtitle = preg_replace('/-([-]+)?/', '-', $blogtitle); // replace dashes with more than two dashes side by side into only a single dash
	return $blogtitle;
}

function get_profile_pic($email = ""){
	/* Database params */
	$dbhost = "localhost";
	$dbuser = "hallofh3_pop";
	$dbpass = "emnalphil02";
	$dbname = "hallofh3_popboard";
	$dblink = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

	if (isset( $_COOKIE['login_cookie'] )) {
		$have_user_id = true;
		$user_id = ucfirst($_COOKIE['login_cookie']);
	
		//find out if user has set gravatar image
		$query3    = "SELECT * FROM users WHERE username='$user_id'";
	    $results3  = mysqli_query($dblink, $query3);
	    $Row3 = mysqli_fetch_assoc($results3);

		if($Row3['gravatar'] == 1){//user has gravatar set
			$email = $Row3['email'];
			$default = "http://popboard.hallofhavoc.com/images/me.jpg";
			$size = 90;
			$grav_url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?d=" . urlencode( $default ) . "&s=" . $size;

			echo '<img src="'.$grav_url.'" alt="">';
		}else if($Row3['gravatar'] == 0 || $Row3['gravatar'] == null){//user set to normal
			echo '<img src="/images/me.jpg" class="profilePic">';
		}
	}

}

function get_comment_pic($email = ""){
	/* Database params */
	$dbhost = "localhost";
	$dbuser = "hallofh3_pop";
	$dbpass = "emnalphil02";
	$dbname = "hallofh3_popboard";
	$dblink = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

	// if (isset( $_COOKIE['login_cookie'] )) { 
	// 	$have_user_id = true; 
	// 	$user_id = ucfirst($_COOKIE['login_cookie']);   
	
		//find out if user has set gravatar image
		$query3    = "SELECT gravatar FROM users";
	    $results3  = mysqli_query($dblink, $query3);
	    $Row3 = mysqli_fetch_assoc($results3);

		if($Row3['gravatar'] == 1){//user has gravatar set
			
			$default = "http://popboard.hallofhavoc.com/images/me.jpg";
			$size = 90;
			$grav_url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?d=" . urlencode( $default ) . "&s=" . $size;

			echo '<img src="'.$grav_url.'" alt="">';
		}else if($Row3['gravatar'] == 0 || $Row3['gravatar'] == null){//user set to normal
			echo '<img src="/images/me.jpg">';
		}
	//}

}

// function get_userpost_pic($email){
// 	$dbhost = "localhost";
// 	$dbuser = "hallofh3_pop";
// 	$dbpass = "emnalphil02";
// 	$dbname = "hallofh3_popboard";
// 	if (isset( $_COOKIE['login_cookie'] )) { 
// 		$have_user_id = true; 
// 		$user_id = ucfirst($_COOKIE['login_cookie']);   
	
// 		//find out if user has set gravatar image
// 		$dblink3 = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
// 		$query3    = "SELECT * FROM users WHERE username='$user_id'";
// 	    $results3  = mysqli_query($dblink3, $query3);
// 	    $Row3 = mysqli_fetch_assoc($results3);

// 		if($Row3['gravatar'] == 1){//user has gravatar set
			
// 			$default = "http://popboard.hallofhavoc.com/images/me.jpg";
// 			$size = 90;
// 			$grav_url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?d=" . urlencode( $default ) . "&s=" . $size;

// 			echo '<img src="'.$grav_url.'" alt="">';
// 		}else if($Row3['gravatar'] == 0 || $Row3['gravatar'] == null){//user set to normal
// 			echo '<img src="images/me.jpg">';
// 		}
// 	}

// }

function get_post_pic($email){
	/* Database params */
	$dbhost = "localhost";
	$dbuser = "hallofh3_pop";
	$dbpass = "emnalphil02";
	$dbname = "hallofh3_popboard";
	$dblink = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	
	//find out if user has set gravatar image
	$query3    = "SELECT gravatar, username FROM users WHERE email = '$email'";
    $results3  = mysqli_query($dblink, $query3);
    $Row3 = mysqli_fetch_assoc($results3);

	if($Row3['gravatar'] == 1){//user has gravatar set
		$default = "http://popboard.hallofhavoc.com/images/me.jpg";
		$size = 90;
		$grav_url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?d=" . urlencode( $default ) . "&s=" . $size;

		echo '<a href="/profile/'.$Row3['username'].'"><img src="'.$grav_url.'" alt=""></a>';
	}else if($Row3['gravatar'] == 0 || $Row3['gravatar'] == null){//user set to normal
		echo '<a href="/profile/'.$Row3['username'].'""><img src="images/me.jpg"></a>';
	}
}


function calculateXP($myXP) {
    $startXP = 0; // Level 1 Start XP
    $endXP = 20;  // Level 1 End XP
    $increaseXP =20;   // Increase by extra how many per level?
    $lvlMultiplier = 100; // Multiply by how many per level? (1- easy / 100- hard)

    /* Calculate Level */
    $myLevel = 0;
    $calcCount = 0;
    do {
        $calcCount = $calcCount+1;
        if ($calcCount % 2 == 0 ) { 
        	$increaseXP = $increaseXP + $lvlMultiplier; 
        }
        if (($myXP < $endXP) && ($myXP >= $startXP)) { 
        	$myLevel = $calcCount; $myStart = $startXP; $myEnd = $endXP; 
        }
        $startXP = $endXP;
        $endXP = $endXP + $increaseXP;
    } while ($myLevel == 0);
    $myLevel--;

    /* Calculate XP to next level */
    $myCurrentXP = $myXP - $myStart;
    $toNextLevel = $myEnd - $myStart;

    /* Calculate Percentage to Next Level */
    $myPercent = (($myXP - $myStart) / ($myEnd - $myStart)) * 100;
    $myPercent = round($myPercent);
    if ($myPercent == 0) { $myPercent = 1; }

    //return array('percent'=>$myPercent,'level'=>$myLevel);
    //echo 'percent: '.$myPercent.' level: '.$myLevel.' Progress: '.$myCurrentXP.'/'.$toNextLevel; 
    ?>
    <div class='userLvl'>
    	<p><?= $myLevel ?></p>
    </div>
    <div class='xpBar'>
    	<p><?= $myCurrentXP ?>/<?= $toNextLevel ?></p>
    	<div class='progressXP' style='width: <?= $myPercent ?>%'></div>
    </div>
    <?
}

// how to output '' days ago, '' hours ago, etc. Must use DateTime() format. ex: 2013-05-29 00:00:00
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

?>