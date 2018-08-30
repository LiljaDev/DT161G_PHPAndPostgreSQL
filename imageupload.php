<?php
/*******************************************************************************
 * Projekt, Kurs: DT161G
 * File: imageupload.php
 * Desc: Handles user image uploads
 *
 * Johan Lilja
 * joli1407
 * joli1407@student.miun.se
 ******************************************************************************/

require_once __DIR__ . "/classes/database.class.php";
require_once __DIR__ . "/classes/image.class.php";
require_once __DIR__ . "/classes/resultenum.class.php";

session_start();
$loggedInUser = unserialize($_SESSION['loggedIn']); //Logged in user object
$response=[];   //Response

header('Content-Type: application/json');
$file = $_FILES["image"];   //Uploaded file
if($file["error"] != UPLOAD_ERR_OK){
    $response["message"] = "File upload error code: " . $file["error"];
    echo json_encode($response);
    exit;
}
//Extract the mime type of the file
$finfo = finfo_open();
$type = finfo_buffer($finfo, file_get_contents($_FILES["image"]["tmp_name"]), FILEINFO_MIME_TYPE);

//If file is of an acceptable type
if($type == "image/png" || $type == "image/jpeg" || $type == "image/gif" || $type == "image/tiff"){
    //Try to extract the datetime when the image was taken
    $exif = exif_read_data($file["tmp_name"]);
    if(isset($exif["DateTimeOriginal"])){
        $dateTime = $exif["DateTimeOriginal"];
    }
    else{
        $dateTime = null;
    }

    //Construct image object and insert image in db
    $image = new Image($_POST["category"], base64_encode(file_get_contents($file["tmp_name"])), $dateTime);
    $dbc = Database::getInstance();
    $imageStatus = ResultEnum::OK;
    $result = $dbc->insertImage($loggedInUser->getUsername(), $image, $_POST["category"], $imageStatus);

    //If the user already uploaded the same image
    if($imageStatus == ResultEnum::IMAGE_DUPLICATE){
        $response["message"] = "Duplicate of displayed image, upload aborted";
        $response["imageType"] = $type;
        $image = $dbc->findImageDuplicate($loggedInUser->getUsername(), $image->getHash(), $status);
        $response["image"] = $image->getImage();
    }

    //If image stored successfully
    if($imageStatus == ResultEnum::OK){
        $response["message"] = "File Successfully uploaded!";
    }
}
else{
    $response["message"] = "File type not allowed! Type identified as: " . $type . " errcode=" . $_FILES["image"]["error"];
}

echo json_encode($response);