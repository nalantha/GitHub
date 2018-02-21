<?php
   include('../include/session.php');
?>

<!DOCTYPE html>
<html>
<head>
   <link rel="stylesheet" type="text/css" href="style/style-tables.css">
   <script type="text/javascript">
        if (frameElement == null) {
            window.location = "index.php";
        }
    </script>
    <style>
      @media screen and (max-width: 600px){
         td:nth-of-type(1):before { content: "Patient"; }
         td:nth-of-type(2):before { content: "PACS ID";}
         td:nth-of-type(3):before { content: "Study date"; }
         td:nth-of-type(4):before { content: "Institution"; }
         td:nth-of-type(5):before { content: "Modality"; }
         td:nth-of-type(6):before { content: "Description"; }
         td:nth-of-type(7):before { content: " "; }

         table.search td:nth-of-type(1):before { content: "Center"; }
         table.search td:nth-of-type(2):before { content: "Patient"; }
         table.search td:nth-of-type(3):before { content: "Start Date"; }
         table.search td:nth-of-type(4):before { content: "End Date"; }
         table.search td:nth-of-type(5):before { content: ""; }
      }
    </style>
</head>
<body>

   <?php
      $patient='';
      $user_role = $_SESSION['user_role'];
      $center_id = $_SESSION['center_id'];
      if ($_SESSION['user_role']=="admin"){
            $center='';
            $center_list = array();
            $center_list[0] = "";                 
            $result = mysqli_query($db, "SELECT * FROM members");
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    if ($row['user_role']=="user"){
                          $center_list[$row['center_id']] = $row['center'];
                    }
                }
            }
      } else {
           $center_sql = mysqli_query($db, "SELECT * FROM members WHERE center_id='$center_id'");
           $userrow = mysqli_fetch_assoc($center_sql);
           $center = "'".$userrow['center']."'";
      }
      $patient_list = array();
      $patient_list[0] = array("","");
      $result = mysqli_query($db, "SELECT * FROM patients");
      if (mysqli_num_rows($result) > 0) {
          while($row = mysqli_fetch_assoc($result)) {
              $patient_list[$row['patient_id']] = array($row['name'],$row['center_id']);
          }
      }

   
   ?>

   <?php
   if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if (isset($_POST['center'])){$center = "'".$center_list[$_POST['center']]."'";};
      if (isset($_POST['patient'])){$patient = "'".$patient_list[$_POST['patient']][0]."'";};
   ?>
   <?php } ?>
   
<script>
function patientlist(){
   var role = "<?php echo $_SESSION['user_role'] ?>";
   if (role == 'admin'){
        x = document.getElementById("center").value;
   } else {
        x = "<?php echo $center_id ?>";
   }
   var opts = "<?php if (isset($_POST['patient'])){echo $_POST['patient'];} else {echo "";} ?>";
   var list = document.getElementById("placepatient");
   while (list.firstChild){
      list.removeChild(list.firstChild);
   }
   var y = document.createElement("select");
   y.name = "patient";
   y.id = "patient";
   list.appendChild(y);
  <?php
   foreach($patient_list as $key => $value){
  ?>
           var option = document.createElement("option");
           option.value="<?= $key ?>";
           option.text="<?= htmlspecialchars($value[0]) ?>";

           if ("<?= $key ?>"==opts){
                option.selected = true;
           }
           if (x==0){
               y.add(option);
           }
           else {
               if ("<?php echo $value[1]?>" == x  || "<?php echo $value[1]?>" == ""){
                   y.add(option);
               }
           }
   <?php } ?>
}
</script>
   <div>
   <form method="post">
   <table class="search">
   <thead>
   <tr>
   <?php
      if ($_SESSION['user_role'] == "admin"){
       echo "<td> Clinic </td>";
      }
   ?>
   <td>Patient Name:</td>
   <td> Start Date </td>
   <td> End Date </td>
   <td>  </td>
   </tr>
   </thead>
   <tbody>
   <tr>
   <?php
   if ($_SESSION['user_role'] == "admin"){
       echo "<td>";
       echo "<select name=\"center\" id=\"center\" onchange=\"patientlist()\">";
       foreach($center_list as $key => $value){
           echo "<option ";
           if(isset($_POST['center'])) {if($_POST["center"] == $key) echo "selected ";
           } else {if($center == $key) echo "selected ";}
           echo "value=\"$key\">$value</option>";
       }
       echo "</select>";
       echo "</td>";
   }

   ?>
  <td>
   <span id="placepatient"></span>
   <script> patientlist()</script>
   </span> </td>
   <td> <input type="text" id="begin_date"> </td>
   <td> <input type="text" id="end_date"> </td> 
   <td>
   <input type="submit" name="submit" class="button" value="Search"></td>
   </tr>
   </tbody>
   </table>
   </form>
   </div>   
<div>
   <?php
   include('../include/pacs_class.php');
   $studyUID="";
   $infoArray=array('PatientName', 'PatientID','StudyDate','InstitutionName','ModalitiesInStudy','StudyDescription','StudyInstanceUID');
   $result = new findpacs("STUDY",$center,$patient,$studyUID,$infoArray);
   list($pacs_out, $No_response)=$result->output();
       echo "<table class=\"list\">";
       echo "<thead>";
       echo "<tr>";
       echo "<th id=\"patient\">Patient Name</td>";
       echo "<th id=\"id\">PACS ID</td>";
       echo "<th id=\"date\">Study Date</td>";
       echo "<th id=\"institution\">Institution</td>";
       echo "<th id=\"modality\">Modality</td>";
       echo "<th id=\"descript\">Study Description</td>";
       echo "<th id=\"button\">              </td>";
       echo "<tr>";
       echo "</thead>";
       echo "<tbody>";
    for ($i=0; $i < $No_response; $i++){
       echo "<tr>";
       //$pacs_out[$i]['StudyDate']= strtotime('Y-m-d', $pacs_out[$i]['StudyDate']);
       foreach($infoArray as $value){
           if ($value !=  "StudyInstanceUID"){
              if (isset($pacs_out[$i][$value])){
                  if (strlen($pacs_out[$i][$value])){
                     echo "<td>".$pacs_out[$i][$value]."</td>";
                  } else {
                     echo "<td>"."-"."</td>";
                  }
              } else {
                  echo "<td>"."-"."</td>";
              }
           }
       }
       $UID = test_input($pacs_out[$i]['StudyInstanceUID']);
       echo "<td> <button type=\"button\" class=\"button\" onclick=\"location.href='dicom_open.php?uid=" .$UID ."'\">DICOM </button>";
       echo "<button type=\"button\" class=\"button\" onclick=\"location.href='register_case.php?action=dicom&uid=" .$pacs_out[$i]['StudyInstanceUID'] ."'\">Register </button> </td>";
       echo "<tr>";
    }
    echo "</tbody>";
    echo "</table>";

    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
?>
 </div>
</body>
</html>
