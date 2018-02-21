<!DOCTYPE HTML>
<html>
<head>
   <title>Case Submit Form</title>
   <link rel="stylesheet" type="text/css" href="style/jquery-ui.css">
   <link rel="stylesheet" type="text/css" href="style/style-register.css">
   <script src="js/jquery-3.2.1.js"></script>
   <script src="js/jquery-ui.js"></script>  
   <script src="js/form_script.js" type="text/javascript"></script>
   <script>
       $( function() {
          $( ".date" ).datepicker({dateFormat:'yy-mm-dd'});
          $( ".date" ).datepicker( "option", "autosize", false );
       } );
   </script>
</head>

<?php
  include('../include/session.php');
  include('../include/cases.php');
  $cases_App = new cases;
  $Error = $cases_App::$output['msg'];

  $user_role = $_SESSION['user_role'];
  if (isset($_GET['action'])){
       $action = $_GET['action'];
  } else {
       $action = "register";
  }

  $center_id = $_SESSION['center_id'];

  if ($action == "update"){
     $case_id = $_GET['case_id'];
     $output = $cases_App->read_db($case_id);
     if ($output['status'] == "success") $casedata = $output['data'];
  } elseif ($action == "dicom") {
     $uid =$_GET['uid'];
     $autoid = $cases_App->createID();
     $dicomdata = $cases_App->read_dicom($uid);
  } else {
     $autoid = $cases_App->createID();
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
     $input =
             array('center_id'=>$_POST['center_id'],'case_id'=>"",
             'patient_id'=>$_POST['patient_id'],
             'clinician'=>$_POST['clinician'],'specialist'=>$_POST['specialist'],
             'radiologist'=>$_POST['radiologist'],'email'=>$_POST['email'],
             'priority'=>$_POST['priority'],'services'=>"",
             'exam_date'=>$_POST['exam_date'],'status'=>$_POST['status'],
             'images_DICOM'=>$_POST['images_DICOM'],'images'=>$_POST['images'],
             'documents'=>$_POST['documents'],'labresult'=>$_POST['labresult'],
             'center_comments'=>$_POST['center_comments'],
             'radiologist_comments'=>$_POST['radiologist_comments']);
     if ($action == "update"){
        $result = $cases_App->update($input,$_SESSION['user_role'],$case_id);
        if ($result['status']=="success"){
            $Error['error'] = "Update successfully.";
        } else {
            $Error = $result['msg'];
        }
     } else {
        foreach ($_POST['services'] as $service) {
           $input['services']=$input['services'].$service.",";
        }
        $input['case_id']=$_POST['case_id'];
        $result = $cases_App->register($input,$_SESSION['user_role']);
        if ($result['status']=="success"){
            $Error['error'] = "Update successfully.";
            header("Location: caseslist.php");
        } else {
            $Error = $result['msg'];
        }
     }
  }

  if ($_SESSION['user_role']=="admin"){
     $center_list = array();
     $result = mysqli_query($db, "SELECT * FROM members");
     if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            if ($row['user_role']=="user"){
               $center_list[$row['center_id']] = $row['center'];
            }
        }              
     }
  } 

  $patient_list = array();
  $result = mysqli_query($db, "SELECT * FROM patients");
  if (mysqli_num_rows($result) > 0) {
     while($row = mysqli_fetch_assoc($result)) {
            $patient_list[$row['patient_id']] = array($row['name'],$row['center_id']);
     }
  }
  
  $service_list = array();
  $service_list[0] = "Select services";
  $result = mysqli_query($db, "SELECT * FROM services");
  if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $service_list[$row['service_id']]= $row['service']." - ".$row['price'];
    }
  }
  if ($action == "update"){
     $service_ids = explode(",",$casedata['services']);
     $services = "";
     $result = mysqli_query($db, "SELECT * FROM services");
     while($row = mysqli_fetch_assoc($result)) {
          foreach ($service_ids as $id){
              if (trim($id) == trim($row['service_id'])) 
                   $services = $services.$row['service'].",";
          }
     }
  }
  $status_list = array('submited','examined','report');
  
?>

