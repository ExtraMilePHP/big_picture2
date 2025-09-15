<?php
ob_start();
error_reporting(0);
ini_set('display_errors', 1);
session_start();
if (empty($_SESSION["token"])) {
    header("Location:index.php");
}


// $userid = $_SESSION['userId'];
// $email = $_SESSION['email'];
// $sessionId = $_SESSION['sessionId'];
// $organizationId = $_SESSION['organizationId'];
// $name = $_SESSION['firstName'] . " " . $_SESSION['lastName'];
// $roles = $_SESSION['roles'];
// $gameId = $_SESSION['gameId'];


    include_once 'dao/config.php';
    include_once '../add_report.php';
    
    $userid = $_SESSION['userId'];
    $email = $_SESSION['email'];
    $organizationId = $_SESSION['organizationId'];
    $sessionId = $_SESSION['sessionId'];
    $roles = $_SESSION['roles'];
    $gameId = $_SESSION['gameId'];
    $fullName = $_SESSION['firstName'] . " " . $_SESSION['lastName'];

if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set("Asia/Kolkata");
}

include_once 'admin/themes/themesets.php';
include_once 'admin/themes/themeTools.php';

require '../vendor/autoload.php'; // Include the Composer autoload file
include_once "s3/s3_functions.php"; // s3 function for easy upload and get signed urls, still need env file and autoload

if ($_SESSION['sessionId'] == "demobypass") {
    $demoprint = "var isdemo='demo';";

}else{
    $demoprint = "var isdemo='';";

}


    if ($sessionId == "demobypass") {

        $auth = "select * from question where userid='$userid' and organizationId='$organizationId' and sessionId='$sessionId'";
        $auth = execute_query( $auth);
        $auth = mysqli_num_rows($auth);

        if ($auth > 0) {
        } else {

            $insert = "INSERT INTO `question`(`userid`,`email`,`usertype`,`name`,`puzzleimg`,`organizationId`,`sessionId`,`score`,`start_time`) VALUES ('$userid','$email','$roles','$fullName','$puzzleimg','$organizationId','$sessionId',0,'$timestamp')";

            if (execute_query( $insert)) {
            }
        }
    } else {
        $selectquery = "select * from question where userid='$userid' and organizationId='$organizationId' and sessionId='$sessionId'";

        $selectresult = execute_query( $selectquery);
        $rowCount = mysqli_num_rows($selectresult);

        if ($rowCount == 0) {

            $insert = "INSERT INTO `question`(`userid`,`email`,`usertype`,`name`,`puzzleimg`,`organizationId`,`sessionId`,`score`,`start_time`) VALUES ('$userid','$email','$roles','$fullName','$puzzleimg','$organizationId','$sessionId',0,'$timestamp')";

            //         echo  $insert;
            //   die();
            if (execute_query( $insert)) {
                if ($roles == "GUEST_USER") {
                    $userid = $_SESSION['userId'];
                    function successResponse($tools)
                    {
                        global $con, $userid, $organizationId, $sessionId;
                        $reportid = $tools["reportId"];
                        // $query1="UPDATE `user` SET `reportid`='$reportid' WHERE userid='$userid' and organizationId='$organizationId' and sessionId='$sessionId'";
                        $query1 = "UPDATE `question` SET `reportid`='$reportid' WHERE userid='$userid'  and organizationId='$organizationId' and sessionId='$sessionId'";
                        // echo $query1;
                        // exit();
                        if (!execute_query( $query1)) {
                            echo mysqli_error($con);
                        };
                    }
                    $data = ["gameId" => $gameId, "name" => $fullName, "sessionId" => $sessionId, "userId" => $userid, "organizationId" => $organizationId, "points" => 0, "time" => "00:00:00", "ans" => ""];
                    addReportGuest($data);
                } else {
                    $userid = $_SESSION['userId'];
                    function successResponse($tools)
                    {
                        global $con, $userid, $organizationId, $sessionId;
                        $reportid = $tools["reportId"];
                        // $query1="UPDATE `user` SET `reportid`='$reportid' WHERE userid='$userid' and organizationId='$organizationId' and sessionId='$sessionId'";
                        $query1 = "UPDATE `question` SET `reportid`='$reportid' WHERE userid='$userid'  and organizationId='$organizationId' and sessionId='$sessionId'";
                        if (!execute_query( $query1)) {
                            echo mysqli_error($con);
                        };
                    }
                    $data = ["points" => 0, "time" => "NA"];
                    addReport($data);
                }
            }
        } else {
        }
    }

    if (function_exists('date_default_timezone_set')) {
        date_default_timezone_set("Asia/Kolkata");
    }
    $stage = 1;
    $timestamp = date('Y-m-d H:i:s');
    if (!isset($_COOKIE['gettime'])) {
        setcookie("gettime", $timestamp, time() + (86400 * 30), "/");
        $hours = 0;
        $minutes = 0;
        $seconds = 0;
    } else {
        if ($stage == 1) {
            setcookie("gettime", $timestamp, time() + (86400 * 30), "/");
            $previousTime = $_COOKIE["gettime"];
            $time = new DateTime($previousTime);
            $timediff = $time->diff(new DateTime());
            $hours = 0;
            $minutes = 0;
            $seconds = 0;
        } else {
            $previousTime = $_COOKIE["gettime"];
            $time = new DateTime($previousTime);
            $timediff = $time->diff(new DateTime());
            $hours = $timediff->format('%h');
            $minutes = $timediff->format('%i');
            $seconds = $timediff->format('%s');
        }
    }
