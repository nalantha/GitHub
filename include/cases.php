<?php
include_once('dbconfig.php');
include_once('pacs_class.php');
class cases extends dbconfig{
   public static $output = array('status'=>"success",'msg'=>"");
   public function __construct(){
      parent::__construct();
      self::$output['msg']=
             array('error'=>"",'center_id'=>"",'case_id'=>"",
                   'patient_id'=>"",'clinician'=>"",'services'=>"",
                   'exam_date'=>"",'update_date'=>"",'submit_date'=>"");
   }

   public function register($input,$user_role){
      $input = self::htmltext($input);
      $result = self::validate($input,"register",$user_role);
      if ($result['status']=="success"){
         $today = date("Y-m-d");
         $sql = "INSERT INTO cases
              (case_id,center_id,patient_id,clinician,specialist, 
               radiologist,email,priority,services,exam_date, 
               submit_date,update_date,status,images_DICOM, 
               images,documents,labresult,center_comments, 
               radiologist_comments) VALUES 
               ('{$input['case_id']}','{$input['center_id']}', 
                '{$input['patient_id']}','{$input['clinician']}',
                '{$input['specialist']}','{$input['radiologist']}',
                '{$input['email']}','{$input['priority']}',
                '{$input['services']}', '{$input['exam_date']}',
                '$today','$today','submited','{$input['images_DICOM']}',
                '{$input['images']}','{$input['documents']}',
                '{$input['labresult']}','{$input['center_comments']}',
                '{$input['radiologist_comments']}')";
         if (!mysqli_query(parent::$db,$sql)){
             self::$output['status']="error";
             self::$output['msg']['error'] = mysqli_error(parent::$db); 
         }  
      }
      return self::$output;
   }

   public function update($input,$user_role,$case_id){
      $input = self::htmltext($input);
      $result = self::validate($input,"update",$user_role);
      if ($result['status']=="success"){
         $today = date("Y-m-d");
         $sql = 
            "UPDATE cases SET 
             center_id='{$input['center_id']}',patient_id='{$input['patient_id']}',
             clinician='{$input['clinician']}',specialist='{$input['specialist']}',
             radiologist='{$input['radiologist']}',email='{$input['email']}',
             priority='{$input['priority']}',
             exam_date='{$input['exam_date']}',update_date='$today', 
             status='{$input['status']}', images_DICOM='{$input['images_DICOM']}',
             images='{$input['images']}', documents='{$input['documents']}',
             labresult='{$input['labresult']}',
             center_comments='{$input['center_comments']}',
             radiologist_comments='{$input['radiologist_comments']}' 
             WHERE case_id='$case_id'";
         if (!mysqli_query(parent::$db,$sql)){
             self::$output['status']="error";
             self::$output['msg']['error'] = mysqli_error(parent::$db);
         }
      }
      return self::$output;
   }

   public function read_db($case_id){
      $sql = mysqli_query(parent::$db,"SELECT * FROM cases WHERE case_id = '$case_id'");
      $result = array('status'=>"",'data'=>"");
      if ($sql) {
         $casedata = mysqli_fetch_assoc($sql);
         $result['status'] = "success";
         $result['data'] = $casedata;
      } else {
         $result['status'] = "error";
         $result['data'] = mysqli_error(parent::$db);
      }
      return $result;
   }

   public function read_dicom($uid){
      $infoArray = array('PatientName', 'PatientID','StudyDate','InstitutionName',
                         'ModalitiesInStudy','StudyDescription','StudyInstanceUID',
                         'ReferringPhysicianName');
      $result = array();
      $pacs = new findpacs("SERIES","","",$uid,$infoArray);
      list($pacs_out, $No_response)=$pacs->output();
      $result['patientName']= $pacs_out[0]['PatientName']; 
      $result['InstitutionName'] = $pacs_out[0]['InstitutionName'];
      $result['ReferringPhysicianName'] = $pacs_out[0]['ReferringPhysicianName'];
      $temp = strtotime($pacs_out[0]['StudyDate']);
      $result['exam_date'] = date('Y-m-d',strtotime($pacs_out[0]['StudyDate']));
      $result['dicominput'] = explode(";",$dicomUID);
      $isdicom = FALSE;
      foreach ($dicominput as $value){
            if ($value == $pacs_out[0]['StudyInstanceUID']){
               $isdicom = TRUE;
            }
      }
       if (!$isdicom) {$DBimages_DICOM = $DBimages_DICOM. $pacs_out[0]['StudyInstanceUID'].";";
      }
      return $result;
   }

   private static function validate($input,$action,$user_role){
      if ($action != "update"){
         if (!strlen($input['patient_id'])){
            self::$output['status']="error";
            self::$output['msg']['patient_id']="required";
         }
         if (!strlen($input['case_id'])){
            self::$output['status']="error";
            self::$output['msg']['case_id']="required";
         }
         if ($user_role == "admin"){
            if (!strlen($input['center_id'])){
               self::$output['status']="error";
               self::$output['msg']['center_id']="required";
            }
         }
      }
 
      if (!strlen($input['clinician'])){
         self::$output['status']="error";
         self::$output['msg']['clinician']="required";
      }
      if ($action != "update"){
         if (!strlen($input['services'])){
            self::$output['status']="error";
            self::$output['msg']['services']="required";
         }
      }
      if (!strlen($input['exam_date'])){
         self::$output['status']="error";
         self::$output['msg']['exam_date']="required";
      }
      if (self::$output['status']=="error"){
         self::$output['msg']['error']="Complete all reqired fields.";
      }
      return self::$output;
   }

   public function createID(){
     $thisyear = date("Y");
     $lastindquery = mysqli_query(parent::$db, "SELECT MAX(ind) AS max FROM cases");
     if (mysqli_num_rows($lastindquery) >= 1){
          $last_ind=mysqli_fetch_assoc($lastindquery)['max'];
          $lastidquery = mysqli_query(parent::$db, "SELECT case_id AS last_id FROM cases WHERE ind='$last_ind'");
          $last_id=mysqli_fetch_assoc($lastidquery)['last_id'];
          if (explode("-",$last_id)[2]== $thisyear){
             $new_id = (int)explode("-",$last_id)[3]+1;
          } else {
             $new_id = 1;
          }
     } else {
          $new_id=1;
     }
     switch (TRUE){
         case ($last_ind<10): 
              $id = "PVDI-CASE-".$thisyear."-000".$new_id; break;
         case ($last_ind<100): 
              $id ="PVDI-CASE-".$thisyear. "-00".$new_id; break;
         case ($last_ind<1000): 
              $id ="PVDI-CASE-".$thisyear. "-0".$new_id; break;
         default: 
              $id = "PVDI-CASE-".$thisyear."-".$new_id; break;
     }
     return $id;
   }

   private static function htmltext($input){
      $input = array_map('trim',$input);
      $input = array_map('stripslashes',$input);
      $input = array_map('htmlspecialchars',$input);
      return $input;
   }
}

?>
