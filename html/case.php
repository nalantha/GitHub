<!DOCTYPE HTML>
<?php
  include('../include/session.php');
  $user_role = $_SESSION['user_role'];
  $case_id = $_GET['case_id'];
  $pre_page = $_GET['page'];
  $sql = mysqli_query($db, "SELECT * FROM cases WHERE case_id = '$case_id'");
  $casedata = mysqli_fetch_assoc($sql);
  $center_id = $casedata['center_id'];
  $patient_id = $casedata['patient_id'];
  $patientsql = mysqli_query($db, "SELECT * FROM patients WHERE patient_id = '$patient_id'");
  $patientdata = mysqli_fetch_assoc($patientsql);
  if ($_SESSION['user_role']=="admin" || $_SESSION['user_role']=="radiologist"){
    $centersql = mysqli_query($db, "SELECT * FROM members WHERE center_id = '$center_id'");
    $centerdata = mysqli_fetch_assoc($centersql);
  }
?>


<html>
<head>
    <title>case</title>
    <link rel="stylesheet" type="text/css" href="style/style-tables.css">
    <script type="text/javascript">
        if (frameElement == null) {
            window.location = "index.php";
        }
    </script>
   <script src="js/jquery-3.2.1.js"></script>
   <script src="js/form_script.js" type="text/javascript"></script>
   <style>
      @media screen and (max-width: 600px){
         table.list td:nth-of-type(1):before { content: "Case ID"; }
         table.list td:nth-of-type(2):before { content: "Clinician";}
         table.list td:nth-of-type(3):before { content: "Status"; }
         table.list td:nth-of-type(4):before { content: "Priority"; }
         table.list td:nth-of-type(5):before { content: "Examined on"; }
         table.list td:nth-of-type(6):before { content: "Submited on"; }
         table.list td:nth-of-type(7):before { content: "Updated on"; }
         table.list td:nth-of-type(8):before { content: " "; }
      }
    </style>
</head>
<body>
<div class="table">
   <span class="heading"> Patient Details - <?php echo $patientdata['name'];?></span> 
   <span class="heading"> <?php    if ($_SESSION['user_role']=="admin" 
                                  || $_SESSION['user_role']=="radiologist") 
         echo "  (Clinic: ".$centerdata['center'].")";?> </span>
   <span class="heading">  
       <?php 
       if ($pre_page == "caseslist") {
          echo "<button type=\"button\" class=\"button\" 
                onclick=\"location.href='register_case.php?action=update&case_id=". 
                $case_id."'\">Edit</button>"; 
          echo "<button type=\"button\" class= \"button\" 
               onclick=\"location.href='caseslist.php'\">Close</button>";
       } else {
          $oldpatient_id = $_GET['oldpatient_id'];
          echo "<button type=\"button\" class=\"button\" 
                onclick=\"location.href='register_case.php?action=update&case_id=".
                $case_id."'\">Edit</button>";
          echo "<button type=\"button\" class= \"button\" 
               onclick=\"location.href='patient.php?patient_id=".$oldpatient_id."'\">Close</button>";
       }
       ?>
   </span>

