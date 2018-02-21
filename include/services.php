<?php
require_once("dbconfig.php");
class service extends dbconfig{
   
   public function getcode(){
      $lastidquery = mysqli_query(parent::$db, "SELECT MAX(ind) AS max FROM services");
      $id=mysqli_fetch_assoc($lastidquery)['max']+1;
      if ($id <10){
           $newID = "item-00".$id;
      }elseif ($id <100){
           $newID = "item-0".$id;
      }else{
           $newID = "item-".$id;
      }
      return $newID;
   }

   public function add($input){
     $output = self::validate($input);
     if ($output['status']=="success"){
        $query = "INSERT INTO services 
                  (service_id, service, price, description) VALUES 
                  ('{$input['service_id']}', '{$input['service']}', 
                   '{$input['price']}', '{$input['description']}')"; 
        if (mysqli_query(parent::$db, $query)){
           $output = array('status'=>"success", 'msg'=>"");
        } else {
           $queryErr = mysqli_error(parent::$db);
           $output = array('status'=>"error", 'msg'=>$queryErr);
        }
     } 
     return $output;
   }

   public function update($input,$oldID){
     $output = self::validate($input,$oldID);
     if ($output['status']=="success"){
        $query = "UPDATE services SET 
                  service='{$input['service']}', 
                  price='{$input['price']}', 
                  description='{$input['description']}' 
                  WHERE service_id='{$input['service_id']}'";
        if (mysqli_query(parent::$db, $query)){
           $output = array('status'=>"success", 'msg'=>"");
        } else {
           $queryErr = mysqli_error(parent::$db);
           $output = array('status'=>"error", 'msg'=>$queryErr);
        }
     }
     return $output;
   }

   public function remove($service_id){
       $result = mysqli_query(parent::$db, 
                              "DELETE FROM services 
                               WHERE service_id = '$service_id'");
       if ($result) {
          $output = array('status'=>"success", 'msg'=>"");
       } else {
          $queryErr = mysqli_error(parent::$db);
          $output = array('status'=>"error", 'msg'=>$queryErr);
       }
       header("Location: services.php?action=start&output=$output");
   }

   public function cancel(){
       $output = array('status'=>"success", 'msg'=>"");
       header("Location: services.php?action=start&output=$output");
   }

   private static function validate($input, $oldID=""){
       $output = array('status'=>"success", 'msg'=>"");
       if (!strlen($input['service_id'])) {
          $output = array('status'=>"error", 'msg'=>"Fill all fields");
       } elseif (!preg_match("/^item-[0-9]{3}$/",$input['service_id'])){
          $output = array('status'=>"error", 
                          'msg'=>"item number shoud be \"item-000\" format");
       } else {
          $queryid = mysqli_query(parent::$db, "SELECT * FROM services 
                                        WHERE service_id = '$service_id'");
          if (mysqli_num_rows($queryid)>= 1 && $input['service_id'] != $oldID){
             $output = array('status'=>"error", 'msg'=>"Service code exists");
          }
       }
       if (!strlen($input['service'])) {
          $output = array('status'=>"error", 'msg'=>"Fill all fields");
       }
       if (!strlen($input['price'])) {
          $output = array('status'=>"error", 'msg'=>"Fill all fields");
       } elseif (!preg_match("/^-?[0-9]+(?:\.[0-9]{2})$/", $input['price'])){
          $output = array('status'=>"error", 
                          'msg'=>"Price should be in \"0.00\" format");
       }
       if (!strlen($input['description'])) {
          $output = array('status'=>"error", 'msg'=>"Fill all fields");
       }
       return $output;
   }
   
}
?>
