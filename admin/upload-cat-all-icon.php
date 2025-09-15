<?php 

ob_start();
session_start();

$settings = json_decode(file_get_contents("settings.js"), true)[0];
$organizationId = $_SESSION['organizationId'];
$sessionId = $_SESSION['sessionId'];

include_once '../dao/config.php';
include_once '../../admin_assets/triggers.php';
include_once 'themes/processThemes.php';
include_once 'themes/themeTools.php';

require '../../vendor/autoload.php'; // Include the Composer autoload file
include_once "../s3/s3_functions.php"; // s3 function for easy upload and get signed urls, still need env file and autoload

$_SESSION["gameTitle"] = $settings["gameName"];
mysqli_set_charset($con, 'utf8');



$data = fetchThemeData();


if (isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $new_filename = uniqid() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
    $local_upload_path = '../uploads/' . $new_filename; // Set the upload path

    if (move_uploaded_file($file["tmp_name"], $local_upload_path)) {
        // Assuming you have a database connection
        // Update the category icon in the database
      

        if (updateThemeData("all_icon_image", $new_filename)) {
            echo json_encode(array("success" => true));
        } else {
            echo json_encode(array("success" => false, "error" => "Failed to update category icons."));
        }
    } else {
        echo json_encode(array("success" => false, "error" => "Failed to save file to local uploads folder."));
    }
} else {
    echo json_encode(array("success" => false, "error" => "No file uploaded."));
}


?>