<div class="detail">
<table class="detail">
  <thead>
  <tr>
  <th colspan="2"> Patient Details </th>
  </tr>
  <thead>
  <tr>
  <td class="text"> Patient ID: </td>
  <td class="textdetail"><?php echo $patientdata['patient_id']; ?>  </td>
  </tr>

  <tr>
  <td class="text">Species:</td>
  <td class="textdetail"><?php echo $patientdata['species']; ?></td>
  </tr>

   <tr>
  <td class="text"> Breed : </td>
  <td class="textdetail"><?php echo $patientdata['breed']; ?></td>
  </tr>

  <tr>
  <td class="text">Gender:</td>
  <td class="textdetail"><?php echo $patientdata['gender']; ?></td>
  </tr>

  <tr>
  <td class="text"> Age: </td>
  <td class="textdetail"><?php echo $patientdata['age']." ".$patientdata['age_unit']; ?></td>
  </tr>

  <tr>
  <td class="text"> Weight: </td>
  <td class="textdetail"><?php echo $patientdata['weight']." ".$patientdata['weight_unit']; ?></td>
  </tr>

   <tr>
  <td class="text"> Clinic Ref. No: </td>
  <td class="textdetail"><?php echo $patientdata['center_ref']; ?></td>
  </tr>

  <tr>
  <td class="text">Center Phone:</td>
  <td class="textdetail"><?php echo $centerdata['phone'] ?></td>
  </tr>

  </table>
  <table class="detail">
  <thead>
  <tr>
  <th colspan="2"> Case Details </th>
  </tr>
  <thead>
  <tr>
  <td class="text"> Case ID: </td>
  <td class="textdetail"><?php echo $casedata['case_id']; ?></td>
  </tr>

   <tr>
  <td class="text"> Case Status: </td>
  <td class="textdetail"><?php echo $casedata['status']; ?></td>
  </tr>

   <tr>
  <td class="text"> Clinician: </td>
  <td class="textdetail"><?php echo $casedata['clinician']; ?></td>
  </tr>
  <tr>
   <td class="text"> Clinician Email: </td>
  <td class="textdetail"><?php echo $casedata['email']; ?></td>
  </tr>
  <tr>
  <td class="text"> Exam Date: </td>
  <td class="textdetail"><?php echo $casedata['exam_date']; ?></td>
  </tr>
  <tr>
  <td class="text"> Submited on: </td>
  <td class="textdetail"><?php echo $casedata['submit_date']; ?></td>
  </tr>
  <tr>
  <td class="text"> Last Update: </td>
  <td class="textdetail"><?php echo $casedata['update_date']; ?></td>
  </tr>
  
  </table>
  </div>
  </div>
 <h1></h1>
  <div class="table">  
  <table class="reports">
  <?php
     $service_ids = explode(",",$casedata['services']);
     $services = "";
     $result = mysqli_query($db, "SELECT * FROM services");
     while($row = mysqli_fetch_assoc($result)) {
          foreach ($service_ids as $id){
              if (trim($id) == trim($row['service_id']))
                   $services = $services.$row['service'].",";
          }
     }
  ?>
  <tr>
  <td class="reports"> <span class="reports"> Services</span> 
  <ul id="servicelist" class="reports"> </ul></td>
  <td class="reports"> <span class="reports"> Center Comments</span>
  <textarea name="comments" class="reports"></textarea></td>
  </tr>
  <script> get_services("<?php echo $services?>"); </script>
  <tr>
  <td class="reports"> <span class="reports"> Lab Results</span>
  <textarea name="lab_results" class="reports"></textarea></td>
  <td class="reports"> <span class="reports"> Radiologist Comments</span>
  <textarea name="radiologist_comments" class="reports"></textarea></td>
  </table>
  </div>
  <h1></h1> 
<?php
echo "<div class=\"table\">";
echo "<h3 align=\"center\">Other Cases</h3>";
echo "<table  class=\"list\">";
    echo "<thead>";
        echo "<tr>";
        echo "<th id=\"caseno\">" . "Case ID" . "</th>";
        echo "<th id=\"clinician\">" . "Clinician " . "</th>";
        echo "<th id=\"status\">" . "Status" . "</th>";
        echo "<th id=\"priority\">" . "Priority" . "</th>";
        echo "<th id=\"examined\">" . "Examined" . "</th>";
        echo "<th width=\"submited\">" . "Submited" . "</th>";
        echo "<th width=\"updated\">" . "Last Update" . "</th>";
        echo "<th width=\"tdbutton\">" . " " . "</th>";
        echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    $referrals = 0;

    $result = mysqli_query($db, "SELECT * FROM cases WHERE patient_id = '$patient_id'");
    if (mysqli_num_rows($result) > 0) {
        // output data of each row
       while($row = mysqli_fetch_assoc($result)) {
           if ($case_id != $row['case_id']){
              echo "<td>" . $row['case_id'] . "</td>";
              echo "<td>" . $row['clinician']. "</td>";
              echo "<td>" . $row['status'] . "</td>";
              echo "<td>" . $row['priority'] . "</td>";
              echo "<td>" . $row['exam_date'] . "</td>";
              echo "<td>" . $row['submit_date'] . "</td>";
              echo "<td>" . $row['update_date'] . "</td>";
              echo "<td colspan=\"2\"> <button type=\"button\" 
                   onclick=\"location.href='case.php?page=case&oldpatient_id="
                          .$patient_id."&case_id=".$row['case_id']."'\"
                          >View</button> </td>";
              echo "</tr>";
           }
        }
     }

echo "</tbody>";
echo "</table>";
echo "</div>";
?>    
</body>
</html>
