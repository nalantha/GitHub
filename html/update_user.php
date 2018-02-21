<?php
  include('../include/session.php');
  include('../include/variable_array.php');
  include('../include/user.php');
  $user_App = new user;
  $Error = $user_App::$output['msg'];
  $user_role =$_SESSION['user_role'];
  $action = $_GET['action'];

  if ($action == "update"){
     if ($user_role == "admin"){
        $center_id = $_GET['center_id'];
     } else {
        $center_id = $_SESSION['center_id'];
     }
     $query = mysqli_query($db,"SELECT * FROM members WHERE center_id = '$center_id'");
     $userdata = mysqli_fetch_assoc($query);
     $username = $userdata['username'];
     $center = $userdata['center'];
  } elseif ($action == "request"){
     $center = $_GET['center'];
     $username = $_GET['username'];
     $query = mysqli_query($db,"SELECT * FROM tempmembers 
                   WHERE center = '$center' and username = '$username'");
     $userdata = mysqli_fetch_assoc($query);
     $autoid = $user_App->createID();
  } else {
     $autoid = $user_App->createID();
     $center = "";
     $username = "";
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
       if ($action == "register" || $action == "request"){
          $output = $user_App->register($center,$username);
       } elseif ($action == "update") {
          $output = $user_App->update($center,$center_id,
                                      $user_role,$username);
       } else {
          $output = $register_App::$output;
       }
       if ($output['status']=="success"){
          header("Location: userlist.php");
       } else {
          $Error = $output['msg'];
       }
  }

?>

<html>
<head>
    <title>User Upadte Form</title>
    <link rel="stylesheet" type="text/css" href="style/style-register.css">
    <script src="js/province_script.js" type="text/javascript"></script>
    <script type="text/javascript">
        if (frameElement == null) {
            window.location="index.php";
        }
    </script> 
</head>
<body> 
<div class="table">
<h3><?php if ($action == "update") {echo "User Update";}
          else {echo "Register New User";}?></h3>
<form method="post"> 
<table border="0" width="500" align="center" class="register">
  <tr>
  <td class="text"> Center ID: </td>
  <td class="input"><?php if ($action=="update") {echo $center_id;} 
      else {echo "<input type=\"text\" class=\"RegInputBox\" name=\"center_id\" value=\"";
            if(isset($_POST['center_id'])) {echo $_POST['center_id'];} else {echo "PVDI-".$autoid;}
            echo "\" readonly> <span class=\"error\">*";
            echo $Error['center_id']."</span>";}?> 
  </td>
  </tr>
  <tr>
  <td class="text"> Center Name: </td> 
  <td class="input"><input type="text" class="RegInputBox" name="center" 
                    value="<?php if(isset($_POST['center'])) {echo $_POST['center'];} 
                           elseif(isset($userdata)) {echo $userdata['center'];}?>">
  <span class="error">* <?php echo $Error['center'];?></span> </td>
  </tr>
  <?php
    if ($user_role=="admin"){
       echo "<tr> <td class=\"text\"> User Role </td>";
       echo "<td class=\"input\"><select  name=\"user_role\" id=\"user_role\">";
       foreach($user_roles as $key => $value){
           echo "<option "; 
           if (isset($_POST['user_role'])){
              if($_POST['user_role'] == $key) echo "selected";
           } elseif(isset($userdata)){
              if($userdata['user_role']== $key) echo "selected";}
           echo " value=\"".$key."\">". htmlspecialchars($value)."</option>";
       }
    
    }
  ?>
  <tr>
  <td class="text">Practice Type:</td>
  <td class="input"><select  name="center_type" id="center_type">
      <?php
        foreach($center_types as $key => $value){
      ?>
        <option <?php if(isset($_POST['center_types']))
            { if($_POST['center_types'] == $key) echo "selected"; 
            } elseif(isset($userdata)){if($userdata['center_type']== $key) echo "selected";}?>  
            value="<?= $key ?>"><?= htmlspecialchars($value) ?> </option>
      <?php
       }
      ?>
  </select>
  <span class="error">* <?php echo $Error['center_type'];?></span></td>

  </tr>
  <tr>
  <td class="text"> E-mail: </td>
  <td class="input"> <input type="text" class="RegInputBox" name="email" 
                     value="<?php if(isset($_POST['email'])) {echo $_POST['email'];} elseif(isset($userdata)){echo $userdata['email'];}?>">
  <span class="error">* <?php echo $Error['email'];?></span> </td>
  </tr>
  <tr>
  <td class="text"> Website: </td>
  <td class="input"><input type="text" class="RegInputBox" name="website" 
                     value="<?php if(isset($_POST['website'])) {echo $_POST['website'];} elseif(isset($userdata)){echo $userdata['web'];}?>">
  <span class="error"><?php echo $Error['website'];?></span> </td>
  </tr>
  
  <tr>
  <td class="text">Local Phone:</td>
  <td class="input"><input type="text" class="RegInputBox" name="phone" 
                    value="<?php if(isset($_POST['phone'])) {echo $_POST['phone'];} elseif(isset($userdata)){echo $userdata['phone'];} ?>">
  <span class="error">* <?php echo $Error['phone'];?></span> </td>
  </tr>
  <tr>
  <td class="text">Mobile Phone:</td>
  <td class="input"><input type="text" class="RegInputBox" name="mphone" 
                    value="<?php if(isset($_POST['mphone'])) {echo $_POST['mphone'];} elseif(isset($userdata)){echo $userdata['mphone'];} ?>"></td>
  </tr>
  <tr>
  <td class="text">Tool-Free Phone:</td>
  <td class="input"><input type="text" class="RegInputBox" name="tfphone" 
                    value="<?php if(isset($_POST['tfphone'])) {echo $_POST['tfphone'];} elseif(isset($userdata)){$userdata['tfphone'];} ?>"></td>
  </tr>
  <tr>
  <td class="text">Fax:</td>
  <td class="input"><input type="text" class="RegInputBox" name="fax" 
                    value="<?php if(isset($_POST['fax'])) {echo $_POST['fax'];} elseif(isset($userdata)){$userdata['fax'];} ?>"></td>
  </tr>
  </tr>
  <td class="text">Street Line1:</td>
  <td class="input"><input type="text" class="RegInputBox" name="street1" 
                    value="<?php if(isset($_POST['street1'])) {echo $_POST['street1'];} elseif(isset($userdata)){echo $userdata['street1'];} ?>">
  <span class="error">* <?php echo $Error['street1'];?></span> </td>
  </tr>
  </tr>
  <td class="text">Street Line2:</td>
  <td class="input"><input type="text" class="RegInputBox" name="street2" 
                    value="<?php if(isset($_POST['street2'])) {echo $_POST['street2'];} elseif(isset($userdata)){echo $userdata['street2'];} ?>"></td>
  </tr>
  </tr>
  <td class="text">City:</td>
  <td class="input"><input type="text" class="RegInputBox" name="city" 
                    value="<?php if(isset($_POST['city'])) {echo $_POST['city'];} elseif(isset($userdata)){echo $userdata['city'];} ?>">
  <span class="error">* <?php echo $Error['city'];?></span> </td>
  </tr>
  </tr>
  <td class="text">Country:</td>
  <td class="input"><select  name="country" id="country" onchange="provinceList()">
      <?php
        foreach($countries as $key => $value){
      ?>
        <option <?php if(isset($_POST['country'])) {if($_POST["country"] == $key) echo "selected";}
                      elseif(isset($userdata)){if($userdata['country'] == $key) echo "selected";}  ?>  
                      value="<?= $key ?>"><?= htmlspecialchars($value) ?> </option>
      <?php
       }
      ?>
     </select>
 
  <span class="error">* <?php echo $Error['country'];?></span> </td>
  </tr>
  </tr>
  <td class="text">Province/State:</td>
  <td class="input">
     <span id="placeprovince"> </span>
  <script> provinceList("<?php if(isset($_POST['province'])) {echo $_POST["province"];
                               }elseif(isset($userdata)){if(isset($userdata['province'])) echo $userdata['province'];}?>") </script>
  <span class="error"><?php echo $Error['province'];?></span> </td>
  </tr>

  </tr>
  <td class="text">Postal/Zip:</td>
  <td class="input"><input type="text" class="RegInputBox" name="postcode" 
                    value="<?php if(isset($_POST['postcode'])) {echo $_POST['postcode'];} elseif(isset($userdata)){echo $userdata['postcode'];} ?>">
  <span class="error"><?php echo $Error['postcode'];?></span> </td>
  </tr>

  <tr>
  <td class="text">Username:</td>
  <td class="input"><input type="text" class="RegInputBox" name="username" 
                     value="<?php if(isset($_POST['username'])) {echo $_POST['username'];}elseif(isset($userdata)){echo $userdata['username'];} ?>">
  <span class="error">* <?php echo $Error['username'];?></span> </td>
  <?php if ($action=="update"){ 
     echo "<tr>";
     echo "<td class=\"text\">Registrated On</td>";
     echo "<td class=\"input\">";
     echo explode(" ",$userdata['reg_date'])[0];
     echo "</td> </tr>";
  }?>
  </tr>
  <tr>
  <td colspan ="2"> <span class="error"> <?php echo $Error['error']; ?> </span></td>
  </tr>
  <tr>
  <td colspan="2" align="center">
  <input type="submit" name="Submit" value=<?php if ($action=="update"){echo "Change";} else{echo "Register";} ?>  class="button">
  <button type="button" class="button" onclick="location.href=<?php if ($user_role=="admin") {echo "'userlist.php'";} else {echo "'start.php'";}?>"> Cancel </button>
  </td>
  </tr>
  </table>
</form>
</div>
</body>
</html>
