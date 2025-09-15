<?php
session_start();
include_once '../dao/config.php';
$organizationId = $_SESSION['organizationId'];
$sessionId = $_SESSION['sessionId'];
$themename=$_GET["themename"];
$table=($_SESSION["sessionId"]=="admin")?"themes_superadmin":"themes";
$delete="delete from $table where themename='$themename'";
// echo $delete;
// die();
if(execute_query($delete)){
    $previousPage = $_SERVER['HTTP_REFERER'];
    // Redirect to the previous page
    header("Location: $previousPage");
}
?>