<?php
ob_start();
session_start();
error_reporting(0);

$settings = json_decode(file_get_contents("settings.js"), true)[0];
$organizationId = $_SESSION['organizationId'];
$sessionId = $_SESSION['sessionId'];
$curruntTheme = $_SESSION['themename'];

include_once '../dao/config.php';
include_once '../../admin_assets/triggers.php';

require '../../aws/aws-autoloader.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

// Query to check the current count of records
$sql = "SELECT COUNT(*) as record_count FROM participants WHERE sessionId='$sessionId' AND themename='$curruntTheme' AND organizationId='$organizationId'";
$result = execute_query($sql);
$row = mysqli_fetch_assoc($result);
$currentCount = intval($row['record_count']);


// Retrieve form data
$firstName = htmlentities($_POST['first_name'], ENT_QUOTES, 'UTF-8');
$lastName = htmlentities($_POST['last_name'], ENT_QUOTES, 'UTF-8');
$emailId = htmlentities($_POST['email_id'], ENT_QUOTES, 'UTF-8');
$action = isset($_POST['action']) ? $_POST['action'] : "add";
$edit_id = isset($_POST['edit_id']) ? intval($_POST['edit_id']) : 0;


if ($action == "add") {
    // Check if the email already exists
    $check_email_query = "SELECT COUNT(*) as email_count FROM participants WHERE email_id='$emailId' AND sessionId='$sessionId' AND themename='$curruntTheme' AND organizationId='$organizationId'";
    $email_result = execute_query($check_email_query);
    $email_row = mysqli_fetch_assoc($email_result);
    $emailCount = intval($email_row['email_count']);
    
    if ($emailCount > 0) {
        echo "duplicate_email";
        exit;
    }

    if ($currentCount >= 10) {
        echo "Maximum limit of 10 participants reached.";
        exit;
    } else {
        $update = "INSERT INTO `participants`(`organizationId`, `sessionId`, `themename`, `first_name`, `last_name`, `email_id`) 
                    VALUES ('$organizationId', '$sessionId', '$curruntTheme', '$firstName', '$lastName', '$emailId')";
        if (execute_query($update)) {
            echo "1010";
        } else {
            echo "Something went wrong: " . mysqli_error($con);
        }
    }

} else if ($action == "edit") {
    $update = "UPDATE `participants` SET `email_id`='$emailId', `first_name`='$firstName', `last_name`='$lastName' WHERE id='$edit_id'";
    if (execute_query($update)) {
        echo "1010";
    } else {
        echo "Something went wrong: " . mysqli_error($con);
    }
}

?>