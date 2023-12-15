<?php
if ($_FILES['csvFile']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['csvFile']['tmp_name'];
    $fileName = $_FILES['csvFile']['name'];
    $uploadDirectory = './uploads/';

    if (!file_exists($uploadDirectory)) {
        mkdir($uploadDirectory, 0777, true);
    }

    $destPath = $uploadDirectory . $fileName;

    if (move_uploaded_file($fileTmpPath, $destPath)) {
        echo "File uploaded successfully to $destPath";
    } else {
        echo "Failed to move file";
    }
} else {
    echo "Error occurred during file upload";
}
?>
