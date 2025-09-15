<?php
ob_start();
error_reporting(0);

session_start();

// print_r($_POST);
// die();
include_once '../dao/config.php';
include_once '../../add_report.php';

$userid = $_SESSION['userId'];
$question = $_POST['question'];

date_default_timezone_set("Asia/Kolkata");
$timestamp = date('Y-m-d H:i:s');

$sessionId = $_SESSION['sessionId'];
$organizationId = $_SESSION['organizationId'];
// $roles = $_SESSION['roles'];
$roles = $_SESSION['roles'];
$gameId = $_SESSION['gameId'];
$fullName = $_SESSION['firstName'] . " " . $_SESSION['lastName'];
$points = $_POST['points'];
$question = $_POST['question'];
// $question_ans = $_POST['question_ans'];
$question_ans = $con->real_escape_string($_POST["question_ans"]);
$answers  = $con->real_escape_string($_POST["answers"]);
$timer = $_POST['timer'];

// print_r($_POST);
// die();
date_default_timezone_set("Asia/Kolkata");


if ($sessionId == "demobypass") {
    $find = "select * from question where userid='$userid' and organizationId='$organizationId' and sessionId='$sessionId'";

    $find = execute_query( $find);
    $find1 = mysqli_num_rows($find);
    $find2 = mysqli_fetch_object($find);

    if ($find1 > 0) {
        if ($question == "1") {
            $query1 = "UPDATE `question` SET `question1`='" . $question_ans . "' ,`score`='" . $points . "',`end_time`='". $timer."' WHERE userid='$userid' and organizationId='$organizationId' and sessionId='$sessionId'";
            if ($con->query($query1)) {
                echo "demo";
            }
        }else if ($question == "2") {
            $query1 = "UPDATE `question` SET `question2`='" . $question_ans . "' ,`score`='" . $points . "',`end_time`='". $timer."' WHERE userid='$userid' and organizationId='$organizationId' and sessionId='$sessionId'";
            if ($con->query($query1)) {
                echo "true";
            }
        } else if ($question == "3") {
            //print_r($_POST);
            $query1 = "UPDATE `question` SET `question3`='" . $question_ans . "' ,`score`='" . $points . "',`end_time`='". $timer."' WHERE userid='$userid' and organizationId='$organizationId' and sessionId='$sessionId'";
            if ($con->query($query1)) {
                echo "true";
            }
        } else if ($question == "4") {
            //print_r($_POST);
            $query1 = "UPDATE `question` SET `completed`='yes' ,`score`='" . $points . "',`end_time`='". $timer."' WHERE userid='$userid' and organizationId='$organizationId' and sessionId='$sessionId'";
            if ($con->query($query1)) {
                echo "true";
            }
        }else if ($question == "5") {
            //print_r($_POST);
            $query1 = "UPDATE `question` SET `question5`='" . $answers . "' ,`score`='" . $points . "',`end_time`='". $timer."' WHERE userid='$userid' and organizationId='$organizationId' and sessionId='$sessionId'";
            // echo $query1;

            if ($con->query($query1)) {
                echo "true";
            }
        }else {
            echo "all";
        }
    }
} else {

    $find = "select * from question where userid='$userid' and organizationId='$organizationId' and sessionId='$sessionId'";

    $find = execute_query( $find);
    $find1 = mysqli_num_rows($find);
    $find2 = mysqli_fetch_object($find);
   

    if ($find1 > 0) {
        $report_id = $find2->reportid;

        if ($question == "1") {
            $query1 = "UPDATE `question` SET `question1`='" . $question_ans . "' ,`score`='" . $points . "',`end_time`='". $timer."' WHERE userid='$userid' and organizationId='$organizationId' and sessionId='$sessionId'";
            if ($con->query($query1)) {
                if ($roles == "GUEST_USER") {
   $timer = "00:" . $timer;
                    $data = ["points" => $points, "time" => $timer, "reportId" => $report_id];
                    $update = updateReportGuestuser($data);
                    if ($update) {
                        echo "true";
                    } else {
                        echo "failed";
                    }
                } else {
                    $data = ["points" => $points, "time" => $timer, "reportId" => $report_id];
                    $update = updateReport($data);
                    if ($update) {
                        echo "true";
                    } else {
                        echo "failed";
                    }
                }
            }
        }else if ($question == "2") {
            $query1 = "UPDATE `question` SET `question2`='" . $question_ans . "' ,`score`='" . $points . "',`end_time`='". $timer."' WHERE userid='$userid' and organizationId='$organizationId' and sessionId='$sessionId'";
            if ($con->query($query1)) {
                if ($roles == "GUEST_USER") {
   $timer = "00:" . $timer;
                    $data = ["points" => $points, "time" => $timer, "reportId" => $report_id];
                    $update = updateReportGuestuser($data);
                    if ($update) {
                        echo "true";
                    } else {
                        echo "failed";
                    }
                } else {
                    $data = ["points" => $points, "time" => $timer, "reportId" => $report_id];
                    $update = updateReport($data);
                    if ($update) {
                        echo "true";
                    } else {
                        echo "failed";
                    }
                }
            }
        } else if ($question == "3") {
            //print_r($_POST);
            $query1 = "UPDATE `question` SET `question3`='" . $question_ans . "' ,`score`='" . $points . "',`end_time`='". $timer."' WHERE userid='$userid' and organizationId='$organizationId' and sessionId='$sessionId'";
            //$res1 = $connPdo->query($query1);
            if ($con->query($query1)) {
                if ($roles == "GUEST_USER") {
   $timer = "00:" . $timer;
                    $data = ["points" => $points, "time" => $timer, "reportId" => $report_id];
                    $update = updateReportGuestuser($data);
                    if ($update) {
                        echo "true";
                    } else {
                        echo "failed";
                    }
                } else {
                    $data = ["points" => $points, "time" => $timer, "reportId" => $report_id];
                    $update = updateReport($data);
                    if ($update) {
                        echo "true";
                    } else {
                        echo "failed";
                    }
                }
            }
        } else if ($question == "4") {
            //print_r($_POST);
           
            $query1 = "UPDATE `question` SET `completed`='yes' ,`score`='" . $points . "',`end_time`='". $timer."' WHERE userid='$userid' and organizationId='$organizationId' and sessionId='$sessionId'";
            //$res1 = $connPdo->query($query1);
            if ($con->query($query1)) {
                if ($roles == "GUEST_USER") {
        $timer = "00:" . $timer;
                    $data = ["points" => $points, "time" => $timer, "reportId" => $report_id];
                    $update = updateReportGuestuser($data);
                    if ($update) {
                        echo "true";
                    } else {
                        echo "failed";
                    }
                } else {
                    $data = ["points" => $points, "time" => $timer, "reportId" => $report_id];
                    $update = updateReport($data);
                    if ($update) {
                        echo "true";
                    } else {
                        echo "failed";
                    }
                }
            }
        }else if ($question == "5") {
            //print_r($_POST);
            $_SESSION["uniqueMsg"] = $points;
            $query1 = "UPDATE `question` SET `question5`='" . $answers . "' ,`score`='" . $points . "',`end_time`='". $timer."' WHERE userid='$userid' and organizationId='$organizationId' and sessionId='$sessionId'";
            // echo $query1;
            //$res1 = $connPdo->query($query1);
            if ($con->query($query1)) {
                if ($roles == "GUEST_USER") {
        $timer = "00:" . $timer;
                    $data = ["points" => $points, "time" => $timer, "reportId" => $report_id];
                    $update = updateReportGuestuser($data);
                    if ($update) {
                        echo "true";
                    } else {
                        echo "failed";
                    }
                } else {
                    $data = ["points" => $points, "time" => $timer, "reportId" => $report_id];
                    $update = updateReport($data);
                    if ($update) {
                        echo "true";
                    } else {
                        echo "failed";
                    }
                }
            }
        }else {
            echo "all";
        }
    }
}
