<html>
<body>
<?php

  require_once 'library.php';
//upload file
if (isset($_POST["submit"])) {
    $target_dir = "input/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $filetype = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    // Check if image file is a actual image or fake image
    if ($filetype == "zip") {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            echo "The file ". basename($_FILES["fileToUpload"]["name"]). " has been uploaded.<br />";
        } else {
            die("Sorry, there was an error uploading your file to {$target_file}.");
        }
        //exract files
        if (extract_zip($target_file)) {
            //process file in loop and delete after processing
            if (process_fa($target_dir)) {
                if (is_file($target_file)) {
                    //delete archive at the end
                    unlink($target_file);
                }
            }
        }

    } else {
        echo "Pokusili ste sa nahrat nepovoleny typ suboru. Nahravajte len ZIP archiv.";
    }
  
} else {
    ?>
<form action="index.php" method="post" enctype="multipart/form-data">
    Subor s archivom faktur:&nbsp;
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="Nahraj a spracuj FA" name="submit">
</form>

<?php
}

?>
</body>
</html>
