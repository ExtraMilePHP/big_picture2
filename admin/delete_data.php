<?php
session_start();
include_once '../dao/config.php';
$userid = $_SESSION['userId'];
$organizationId = $_SESSION['organizationId'];
$sessionId = $_SESSION['sessionId'];
include_once("userFunction.php");

  $themename = $_POST["themename"];
  $id = $_POST["id"];
  $table = $_POST["table"];
 
$conditions = array("organizationId" => $organizationId,
"sessionId" => $sessionId,"id"=> $id,"themename" => $themename);


if(deleteRecord($table, $conditions)){
    echo "success";
} else {
    echo "check query";
}
  
  
