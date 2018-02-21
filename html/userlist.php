<!DOCTYPE HTML>  
<?php
   include('../include/session_admin.php');
?>
<html>
<head>
    <title>PVDI User Registration Form</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="style/style-tables.css">
    <style>
      @media screen and (max-width: 600px){
         table.members td:nth-of-type(1):before { content: "ID"; }
         table.members td:nth-of-type(2):before { content: "Center";}
         table.members td:nth-of-type(3):before { content: "Email"; }
         table.members td:nth-of-type(4):before { content: "Phone"; }
         table.members td:nth-of-type(5):before { content: "City"; }
         table.members td:nth-of-type(6):before { content: "Role"; }
         table.members td:nth-of-type(7):before { content: "Status"; }
         table.members td:nth-of-type(8):before { content: " "; }
      }
    </style>
    <script src="js/table_script.js" type="text/javascript"></script>
</head>
<body> 
<div class="main">

<?php
   $pages = 1;
   $items = 5;
   $itr = 0;
   // getting non-registed users
   $tempusers = array();
   $sql = "SELECT * FROM tempmembers";
   $result = mysqli_query($db, $sql);
   $tempind = 0;
   if (mysqli_num_rows($result) > 0) {
      while($row = mysqli_fetch_assoc($result)) {
          $tempusers[$tempind] = $row;
          $tempind++;
      }
   }
   // getting registed users
   $users = array();
   $sql = "SELECT * FROM members";
   $result = mysqli_query($db, $sql);
   $ind = 0;
   if (mysqli_num_rows($result) > 0) {
      while($row = mysqli_fetch_assoc($result)) {
          $users[$ind] = $row;
          $ind++;
      }
   }

   echo "<h3 align=\"center\">PVDI users</h3>";
   echo "<button type=\"button\" class=\"topbutton\" ".
         "onclick=\"location.href='update_user.php?".
         "center= &username= &action=register&center_id=none'\"
         >New user </button>";
    echo "<span id=\"pagenumbers\" style=\"float:right\"> </span>";
    begintable1:
    $h = 0;
    echo "<div id=\"page".$pages."\" class=\"tabcontent\">";
    if ($tempind > 0) {
       echo "<h3 class=\"sub\">New Users</h3>";
       echo "<table class=\"list members\">";
       echo "<thead>";
       echo "<tr>";
       echo "<th id=\"center\">" . "Center" . "</th>";
       echo "<th id=\"email\">" . "Email" . "</th>";
       echo "<th id=\"phone\">" . "Phone" . "</th>";
       echo "<th id=\"city\">" . "City" . "</th>";
       echo "<th id=\"Province\">" . "Province" . "</th>";
       echo "<th id=\"action\">" . "Action" . "</th>";
       echo "</tr>";
       echo "</thead>";
       echo "<tbody>";
    // output data of each row    
       for ($i=$itr; $i<$tempind; $i++) {
           echo "<tr>";
           echo "<td>" . $tempusers[$i]['center'] . "</td>";
           echo "<td>" . $tempusers[$i]['email'] . "</td>";
           echo "<td>" . $tempusers[$i]['phone'] . "</td>";
           echo "<td>" . $tempusers[$i]['city'] . "</td>";
           echo "<td>" . $tempusers[$i]['province'] . "</td>";
           echo "<td> <button type=\"button\" class=\"button\" 
                onclick=\"location.href='update_user.php?center=" . 
                $tempusers[$i]['center'] . "&username=" . 
                $tempusers[$i]['username'] . 
                "&action=request&center_id= '\">Register </button>
                </td>";
           echo "</tr>";
           $itr++;
           $h++;
           if ($h == $items && $tempind-$itr > 0){
              $pages = $pages + 1;
              echo "</tbody>";
              echo "</table>";
              echo "</div>";
              goto begintable1;
           }
       }
      
       echo "</tbody>";
       echo "</table>";

    } 
    $itr = 0;
    begintable2:
    if ($itr != 0) { 
       $h = 0;
       echo "<div id=\"page".$pages."\" class=\"tabcontent\">";
    }
    echo "<span class=\"subheading\"><h3 class=\"sub\">Registed Users</h3><span>";
    echo "<table class=\"list members\">";
    echo "<thead>";
    echo "<tr>";
    echo "<th id=\"id\">" . "ID" . "</th>";
    echo "<th id=\"center\">" . "Center" . "</th>";
    echo "<th id=\"email\">" . "Email" . "</th>";
    echo "<th id=\"phone\">" . "Phone" . "</th>";
    echo "<th id=\"city\">" . "City" . "</th>";
    echo "<th id=\"role\">" . "User Role" . "</th>";
    echo "<th id=\"status\">" . "Status" . "</th>";
    echo "<th id=\"action\">" . "Action" . "</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    for ($i=$itr; $i<$ind; $i++) {
        if ($users[$i]['active']=="no"){
          echo "<tr bgcolor=\"#A4A4A4\">";
        } else {
          echo "<tr>";
        }
        echo "<td>" . $users[$i]['center_id'] . "</td>";
        echo "<td>" . $users[$i]['center'] . "</td>";
        echo "<td>" . $users[$i]['email'] . "</td>";
        echo "<td>" . $users[$i]['phone'] . "</td>";
        echo "<td>" . $users[$i]['city'] . "</td>";
        echo "<td>" . $users[$i]["user_role"] . "</td>";
        if ($users[$i]['active']=="yes"){
            echo "<td>" . "Active" . "</td>";
        } else {
            echo "<td>" . "Inactive" . "</td>";
        }
        echo "<td>
             <button type=\"button\" class=\"button\" 
             onclick=\"location.href='update_user.php?&action=update&center_id=".
             $users[$i]["center_id"]."&center=".$users[$i]['center']."&username=".
             $users[$i]['username']."'\">Update </button>";
        if ($users[$i]['user_role']!="admin"){
        if ($users[$i]['active']=="yes"){ 
            echo "<button type=\"button\" class=\"button\" 
                  onclick=\"location.href='activate_user.php?active=yes" .
                 "&center_id=" . $users[$i]['center_id'] . "'\">Disable </button>  </td>";
        } else{
            echo "<button type=\"button\" class=\"button\" 
                 onclick=\"location.href='activate_user.php?active=no" .
                 "&center_id=" . $users[$i]['center_id'] . "'\">Activate </button>  </td>";
        }
        }
        echo "</tr>";
        $itr++;
        $h++;
        if ($h == $items && $ind-$itr > 0){
           $pages = $pages + 1;
           echo "</tbody>";
           echo "</table>";
           echo "</div>";
           goto begintable2;
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
<script> pageButton(<?=$pages?>,"pagenumbers"); </script>
</div>
</body>
</html>
