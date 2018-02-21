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
      @media screen and (max-width: 600px){
         td:nth-of-type(1):before { content: \"Patient No\"; }
         td:nth-of-type(2):before { content: \"Patient\";}";
         if ($_SESSION['user_role']=="admin" || $_SESSION['user_role']=="radiologist"){  
            echo "td:nth-of-type(3):before { content: \"Center\"; }
                 td:nth-of-type(4):before { content: \"Status\"; }
                 td:nth-of-type(5):before { content: \"Priority\"; }
                 td:nth-of-type(6):before { content: \"Examined\"; }
                 td:nth-of-type(7):before { content: \"Submited\"; }";
         } else {
            echo "td:nth-of-type(3):before { content: \"Status\"; }
                 td:nth-of-type(4):before { content: \"Priority\"; }
                 td:nth-of-type(5):before { content: \"Examined\"; }
                 td:nth-of-type(6):before { content: \"Submited\"; }";}
    echo "</style>";
    ?>
    <script src="js/table_script.js" type="text/javascript"></script>
</head>
<body> 
<div class="main">
<?php

   $pages = 1;
   $items = 4;
   $itr = 0;
   $cases = array();

   if ($_SESSION['user_role']=="admin" || $_SESSION['user_role']=="radiologist"){
      $result = mysqli_query($db, "SELECT * FROM cases");
      echo "<h3 align=\"center\">Submited Cases</h3>";
   } else {
      $center_id = $_SESSION['center_id'];
      $result = mysqli_query($db, "SELECT * FROM cases WHERE center_id='$center_id'");
      $centersql = mysqli_query($db, "SELECT center FROM members WHERE center_id='$center_id'");
      $centerrow = mysqli_fetch_assoc($centersql);
      $center = $centerrow['center'];
      echo "<h3 align=\"center\">Submited Cases for ".$center."</h3>";
   } 

   if (mysqli_num_rows($result) > 0) {
      // output data of each row  
      $ind = 0;  
      while($row = mysqli_fetch_assoc($result)) {
         $cases[$ind] = $row;
         $row_center_id = $row['center_id'];
         $row_patient_id = $row['patient_id'];
         $sqlcenter = mysqli_query($db,"SELECT center FROM members WHERE center_id = '$row_center_id'");
         $row_center = mysqli_fetch_assoc($sqlcenter);
         $cases[$ind]['center']= $row_center['center'];
         $sqlpatient = mysqli_query($db,"SELECT name FROM patients WHERE patient_id = '$row_patient_id'");
         $row_patient = mysqli_fetch_assoc($sqlpatient);
         $cases[$ind]['patient'] = $row_patient['name'];
         $ind++;
      }
   }
   echo "<button type=\"button\" class=\"topbutton\" 
         onclick=\"location.href='register_case.php?action=register'\">Submit New Case</button>";
   begintable:
   $h = 0;
   echo "<div id=\"page".$pages."\" class=\"tabcontent\">";
   echo "<table  class=\"list\">";
   echo "<thead>";
        echo "<tr>";
        if ($_SESSION['user_role']=="admin" || $_SESSION['user_role']=="radiologist"){
           echo "<th id=\"patientno\">" . "Case No" . "</th>";
        } else {
           echo "<th id=\"patientno\">" . "Clinic Ref" . "</th>";
        }        
        echo "<th id=\"pateint\">" . "Patient " . "</th>";
        if ($_SESSION['user_role']=="admin" || $_SESSION['user_role']=="radiologist"){
           echo "<th id=\"center\">" . "Center" . "</th>";
        }
        echo "<th id=\"status\">" . "Status" . "</th>";
        echo "<th id=\"priority\">" . "Priority" . "</th>";
        echo "<th id=\"examined\">" . "Examined" . "</th>";
        echo "<th width=\"submited\">" . "Submited" . "</th>";
        echo "<th width=\"tdbutton\">" . " " . "</th>";
        echo "</tr>";
   echo "</thead>";
   echo "<tbody>";

   for ($i=$itr; $i<$ind; $i++){
       // output data of each row              
       if ($cases[$i]['status']=="active"){
          echo "<tr class=\"hover\" gcolor=\"#A4A4A4\">";
       } else {
          echo "<tr class=\"hover\">";
       }
       echo "<td>" . $cases[$i]['case_id'] . "</td>";
       echo "<td>" . $cases[$i]['patient']. "</td>";
       if ($_SESSION['user_role']=="admin" 
       || $_SESSION['user_role']=="radiologist"){
           echo "<td>" . $cases[$i]['center'] . "</td>";
       }
       echo "<td>" . $cases[$i]['status'] . "</td>";
       echo "<td>" . $cases[$i]['priority'] . "</td>";
       echo "<td>" . $cases[$i]['exam_date'] . "</td>";
       echo "<td>" . $cases[$i]['submit_date'] . "</td>";
       echo "<td colspan=\"2\"> <button type=\"button\" 
             onclick=\"location.href='case.php?page=caseslist&case_id=".
             $cases[$i]['case_id']."'\">View</button> </td>";
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
   mysqli_close($db);
   if ($pages > 1){
       for ($i=1; $i<=$pages; $i++) {
           echo "<button type=\"button\" class=\"tablinks\" onclick=\"openPage('page".
                 $i."')\">Page".$i."</button>";
       }
   }
?> 
<script> openPage("page1"); </script>
</div>
</body>
</html>
