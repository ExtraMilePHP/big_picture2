<?php
//error_reporting(E_ALL);
//ini_set('display_errors',1);
@session_start();
include_once '../dao/config.php';
include_once '../classes/EventRegister.php';
include_once '../classes/PdoClass.php';
$objEventRegister = new EventRegister();
$objpdoClass = new PdoClass();
$counter=@$_GET['counter'];
$userid=$_GET['userid'];
$gameid=$_GET['gameid'];
$position=@$_GET['position'];
$game1val=$_GET['game1val'];
      // echo "<pre>"; print_r($_GET);die;
switch($gameid){
	case 1:
	//echo $game1val;die;
		$ckbx='';
		$result = $objEventRegister->addUserPointsGame1($userid,$gameid,$game1val,$ckbx,$connPdo,$objpdoClass);
		if($result)
			echo 1;
		else
			echo 0;
		break;

	case 2:

		$ckbx=array(
		'0' =>$_GET['chk1'],
		'1' =>$_GET['chk2'],
		'2' =>$_GET['chk3'],
		'3' =>$_GET['chk4'],
		'4' =>$_GET['chk5'],
		'5' =>$_GET['chk6'],
		'6' =>$_GET['chk7'],
		);
	// echo "<pre>"; print_r($ckbx);die;
		$result = $objEventRegister->addUserPointsGame1($userid,$gameid,$game1val,$ckbx,$connPdo, $objpdoClass);
		echo 2;
		break;
	case 3:

	$ckbx='';
	$result = $objEventRegister->addUserPointsGame1($userid,$gameid,$game1val,$ckbx,$connPdo, $objpdoClass);
	echo 3;
	break;
	case 4:
	$ckbx='';
	$ans=array(
	'0' =>$_GET['ans1'],
	'1' =>$_GET['ans2'],
	'2' =>$_GET['ans3'],
	'3' =>$_GET['ans4'],
	'4' =>$_GET['ans5'],
	'5' =>$_GET['ans7'],
	);
	//print_r($ans);die;

	$result = $objEventRegister->addUserPointsGame1($userid,$gameid,$ans,$_GET['ans6'],$connPdo, $objpdoClass);
	echo 4;
	break;
	case 5:
	$ckbx='';
	$ans=$_GET;
	  // print_r($ans);die;
	$result = $objEventRegister->addUserPointsGame1($userid,$gameid,$ans,$_GET,$connPdo, $objpdoClass);
	echo 5;
	break;
	case 6:
	$ckbx='';
	$ans=$_GET;
	// print_r($ans);die;
	$result = $objEventRegister->addUserPointsGame1($userid,$gameid,$ans,$_GET,$connPdo, $objpdoClass);
	echo 5;
	break;
	case 7:
	$ckbx='';
	$ans=array(
	'0' =>$_GET['ans1'],
	'1' =>$_GET['ans2'],
	'2' =>$_GET['ans3'],
	'3' =>$_GET['ans4']
	);
	 // print_r($ans);die;
	$result = $objEventRegister->addUserPointsGame1($userid,$gameid,$ans,$_GET,$connPdo, $objpdoClass);
	echo 5;
	break;
}
?>
