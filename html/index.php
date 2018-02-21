<?php
   include('../include/session.php');
?>

<html lang="en">

<head>
<body onresize="closeNav()">
<meta charset="utf-8">

<meta name="author" content=" ">
<meta name="viewport" content="width=device-width, height=device-height,initial-scale=1, maximum-scale=1">
<link rel="stylesheet" href="style/style-index.css">
<script src="js/jquery-3.2.1.js"></script>
<script src="js/index.js"></script>
<base target="_parent" />
</head>

<body>
<div class="main-div" id="main-div">
<header>
<img class="logo" src="images/logo-pvdi-index_small.png" align="left">

<label for="show-menu" class="show-menu" id="show-menu" onclick="openNav()"><div class="div-menu"></div>
<div class="div-menu"></div> <div class="div-menu"></div></label> 
</header>
<ul class="main-menu" id="menu">
        <label for="show-menu2" class="show-menu2" onclick="closeNav()"><div class="div-menu"></div>
        <div class="div-menu"></div> <div class="div-menu"></div></label> 
        <div class="closebtn"><a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&#10006;</a></div>
        <li><a href="start.php" class="current" target="mainFrame" >Home</a></li>
        <li><a href="caseslist.php" target="mainFrame">Cases</a></li>
        <li><a href="patientlist.php" target="mainFrame">Patients</a></li>
        <li><a href="../webcalender/login.php" target="mainFrame" >Schedule</a></li>
        <li class="dropdown"> 
            <a href="javascript:void(0)" class="dropbtn"><?php echo $_SESSION["login_user"]; ?></a>
            <ul class="dropdown-content">
               <?php
                   if ($_SESSION['user_role']!="admin") {
                        echo "<a href=\"update_user.php?action=update\" target=\"mainFrame\">Update account</a>";
                   } else { 
                        echo "<a href=\"services.php?action=start\" target=\"mainFrame\">Services</a>";
                        echo "<a href=\"userlist.php\" target=\"mainFrame\" >user accounts</a>";                        
                   }
                   echo "<a href=\"change_password.php\" target=\"mainFrame\">Change password</a>"; 
               ?>
            </ul>
        </li>
        <li><a href="dicom_page.php" target="mainFrame">PACS</a></li>
        <li><a href="logout.php">logout</a></li>
</ul>


 <iframe src="start.php" name="mainFrame" id="mainFrame"></iframe>

</div>
</script>
</body>

</html>
