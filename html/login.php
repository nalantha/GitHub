<?php
   include("../include/user_login.php");
   session_start();
   $login_App = new user_login;
   $error=" ";
   if($_SERVER["REQUEST_METHOD"] == "POST") {
      // username and password sent from form 
      $username = $_POST['username'];
      $password = $_POST['password'];
      
      $login = $login_App->login($username,$password); 
      // If result matched $myusername and $mypassword, table row must be 1 row
      if ($login['status']=="success"){
         header("location: ./index.php");     
      } else {
         $error = $login['msg'];
      }
   }
?>
<html>
	<head>
		<title>Sign-In</title>
		<link rel="stylesheet" type="text/css" href="style/style-sign.css">
                <base target="_parent">

	</head>
	<body id="body-color">
		<center><img src="images/logo-pvdi-index.png" style="max-width:350px;width:35%; "></center>
		<div id="Sign-In">
		<fieldset style="width:80%; margin: 5% auto 0 auto;"><legend>Login</legend>
		<form method="POST" action=""  autocomplete="off">
			<label>Username </label><input type="text" name="username" size="30" required/><br />
			<label>Password </label><input type="password" name="password" size="30" required/><br />
			<input id="button" type="submit" name="submit" value="Log-In"/>
                        <button type="button" id="button"  onclick="location.href='register_user.php'">Register </button>
		</form>
                
		<div style = "font-size:11px; color:#cc0000; margin-top:10px"><?php echo $error; ?></div>
		</fieldset>
		</div>
	</body>
</html> 




