<?php

require_once("dbconfig.php");
class user_login extends dbconfig{

   public static function login($username, $password){
      $username = self::htmltext($username);
      $password = self::htmltext($password);
      $usercheck = self::checkUser($username,$password);
      if ($usercheck['status']=="error"){
          $output = $usercheck;
      } else {
          $output = self::setSession($username, $password);
      }
      return $output;
   }

   // Check if username/password is incorrect and acount is active
   private static function checkUser($username, $password){
      $query = "SELECT * FROM members WHERE username = '$username'";
      $result = mysqli_query(parent::$db,$query);
      if (!$result){
          $queryErr = mysqli_error(parent::$db);
          $output = array('status'=>"error", 'msg'=>$queryErr);
      } else {
          $count = mysqli_num_rows($result);
          if ($count == 0) {
              $output = array('status'=>"error", 'msg'=>"Invalid username");
          } elseif ($count == 1) {
              $userdata = mysqli_fetch_assoc($result);
              $dbpassword = $userdata['password'];
              $active = $userdata['active'];
              if ($active == "yes" &&  password_verify($password,$dbpassword)) {
                   $output = array('status'=>"success", 'msg'=>"success");
              } elseif ($active != "yes" ){
                   $output = array('status'=>"error", 
                             'msg'=>"Your account is inactive. Contact admin.");
              } else {
                   $output = array('status'=>"error", 'msg'=>"Invalid password");
              }
          } else {
              $output = array('status'=>"error", 
                              'msg'=>"More than one user with same username");
          }
      }
      return $output;
   }

   private static function setSession($username,$password){
      $query = "SELECT * FROM members WHERE username = '$username'";
      $result = mysqli_query(parent::$db,$query);
      if (!$result){
          $queryErr = mysqli_error(parent::$db);
          $output = array('status'=>'error', 'msg'=>$queryErr);
      } else {
          $userdata = mysqli_fetch_assoc($result);
          $_SESSION['login_user'] = $username;
          $_SESSION['login_password'] = $password;
          $_SESSION['user_role'] =  $userdata['user_role'];
          $_SESSION['center_id'] = $userdata['center_id'];
          $output = array('status'=>'success', 'msg'=>'');
      }
      return $output;
   }

   private static function htmltext($input){
      $input = trim($input);
      $input = stripslashes($input);
      $input = htmlspecialchars($input);
      return $input;
   }

}
?>
