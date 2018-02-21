<!DOCTYPE HTML>  
<?php
   include('../include/session_admin.php');
   include('../include/services.php');
   $service_App = new service;
   $action = $_GET['action'];
   
   $autocode = $service_App->getcode();
   $code= $service=$price=$description="";
   $error = "";
   if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $input = array('service_id'=> $_POST['service_id'],
                     'service'=> $_POST['service'],
                     'price'=> $_POST['price'],
                     'description'=> $_POST['description']);
     
      switch ($action){
      case "start":
         $result = $service_App->add($input);
         break;
      case "update":
         $service_id = $_GET['service_id'];
         $result = $service_App->update($input,$service_id);
         break;
      case "remove":
         $service_id = $_GET['service_id'];
         $result = $service_App->remove($service_id);
         break;
      }        
      
      if ($result['status']=="success") { 
         $autocode = $service_App->getcode();
         unset($_POST['service'], $_POST['price'], $_POST['description']);  
      } else {
         $error = $result['msg'];
      }

   }         
?>
<html>
<head>
    <title>Services</title>
        <link rel="stylesheet" type="text/css" href="style/style-tables.css">
    <style>
        .error {color: #FF0000;}
        @media screen and (max-width: 600px){ 
           td:nth-of-type(1):before { content: "Service ID"; }
           td:nth-of-type(2):before { content: "Service"; }
           td:nth-of-type(3):before { content: "Price (CAD)"; }
           td:nth-of-type(4):before { content: "Description"; }
        }
    </style>
    <script src="js/table_script.js" type="text/javascript"></script>
</head>
<body> 
<div class="main">
<h3 align="center">Services</h3>
<?php
    $pages = 1; // number of pages
    $items = 10; // number of items in the page
    $services = array();
    $sql = "SELECT * FROM services";
    $result = mysqli_query($db, $sql);

    function my_sort($a, $b){
       if ($a == $b) return 0;
       return ($a > $b) ? -1 : 1;
    }

    $ind = 0;
    if (mysqli_num_rows($result) > 0) {
    // output data of each row   
      while($row = mysqli_fetch_assoc($result)) {
         $services[$ind]=$row;
         $ind++;
      }
    }
    //usort($services,"my_sort"); 
    $itr = 0;

    begindiv:
    $h = 0;
    echo "<div id=\"page".$pages."\" class=\"tabcontent\">";
    echo "<table  class=\"list\">";
    echo "<thead>";
        echo "<tr>";
        echo "<th id=\"id\" style=\"width:20%;\">" . "Service id" . "</th>";
        echo "<th id=\"service\"  style=\"width:20%;\">" . "Service" . "</th>";
        echo "<th id=\"price\"   style=\"width:20%;\">" . "Price (CAD)" . "</th>";
        echo "<th id=\"descript\"   style=\"width:20%;\">" . "Description" . "</th>";
        echo "<th id=\"button\"   style=\"width:20%;\">" . " " . "</th>";
        echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
 
    echo "<form method=\"post\">";
    if ($pages == 1){
        echo "<tr>";

       if ($action == "start"){
          echo "<td><input type=\"text\" class=\"InputBox\" 
                name=\"service_id\" value=\"".$autocode. "\" required> </td>";
          echo "<td><input type=\"text\" class=\"InputBox\" name=\"service\" 
               value=\"";  if(isset($_POST['service'])) echo $_POST['service']; 
          echo"\"  required>  </td>";
          echo "<td><input type=\"text\" class=\"InputBox\" name=\"price\" 
                value=\""; if(isset($_POST['price'])) echo $_POST['price']; 
          echo "\"  required> </td>";
          echo "<td><input type=\"text\" class=\"InputBox\" name=\"description\" 
                value=\""; if(isset($_POST['description'])) echo $_POST['description']; 
          echo "\"  </td>";
          echo "<td><input type=\"submit\" name=\"Add\" value=\"Add\"  
                class=\"button\"> </td>";
       } else{
          $service_id = $_GET['service_id'];
          $result = mysqli_query($db, "SELECT * FROM services 
                                  WHERE service_id ='$service_id'");
          $servicerow = mysqli_fetch_assoc($result); 
          echo "<td><input type=\"text\" class=\"InputBox\" name=\"service_id\" 
                value=\"".$service_id. "\"  readonly></td>";
          echo "<td><input type=\"text\" class=\"InputBox\" name=\"service\" 
                value=\"";  if(isset($_POST['service'])) {echo $_POST['service'];
                            } else {echo $servicerow['service'];} echo "\"  required>  </td>";
          echo "<td><input type=\"text\" class=\"InputBox\" name=\"price\" value=\""; 
                if(isset($_POST['price'])) {echo $_POST['price'];
                } else {echo $servicerow['price'];} echo "\"  required> </td>";
          echo "<td><input type=\"text\" class=\"InputBox\" name=\"description\" value=\""; 
                if(isset($_POST['description'])) {echo $_POST['description'];
                } else {echo $servicerow['description'];} echo "\" > </td>";
          if ($action == "update"){
             echo "<td> <input class=\"button\" type=\"submit\" value=\"Update\" name=\"update\">";
          } elseif ($action == "remove") {
             echo "<td> <input class=\"button\" type=\"submit\" value=\"Remove\" name=\"remove\">";
          }
          echo "<button class=\"button\" type=\"button\" 
                onclick=\"service_cancelButton()\">Cancel </button></td>";
       }
       echo "</tr>";


       echo "<span class:\"error\">". $error ."</span>";
    }

    // output data of each row
    for ($i=$itr; $i<$ind; $i++){
        echo "<tr>";
        echo "<td>" . $services[$i]['service_id'] . "</td>";
        echo "<td>" . $services[$i]['service'] . "</td>";
        echo "<td class=\"price\">" . $services[$i]['price'] . "</td>";
        echo "<td>" . $services[$i]['description'] . "</td>";
        echo "<td> <button type=\"button\" class=\"button\" 
              onclick=\"service_editButton('". 
              $services[$i]['service_id'] . "')\">Edit </button>";
        echo "<button type=\"button\" class=\"button\"  
              onclick=\"service_removeButton('". 
              $services[$i]['service_id'] . "')\">Remove </button>  </td>";
        echo "</tr>";
        $itr = $itr + 1;
       
        $h = $h + 1;
        if ((($h == $items-1 && $pages == 1) || ($h == $items)) && $ind-$itr > 0){
          $pages = $pages + 1;
          echo "</tbody>";
          echo "</table>";
          echo "</div>";
          goto begindiv;
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
<script> openPage("page1"); </script>
</div>
</body>
</html>
