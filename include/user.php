<?php
require_once("webcaluser.php");
require_once("dbconfig.php");
class user extends dbconfig{

   public static $output = array('status'=>"success",'msg'=>"");
   public static $output_password = array('status'=>"success",'msg'=>"");
   public static $webcal_App; 

   public function __construct(){
      parent::__construct();
      self::$output['msg']= 
           array('error'=>"",'center_id'=>"",'center'=>"",'center_type'=>"",
                 'email'=>"",'website'=>"",'phone'=>"",'street1'=>"",
                 'city'=>"",'country'=>"",'province'=>"",
                 'postcode'=>"",'username'=>"",'password'=>"",'terms'=>"");
      self::$output_password['msg'] =
           array('error'=>"",'password'=>"",'newpassword'=>"",
                 'confirm_newpassword'=>"");
      self::$webcal_App = new webcal; 
   }
      
   public function request(){
     $input = array('center'=>$_POST['center'],
           'center_type'=>$_POST['center_type'],'email'=>$_POST['email'],
           'website'=>$_POST['web'],'phone'=>$_POST['phone'],
           'mphone'=>$_POST['mphone'],'tfphone'=>$_POST['tfphone'],
           'fax'=>$_POST['fax'],'street1'=>$_POST['street1'],
           'street2'=>$_POST['street2'],'city'=>$_POST['city'],
           'country'=>$_POST['country'],'province'=>$_POST['province'],
           'postcode'=>$_POST['postcode'],'username'=>$_POST['username'],
           'password'=>$_POST['password'],
           'confirm_password'=>$_POST['confirm_password'],
           'terms'=> isset($_POST['terms']));
      $input = self::htmltext($input);
      $result = self::validate($input,"request");
      if ($result['status']=="success") {
         $query = "INSERT INTO tempmembers 
                   (center, center_type, email, web, phone, mphone, tfphone, fax, 
                   street1,street2, city, country, province, postcode,
                   username, password) VALUES 
                   ('{$input['center']}', '{$input['center_type']}', 
                    '{$input['email']}','{$input['website']}',
                    '{$input['phone']}','{$input['mphone']}','{$input['tfphone']}',
                    '{$input['fax']}','{$input['street1']}','{$input['street2']}', 
                    '{$input['city']}','{$input['country']}','{$input['province']}',
                    '{$input['postcode']}','{$input['username']}','{$input['password']}')"; 
         if (mysqli_query(parent::$db, $query)) {
            self::$output['status'] = "success";
            self::$output['msg']['error']="";
         } else {
            $queryErr = mysqli_error(parent::$db);
            self::$output['status'] = "error";
            self::$output['msg']['error']=$queryErr;
         }
      }
      return self::$output;
   }

