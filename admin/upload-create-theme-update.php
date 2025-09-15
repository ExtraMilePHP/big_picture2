<?php 
session_start();
include_once '../dao/config.php';

$organizationId = $_SESSION['organizationId'];
$sessionId = $_SESSION['sessionId'];

include_once 'themes/processThemes.php';
include_once 'themes/themeTools.php';

require '../../vendor/autoload.php'; // Include the Composer autoload file
include_once "../s3/s3_functions.php"; // s3 function for easy upload and get signed urls, still need env file and autoload

// print_r($_FILES);


    $validExt = array("jpg", "jpeg", "png");
    $maxfileLimit = 5 * 1024 * 1024; // in bytes (5 MB)
    $uploadDir = '../uploads/';
    $response = array();
    $filenames = array();


    $targets=["themeImage","logo","background_mob","background_desk"];
    for ($i = 1; $i <= 4; $i++) {
        if(isset($_FILES['file' . $i])){
            $file = $_FILES['file' . $i];
            $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $fileSize = $file['size'];
    
            if (!in_array($fileExt, $validExt)) {
                $response['error'] = "Unsupported file format for file $i";
                echo json_encode($response);
                exit;
            }
    
            if ($fileSize > $maxfileLimit) {
                $response['error'] = "Filesize exceeds max limit of 5 MB for file $i";
                echo json_encode($response);
                exit;
            }
    
            $uniqueName = uniqueName(basename($_FILES["file".$i]["name"])); // Generate unique name
            $targetFile = $uploadDir . $uniqueName; // use for upload
            array_push($filenames, $uniqueName);

    
            if (uploadOnS3($uniqueName,$file['tmp_name'])) {
                 updateThemeData($targets[$i-1],$uniqueName);
                // $response['file' . $i] = "File $i uploaded successfully.";
            } else {
                $response['error'] = "Error uploading file $i";
                echo json_encode($response);
                exit;
            }
        }
    }

    echo "true";

    

    // Create new theme if all files uploaded successfully
    // if (createNewTheme($filenames, array("themeImage","logo", "background_desk", "background_mob"))) {
    //     echo "true";
    // } else {
    //     $response['error'] = "Error creating new theme.";
    //     echo json_encode($response);
    // }

?>
