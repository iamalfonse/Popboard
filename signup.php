<?php
/**
 * signup.php
 *
 * Simple example of a user registration page.  Note that this version is leaving out
 * some important checks for SQL Insertion!
 *
 *CREATE TABLE users (
 * user_id  int unsigned NOT NULL AUTO_INCREMENT,
 * username varchar(30) NOT NULL,
 * displayname varchar(30) NOT NULL,
 * email    varchar(50) NOT NULL,
 * passwd   char(40) NOT NULL,
 * PRIMARY KEY (user_id),
 * UNIQUE KEY username (username)
 *)
 *
 *
 **/

/* Database params */
include("config.php");
$dblink = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

/* This will tell us to display an error message */
$have_error = false;
$errmsg     = "";

if (isset($_POST['submit'])) {

    /* Process the new user registration */
    $new_username  = strtolower(mysqli_real_escape_string($dblink, $_POST['new_username']));
    $validUsername = preg_match("/^([a-zA-Z0-9_]+)*$/, {$_POST['new_username']}");
    $display_username = mysqli_real_escape_string($_POST['new_username']);
    $new_pass      = mysqli_real_escape_string($dblink, $_POST['new_pass']);
    $new_pass_conf = mysqli_real_escape_string($dblink, $_POST['new_pass_conf']);
    $new_email     = mysqli_real_escape_string($dblink, $_POST['new_email']);
    $validEmail = preg_match("/^[^0-9][A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/, {$new_email}");
    $joindate      = date("Y-m-d");
    if($new_username == "" || $new_pass == "" || $new_pass_conf == "" || $new_email == ""){
    	$have_error = true;
        $errmsg = "Please fill in the required fields.";
        //header("Location: signup.php"); 
        //exit;
    }else if(!$validUsername){
        //username not valid
        $have_error = true;
        $errmsg = "The username you entered has invalid characters. Please enter only letters, numbers, or underscores.";
    }else if(!$validEmail){
        //email not valid
        $have_error = true;
        $errmsg = "The email you have entered is invalid. Please check it again.";
    }else {
        //valid username, password, password conf, and email

        if ($new_pass != $new_pass_conf) {
            $have_error = true;
            $errmsg = "Your password and password confirmation did not match!";
        } else {
            /* Open connection to the database */
            $dblink = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
            if (!$dblink) {
                $errmsg = "<p class='error'>*** Failure!  Unable to connect to database!" . mysqli_connect_errno()."</p>"; 
                $have_error = true;
            } else {
                /* Connection is okay... go ahead and try to add user to db. */
           
                /* Does this username already exist? */
                $query    = "SELECT * FROM users WHERE username='$new_username'";
                $results  = mysqli_query($dblink, $query);
                $num_rows = mysqli_num_rows($results);

                if ($num_rows != 0) {
                    /* This username is already taken! */
                    $have_error = true;
                    $errmsg = "That username is already taken.  Please select another.";   
                } else { 


                    /* Is this email already taken? */
                    $query    = "SELECT * FROM users WHERE email='$new_email'";
                    $results  = mysqli_query($dblink, $query);
                    $num_rows = mysqli_num_rows($results);

                    if($num_rows != 0){
                        /* This email is already taken! */
                        $have_error = true;
                        $errmsg = "That email is already in use.  Please select another.";   
                    }else{
                        /* Insert new row... */
                        $q2 = "INSERT INTO users(username, displayname, passwd, email, active, joindate) VALUES('$new_username', '$display_username', sha1('$new_pass'), '$new_email', '0', '$joindate')";
                        $r2 = mysqli_query($dblink, $q2);
                         
                        //user was added to database
                        //get the new user id  
                        $userid = mysqli_insert_id();  
                        //print_r($userid.'WTF Crap!');die; 
                        //create a random key  
                        $key = $new_username . $new_email . date('mY');  
                        $key = md5($key);

                        //add confirm row  
                        $confirm = mysqli_query($dblink, "INSERT INTO `confirm` VALUES(NULL,'$userid','$key','$new_email')"); 
 
                        //let's send the email  
                        $to = $new_email;
                        $subject = "Activate your Popboard account";
                        $message = "
                                    <html>
                                    <head>
                                    </head>
                                    <body style='margin:0;padding:0;'>
                                    <table width='100%' style='font-family: helvetica-neue, arial;border-collapse: collapse;margin:0;padding:0;' border='0'>
                                        <thead>
                                            <tr style='background:#2070A1;'>
                                                <td style='padding:5px 10px'>
                                                    <h1 style='color:#ffffff;text-shadow:0 1px  #333;'>Popboard</h1>
                                                </td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style='padding:0 5px;'>
                                                    <h3>Confirm Email!</h3>
                                                    <p>Before you can post on Popboard, we need to confirm your email.</p>
                                                    <p>Please click on the link below to activate your account.</p>
                                                    <p><a href='http://popboard.hallofhavoc.com/success?email=".$new_email."&key=".$key."' >Confirm Email</a></p>
                                                </td>
                                            <tr>
                                                <td></td>
                                                <td>
                                                    <p style='background-color:#F2F3F6;'>Sent by &copy;Popboard. Did you recieve this in error? If so, please ignore it.</p>
                                                </td>
                                                <td></td>
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

                        /* Successful registration!  Now redirect the user to the success page. */
                        header("Location: /success"); 
                        exit;
                        
                    }
                    
                }

                /* Close our connection to the database */
                mysqli_close($dblink);
            } 

    	}
    } 

}/* end if (isset(...) */
?>


  
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="/stylesheets/style1.css" rel="stylesheet" type="text/css" />
<link href='http://fonts.googleapis.com/css?family=Brawler' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
<script src="/js/cleartext.js" type="text/javascript"></script>
<script src="/js/jquery.validate.min.js" type="text/javascript"></script>