   public function register($center,$username){
      $input = array('center'=>$_POST['center'],
           'center_id'=>$_POST['center_id'],
           'center_type'=>$_POST['center_type'],
           'user_role'=>$_POST['user_role'],
           'email'=>$_POST['email'],'website'=>$_POST['website'],
           'phone'=>$_POST['phone'],'mphone'=>$_POST['mphone'],
           'tfphone'=>$_POST['tfphone'],'fax'=>$_POST['fax'],
           'street1'=>$_POST['street1'],'street2'=>$_POST['street2'],
           'city'=>$_POST['city'],'country'=>$_POST['country'],
           'province'=>$_POST['province'],'postcode'=>$_POST['postcode'],
           'username'=>$_POST['username']);

      $input = self::htmltext($input);
      $result = self::validate($input,"register");
      if ($result['status']=="success") {
         $querypassword = mysqli_query(parent::$db, "SELECT * FROM tempmembers
                                       WHERE center = '$center' and username ='$username'");
         if (mysqli_num_rows($querypassword)>=1){
            $temppassword = mysqli_fetch_assoc($querypassword)['password'];
         }else{
            $temppassword = $input["username"];
         }
         $option = ['cost' => 12,];
         $password = password_hash($temppassword,PASSWORD_BCRYPT, $option);
         $today = date("Y-m-d");
         $query = "INSERT INTO members 
                   (center, center_type, center_id, user_role, email, web, phone, mphone, 
                   tfphone, fax, street1,street2, city, country, province, postcode,
                   username, password,active,reg_date) VALUES 
                   ('{$input['center']}', '{$input['center_type']}', '{$input['center_id']}',
                    '{$input['user_role']}','{$input['email']}','{$input['website']}',
                    '{$input['phone']}','{$input['mphone']}','{$input['tfphone']}',
                    '{$input['fax']}','{$input['street1']}','{$input['street2']}', 
                    '{$input['city']}','{$input['country']}','{$input['province']}',
                    '{$input['postcode']}','{$input['username']}','$password',
                    'yes','$today')"; 
         if (mysqli_query(parent::$db, $query)) {
            self::$output['status'] = "success";
            self::$output['msg']['error']="";
            mysqli_query(parent::$db, "DELETE FROM tempmembers 
                         WHERE username = '{$input['username']}'");
            if ($input['user_role'] == "admin") {$is_admin = "Y";}
            else {$is_admin = "N";}
            self::$webcal_App->add_user($input['center_id'],$temppassword,$input['center'],
                            $input['center_id'],$input['email'],$is_admin);
         } else {
            $queryErr = mysqli_error(parent::$db);
            self::$output['status'] = "error";
            self::$output['msg']['error']=$queryErr;
         }
      }
      return self::$output;
   }

   public function update($center, $center_id,$user_role,$username){
      $input = array('center'=>$_POST['center'],'center_id'=>"",
           'center_type'=>$_POST['center_type'],'user_role'=>"",
           'email'=>$_POST['email'],'website'=>$_POST['website'],
           'phone'=>$_POST['phone'],'mphone'=>$_POST['mphone'],
           'tfphone'=>$_POST['tfphone'],'fax'=>$_POST['fax'],
           'street1'=>$_POST['street1'],'street2'=>$_POST['street2'],
           'city'=>$_POST['city'],'country'=>$_POST['country'],
           'province'=>$_POST['province'],'postcode'=>$_POST['postcode'],
           'username'=>$_POST['username']);
      if ($user_role == "admin") $input['user_role']=$_POST['user_role'];

      $input = self::htmltext($input);
      $userquery = mysqli_query(parent::$db, "SELECT * FROM members 
                                     WHERE center_id = '$center_id'");
      $userdata = mysqli_fetch_assoc($userquery);
      $result = self::validate($input,"update",$username,$center);
      if ($result['status']=="success") {
         if ($user_role == "admin") {
            $input['user_role'] =$userdata['user_role'];
         }
         $query = "UPDATE members SET 
                  center='{$input['center']}', center_type='{$input['center_type']}', 
                  user_role='{$input['user_role']}', email='{$input['email']}', 
                  web='{$input['website']}', phone='{$input['phone']}', 
                  mphone='{$input['mphone']}',tfphone='{$input['tfphone']}', 
                  fax='{$input['fax']}',street1='{$input['street1']}', 
                  street2='{$input['street2']}', city='{$input['city']}', 
                  country='{$input['country']}', province='{$input['province']}', 
                  postcode='{$input['postcode']}', username='{$input['username']}' 
                  WHERE center_id = '$center_id'";
         if (mysqli_query(parent::$db, $query)) {
            self::$output['status'] = "success";
            self::$output['msg']['error']="";
            if ($input['user_role'] == "admin") {$is_admin = "Y";}
            else {$is_admin = "N";}
            self::$webcal_App->update_user ($center_id, $input['center'], 
                               $input['email'], $is_admin );
         } else {
            $queryErr = mysqli_error(parent::$db);
            self::$output['status'] = "error";
            self::$output['msg']['error']=$queryErr;
         }         
      }
      return self::$output;
   }

   public function change_password($input,$center_id){
      $input = self::htmltext($input);
      $result = self::validate_password_input($input);
      if ($result['status'] == "success"){
         $userquery = mysqli_query(parent::$db, "SELECT * FROM members 
                                     WHERE center_id = '$center_id'");
         if ($userquery) {
            $userdata = mysqli_fetch_assoc($userquery);
            $count = mysqli_num_rows($userquery);
            if ($count == 1 && password_verify($input['password'],$userdata['password'])) {
               $option = ['cost' => 12,];
               $newpassword = password_hash($input['newpassword'],PASSWORD_BCRYPT, $option);
               $insertsql = "UPDATE members SET password='$newpassword' 
                             WHERE center_id = '$center_id'";
               if (mysqli_query(parent::$db, $insertsql)) {
                  self::$webcal_App->update_user_password ( $center_id, $input['newpassword']);
                  $_SESSION['login_password'] = $input['newpassword'];
                  self::$output_password['msg']['error']= 
                                $center_id." - Password is successfuly updated";
               } else {
                  self::$output_password['status'] = "error";
                  self::$output_password['msg']['error']= mysqli_error(parent::$db);
               }    
            } else {
               self::$output_password['status'] = "error";
               self::$output_password['msg']['error']="Error: Current password not matching";
            }
         } else {
            $queryErr = mysqli_error(parent::$db);
            self::$output_password['status'] = "error";
            self::$output_password['msg']['error']=$queryErr;
         }
       } else {
         self::$output_password = $result;
       }
       return self::$output_password;
   }

   private static function validate_password_input($input){
      if (!strlen($input['password'])){
         self::$output_password['status']="error";
         self::$output_password['msg']['password']="required";
      }
      if (!strlen($input['newpassword'])){
         self::$output_password['status']="error";
         self::$output_password['msg']['newpassword']="required";
      }
      if (!strlen($input['confirm_newpassword'])){
         self::$output_password['status']="error";
         self::$output_password['msg']['confirm_newpassword']="required";
      }
      if($input['newpassword'] != $input['confirm_newpassword']){
         self::$output_password['status']="error";
         self::$output_password['msg']['confirm_newpassword']="not matching";
      }
      return self::$output_password;
   }

   private static function validate($input, $action, $username="", $center=""){
      if ($action == "register"){
         if (!strlen($input['center_id'])){
            self::$output['status']="error";
            self::$output['msg']['center_id']="required";
         }
      }
      if (!strlen($input['center'])){
         self::$output['status']="error";
         self::$output['msg']['center']="required";
      }
      if (!strlen($input['center_type'])){
         self::$output['status']="error";
         self::$output['msg']['center_type']="required";
      }
      if (!strlen($input['email'])){
         self::$output['status']="error";
         self::$output['msg']['email']="required";
      } elseif (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
         self::$output['status']="error";
         self::$output['msg']['email']="Invalid email";
      }
      if (strlen($input['website'])){
         if (!filter_var($input['website'],FILTER_VALIDATE_URL)){
            self::$output['status']="error";
            self::$output['msg']['website']="* Invalid URL";
         }
      }
      if (!strlen($input['phone'])){
         self::$output['status']="error";
         self::$output['msg']['phone']="required";
      }
      if (!strlen($input['street1'])){
         self::$output['status']="error";
         self::$output['msg']['street1']="required";
      }
      if (!strlen($input['city'])){
         self::$output['status']="error";
         self::$output['msg']['city']="required";
      }
      if (!strlen($input['country'])){
         self::$output['status']="error";
         self::$output['msg']['country']="required";
      }
      if ($input["country"]=="CA" || $input["country"]=="US" || 
                                       $input["country"]=="MX"){
         if (!strlen($input['province'])){
            self::$output['status']="error";
            self::$output['msg']['province']="*required";
         }
      }
      if ($input["country"]=="CA" || $input["country"]=="US"){
         if (!strlen($input['postcode'])){
            self::$output['status']="error";
            self::$output['msg']['postcode']="*required";
         }
      }
      if (!strlen($input['username'])){
         self::$output['status']="error";
         self::$output['msg']['username']="required";
      }
      if ($action == "request"){
         if (!strlen($input['password'])){
            self::$output['status']="error";
            self::$output['msg']['password']="required";
         }
         /* Password Matching Validation */
         if($input['password'] != $input['confirm_password']){
            self::$output['status']="error";
            self::$output['msg']['password']="not matching";         
         }
         if ($input['terms']!=1) {
            self::$output['status']="error";
            self::$output['msg']['terms']="Accept terms and conditions.";
         }
         if (self::$output['status']=="error"){
            self::$output['msg']['error']="Please fill required fields";
         } else {
            self::checkUser($input['username'],$input['center'],$username,$center);
            if ($action == "request") {
               self::checkTempUser($input['username'],$input['center']);
            }
         }
      }
      return self::$output;
   }

   private static function checkTempUser($username,$center){
      $userquery = mysqli_query(parent::$db, "SELECT * FROM tempmembers 
                                             WHERE username = '$username'");
      $centerquery = mysqli_query(parent::$db, "SELECT * FROM tempmembers 
                                             WHERE center = '$center'");
      if (mysqli_num_rows($userquery)>= 1){
         self::$output['status']="error";
         self::$output['msg']['error']="Username exists";
      } elseif (mysqli_num_rows($centerquery)>= 1){
         self::$output['status']="error";
         self::$output['msg']['error'] = "Clinic registration is waiting for approval";
      }
   }

   private static function checkUser($username,$center,$old_username,$old_center){
       $queryuser = mysqli_query(parent::$db,"SELECT * FROM members 
                                           WHERE username = '$username'");
       $querycenter = mysqli_query(parent::$db, "SELECT * FROM members 
                                             WHERE center = '$center'");
       if (mysqli_num_rows($queryuser)>= 1 && $username != $old_username){
          self::$output['status']="error";
          self::$output['msg']['error']="Username exists";
       } elseif (mysqli_num_rows($querycenter)>= 1 && $center != $old_center){
          self::$output['status']="error";
          self::$output['msg']['error']="Center already registerd";
       }
   }

   private static function htmltext($input){
      $input = array_map('trim',$input);
      $input = array_map('stripslashes',$input);
      $input = array_map('htmlspecialchars',$input);
      return $input;
   }

   public function createID(){
     $lastindquery = mysqli_query(Parent::$db, "SELECT MAX(ind) AS max FROM members");
     if (mysqli_num_rows($lastindquery) >= 1){
         $last_ind=mysqli_fetch_assoc($lastindquery)['max']+1;
     } else {
         $last_ind=1;
     }
     switch (TRUE){
         case ($last_ind<10): $id = "000".$last_ind; break;
         case ($last_ind<100): $id = "00".$last_ind; break;
         case ($last_ind<1000): $id = "0".$last_ind; break;
         default: $id = $last_ind; break;
     }   
     return $id;
   }
}
?>