</head>
<body>
<script>
function patientlist(){
   var role = "<?php echo $_SESSION['user_role'] ?>";
   if (role == 'admin'){
        x = document.getElementById("center_id").value;
   } else {
        x = "<?php echo $center_id ?>";
   }
   var opts = "<?php if (isset($_POST['patient_id'])){echo $_POST['patient_id']; 
                     } elseif (isset($casedata)){echo $casedata['patient_id'];
                     } else {echo "";} ?>";
   var name = "<?php if (isset($dicomdata)){echo trim($dicomdata['patientName']);} else {echo "";} ?>";
   var list = document.getElementById("placepatient");
   while (list.firstChild){
      list.removeChild(list.firstChild);
   }
   var y = document.createElement("select");
   y.name = "patient_id";
   y.id = "patient_id";
   list.appendChild(y);
   var option = document.createElement("option");
   option.value="0";
   option.text="";
   y.add(option);
  <?php
   foreach($patient_list as $key => $value){
  ?>
           var option = document.createElement("option");
           option.value="<?= $key ?>";
           option.text="<?= htmlspecialchars($value[0]) ?>";
           if (opts=="<?= $key?>" || name=="<?= trim($value[0]) ?>") {
                option.selected = true;
           }
           if ("<?php echo $value[1]?>" == x ){
               y.add(option);
           }
   <?php } ?>
}
</script>
<div class="table">
<form method="post" name="case">
<table border="0" width="500" align="center" class="register">
  <tr>
  <td colspan="2" class="heading"> 
     <h2><?php if ($action == "update") {echo "Case Update";} 
               else {echo "New Case";}?></h2> 
  </td>
  </tr>
  <tr>
  </tr>
  <?php
  if ($_SESSION['user_role'] == "admin"){
    echo "<tr> <td class=\"text\"> Center </td> <td class=\"input\">";
    echo "<select name=\"center_id\" id=\"center_id\" onchange=\"patientlist()\">";
    foreach($center_list as $key => $value){
           echo "<option "; 
           if(isset($_POST['center_id'])) {if($_POST["center_id"] == $key) echo "selected ";
           } elseif (isset($casedata)) {if($casedata['center_id'] == $key) echo "selected ";
           } elseif (isset($dicomdata)) {if($dicomdata['InstitutionName'] == $value) echo "selected ";}
           echo "value=\"$key\">$value</option>";
    }
    echo "</select>";
    echo "</td></tr>"; 
  } 
  ?>
  <tr>
  <td class="text" > Case ID: </td>
  <td class="input"><?php if ($action == "update") {echo $case_id;}
      else {echo "<input type=\"text\" class=\"RegInputBox\" name=\"case_id\" value=\"";
            if(isset($_POST['case_id'])) {echo $_POST['case_id'];} else {echo $autoid;}
            echo "\" readonly> <span class=\"error\">*";
            echo $Error['case_id']." </span>";}?>
  </td>
  </tr>

  <tr>
  <td class="text">Patient Name:</td>
  <td class="input">
   <span id="placepatient"></span>
   <script> patientlist()</script>
   <span class="error">* <?php echo $Error['patient_id']; ?></span> </td>
  </tr>
  </tr>
  <td class="text">Case Status:</td>
  <td class="input"><select  name="status" id="status">
      <?php
        foreach($status_list as $value){
      ?>
        <option <?php if(isset($_POST['status'])) {if($_POST['status'] == $value) echo "selected";
                      } elseif(isset($casedata)){if($casedata['status'] == $value) echo "selected";}?>  
                value="<?php echo $value ?>"><?php echo $value ?></option>
      <?php
       }
      ?>
     </select></td>
  </tr>

   <tr>
  <td class="text"> Clinician : </td>
  <td class="input"> <input type="text" class="RegInputBox" name="clinician" 
                      value="<?php if(isset($_POST['clinician'])) {echo $_POST['clinician'];
                                   } elseif (isset($casedata)) {echo $casedata['clinician'];
                                   } elseif (isset($dicomdata)) {echo $dicomdata['clinician'];}?>">
   <span class="error">* <?php echo $Error['clinician']; ?></span> </td>  
  </tr>
   <tr>
  <td class="text"> Email : </td>
  <td class="input"> <input type="text" class="RegInputBox" name="email" 
                      value="<?php if(isset($_POST['email'])) {echo $_POST['email'];
                                   } elseif (isset($casedata)) {echo $casedata['email'];}?>">
  </tr>
 <tr>
  <td class="text">Priority:</td>
  <td class="input"><select name="priority">
        <option <?php if(isset($_POST['priority'])) 
                {if($_POST["priority"] == "Normal") echo "selected";
                } elseif (isset($casedata)) 
                {if($casedata["priority"] == "Normal") echo "selected";} ?> 
                value="Normal">Normal</option>
        <option <?php if(isset($_POST['priority'])) 
                {if($_POST["priority"] == "STAT") echo "selected";
                } elseif (isset($casedata))
                {if($casedata["priority"] == "STAT") echo "selected";} ?> 
                value="STAT">STAT-add $35.00</option>
      </select>
     </td>
  </tr>
  <?php if ($action != "update"){?>
   <tr>
  <td class="text">Services:</td>
  <td class="input"><select  name="services" id="services" onchange="select_service();">
      <?php
        foreach($service_list as $key => $value){
      ?>
        <option value="<?php echo $key ?>"><?php echo $value ?></option>
      <?php
       }
      ?>
     </select>
     </td>
  </tr>
  <?php }?>
  <tr>
  <?php if ($action == "update"){
     echo "<td class=\"text\">Services</td>";
  } else {
  echo "<td class=\"text\"></td>";
  } ?>
  <td class="input"><ul id="servicelist" class="servicelist"></ul>
     <span class="error">* <?php echo $Error['services']; ?></span> </td>
  </tr>
  <?php if ($action == "update"){ ?>
    <script> get_services("<?php echo $services?>"); </script>
  <?php }?>
  <tr>
  <td class="text"> Specialist: </td>
  <td class="input"><input type="text" class="RegInputBox"  name="specialist" 
                     value="<?php if(isset($_POST['specialist'])) {echo $_POST['specialist'];
                                  } elseif (isset($casedata)) {echo $casedata['specialist'];
                                  } elseif (isset($dicomdata)) {echo $dicomdata['ReferringPhysicianName'];}?>"></td>
  </tr>
  <tr>
  <td class="text"> Radiologist : </td>
  <td class="input"> <input type="text" class="RegInputBox" name="radiologist" 
                     value="<?php if(isset($_POST['radiologist'])) {echo $_POST['radiologist'];
                                  } elseif (isset($casedata)) {echo $casedata['radiologist'];}?>">
  </tr>
	  <td class="text"> Exam Date: </td>
  <td class="input"><input type="text" class="date" id="exam_date" name="exam_date" 
                    value="<?php if(isset($_POST['exam_date'])) {echo $_POST['exam_date'];
                                 } elseif(isset($casedata)) {echo $casedata['exam_date'];
                                 } elseif(isset($dicomdata)) {echo $dicomdata['exam_date'];}?>" readonly> 
  <span class="error">* <?php echo $Error['exam_date']; ?></span> </td>
  </tr>

 <?php
   if ($action == "update"){
      echo "<tr> <td class=\"text\">Submitted on </td>";
      echo "<td class=\"input\">";
      if (isset($casedata['submit_date'])) echo $casedata['submit_date'];
      echo "</td></tr>";
      echo "<tr> <td class=\"text\">Last update </td>";
      echo "<td class=\"input\">";
      if (isset($casedata['update_date'])) echo $casedata['update_date'];
      echo "</td></tr>";
   }
 ?>
 <tr>
  <td class="text"> DICOM files: </td>
  <td class="input"><ul id="dicomlist" class="imagelist">
  <li> <input type="file" id="myFile"> </li>  </ul>
   
  <?php if ($action == "dicom") {?>
      <script> add_dicom("<?php echo $uid; ?>"); </script>
  <?php  }  ?>
  </td>
  </tr>

  <tr>
  <td class="text"> Image files: </td>
  <td class="input"><input type="hidden" class="RegInputBox" name="images" 
                     value="<?php if(isset($_POST['images'])) {echo $_POST['images'];
                                  } elseif(isset($casedata)) {echo $casedata['images'];}?>" readonly>
  <?php
    //$i=0;
    //foreach ($dicominput as $value){
    //  $i++;
    //  echo "<button type=\"button\" class=\"button\" onclick=\"location.href='dicom_open.php?uid=" .$value ."'\">DICOM ".$i."</button>";
    //}
  ?>

  </td>
  </tr>
  
  <tr>
  <td class="text"> Documents: </td>
  <td class="input"><input type="hidden" class="RegInputBox" name="documents" 
                     value="<?php if(isset($_POST['documents'])) {echo $_POST['documents'];
                                  } elseif(isset($casedata)) {echo $casedata['documents'];}?>" readonly>
  </td></tr>

  <tr>
  <td class="text"> Lab Results: </td>
  <td class="input"><input class="RegInputBox" name="labresult" 
                     value="<?php if(isset($_POST['labresult'])) {echo $_POST['labresult'];
                                  } elseif(isset($casedata)) {echo $casedata['labresult'];}?>" readonly>
  </td></tr>

  <tr>
  <td class="text"> Clinic Comments: </td>
  <td class="input"><input class="RegInputBox" name="center_comments" 
                     value="<?php if(isset($_POST['center_comments'])) {echo $_POST['center_comments'];
                                  } elseif(isset($casedata)) {echo $casedata['center_comments'];}?>" readonly>
  </td></tr>

  <tr>
  <td class="text"> Rediologist Comments: </td>
  <td class="input"><input  class="RegInputBox" name="radiologist_comments" 
                     value="<?php if(isset($_POST['radiologist_comments'])) {echo $_POST['radiologist_comments'];
                                  } elseif(isset($casedata)) {echo $casedata['radiologist_comments'];}?>" readonly>
  </td></tr>
  <tr><td colspan="2"> <?php echo $Error['error'];?></td></tr>
  <tr>
  <td colspan="2" text-align="center">
  <?php 
    if ($action == "update"){
       echo "<input type=\"submit\" name=\"Submit\" value=\"Change\" class=\"button\">";
       echo "<button type=\"button\" class=\"button\" 
             onclick=\"location.href='case.php?case=patient&case_id=".$case_id."'\"> Cancel </button>";
    } else {
       echo "<input type=\"submit\" name=\"Submit\" value=\"Submit\" class=\"button\">";
       echo "    ";
       echo "<button type=\"button\" class=\"button\" onclick=\"location.href='patientlist.php'\"> Cancel </button>";
    }
   ?>
  </td>
  </tr>
  </table>
</form>
</div>

</body>
</html>
