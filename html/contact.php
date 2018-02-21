<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nameErr= $emailErr = $commentErr=$message=$phone="";
  $require = True;
  if (empty($_POST["name"])){
     $nameErr = "* Required";
     $require = False;
  } else { 
     $name = $_POST["name"];
  }
  if (empty($_POST["email"])) {
    $emailErr = "* Required";
    $require = False;
  } else {
    $email = $_POST["email"];
    // check if e-mail address is well-formed
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $emailErr = "* Invalid email";
      $require = False;
    }
  }
  if (!empty($_POST["phone"])){
     $phone = $_POST["phone"];
  }
  if (empty($_POST["comment"])){
     $commentErr = "* Required";
     $require = False;
  } else {
     $body = $_POST["comment"];
  }
  if ($require){
     $text = "Name: ".$name."<br />Email: ".$email."<br />Phone: ".$phone."<br /><br />".$body;
     include("../include/contactConf.php");
  }
}
?>
<html>
<head>
  <style>
     p{padding:0;margin:0;width:60px}
     body{padding:0;margin:0;background-color:#6699cc}
     table{width:600px;background:#FFFFFF;}
     table td{padding: 5px 5px 5px 20px;text-align:left}
     table td.center{text-align:center}
     input{width:250px}
     textarea{width:250px;resize:none}
     input.button{padding:5px;width:100px}
     span.error{color:#FF0000;width:40px}
  </style>
</head>

<body>
  <form method="POST">
     <table border="0" width="500" align="center" class="demo-table"> 
        <tr>
          <td colspan='2' class=center>
            <h2>Contact Form<h2>
          </td>
        </tr>
        <tr>
          <td><p>Name:</p></td> 
          <td><input type="text" name="name"  value="<?php echo $_POST["name"];?>">
          <span class="error"><?php echo $nameErr; ?></span></td>
        </tr>
        <tr>
          <td><p>Email:</p></td> 
          <td><input type="text" name="email" value="<?php echo $_POST["email"];?>">
          <span class="error"><?php echo $emailErr; ?></span></td>
        </tr>
        <tr>
          <td><p>Phone:</p></td> 
          <td><input type="text" name="phone" value="<?php echo $_POST["phone"];?>"></td>
        </tr>
        <tr>
          <td><p>Message:</p></td> 
          <td><textarea name="comment" rows="5" cols="40"><?php echo $_POST['comment'];?></textarea>
          <span class="error"><?php echo $commentErr; ?></span></td>
        </tr>
        <tr>
          <td colspan='2' class=center>
            <input class="button" type="submit" name="Submit" value="Submit">
          </td>
        </tr>
        <tr>
          <td colspan='2' class=center>
            <?php echo " ".$Message;?>
          </td>
        </tr>
     </table>
</form>

</body>
</html>
