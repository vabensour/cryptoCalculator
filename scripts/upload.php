<?php
$target_dir = "../uploads/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
$msgError = '';

// Check if file already exists
if (file_exists($target_file)) {
  $msgError .= "Sorry, file already exists.";
  $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    $msgError .= "Sorry, your file was not uploaded.";
    header('Location: ../index.php?upload=false&uploadError='.$msgError);
    exit();
// if everything is ok, try to upload file
} else {
  if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
    header('Location: ../index.php?upload=true');
    exit();
  } else {
    $msgError .=  "Sorry, there was an error uploading your file.";
    header('Location: ../index.php?upload=false&uploadError='.$msgError);
    exit();
  }
}
