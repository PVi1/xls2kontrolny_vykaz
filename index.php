<html>
<body>
<?php

  require_once 'library.php';
//upload file
  $target_dir = "input/";
  $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
  $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
  // Check if image file is a actual image or fake image
  if(isset($_POST["submit"])) {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
       echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
   } else {
       echo "Sorry, there was an error uploading your file.";
   }
  //exract files
  process_zip($target_dir . basename($_FILES["fileToUpload"]["name"]));

//process file in loop and delete after processing


//delete archive at the end

/*
    openfile(__DIR__ . '/input/Fa_k_DPH/F182345 Motosam.xls');
    openfile(__DIR__ . '/input/Fa_k_DPH/F182402 Subtil pril 1.xls');
    openfile(__DIR__ . '/input/Fa_k_DPH/F182402 Subtil pril 2.xls');
*/
}
else {
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
