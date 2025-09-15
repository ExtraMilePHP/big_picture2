<?php
ob_start();
session_start();


$settings=json_decode(file_get_contents("settings.js"),true)[0];
$organizationId=$_SESSION['organizationId'];
$sessionId=$_SESSION['sessionId'];


$curruntTheme = $_SESSION['themename'];

include_once '../dao/config.php';
include_once '../../admin_assets/triggers.php';

ini_set("error_log", "/var/www/extramileplay/php/big_picture_new/admin/error.log");

require '../../aws/aws-autoloader.php';
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
// Instantiate an Amazon S3 client.
$s3 = new Aws\S3\S3Client([
    'version' => $version,
    'region'  => $region,
    'credentials' => [
    'key'    => $key,
    'secret' => $secretkey
    ]
    ]);
    $question = htmlentities($_POST['question'], ENT_QUOTES, 'UTF-8');
    // $category = htmlentities($_POST['category'], ENT_QUOTES, 'UTF-8');
    $action = isset($_POST['action']) ? $_POST['action'] : "add";
    $edit_id = isset($_POST['edit_id']) ? intval($_POST['edit_id']) : 0;    

    // print_r($_POST);
    // die();

if ($action == "add") {
    $update = "INSERT INTO `questions`(`organizationId`, `sessionId`, `themename`, `question_name`) VALUES ('$organizationId','$sessionId','$curruntTheme','$question')";
    if (execute_query( $update)) {
        echo "1010";
    } else {
        echo "something went wrong " . mysqli_error($con);
    }
} else if ($action == "edit") {
    $update = "UPDATE `questions` SET `question_name`='$question' " . $bind . " WHERE id='$edit_id'";

    // echo $update;
    // die();

    if (execute_query( $update)) {
        echo "1010";
    } else {
        echo "something went wrong " . mysqli_error($con);
    }
}

?>