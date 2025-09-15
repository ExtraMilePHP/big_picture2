<?php
ob_start();
session_start();
$settings=json_decode(file_get_contents("settings.js"),true)[0];
$organizationId=$_SESSION['organizationId'];
$sessionId=$_SESSION['sessionId'];
include_once '../dao/config.php';
include_once '../../admin_assets/triggers.php';
include_once 'themes/processThemes.php';
include_once 'themes/themeTools.php';



require '../../vendor/autoload.php'; // Include the Composer autoload file
include_once "../s3/s3_functions.php"; // s3 function for easy upload and get signed urls, still need env file and autoload

function resizeImage($inputImagePath, $outputImagePath, $maxHeight) {
    list($width, $height, $type) = getimagesize($inputImagePath);

    // Determine the image type and set appropriate functions for loading and saving
    switch ($type) {
        case IMAGETYPE_JPEG:
            $imageCreateFunc = 'imagecreatefromjpeg';
            $imageSaveFunc = 'imagejpeg';
            $extension = 'jpg';
            break;
        case IMAGETYPE_PNG:
            $imageCreateFunc = 'imagecreatefrompng';
            $imageSaveFunc = 'imagepng';
            $extension = 'png';
            break;
        default:
            throw new Exception('Unsupported image type');
    }

    if ($height > $maxHeight) {
        $newHeight = $maxHeight;
        $newWidth = ($newHeight / $height) * $width;

        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        $image = $imageCreateFunc($inputImagePath);

        // Preserve transparency for PNG images
        if ($type == IMAGETYPE_PNG) {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
        }

        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        $imageSaveFunc($newImage, $outputImagePath);

        imagedestroy($image);
        imagedestroy($newImage);
    } else {
        copy($inputImagePath, $outputImagePath);
    }
}



$events=$_GET["events"];
if(isset($events)){
    if($events=="upload_background"){
        $random_node=rand(1,500).rand(1,1000);
        $filename=$random_node.basename($_FILES["file"]["name"]);
        $target_file ="../uploads_background/".$filename;
        $uploadType=$_POST["uploadType"];
        if(move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)){
            if($uploadType=="web"){
                updateValues("background_web",$filename);
            }else{
                updateValues("background_mob",$filename);
            }
            echo "true";
         }
    }
    if($events=="upload_logo"){
        $random_node=rand(1,500).rand(1,1000);
        $filename=uniqueName(basename($_FILES["file"]["name"]));
        if(uploadOnS3($filename,$_FILES["file"]["tmp_name"])){  // required gamename,filename,tmp_name of the file
            if(updateThemeData("puzzle_image",$filename)){
                echo "true";
            }    
        }
    }

    
    if($events == "upload_logo1"){
        $textinput = $_POST["textinput"] ?? '';
        $file = $_FILES["file"] ?? null;
    
        if ($file && $textinput) {
            echo "Please upload either an image or enter text, not both.";
        } elseif ($file) {
            $random_node = rand(1, 500) . rand(1, 1000);
            $filename = uniqueName(basename($file["name"]));
            
            if (uploadOnS3($filename, $file["tmp_name"])) {  // required gamename, filename, tmp_name of the file
                if (updateThemeData("puzzle_image", $filename)) {
                    $textinput = "";
                   
                    updateThemeData("Correct_ans_text_popup", $textinput);
                    echo "true";
                    
                } 
                   
            } else {
                echo "Error uploading image.";
            }
        } elseif ($textinput) {
            $filename="";
            if (updateThemeData("Correct_ans_text_popup", $textinput)) {
                updateThemeData("puzzle_image", $filename);
                echo "true";
            } else {
                echo "Error updating text.";
            }
        } else {
            echo "No data provided.";
        }
    }
    

    
    if($events == "upload_logo2"){
        $textinput = $_POST["textinput"] ?? '';
        $file = $_FILES["file"] ?? null;
    
        if ($file && $textinput) {
            echo "Please upload either an image or enter text, not both.";
        } elseif ($file) {
            $random_node = rand(1, 500) . rand(1, 1000);
            $filename = uniqueName(basename($file["name"]));
            
            if (uploadOnS3($filename, $file["tmp_name"])) {  // required gamename, filename, tmp_name of the file
                if (updateThemeData("end_game_img_popup", $filename)) {
                    $textinput = "";
                    updateThemeData("end_game_text_popup", $textinput);
                    echo "true";
                }    
            } else {
                echo "Error uploading image.";
            }
        } elseif ($textinput) {
            $filename="";
            if (updateThemeData("end_game_text_popup", $textinput)) {
                updateThemeData("end_game_img_popup", $filename);
                echo "true";
            } else {
                echo "Error updating text.";
            }
        } else {
            echo "No data provided.";
        }
    }



}
?>