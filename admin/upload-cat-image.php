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

$tabName = "Settings";

$data = fetchThemeData();

$default_cat = unserialize($data["default_cat"]);
$custom_cat = unserialize($data["custom_cat"]);
$cat_icons = unserialize($data["cat_icons"]);

$selected_cat = $_POST["current_cat"]; // Capture the selected category
$filename = $_FILES["file"]["name"];
$extension = pathinfo($filename, PATHINFO_EXTENSION); // Get the file extension
$rand=rand(1111,9999).rand(1111,9999).rand(1111,9999);
$new_filename = $rand . '.' . $extension; // Create a new filename based on the selected category

$local_upload_path = '../uploads/' . $new_filename; // Set the new upload path

if (move_uploaded_file($_FILES["file"]["tmp_name"], $local_upload_path)) {
    // Update category icons
    $all_cat = array_merge($default_cat, $custom_cat);
    for ($i = 0; $i < sizeof($all_cat); $i++) {
        if ($all_cat[$i] == $selected_cat) {
            $cat_icons[$selected_cat] = $new_filename;
        }
    }

    // Save updated category icons
    if (updateThemeData("cat_icons", serialize($cat_icons))) {
        echo json_encode(array("success" => true));
    } else {
        echo json_encode(array("success" => false, "error" => "Failed to update category icons."));
    }
} else {
    echo json_encode(array("success" => false, "error" => "Failed to save file to local uploads folder."));
}


?>