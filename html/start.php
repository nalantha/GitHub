<?php
   include('../include/session.php');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Services page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style/style-tables.css">
    <style>
        @media screen and (max-width: 600px){
           td:nth-of-type(1):before { content: "Service"; }
           td:nth-of-type(2):before { content: "Price (CAD)"; }
           td:nth-of-type(3):before { content: "Description"; }
        }
    </style>
    <script type="text/javascript">
        if (frameElement == null) {
            window.location = "index.php";
        }
    </script>

</head>
<body>
<div>
<h1 align="center">Prairie Veterinary Diagnostic Imaging</h1>
<p align="center">
Prairie Veterinary Diagnostic Imaging Centre <br />
411-E Herold Court <br />
Saskatoon, SK S0V 0A7 </P>
</div>
<h1></h1>
<div>
<?php
echo "<h3 align=\"center\">Current Service List</h3>";
echo "<table class=\"list\">";
    echo "<thead>";
        echo "<tr>";
        echo "<th class=\"service\"> Service </th>";
        echo "<th class=\"price\"> Price (CAD) </th>";
        echo "<th class=\"descript\"> Description </th>";
        echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

$sql = "SELECT * FROM services";
$result = mysqli_query($db, $sql);
if (mysqli_num_rows($result) > 0) {
    // output data of each row    
    while($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['service'] . "</td>";
        echo "<td>" . $row['price'] . "</td>";
        echo "<td>" . $row['description'] . "</td>";
        echo "</tr>";
    }
}
echo "</tbody>";
echo "</table>";
?>
<!--</div>-->
</body>
</html>

