<?php
session_start();
include_once '../dao/config.php';
$userid = $_SESSION['userId'];
$organizationId = $_SESSION['organizationId'];
$sessionId = $_SESSION['sessionId'];
include_once("userFunction.php");


  // Extract form data
//   print_r($_POST);
//   die();
  $themeid = $_POST["themeid"];
  $id = $_POST["id"];
  $words_text = $_POST["words_text"];
  $heading_title = $_POST["heading_title"];
  $gridsize = $_POST["gridsize"];
  $rightdiagonal = $_POST["rightdiagonal"];
  if($rightdiagonal== "yes"){
    $leftdiagonal = "yes";
  }else{
    $leftdiagonal = "no";
  }

  $words = array(
    "words" => $words_text,
    "title" => $heading_title,
    "gridzise" =>  $gridsize,
    "rightDiagonal" => $rightdiagonal,
    "leftDiagonal" => $leftdiagonal
);
$conditions = array("userid" => $userid,
"organizationId" => $organizationId,
"sessionId" => $sessionId,"id"=> $id,"themeid" => $themeid,);

if(updateRecord("game", $words, $conditions)){
    echo "success";
} else {
    echo "check query";
}
  
  
