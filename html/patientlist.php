<!DOCTYPE HTML>  
<?php
   include('../include/session.php');
?>
<html>
<head>
    <title>PVDI User Registration Form</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="style/style-tables.css">
    <?php
      echo "<style>
      @media screen and (max-width: 600px){";
         if ($_SESSION['user_role']=="admin" || $_SESSION['user_role']=="radiologist"){
            echo "td:nth-of-type(1):before { content: \"Patient\"; }
                  td:nth-of-type(2):before { content: \"Center\"; }
                  td:nth-of-type(3):before { content: \"Patient ID\"; }
                  td:nth-of-type(4):before { content: \"Cases\"; }
                  td:nth-of-type(5):before { content: \"Species\"; }
                  td:nth-of-type(6):before { content: \"Gender\"; }
                  td:nth-of-type(7):before { content: \"Status\"; }";
         } else {
            echo "td:nth-of-type(1):before { content: \"Patient\"; }
                  td:nth-of-type(2):before { content: \"Center Ref\"; }
                  td:nth-of-type(3):before { content: \"Cases\"; }
                  td:nth-of-type(4):before { content: \"Species\"; }
                  td:nth-of-type(5):before { content: \"Gender\"; }
                  td:nth-of-type(6):before { content: \"Owner\"; }
                  td:nth-of-type(7):before { content: \"Status\"; }";
         }
      echo "</style>";
    ?>
    <script src="js/table_script.js" type="text/javascript"></script>
</head>
<body> 
<div class="main">
<?php

   $pages = 1;
   $items = 2;
   $itr = 0;
   $patients = array();
 
   if ($_SESSION['user_role']=="admin" || $_SESSION['user_role']=="radiologist"){
      $result = mysqli_query($db, "SELECT * FROM patients");
      echo "<h3 align=\"center\">Registed Patients</h3>";
   } else {
      $center_id = $_SESSION['center_id'];
      $center_sql = mysqli_query($db, "SELECT center FROM members WHERE center_id='$center_id'");
      $userrow = mysqli_fetch_assoc($center_sql);
      $center = $userrow['center'];
      $result = mysqli_query($db, "SELECT * FROM patients WHERE center_id='$center_id'");
      echo "<h3 align=\"center\">Patients - ".$center."</h3>";
   }
   $ind = 0;
   if (mysqli_num_rows($result) > 0) {
      // output data of each row    
      while($row = mysqli_fetch_assoc($result)) {
         $patients[$ind] = $row;
         if ($_SESSION['user_role']=="admin" || $_SESSION['user_role']=="radiologist"){
            $row_center_id = $row['center_id'];
            $sqlcenter = mysqli_query($db,"SELECT center FROM members WHERE center_id = '$row_center_id'");
            $row_center = mysqli_fetch_assoc($sqlcenter);
            $patients[$ind]['center']= $row_center['center'];
         } 
         $ind++;
      }
    }
    echo "<button type=\"button\" class=\"topbutton\" 
          onclick=\"location.href='register_patient.php?action=register'\">Add New Patient</button>";
    begintable:
    $h = 0;
    echo "<div id=\"page".$pages."\" class=\"tabcontent\">";
    echo "<table  class=\"list\">";
    echo "<thead>";
        echo "<tr>";
         echo "<th id=\"patient\">" . "Patient Name" . "</th>";
        if ($_SESSION['user_role']=="admin" || $_SESSION['user_role']=="radiologist"){
           echo "<th id=\"center\">" . "Center" . "</th>";
           echo "<th id=\"id\">" . "Patient ID" . "</th>";
        } else {
           echo "<th id=\"id\">" . "Center Ref" . "</th>";
        }
        echo "<th id=\"cases\">" . "Cases" . "</th>";
        echo "<th id=\"species\">" . "Species" . "</th>";
        echo "<th id=\"gender\">" . "Gender" . "</th>";
        if ($_SESSION['user_role']=="user"){
        echo "<th id=\"owner\">" . "Owner" . "</th>";
        }
        echo "<th id=\"active\">" . "Active" . "</th>";
        echo "<th id=\"empty\">" . " " . "</th>";
        echo "</tr>";
    echo "</thead>";
    echo "<tbody>";


    $cases = 0;
    $lastcase = "";

    for ($i=$itr; $i<$ind; $i++){
        if ($patients[$i]['active']=="no"){
           echo "<tr bgcolor=\"#A4A4A4\" class=\"hover\">";
        } else {
           echo "<tr class=\"hover\" 
                 onclick=\"location.href='patient.php?patient_id=".
                 $patients[$i]['patient_id']."'\">";
        }
        echo "<td>" . $patients[$i]['name'] . "</td>";
        if ($_SESSION['user_role']=="admin" || $_SESSION['user_role']=="radiologist"){
           echo "<td>" . $patients[$i]['center'] . "</td>";
           echo "<td>" . $patients[$i]['patient_id'] . "</td>";
        } else {
           echo "<td>" . $patients[$i]["center_ref"] . "</td>";
        }
        echo "<td>" . $cases . "</td>";
        echo "<td>" . $patients[$i]['species'] . "</td>";
        echo "<td>" . $patients[$i]['gender'] . "</td>";
        if ($patients[$i]['active']=="yes"){
           echo "<td>" . "Active" . "</td>";
        } else {
           echo "<td>" . "Inactive" . "</td>";
        }
        echo "<td> <button type=\"button\" class=\"button\" 
              onclick=\"location.href='register_patient.php?action=update&patient_id=".
              $patients[$i]['patient_id']."'\">View</button> </td>";
        echo "</tr>";
        $itr++;
        $h++;
        if ($h == $items && $ind-$itr > 0){
          $pages = $pages + 1;
          echo "</tbody>";
          echo "</table>";
          echo "</div>";
          goto begintable;
        }
    }

    echo "</tbody>";
    echo "</table>";
    echo "</div>";
    if ($pages > 1){
       for ($i=1; $i<=$pages; $i++) {
           echo "<button type=\"button\" class=\"tablinks\" onclick=\"openPage('page".
                 $i."')\">Page".$i."</button>";
       }
    }
?> 
</div>
<script> openPage("page1"); </script>
</body>
</html>
