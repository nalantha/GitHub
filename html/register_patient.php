<!DOCTYPE HTML>
<?php
  include('../include/session.php');
  require_once('../include/patient.php');
  $patient_App = new patient;
  $Error = $patient_App::$output['msg'];
  $user_role = $_SESSION['user_role'];
  $center_id = $_SESSION['center_id'];
  $action = $_GET['action'];
  
  $species_list = 
     array(' ','Canine','Feline','Equine','Bovine','Caprine','Ovine',
           'Porcine','Avian','Ferret','Rabbit','Small Rodent','Amphibian',
           'Reptile','Fish','Exotic','Small Mammal/Other');
  $gender_list = 
     array(' ','Female','Male','Neutered, Male','Spayed, Female','Unknown'); 
  $weightunit_list = array('kg','lb');
  $ageunit_list = array('months','years');

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

  if ($action == "update"){
     $patient_id = $_GET['patient_id'];
     $sql = mysqli_query($db, "SELECT * FROM patients 
                              WHERE patient_id ='$patient_id'");
     $patientdata = mysqli_fetch_assoc($sql);
     $name = $patientdata['name'];
     $center_id = $patientdata['center_id'];
  }else{
     $thisyear = date("Y");
     $autoid = $patient_App->createID();
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
     $input = array('patient_id'=>"",'center_id'=>"",
               'name'=>$_POST['name'],'species'=>$_POST['species'],
               'breed'=>$_POST['breed'],'gender'=>$_POST['gender'], 
               'age'=>$_POST['age'],'age_unit'=>$_POST['age_unit'],
               'weight'=>$_POST['weight'],'weight_unit'=>$_POST['weight_unit'],
               'center_ref'=>$_POST['center_ref'],'owner'=>$_POST['owner'],
               'owner_email'=>$_POST['owner_email'],'owner_phone'=>$_POST['owner_phone'], 
               'owner_phone2'=>$_POST['owner_phone2'],'address'=>$_POST['address']);
      if ($action == "update"){
         $output = $patient_App->update($input,$user_role,$patient_id,$center_id,$name);
         if ($output['status']=="success"){
            header("Location: patient.php?patient_id=$patient_id");
         } else {
            $Error = $output['msg'];
         }  
      } else {
         $input['patient_id'] = $_POST['patient_id'];
         if ($user_role == "admin"){
            $input['center_id'] = $_POST['center_id'];
         } else {
            $input['center_id'] = $center_id;
         }
         $output = $patient_App->register($input,$user_role);
         if ($output['status']=="success"){
            header("Location: patientlist.php");
         } else {
            $Error = $output['msg'];
         }
      }
  }
?>


<html>
<head>
    <title>Patient Registration Form</title>
        <link rel="stylesheet" type="text/css" href="style/style-register.css">
</head>
<body>

<div class="table">
<form method="post">
<table border="0" width="500" align="center" class="register">
  <tr>
  <td colspan="2" class="heading"> <h2><?php 
   if ($action == "update") {echo "Patient Update";} else {echo "New Patient";}?></h2> </td>
  </tr>
  <tr>
  <td colspan="2" class="text"> <p><span class="error">* required field.</span></p> </td>
  </tr>
  <tr>
  <td class="text"> Patient ID: </td>
  <td class="input"><?php if ($action=="update") {echo $patient_id;}
      else {echo "<input type=\"text\" class=\"RegInputBox\" name=\"patient_id\" 
            value=\"";
            if(isset($_POST['patient_id'])) {echo $_POST['patient_id'];} 
            else {echo $autoid;}
            echo "\" readonly> <span class=\"error\">*";
            echo "{$Error['patient_id']} </span>";}?>
  </td>
  </tr>
  <tr>
  <td class="text"> Name : </td> 
  <td class="input"> <input type="text" class="RegInputBox" name="name" 
                      value="<?php if(isset($_POST['name'])) {echo $_POST['name'];
                              } elseif(isset($patientdata)) {echo $patientdata['name'];}?>">
  <span class="error">* <?php echo $Error['name'];?></span> </td>
  </tr>
  <?php
  if ($_SESSION['user_role'] == "admin"){
    echo "<tr> <td class=\"text\"> Center </td> <td class=\"input\">";
    echo "<select name=\"center_id\" id=\"center_id\" onchange=\"patientlist()\">";
    foreach($center_list as $key => $value){
           echo "<option ";
           if(isset($_POST['center_id'])) {if($_POST["center_id"] == $key) echo "selected ";
           } elseif(isset($patientdata)) {if($patientdata['center_id'] == $value) echo "selected ";}
           echo "value=\"$key\">$value</option>";
    }
    echo "</select>";
    echo "<span class=\"error\">*". $Error['center_id']."</span> </td>";
    echo "</td></tr>";
  }
  ?>
   </tr>
  <td class="text">Species:</td>
  <td class="input"><select  name="species" id="species">
      <?php
        foreach($species_list as $value){
      ?>
        <option <?php if(isset($_POST['species'])) {if($_POST["species"] == $value) echo "selected";
                      } elseif(isset($patientdata)){if($patientdata['species'] == $value) echo "selected";}?>  
                value="<?php echo $value ?>"><?php echo $value ?></option>
      <?php
       }
      ?>
     </select>
   <span class="error">* <?php echo $Error['species']; ?></span> </td>
  </tr>
   <tr>
  <td class="text"> Breed : </td>
  <td class="input"> <input type="text" class="RegInputBox" name="breed" 
                       value="<?php if(isset($_POST['breed'])) {echo $_POST['breed'];
                                    } elseif(isset($patientdata)){echo $patientdata['breed'];}?>">
  <span class="error">* <?php echo $Error['breed'];?></span> </td>
  </tr>
  </tr>
  <td class="text">Gender:</td>
  <td class="input"><select  name="gender" id="gender">
      <?php
        foreach($gender_list as $value){  
      ?> 
        <option <?php if(isset($_POST['gender'])) {if($_POST["gender"] == $value) echo "selected";
                      } elseif(isset($patientdata)) {if($patientdata['gender'] == $value) echo "selected";} ?>  
                value="<?php echo $value ?>"><?php echo $value ?></option>
      <?php
       }
      ?>     
     </select>
   <span class="error">* <?php echo $Error['gender']; ?></span> </td>
  </tr>
  <tr>
  <td class="text"> Age: </td>
  <td class="input"> <input type="text" class="MediumBox" name="age" 
                      value="<?php if(isset($_POST['age'])) {echo $_POST['age'];
                                   } elseif(isset($patientdata)) {echo $patientdata['age'];}?>">
  <select class=SmallBox name="age_unit" id="age_unit">
      <?php
        foreach($ageunit_list as $value){
      ?>
        <option <?php if(isset($_POST['age_unit'])) {if($_POST["age_unit"] == $value) echo "selected"; 
                      } elseif(isset($patientdata)){if($patientdata['age_unit'] == $value) echo "selected";}?>  
                value="<?php echo $value ?>"><?php echo $value ?></option>
      <?php
       }
      ?>
   </select>
  <span class="error">* <?php echo $Error['age'].$Error['age_unit'];?></span> </td>
  </tr>
  <tr>
  <td class="text"> Weight: </td>
  <td class="input"><input type="text" class="MediumBox" name="weight" 
                     value="<?php if(isset($_POST['weight'])) {echo $_POST['weight'];
                     } elseif(isset($patientdata)) {echo $patientdata['weight'];}?>">
  <select class=SmallBox name="weight_unit" id="weight_unit">
      <?php
        foreach($weightunit_list as $value){
      ?>
        <option <?php if(isset($_POST['weight_unit'])) {if($_POST["weight_unit"] == $value) echo "selected"; 
                      } elseif(isset($patientdata)) {if($patientdata['weight_unit'] == $value) echo "selected";}?>  
                value="<?php echo $value ?>"><?php echo $value ?></option>
      <?php
       }
      ?>
   </select>
  <span class="error">* <?php echo $Error['weight'].$Error['weight_unit'];?></span> </td>
  </tr>
   <tr>
  <td class="text"> Clinic Ref. No: </td>
  <td class="input"><input type="text" class="RegInputBox" name="center_ref" 
                     value="<?php if(isset($_POST['center_ref'])) {echo $_POST['center_ref'];
                     } elseif(isset($patientdata)) {echo $patientdata['center_ref'];}?>">
  <span class="error">* <?php echo $Error['center_ref'];?></span> </td>
  </tr>
  <?php
    if ($action == "update"){
      echo "<tr><td class=\"text\">Registered on</td>
                <td class=\"input\">".$patientdata['reg_date']." </td> </tr>";
    }
  ?>
  <tr><td colspan='2'><h3>Owner Contact Infomation</h3></td></tr>
     <tr>
  <td class="text"> Owner Name: </td>
  <td class="input"><input type="text" class="RegInputBox" name="owner" 
                     value="<?php if(isset($_POST['owner'])) {echo $_POST['owner'];
                                  } elseif(isset($patientdata)) {echo $patientdata['owner'];}?>">
  <span class="error">*<?php echo $Error['owner'];?></span></td>
  </tr>
  <tr>
  <td class="text"> Email: </td>
  <td class="input"><input type="text" class="RegInputBox" name="owner_email" 
                     value="<?php if(isset($_POST['owner_email'])) {echo $_POST['owner_email'];
                                  } elseif(isset($patientdata)) {echo $patientdata['owner_email'];}?>"></td>
  </tr>
  <td class="text"> Phone: </td>
  <td class="input"><input type="text" class="RegInputBox" name="owner_phone" 
                     value="<?php if(isset($_POST['owner_phone'])) {echo $_POST['owner_phone'];
                                  } elseif(isset($patientdata)) {echo $patientdata['owner_phone'];}?>"></td>    
  </tr>
  <td class="text"> Secondary Phone: </td>           
  <td class="input"><input type="text" class="RegInputBox" name="owner_phone2" 
                     value="<?php if(isset($_POST['owner_phone2'])) {echo $_POST['owner_phone2'];
                                  } elseif(isset($patientdata)) {echo $patientdata['owner_phone2'];}?>"></td>
  </tr>
  <td class="text"> Address: </td>
  <td class="input"><textarea name="address" rows="5" cols="40">
                     <?php if(isset($_POST['address'])) {echo $_POST['address'];
                           } elseif(isset($patientdata)) {echo $patientdata['address'];}?></textarea></td>
  </tr>
  <tr>
  <td colspan ="2" class="input"> <span class="error"> <?php echo $Error['error']; ?> </span></td>
  </tr>
  <tr>
  <td colspan="2">
  <input type="submit" name="Submit" value=<?php if ($action=="update"){echo "Change";} else{echo "Add";} ?>  class="button">
  <button type="button" class="button" onclick="location.href='patientlist.php'"> Cancel </button>
  </td>
  </tr>
  </table>
</form>
</div>
</body>
</html>
