<?php
   define('DB_SERVER', 'localhost');
   define('DB_USERNAME', 'pvdiadmin');
   define('DB_PASSWORD', 'h2ng2m2WA');
   define('DB_DATABASE', 'pvdi');
   $db = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
   if (mysqli_connect_errno())
      { 
          echo "Failed to connect to MySQL: " . mysqli_connect_error();
      }
?>
