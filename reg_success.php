<?php
/**
 * reg_success.php
 *
 * Display a login form. Also process submission of login credentials.
 *
 **/

/* Declare the parameters for accessing the database again. */
//DATABASE PARAMS
include("config.php");
$dblink = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="/stylesheets/style1.css" rel="stylesheet" type="text/css" />
<link href='http://fonts.googleapis.com/css?family=Brawler' rel='stylesheet' type='text/css'>
<script src="/cleartext.js" type="text/javascript"></script>
<title>Popboard</title>
</head>
<body class="registration">

<div id="container">
	<div id="top">
    	<div id="logomenu">
        	<h1><a href="/">Logo</a></h1>
            
        </div><!--#logomenu-->
    </div><!--#top-->
	
    <div id="bottom">
		<div id="signup">
		<? 
		if(!empty($_GET['email']) || !empty($_GET['key'])){  
			echo "<h2>Registration Successful!</h2>";
		}else {
			echo "<h2>Activate Your Account</h2>";
		}
		?>
        
        <?php 
        	if($have_error){
				echo $errmsg;
			}
        ?>
        <?php 
        	/* This is when the user clicks on the email link
			 * that they recieved for confirmation. It checks
			 * the database to see if a key is assigned
			 */

			//quick/simple validation  
			if(empty($_GET['email']) || empty($_GET['key'])){  
			    
			    echo '<p class="normal">Please check your email to activate your account.</p>';  
			} else{  
			  	
			    //cleanup the variables  
			    $email = mysqli_real_escape_string($dblink, $_GET['email']);  
			    $key = mysqli_real_escape_string($dblink, $_GET['key']); 
			  
			  	
			    //check if the key is in the database  
			    $check_key = mysqli_query($dblink,"SELECT * FROM `confirm` WHERE `email` = '$email' AND `key` = '$key' LIMIT 1") or die(mysqli_error());  
			  	
			  	if($check_key){
			        //get the confirm info  
			        $confirm_info = mysqli_fetch_assoc($check_key);

			        //confirm the email and update the users database  
			        $update_users = mysqli_query($dblink,"UPDATE `users` SET `active` = 1 WHERE `email` = '$confirm_info[email]'");

			        //send out a welcome email
                    $to = $confirm_info[email];
                    $subject = "Thanks for joining Popboard!";
                    $message = "
                                <html>
                                <head>
                                </head>
                                <body style='margin:0;padding:0;'>
                                <table width='100%' style='font-family: helvetica-neue, arial;border-collapse: collapse;margin:0;padding:0;' border='0'>
                                    <thead>
                                        <tr style='background:#2070A1;'>
                                            <td style='padding:5px 10px'>
                                                <h1 style='color:#ffffff;text-shadow:0 1px  #333;'>Welcome to Popboard!</h1>
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style='padding:0 5px;'>
                                                <h3>Thanks for joining our growing community!</h3>
                                                <p>You can log in <a href='http://popboard.hallofhavoc.com'>here</a> and begin posting on our wall.</p>
                                                <p>Also, don't forget to update your profile and manage your posts.</p><br/>
                                                <p>If you have any questions, comments, concerns or suggestions please email them to <a href='mailto:support@popboard.hallofhavoc.com'>support@popboard.hallofhavoc.com</a></p>
                                                
                                            </td>
                                        <tr>
                                            <td>
                                               
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                </body>
                                </html>
                                ";
                    
                    // To send HTML mail, the Content-type header must be set
                    $headers ="MIME-Version: 1.0" . "\r\n";
                    $headers .= "X-Mailer: PHP/" .phpversion() ."\n";
                    $headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
                    $headers .= "From: noreply@popboard.hallofhavoc.com";
                    mail($to, $subject, $message, $headers);

			        //delete the confirm row  
			        $delete = mysqli_query($dblink,"DELETE FROM `confirm` WHERE `id` = '$confirm_info[id]' ");  

			        if($update_users){  
			  
			            echo '<p class="success">Your account has been confirmed. Thank-You!</p>';  
			  			echo '<h3>You can now sign in.</h3>';
			        }else{  
			   
			            echo'<p class="error">The user could not be updated Reason: '.mysqli_error().'</p>'; 
			  
			        }  
			    }else{
			    	echo '<p class="error">Oops! That account has already been activated or is not in our system.</p>';
			    }
			  
			   
			  
			}  
        ?>
		<form id="signinform" method="post" action="index.php">
            	<input class="userinput" type="text" name="username" placeholder="Username" />
                <input class="userinput" type="password" name="passwd" placeholder="Password" />
                <input type="submit" name="login" value="Sign In" class="submitbtn"/>
				
        </form>
        </div>
    </div><!--#bottom-->
       
</div><!--#container-->

</body>
</html>