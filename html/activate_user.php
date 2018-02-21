<?php
  $center_idVar = $_GET['center_id'];
  $activeVar = $_GET['active'];
  include("../include/dbconfig.php");
  if ($activeVar == "yes"){
     mysqli_query($db, "UPDATE members SET active='no' WHERE center_id = '$center_idVar'");
  } else {
     mysqli_query($db, "UPDATE members SET active='yes' WHERE center_id = '$center_idVar'");
  }
  mysqli_close($db);
  header("Location: userlist.php");
?>
