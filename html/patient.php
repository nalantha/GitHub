<!DOCTYPE HTML>
<?php
  include('../include/session.php');
  $user_role = $_SESSION['user_role'];
  $patient_id = $_GET['patient_id'];
  $sql = mysqli_query($db, "SELECT * FROM patients WHERE patient_id = '$patient_id'");
  $patientrow = mysqli_fetch_assoc($sql);
  $center_id = $patientrow['center_id'];
  if ($_SESSION['user_role']=="admin" || $_SESSION['user_role']=="radiologist"){
    $sqlcenter = mysqli_query($db, "SELECT * FROM members WHERE center_id = '$center_id'");
    $centerrow = mysqli_fetch_assoc($sqlcenter);
  }
?>


<html>
<head>
    <title>Patient</title>
    <link rel="stylesheet" type="text/css" href="style/style-tables.css">
    <script type="text/javascript">
        if (frameElement == null) {
            window.location = "index.php";
        }
    </script>
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
   <span class="heading"> Patient Details - <?php echo $patientrow['name'];?></span> 
   <span class="heading"> <?php    if ($_SESSION['user_role']=="admin" 
                                  || $_SESSION['user_role']=="radiologist") 
         echo "  (Center: ".$centerrow['center'].")";?> </span>
   <span class="heading">   
       <button type="button" class= "button" 
        onclick="location.href='register_patient.php?action=update&patient_id=<?php 
                 echo $patientrow['patient_id']?>'">Edit</button> 
       <button type="button" class= "button" 
        onclick="location.href='patientlist.php'">Close</button>
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
  <td class="textdetail"><?php echo $patientrow['patient_id']; ?>  </td>
  </tr>

  <tr>
  <td class="text">Species:</td>
  <td class="textdetail"><?php echo $patientrow['species']; ?></td>
  </tr>

   <tr>
  <td class="text"> Breed : </td>
  <td class="textdetail"><?php echo $patientrow['breed']; ?></td>
  </tr>

  <tr>
  <td class="text">Gender:</td>
  <td class="textdetail"><?php echo $patientrow['gender']; ?></td>
  </tr>

  <tr>
  <td class="text"> Age: </td>
  <td class="textdetail"><?php echo $patientrow['age']." ".$patientrow['age_unit']; ?></td>
  </tr>

  <tr>
  <td class="text"> Weight: </td>
  <td class="textdetail"><?php echo $patientrow['weight']." ".$patientrow['weight_unit']; ?></td>
  </tr>

   <tr>
  <td class="text"> Clinic Ref. No: </td>
  <td class="textdetail"><?php echo $patientrow['center_ref']; ?></td>
  </tr>

  <tr>
  <td class="text">Registered on:</td>
  <td class="textdetail"><?php echo $patientrow['reg_date'] ?></td>
  </tr>

  </table>
  <table class="detail">
  <thead>
  <tr>
  <th colspan="2"> Owner Contact Infomation </th>
  </tr>
  <thead>
  <tr>
  <td class="text"> Owner Name: </td>
  <td class="textdetail"><?php echo $patientrow['owner']; ?></td>
  </tr>

   <tr>
  <td class="text"> Email: </td>
  <td class="textdetail"><?php echo $patientrow['owner_email']; ?></td>
  </tr>

   <tr>
  <td class="text"> Phone: </td>
  <td class="textdetail"><?php echo $patientrow['owner_phone']; ?></td>
  </tr>
  <tr>
   <td class="text"> Secondary Phone: </td>
  <td class="textdetail"><?php echo $patientrow['owner_phone2']; ?></td>
  </tr>

  <tr>
  <td class="text"> Address: </td>
  <td rowspan="4" class="textdetail"><?php echo $patientrow['address']; ?></td>
  </tr>

  </table>
  </div>
  </div>
  <h1></h1> 
<?php
echo "<div class=\"table\">";
echo "<h3 align=\"center\">Cases</h3>";
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
           echo "<td>" . $row['case_id'] . "</td>";
           echo "<td>" . $row['clinician']. "</td>";
           echo "<td>" . $row['status'] . "</td>";
           echo "<td>" . $row['priority'] . "</td>";
           echo "<td>" . $row['exam_date'] . "</td>";
           echo "<td>" . $row['submit_date'] . "</td>";
           echo "<td>" . $row['update_date'] . "</td>";
           echo "<td colspan=\"2\"> <button type=\"button\" 
                 onclick=\"location.href='case.php?page=patient&oldpatient_id=".
                         $patient_id."&case_id=".$row['case_id']."'\">
                         View</button> </td>";
           echo "</tr>";
        }
     }

echo "</tbody>";
echo "</table>";
echo "</div>";
?>    
</body>
</html>
