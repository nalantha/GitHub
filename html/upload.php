<?php
include("file_class.php");
?>
<!DOCTYPE html>
<html>
<body>

<form action="upload.php" method="post" enctype="multipart/form-data">
    Select image to upload:
    <input type="file" name="fileToUpload" id="fileToUpload" accept=".dcm">
    <input type="submit" value="Upload Image" name="submit">
</form>

</body>
</html>

