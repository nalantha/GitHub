<?php
   session_start();
   $out = shell_exec("rm -rf /usr/share/PVDI/html/tmp/*");   
   if(session_destroy()) {
      header("Location: login.php");
   }
?>
