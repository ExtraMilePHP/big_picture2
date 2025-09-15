<?php
if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set("Asia/Kolkata");
}
$database = "extramileplay_big_picture_customised";
$s3_folder="big_picture_customised";
if (file_exists("../../env.php")) {
    include_once("../../env.php");
}
else {
    include_once("../env.php");
}

?>