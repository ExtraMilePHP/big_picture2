<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once '../dao/config.php';
session_start();
$organizationId=$_SESSION['organizationId'];
$sessionId=$_SESSION['sessionId'];
$preexist_array=array("question_name");    // first row of excel to detect this data
$database_fields=array("question_name"); 
$table_name="questions"; // table name
$filename=$_FILES['file']; // filename
// $question_set=$_POST["setid"]; 
// $curruntTheme=$_GET["name"];

$curruntTheme = $_SESSION['themename'];

require_once("insertCSV.php");
// $insertCSV=new insertCSV($filename,$preexist_array,$database_fields,$table_name,$con,$organizationId,$sessionId,$question_set,$curruntTheme);
$insertCSV=new insertCSV($filename,$preexist_array,$database_fields,$table_name,$con,$organizationId,$sessionId,$curruntTheme);
$insertCSV->run();

?>