$data = fetchThemeData();
//  print_r($data);
  
   $questions_answers = unserialize($data['questions_and_answers']); 
   $dynamic_rightPoints  = $data['points_01'];
   $submit_words  = $data['questions_01'];
$show_clue = $data["show_clue"];

  $curruntTheme = $data["themeName"]; // Fetch theme name
    $_SESSION['themename'] = $curruntTheme; // Store theme name in session
    

$getId11="select * from question where userid='$userid' and  organizationId='$organizationId' and sessionId='$sessionId'";
$getId11=execute_query($getId11);
$getId11=mysqli_fetch_object($getId11);
$question5=$getId11->question5;
$unscramble_ans=$getId11->unscramble_ans;

if(!empty($question5) ){
    header("Location:think-fast.php");
}

?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Big Picture 2.0</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <link href="css/games-global.css?v=1" rel="stylesheet">
    <link href="css/style.css" type="text/css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">

    <style>
    .auto {
        margin: auto;
        float: none;
    }

    body {
        background: url(<?php echo possibleOnS3("uploads/", $data["background_desk"]) ?>);
        margin: 0px;
        padding: 0px;
        background-size: 100% 100%;
        background-repeat: no-repeat;
        background-size: cover;
        background-repeat: no-repeat;
        background-attachment: fixed;
    }


    #example1 li {
        color: <?php echo $data["landing_page_title_color"];
        ?>;
        width: max-content;
    }

    .welcome-home {
        width: 250px;
        margin-top: 116px;
        margin-left: 231px;
    }

    .button-submit {
        width: max-content;
        margin: auto;
        position: relative;
        color: <?php echo $data["landing_page_button_color"];
        ?>;
        background-color: <?php echo $data["landing_page_button_bgcolor"];
        ?>
    }


    .rule-list li {

        color: <?php echo $data["landing_page_title_color"];
        ?>;

    }

    .question-label {
        color: <?php echo $data["landing_page_title_color"];
        ?>;
        width: 100%;
    }

    .score {
        position: absolute;
        text-align: center;
        border: 1px solid #fff;
        float: right;
        padding: 4px 11px;
        border-radius: 10px;
        font-size: 18px;
        z-index: 9999999999;
        margin-top: 20px;
        color: <?php echo $data["landing_page_title_color"];
        ?>;
        background-color: <?php echo $data["landing_page_button_bgcolor"];
        ?>;
        left: 71px;
        width: 120px;
    }

    .timer-back {
        position: absolute;
        font-size: 18px;
        font-weight: 100 !important;
        border-radius: 10px;
        border: 1px solid #ffffff;
        text-align: center;
        padding: 5px;
        width: 120px;
        margin-left: 30px;
        z-index: 9999999999;
        margin-top: 20px;
        color: <?php echo $data["landing_page_title_color"];
        ?>;
        background-color: <?php echo $data["landing_page_button_bgcolor"];
        ?>
    }

    #timer {
        width: 150px;
        background-color: <?php echo $data["landing_page_button_bgcolor"];
        ?>;
        color: <?php echo $data["landing_page_title_color"];
        ?>;
        text-align: center;
        margin: 0 auto;
        border-radius: 8px;
        padding: 5px;
        margin-top: 10px;
        font-size: 18px;
    }

    .correct-answer {
        background-color: green !important;
    }

    .wrong-answer {
        background-color: red !important;
    }

    .question-box {
        /* display: flex; */
        align-items: center;
        margin-bottom: 10px;
        color: <?php echo $data["landing_page_title_color"];
        ?>;
    }

    .question-number {
        font-weight: bold;
        margin-right: 10px;
    }

    .get-answer {
        width: 100%;
        padding: 10px;
        font-size: 14px;
        border-radius: 4px;
    }

    .get-answer:not(:last-child) {
        margin-bottom: 15px;
        /* Add gap between input boxes */
    }

    .full-screen {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: white;
        color: white;
        text-align: center;
        line-height: 100vh;
        z-index: 10000;
        font-size: 15px;
        color: black;
    }

    .show-clue {
        padding-top: 15px;
        width: 20px;
        height: 20px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .show-clue img {
        width: 100%;
    }

    .unscramble-input {
        display: flex;
        align-items: center;
        border: 1px solid;
    }

    .custom-icon {
        display: flex;
        gap: 10px;
    }

    .modal-body {
        position: relative;
        padding: 15px;
        color: <?php echo $data["landing_page_title_color"];
        ?>;
    }

    #popup-container {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        color: white;

    }

    #popup-container textarea {
        padding: 10px;
        font-size: 16px;
        border: none;
        border-radius: 5px;
        outline: none;
        width: 500px;
        height: auto;
        text-align: center;
        background: #fff;
        color: #000;
        overflow: auto;
        resize: none;
        /* Disable resizing */
    }



    #popup-container input[type="text"]::placeholder {
        color: #aaa;
        /* Optional: placeholder text color */
    }

    #popup-end-game {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        color: white;

    }

    #popup-end-game textarea {
        padding: 10px;
        font-size: 16px;
        border: none;
        border-radius: 5px;
        outline: none;
        width: 500px;
        height: auto;
        text-align: center;
        background: #fff;
        color: #000;
        overflow: auto;
        resize: none;
        /* Disable resizing */
    }



    #popup-end-game input[type="text"]::placeholder {
        color: #aaa;
        /* Optional: placeholder text color */
    }

    .center-wrapper {
        display: flex;
        justify-content: end;
        align-items: flex-start;
        /* or center if you want vertical centering */
        /* min-height: 100vh; */
    }

    .qa-box {
        width: 100%;
        max-width: 990px;
    }

    .question-box {
        color: <?php echo $data["landing_page_title_color"];
        ?>;
        /* same neon green as your design */
        font-weight: bold;
    }

    .input-group .form-control {
        max-width: 250px;
    }

    .points {
        background-color: <?php echo $data["landing_page_button_bgcolor"];
        ?>;
        float: right;
        width: 120px;
        font-size: 18px;
        padding: 5px;
        text-align: center;
        color: <?php echo $data["landing_page_title_color"];
        ?>;
        margin: 0 auto;
        border-radius: 8px;
        margin-top: 10px;

    }

    .unscramble-input {
        width: 100% !important;
    }

    .hint {
        background-image: url(<?php echo possibleOnS3('uploads/', $data['background_desk']);
        ?>);
        background-size: 100% 100%;
        background-attachment: fixed;
        background-repeat: no-repeat;
        background-position: center;
        width: 80%;
        height: 350px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin-left: 117px;
    }

    .container {
        margin-top: 30px;

    }

    .modal-footer {
        padding: 15px;
        text-align: center;
        border-top: 1px solid #e5e5e5;
    }

    .yellow-input {
        border: 1px solid !important;
        border: none;
        border-radius: 0;
        height: 35px;
    }

    .question-label {
        font-weight: bold;
        /* white-space: nowrap; */
    }

    @media (max-width: 768px) {
        .question-label {
            width: 90% !important;
            white-space: normal;
        }
    }

    .main-css {
        display: flex;
        justify-content: center;
        width: 100%;
        gap: 20px;
        flex-wrap: wrap;


    }

    .myDiv {
        width: 48%;
    }

    .mtop1 {
        margin: 10px 0px;
        font-size: 20px;
        text-align: center;
        color: <?php echo $data["landing_page_title_color"];
        ?>
    }

    @media (max-width:768px) {
        body {
            background: url(<?php echo possibleOnS3("uploads/", $data["background_mob"]) ?>);
            /* background: url(../img/bg.jpg); */
            margin: 0px;
            padding: 0px;
            background-size: 100% 100%;
            background-attachment: fixed;
            background-repeat: no-repeat;
        }

        .mtop1 {
            margin-top: 71px;
            font-size: 22px;
            text-align: center;
            color: <?php echo $data["landing_page_title_color"];
            ?>
        }


        .desk {
            display: none;
        }

        .mob {
            display: block;
        }

        .container-control {
            margin-top: 45px;
        }

        #example1 li {
            color: <?php echo $data["landing_page_title_color"];
            ?>;
            width: 100%;
            text-align: left;
        }

        .qa-box {
            width: 70%;
            max-width: 990px;
            margin-top: 22px;
        }

        #timer {
            width: 135px;
            background-color: <?php echo $data["landing_page_button_bgcolor"];
            ?>;
            color: <?php echo $data["landing_page_title_color"];
            ?>;
            text-align: center;
            margin: 0 auto;
            border-radius: 8px;
            padding: 5px;
            margin-top: 77px;
            border: 1px solid white;
            margin-right: 50px;
        }

        .score {
            background-color: <?php echo $data["landing_page_button_bgcolor"];
            ?>;
            float: right;
            width: 120px;
            font-size: 18px;
            padding: 5px;
            text-align: center;
            margin-top: 77px;
            color: <?php echo $data["landing_page_title_color"];
            ?>;
            left: 15px;
        }

        .hint {
            background-image: url(<?php echo possibleOnS3('uploads/', $data['background_mob']);
            ?>);
            background-size: 100% 100%;
            background-repeat: no-repeat;
            background-position: center;
            width: 85%;
            height: 247px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-left: 38px;
            margin-top: 75px;
        }

        .myDiv {
            width: 45%;
        }

        .main-css {
            display: flex;
            justify-content: center;
            width: 100%;
            gap: 5px;
            flex-wrap: wrap;
        }


    }

    @media (min-width: 600px) and (max-width: 1200px) {

        .mtop1 {
            margin: 10px 0px;
            font-size: 14px;
            text-align: center;
        }

        .get-answer:not(:last-child) {
            margin-bottom: -4px;
        }

        .yellow-input {
            border: 1px solid !important;
            border: none;
            border-radius: 0;
            height: 27px;
        }

        .question-label {
            font-weight: bold;
            font-size: 11px;
        }

        .myDiv {
            width: 45%;
        }

        #timer {
    width: 93px;
    text-align: center;
    margin: 0 auto;
    border-radius: 8px;
    padding: 5px;
    margin-top: 10px;
    font-size: 12px;
}

