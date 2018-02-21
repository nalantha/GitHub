<?php
  $UID = $_GET['uid'];
  echo $UID;
  include('../include/pacs_class.php');
  $out = new getdicom("STUDY",$UID);
  echo $out->output();
?>
