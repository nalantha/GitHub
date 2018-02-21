<?php
   include('dbconfig_copy.php');
   session_start();
   if(!isset($_SESSION['login_user'])){
       header("Location: login.php");
       exit();
   } else {
       $user_check = $_SESSION['login_user'];
       $ses_sql = mysqli_query($db,"select username from members where username = '$user_check' ");
       if (mysqli_num_rows($ses_sql) == 1){
          $row = mysqli_fetch_assoc($ses_sql);
          $login_session = $row['username'];
          setcookie(session_name(), $_COOKIE[session_name()], time() + 20*60);
       } else { 
          header("Location: login.php");
          exit();
       }
    }
?>
