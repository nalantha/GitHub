<?php
include('../include/session.php');
include("../include/user.php");
?>
<html>
<head>
    <title>User Upadte Form</title>
        <link rel="stylesheet" type="text/css" href="style/style-register.css">
    <style>
      .error {color: #FF0000;}
    </style>
    <script type="text/javascript">
        if (frameElement == null) {
            window.location="index.php";
        }
    </script>
</head>
<body>
<?php
$center_id = $_SESSION['center_id'];
$user_App = new user;
$Error = $user_App::$output_password['msg'];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
   $input = array('password'=>$_POST['password'],
                  'newpassword'=>$_POST['newpassword'],
                  'confirm_newpassword'=>$_POST['confirm_newpassword']);
   $output = $user_App->change_password($input,$center_id);
   $Error = $user_App::$output_password['msg'];
}
?>
<div>
<form method="post">
<table border="0" width="500" align="center" class="register">
<tr>
<td colspan="2" class="heading"> <h2>Change password</h2> </td>
</tr>
<tr>
  <td class="text"> Center ID: </td>
  <td class="input"><?php echo $center_id;?> </td>
</tr>
<tr>
  <td class="text">Current password:</td>
  <td class="input"><input type="password" class="RegInputBox" name="password"
                     value="<?php if(isset($_POST['password'])) echo $_POST['password']; ?>">
  <span class="error">* <?php echo $Error['password'];?></span> </td>
</tr>
  <tr>
  <td class="text">New password:</td>
  <td class="input"><input type="password" class="RegInputBox" name="newpassword"
                     value="<?php if(isset($_POST['newpassword'])) echo $_POST['newpassword']; ?>">
  <span class="error">* <?php echo $Error['newpassword'];?></span> </td>
  </tr>
  <tr>
  <td class="text">Confirm new password:</td>
  <td class="input"><input type="password" class="RegInputBox" name="confirm_newpassword" 
                    value="<?php if(isset($_POST['confirm_newpassword'])) echo $_POST['confirm_newpassword']; ?>">
  <span class="error">* <?php echo $Error['confirm_newpassword'];?></span> </td>
  </tr>
  <tr>
  <td colspan="2" align="center">
  <input type="submit" name="Submit" value="Change"  class="button">
  <button type="button" class="button" onclick="location.href='start.php'"> Cancel </button>
  </td>
  </tr>
  <tr>
  <td colspan="2" align="left">
  <span class="error">* <?php echo $Error['error'];?></span>
  </td>
  </tr>
</table>
</form>
</div>
</body>
</html>