<title>Popboard</title>

</head>
<body>
<div id="container">
    <div id="top">
        <div id="logomenu">
            <h1><a href="/">Logo</a></h1>
            <div class="tab">
                <p><a href="/">Back</a></p>
            </div><!--.tab-->
        </div><!--#logomenu-->
    </div><!--#top-->
    <div id="bottom">
        
        <div id="signup">
            <div class="signup-banner"><h2>Popboard Sign Up</h2></div>
            <?php
                if ($have_error) {
                    echo "<p class='error'>$errmsg</p>\n";
                }
            ?>
            <form action="signup.php" method="post" id="signupform">
                <p>Username<br /><input class="signuptext userinput required new_username" type="text" name="new_username" size="20" />
                    <div id="nametaken"></div>
                </p>
                <p>Password<br /><input class="signuptext userinput required" type="password" name="new_pass" size="20" /></p>
                <p>Password Confirmation<br /><input class="signuptext userinput required" type="password" name="new_pass_conf" size="20" /></p>
                <p>Email<br /><input class="signuptext userinput required email" type="text" name="new_email" size="30" /></p>
                <p class='note'>Note: A verification email will be sent to this email address.</p>
                <input class="submitbtn" type="submit" name="submit" value="Register" />
            </form>
        
        </div><!--#signup-->
    </div><!--#bottom-->
</div><!--#container-->






<script type="text/javascript">
    
    $('.new_username').on('keyup', function(){
        $.ajax({
            url: "/checkusername",
            datatype: "html",
            data: {check_username: $('.new_username').val()},
            beforeSend: function() {
                
            },
            success: function(data) {
                $('#nametaken').html(data);
            }
        });
    });
    

    // var ajaxobj;
    
    // function startRequest() {
    //     //createXMLHttpRequest();
        
    //     var form_username = document.forms[0].new_username.value;
    //     var req_string = "checkusername.php?check_username=" + form_username;
        
    //     if (window.ActiveXObject) { //for IE
    //         ajaxobj = new ActiveXObject("Microsoft.XMLHTTP"); 
    //     } else if (window.XMLHttpRequest) { //for Other Browsers
    //         ajaxobj = new XMLHttpRequest();
    //     }
    //     //alert("createdXMLHttpRequest");
        
    //     ajaxobj.open("POST", req_string, true); 
    //     ajaxobj.onreadystatechange = handleResponse;
    //     ajaxobj.send(null);
    // }
    
    // function handleResponse() {
    //     if (ajaxobj.readyState == 4) { 
    //         if (ajaxobj.status == 200) {
    //             /* Do something appropriate here... */ 
    //             //alert("Got the response: " + ajaxobj.responseText);
    //             //document.write(ajaxobj.responseText);
    //             document.getElementById('nametaken').innerHTML=ajaxobj.responseText;
    //         }
    //     }
    // }
</script>

</body>
</html>