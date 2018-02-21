<?php
include_once('dbconfig.php');
class patient extends dbconfig{

   public static $output = array('status'=>"success",'msg'=>"");

   public function __construct(){
      parent::__construct();
      self::$output['msg']=
             array('error'=>"",'patient_id'=>"",'name'=>"",'center_id'=>"",'species'=>"",
                   'breed'=>"",'gender'=>"",'age'=>"",'age_unit'=>"",
                   'weight'=>"",'weight_unit'=>"",'center_ref'=>"",
                   'owner'=>"",'owner_email'=>"");
   }

   public function register($input,$user_role){
      $input=self::htmltext($input);
      $result=self::validate($input,"register",$user_role,$input['center_id'],"");
      if ($result['status']=="success"){
         $today = date("Y-m-d");
         $sql = "INSERT INTO patients
                    (patient_id,center_id, name, species, breed, gender, 
                     age, age_unit,weight, weight_unit, center_ref,
                     active, reg_date, owner, owner_email, owner_phone, 
                     owner_phone2, address) VALUES 
                     ('{$input['patient_id']}','{$input['center_id']}',
                      '{$input['name']}','{$input['species']}','{$input['breed']}',
                      '{$input['gender']}','{$input['age']}','{$input['age_unit']}',
                      '{$input['weight']}','{$input['weight_unit']}',
                      '{$input['center_ref']}','yes','$today','{$input['owner']}', 
                      '{$input['owner_email']}','{$input['owner_phone']}',
                      '{$input['owner_phone2']}','{$input['address']}')";
         if (!mysqli_query(parent::$db, $sql)) {
            self::$output['status']="error";
            self::$output['msg']['error']= mysqli_error(parent::$db);
         }         
      }
      return self::$output;
   }

   public function update($input,$user_role,$patient_id,$center_id,$name){
      $input=self::htmltext($input);
      $result=self::validate($input,"update",$user_role,$center_id,$name);
      if ($result['status']=="success"){
         $sql = "UPDATE patients SET 
                       name='{$input['name']}',species='{$input['species']}', 
                       breed='{$input['breed']}',gender='{$input['gender']}',
                       age='{$input['age']}',age_unit='{$input['age_unit']}',
                       weight='{$input['weight']}', weight_unit='{$input['weight_unit']}',
                       center_ref='{$input['center_ref']}',owner='{$input['owner']}', 
                       owner_email='{$input['owner_email']}',owner_phone='{$input['owner_phone']}', 
                       owner_phone2='{$input['owner_phone2']}', address='{$input['address']}' 
                       WHERE patient_id='$patient_id'";
         if (!mysqli_query(parent::$db, $sql)) {
            self::$output['status']="error";
            self::$output['msg']['error']= mysqli_error(parent::$db);
         }
      } 
      return self::$output;
   }
  
   public function createID(){
      $thisyear = date("Y");
      $lastindquery = mysqli_query(parent::$db, 
                       "SELECT MAX(ind) AS max FROM patients");
      if (mysqli_num_rows($lastindquery) >= 1){
         $last_ind=mysqli_fetch_assoc($lastindquery)['max']+1;
      } else {
          $last_ind=1;
      }
      switch (TRUE){
         case ($last_ind<10): 
              $id = "PVDI-".$thisyear."-000".$last_ind; 
              break;
         case ($last_ind<100): 
              $id ="PVDI-".$thisyear. "-00".$last_ind; 
              break;
         case ($last_ind<1000): 
              $id ="PVDI-".$thisyear. "-0".$last_ind; 
              break;
         default: 
              $id = "PVDI-".$thisyear."-".$last_ind; 
              break;
      }
      return $id;
   }   

   private static function validate($input,$action,$user_role,$center_id,$name){
      if ($action != "update"){
         if (!strlen($input['patient_id'])){
            self::$output['status']="error";
            self::$output['msg']['patient_id']="required";
         }
         if ($user_role == "admin"){
            if (!strlen($input['center_id'])){
               self::$output['status']="error";
               self::$output['msg']['center_id']="required";
            }
         }
      }
      if (!strlen($input['name'])){
         self::$output['status']="error";
         self::$output['msg']['name']="required";
      }
      if (!strlen($input['species'])){
         self::$output['status']="error";
         self::$output['msg']['species']="required";
      }
      if (!strlen($input['breed'])){
         self::$output['status']="error";
         self::$output['msg']['breed']="required";
      }
      if (!strlen($input['gender'])){
         self::$output['status']="error";
         self::$output['msg']['gender']="required";
      }  
      if (!strlen($input['age'])){
         self::$output['status']="error";
         self::$output['msg']['age']="required";
      }
      if (!strlen($input['age_unit'])){
         self::$output['status']="error";
         self::$output['msg']['age_unit']="required";
      }  
      if (!strlen($input['weight'])){
         self::$output['status']="error";
         self::$output['msg']['weight']="required";
      }
      if (!strlen($input['weight_unit'])){
         self::$output['status']="error";
         self::$output['msg']['weight_unit']="required";
      }
      if (!strlen($input['owner'])){
         self::$output['status']="error";
         self::$output['msg']['owner']="required";
      }
      if (strlen($input['owner_email'])){
         if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            self::$output['status']="error";
            self::$output['msg']['owner_email']="Invalid email";
         }
      }
      if (self::$output['status']=="success"){
         $patientquery = mysqli_query(parent::$db, "SELECT * FROM patients 
                                     WHERE name = '{$input['name']}' and center_id = '$center_id'");
         if ($patientquery) {
            if (mysqli_num_rows($patientquery)>= 1 && $input['name'] != $name){
               self::$output['status']="error";
               self::$output['msg']['error']="Patient name exists";
            }            
         } else {
            self::$output['status']="error";
            self::$output['msg']['error']=mysqli_error(parent::$db);
         }
      } else {
         self::$output['msg']['error']="Complete all required fields";
      }
      return self::$output;
   }

   private static function htmltext($input){
      $input = array_map('trim',$input);
      $input = array_map('stripslashes',$input);
      $input = array_map('htmlspecialchars',$input);
      return $input;
   }
}
?>