.score {
    position: absolute;
    text-align: center;
    border: 1px solid #fff;
    float: right;
    padding: 4px 11px;
    border-radius: 10px;
       font-size: 12px;
    z-index: 9999999999;
    margin-top: 7px;
    left: 76px;
    width: 91px;
}

.button-submit {
    top: 18px;
}
    }
    </style>
</head>

<body>

    <?php 
        include("../actions-default.php");
        back("rules.php");
    
    ?>

    <div class="container-fluid">

        <div class="container-fluid mob-margin" style="margin-top:10px;">
            <div class="row ">
                <div class="col-sm-4 col-md-4 col-lg-4 col-xs-8" style="display:inline-flex; ">
                    <div class="timer-back" id="timer">Time:00:00</div>
                </div>

                <div class="col-sm-2 col-md-2 col-lg-2 col-xs-4" style="float:right;">
                    <div class="score">Points: <span id="points">0</span></div>
                </div>
            </div>

        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-2 col-md-2 col-lg-2 col-xs-12">
                <!-- <div class="countdown"></div> -->
            </div>
            <div class="col-sm-10 col-md-10 col-lg-10 col-xs-12 auto">
                <div class="row">
                    <div class="col-sm-9 col-md-9 col-lg-9 col-xs-12 auto">
                        <div class="col-sm-3 col-md-3 col-lg-3 col-xs-8 auto">
                            <img src="img/chall1.png" class="" alt="" style="width:100%;">
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xs-12 mtop1">
                            <?php echo htmlspecialchars($data["landing_page_title1"], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="popup-container" class="correct-popup" style="display: none;">
            <?php if (!empty($data["Correct_ans_img_popup"])): ?>
            <!-- Show image popup if puzzle_image is not empty -->
            <img id="popup-image" src="<?php echo possibleOnS3("uploads/", $data["Correct_ans_img_popup"]); ?>"
                alt="Success">
            <?php elseif (!empty($data["Correct_ans_text_popup"])): ?>
            <!-- Show text popup if puzzle_image is empty and Correct_ans_text_popup is not empty -->
            <textarea readonly id="Correct_ans_text_popup"
                class="form-control"><?php echo htmlspecialchars($data["Correct_ans_text_popup"], ENT_QUOTES, 'UTF-8'); ?></textarea>
            <?php endif; ?>
        </div>

        <div id="popup-end-game" style="display: none;">
            <?php if (!empty($data["end_game_img_popup"])): ?>
            <!-- Show image popup if end_game_img_popup is not empty -->
            <img id="popup-end" src="<?php echo possibleOnS3("uploads/", $data["end_game_img_popup"]); ?>" alt="Submit">
            <?php elseif (!empty($data["end_game_text_popup"])): ?>
            <!-- Show text popup if end_game_img_popup is empty and end_game_text_popup is not empty -->
            <textarea readonly id="end_game_text_popup"
                class="form-control"><?php echo htmlspecialchars($data["end_game_text_popup"], ENT_QUOTES, 'UTF-8'); ?></textarea>
            <?php endif; ?>
        </div>
        <?php
        $questions_and_answers = isset($data["questions_and_answers"]) ? unserialize($data["questions_and_answers"]) : [];

        if (!is_array($questions_and_answers)) {
            echo "Invalid question and answer data.";
            return;
        }

        $questions = array_column($questions_and_answers, 'question');
        $answers = array_column($questions_and_answers, 'answer');
        // $totalQuestions = count($questions);
        $totalQuestions = min(count($questions), $submit_words); 
        ?>
        <div class="container">
            <div class="row">
                <div class="main-css">
                    <?php for ($i = 0; $i < $totalQuestions; $i++): ?>
                    <?php if ($i % 2 === 0): ?>
                    <!-- Start new row every two questions -->
                    <div class="w-100 d-block d-md-none"></div>
                    <?php endif; ?>

                    <div class="myDiv d-flex align-items-center">
                        <div class="w-100 d-flex align-items-center">
                            <div class="question-label me-2" style="">
                                <?php echo ($i + 1) . '. ' . htmlspecialchars($questions[$i]); ?>
                            </div>
                            <div class="flex-grow-1 custom-icon d-flex align-items-center">
                                <input type="text" id="question_<?php echo $i; ?>" name="answers[<?php echo $i; ?>]"
                                    class="form-control get-answer unscramble-input yellow-input" placeholder=""
                                    pos="<?php echo $i; ?>">
                                <div class="show-clue ms-2"
                                    data-clue="<?php echo htmlspecialchars($questions_and_answers[$i]['clue']); ?>"
                                    style="cursor: pointer;">
                                    <img src="img/info.png" alt="info">
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>

        <div class="col-md-12 text-center">
            <input type="submit" value="SUBMIT" name="submit" class="button-submit btn btn-login" style="display:none;">
        </div>

        <div class="modal" id="clueModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content hint" style="">
                    <div class="modal-body scroll-bar">
                        <div class="modal-body" id="clueContent" style="font-size: 25px;">

                        </div>
                        <div class="modal-footer" style=" border:none;">
                            <button type="button" class="btn btn-danger" data-dismiss="modal"
                                style="color:<?php echo $data["landing_page_title_color"]; ?>;background-color: <?php echo $data["landing_page_button_bgcolor"]; ?>;border: none;">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/sweetalert.min.js"></script>
    <script>
    $(".show-clue").click(function() {
        // Get the clue from the data attribute
        const clue = $(this).data('clue');

        // Set the clue content in the modal
        $("#clueContent").text(clue);

        // Show the modal
        $('#clueModal').modal('show');
    });
    </script>

    <script>
    var sessionId = "<?php echo $sessionId ?>";

    console.log("session id ", sessionId);
    var target,
        seconds = <?php echo $seconds ?>,
        minutes = <?php echo $minutes; ?>,
        hours = <?php echo $hours ?>;
    var t;

    function add() {
        seconds++;
        if (seconds >= 60) {
            seconds = 0;
            minutes++;
            if (minutes >= 60) {
                minutes = 0;
                hours++;
            }
        }

        target = (minutes ? (minutes > 9 ? minutes : "0" +
            minutes) : "00") + ":" + (seconds > 9 ? seconds : "0" + seconds);
        $("#timer").html("Time: " + target);
        $(".timer").val(target);

        timer();
    }

    function timer() {
        t = setTimeout(add, 1000);
    }
    timer();
    var organizationId = "<?php echo $organizationId;?>";
    var showclue = <?php echo json_encode($show_clue);?>;

    console.log(showclue);
    // document.querySelectorAll('.show-clue').forEach(el => {
    //     const clue = el.getAttribute('data-clue');
    //     if (!clue || clue.trim() === '') {
    //         el.style.display = 'none';
    //     }
    // });

    if (showclue == "false") {
        $(".show-clue").hide();
    } else {
        $(".show-clue").show();

    }


    $("#question_0, #question_1,#question_2,#question_3,#question_4,#question_5,#question_6,#question_7,#question_8,#question_9")
        .on("input", function() {
            $(this).val($(this).val().replace(/^\s+/, ""));
        });


    function checkSubmitButtonVisibility() {
        var filledCount = 0;

        $(".unscramble-input").each(function() {
            if ($(this).val().trim() !== "") {
                filledCount++;
            }
        });

        if (filledCount >= submit_words) {
            $(".button-submit").show();
        } else {
            $(".button-submit").hide();
        }
    }

    <?php echo $demoprint; ?>
    // console.log("this");
    var admin_rightpoints = <?php echo $dynamic_rightPoints  ?>;
    var submit_words = <?php echo $submit_words  ?>;
    console.log(submit_words);
    var correct = <?php echo json_encode($answers) ?>;
    var collectAnswers = [];
    var userAnswers = [];
    var points = 0;
    var questionLength = <?php echo sizeOf($questions); ?>;
    var token = "<?php echo $_SESSION['token']; ?>"
    var click_count = 0;

    function prepareArray() {
        collectAnswers = [];
        userAnswers = [];
        points = 0; // Reset points on each prepareArray call

        for (var i = 0; i < questionLength; i++) {
            var inputElement = $(".get-answer").eq(i); // Get the input element

            if (inputElement.length > 0) { // Check if the element exists
                var value = inputElement.val();
                value = value ? value.trim().toLowerCase() : ""; // Ensure value is not null or undefined
                collectAnswers.push(value);
                var lowercorrect = correct[i].toLowerCase();
                if (lowercorrect === value) {
                    userAnswers.push(correct[i]);
                    points = points + 1;
                    $("#points").html(points);
                    console.log("correct");
                    console.log("points", points);
                } else {
                    console.log("incorrect");
                }

            } else {
                console.warn(`Input at index ${i} not found.`);
            }
        }
        send();
    }
    $(".unscramble-input").on("keyup", function() {
        var value = $(this).val().trim().toLowerCase();
        var pos = $(this).attr("pos");

        if (pos !== undefined && correct[pos].toLowerCase() === value) {
            if (!$(this).data("correct")) { // Check if already marked correct
                $(this).css("background", "green");
                $(this).css("color", "white");

                $(this).attr("readonly", true);
                $(this).data("correct", true); // Mark as scored
                points = points + 1;
                $("#points").html(points); // Update points in HTML
                $('#popup-container').fadeIn();
                setTimeout(() => {
                    $('#popup-container').fadeOut();
                }, 1500);
                click_count = click_count + 1;
            }
        } else {
            $(this).css("background", "red");
            $(this).css("color", "white");
            click_count = click_count + 1;
        }

        console.log("this", click_count);

        checkSubmitButtonVisibility(); // ✅ Call to check visibility of submit button
    });



    function send() {

        $.ajax({
            type: 'post',
            url: 'ajaxcall/question-insert.php',
            data: {
                answers: collectAnswers.toString(),
                question: 5,
                points: points,
                timer: target
            },
            success: function(data) {
                console.log("response check----", data.trim());
                if (sessionId == "demobypass") {
                    swal("Subscribe to any PLAN to play with your peers.", "", "success").then(() => {
                        window.location = "<?php echo $base_url ?>/plans";
                    });
                } else if (data.trim() === "true") {
                    window.location = "think-fast.php";
                } else {
                    // handle other cases
                    // alert(data);
                }
            }
        });
    }


    $(".button-submit").click(function() {
        $(this).prop('disabled', true);
        prepareArray();
    });
    // $('#rules').modal('show');
    $(".rules").click(function() {
        $('#rules').modal('show');
    });
    </script>

</body>

</html>