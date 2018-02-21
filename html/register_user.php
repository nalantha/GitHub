<!DOCTYPE HTML>  
<html>
<head>
    <title>User Registration Form</title>
    <link rel="stylesheet" type="text/css" href="style/style-register.css">
    <script src="js/province_script.js" type="text/javascript"></script>
</head>
<body>  

<?php
include("../include/variable_array.php");
include("../include/user.php");
$user_App = new user;
$output = $user_App::$output;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
   $output = $user_App->request();
   if ($output['status']=="success") {
      $submitErr = "Registration submited";
      unset($_POST);
   } else {
      $submitErr = $output['msg']['error'];
   }
}
?>

<img src="images/logo-pvdi-index.png">
<div class="table">
<h3>User Registration</h3> 
<form method="post"  action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
<table border="0" width="500" align="center" class="register">
  <tr>  
  <td colspan="2" class="input"> <p><span class="error">* required field.</span></p> </td>
  </tr>
  <tr>
  <td class="text"> Center Name: </td> 
  <td class="input"><input type="text" class="RegInputBox" name="center" 
      value="<?php if(isset($_POST['center'])) echo $_POST['center'];?>">
  <span class="error">* <?php echo $output['msg']['center'];?></span> </td>
  </tr>
  <tr>
  <td class="text">Practice Type:</td>
  <td class="input"><select  name="center_type" id="center_type">
      <?php
        foreach($center_types as $key => $value){
      ?>
        <option <?php if(isset($_POST['center_types']))
            if($_POST['center_types'] == $key) echo "selected"; ?>  
            value="<?= $key ?>"><?= htmlspecialchars($value) ?> </option>
      <?php
       }
      ?>
  </select>

  <span class="error">* <?php echo $output['msg']['center_type'];?></span> </td>
  </tr>
  <tr>
  <td class="text"> E-mail: </td>
  <td class="input"> <input type="text" class="RegInputBox" name="email" 
      value="<?php if(isset($_POST['email'])) echo $_POST['email'];?>">
      <span class="error">* <?php echo $output['msg']['email'];?></span> </td>
  </tr>
  <tr>
  <td class="text"> Website: </td>
  <td class="input"><input type="text" class="RegInputBox" name="web" 
      value="<?php if(isset($_POST['web'])) echo $_POST['web'];?>">
  <span class="error"><?php echo $output['msg']['website'];?></span> </td>
  </tr>
  
  <tr>
  <td class="text">Local Phone:</td>
  <td class="input"><input type="text" class="RegInputBox" name="phone" 
      value="<?php if(isset($_POST['phone'])) echo $_POST['phone']; ?>">
  <span class="error">* <?php echo $output['msg']['phone'];?></span> </td>
  </tr>
  <tr>
  <td class="text">Mobile Phone:</td>
  <td class="input"><input type="text" class="RegInputBox" name="mphone" 
      value="<?php if(isset($_POST['mphone'])) echo $_POST['mphone']; ?>"></td>
  </tr>
  <tr>
  <td class="text">Tool-Free Phone:</td>
  <td class="input"><input type="text" class="RegInputBox" name="tfphone" 
      value="<?php if(isset($_POST['tfphone'])) echo $_POST['tfphone']; ?>"></td>
  </tr>
  <tr>
  <td class="text">Fax:</td>
  <td class="input"><input type="text" class="RegInputBox" name="fax" 
      value="<?php if(isset($_POST['fax'])) echo $_POST['fax']; ?>"></td>
  </tr>
  </tr>
  <td class="text">Street Line1:</td>
  <td class="input"><input type="text" class="RegInputBox" name="street1" 
      value="<?php if(isset($_POST['street1'])) echo $_POST['street1']; ?>">
  <span class="error">* <?php echo $output['msg']['street1'];?></span> </td>
  </tr>
  </tr>
  <td class="text">Street Line2:</td>
  <td class="input"><input type="text" class="RegInputBox" name="street2" 
      value="<?php if(isset($_POST['street2'])) echo $_POST['street2']; ?>"></td>
  </tr>
  </tr>
  <td class="text">City:</td>
  <td class="input"><input type="text" class="RegInputBox" name="city" 
      value="<?php if(isset($_POST['city'])) echo $_POST['city']; ?>">
  <span class="error">* <?php echo $output['msg']['city'];?></span> </td>
  </tr>
  </tr>
  <td class="text">Country:</td>
  <td class="input"><select  name="country" id="country" onchange="provinceList()">
      <?php
        foreach($countries as $key => $value){
      ?>
        <option <?php if(isset($_POST['country'])) 
            if($_POST["country"] == $key) echo "selected"; ?>  
            value="<?= $key ?>"><?= htmlspecialchars($value) ?> </option>
      <?php
       }
      ?>
     </select>
 
  <span class="error">* <?php echo $output['msg']['country'];?></span> </td>
  </tr>
  </tr>
  <td class="text">Province/State:</td>
  <td class="input">
     <span id="placeprovince"> </span>
  <script> 
     provinceList("<?php if(isset($_POST['province'])) echo $_POST["province"];?>") 
  </script>
  <span class="error"><?php echo $output['msg']['province'];?></span> </td>
  </tr>

  </tr>
  <td class="text">Postal/Zip:</td>
  <td class="input"><input type="text" class="RegInputBox" name="postcode" 
      value="<?php if(isset($_POST['postcode'])) echo $_POST['postcode']; ?>">
  <span class="error"><?php echo $output['msg']['postcode'];?></span> </td>
  </tr>

  <tr>
  <td class="text">Username:</td>
  <td class="input"><input type="text" class="RegInputBox" name="username" 
      value="<?php if(isset($_POST['username'])) echo $_POST['username']; ?>">
  <span class="error">* <?php echo $output['msg']['username'];?></span> </td>
  </tr>
  <tr>
  <td class="text">Password:</td>
  <td class="input"><input type="password" class="RegInputBox" name="password" 
      value="<?php if(isset($_POST['password'])) echo $_POST['password']; ?>">
  <span class="error">* <?php echo $output['msg']['password'];?></span> </td>
  </tr>
  <tr>
  <td class="text">Confirm Password:</td>
  <td class="input"><input type="password" class="RegInputBox" name="confirm_password" 
      value="<?php if(isset($_POST['confirm_password'])) 
                   echo $_POST['confirm_password']; ?>">
  </td>
  </tr>
  
  <tr>
  <td class="text">Comment:</td> 
  <td class="input"><textarea name="comment" rows="5" cols="40">
      <?php if(isset($_POST['comments'])) echo $_POST['comments'];?></textarea></td>
  </tr>
  <tr>
  <td colspan ="2" class="input"> <input type="checkbox" name="terms" class="checkbox" 
      <?php if(isset($_POST["terms"])) echo "checked"; ?> >I accept Terms and Conditions
      <span class="error">* <?php echo $output['msg']['terms'];?></span> </td>
  </tr>
  <tr>
  <td colspan ="2" class="input"> <span class="error"> 
             <?php if(isset($submitErr)) echo $submitErr; ?> </span> </td>
  </tr>
  </table>
  <input type="submit" name="Submit" value="Submit"  class="button">
  <input type="reset" name="reset" value="Reset"  class="button">  
</div>
</form>

<?php

?>

</body>
</html>
