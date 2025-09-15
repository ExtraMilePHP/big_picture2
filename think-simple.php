<?php
    ob_start();
    error_reporting(0);
    session_start();

    if ($_SESSION['token'] == "") {
        header('location:index.php');
    }

    include_once 'dao/config.php';

$settings = json_decode(file_get_contents("admin/settings.js"), true)[0];
include_once '../admin_assets/triggers.php';

include_once 'admin/themes/themesets.php';
include_once 'admin/themes/themeTools.php';

require '../vendor/autoload.php'; // Include the Composer autoload file
include_once "s3/s3_functions.php"; // s3 function for easy upload and get signed urls, still need env file and autoload
// include_once 'admin/process-questions1.php';

    $userid = $_SESSION['userId'];
    $email = $_SESSION['email'];
    $organizationId = $_SESSION['organizationId'];
    $sessionId = $_SESSION['sessionId'];
    $roles = $_SESSION['roles'];
    $points = $_POST['points'];

    if (function_exists('date_default_timezone_set')) {
        date_default_timezone_set("Asia/Kolkata");
    }
    $stage = 2;
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

    $getId11="select * from question where userid='$userid' and organizationId='$organizationId' and sessionId='$sessionId'";
    // echo  $getId11;
    $getId11=execute_query($getId11);
    $getId11=mysqli_fetch_object($getId11);
    $score=$getId11->score;
    $question2=$getId11->question2;
    if(isset($question2)){
        header("Location: think-closer.php");
    }
    $data = fetchThemeData();



    $curruntTheme = $data["themeName"]; // Fetch theme name
    $_SESSION['themename'] = $curruntTheme; // Store theme name in session
    
    $rules = unserialize($data["rules"]);
    
    $query = "SELECT * FROM pairs WHERE organizationId='$organizationId' AND sessionId='$sessionId' and themename='$curruntTheme'";
    // echo  $query;
    $result = execute_query($query);
    $rowCount = mysqli_num_rows($result);
  
    ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Big Picture 2.0</title>
    <link href="css/bootstrap.min.css" type="text/css" rel="stylesheet">
    <link href="css/style.css" type="text/css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/sweetalert.css" rel="stylesheet">
    <script src="js/jquery.min.js"></script>
    <script src="js/sweetalert.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <style>
    input {
        pointer-events: auto !important;
        z-index: 1000 !important;
        position: relative;
    }

    .score {
        position: absolute;
      
        text-align: right;
        border: 1px solid #fff;
        float: right;
        padding: 4px 11px;
        border-radius: 10px;
        font-size: 18px;
       
        margin-right: 25px;
        z-index: 9999999999;
        margin-top: 20px;
           color: <?php echo $data["landing_page_title_color"];
        ?>;
        background-color: <?php echo $data["landing_page_button_bgcolor"];
        ?>
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

    @media screen and (min-width:320px) and (max-width:940px) and (orientation:landscape) {


        #timerBox {
            position: absolute;
            top: 28px;
            left: 6%;
            width: 15%;
            font-size: 23px;
            background: black;
            border-radius: 11px;
               color: <?php echo $data["landing_page_title_color"];
        ?>;
        background-color: <?php echo $data["landing_page_button_bgcolor"];
        ?>
        }

        #PointBox {
            position: absolute;
            top: 28px;
            left: 6%;
            width: 15%;
            font-size: 23px;
            background: black;
            border-radius: 11px;
            float: right;
   color: <?php echo $data["landing_page_title_color"];
        ?>;
        background-color: <?php echo $data["landing_page_button_bgcolor"];
        ?>
        }



    }

    .chan2bg {
        background: url(image/bg.png);
        margin: 0px;
        padding: 0px;
        background-size: 100%;
        background-attachment: fixed;
    }



    .row {
        margin-right: 0px !important;
        margin-left: 0px !important;
    }

    h4 {
        font-size: 22px;
    }

    @media (max-width: 766px) {
        h4 {
            font-size: 18px;
        }
    }

    .btn-login {
        width: max-content;
        margin: auto;
        position: relative;
        display: block;
           color: <?php echo $data["landing_page_button_color"];
        ?>;
        background-color: <?php echo $data["landing_page_button_bgcolor"];
        ?>
    }

    .countdown {
        background-color: #120f86;
        width: 75px;
        padding: 9px;
        height: 53px;
        border-radius: 50%;
        font-size: 22px;
        text-align: center;
        color: #ffffff;
    }
    </style>

</head>

<body class="loginbg">
    <style>
    .loginbg {
        /* background: url(../img/bg.jpg); */
        background: url(<?php echo possibleOnS3("uploads/", $data["background_desk"]) ?>);
        margin: 0px;
        padding: 0px;
        background-size: 100% 100%;
        background-repeat: no-repeat;
        background-size: cover;
        background-repeat: no-repeat;
        background-attachment: fixed;

    }


    .loginbg1 {
        background: url(<?php echo possibleOnS3("uploads/", $data["background_mob"]) ?>);
        /* background: url(../img/bg.jpg); */
        margin: 0px;
        padding: 0px;
        background-size: 100% 100%;
        background-attachment: fixed;
        background-repeat: no-repeat;
    }

    th,
    td {
        padding: 0px;

    }

    .details {
        font-size: 16px !important;
    }

    .mtop1 {
        margin: 10px 0px;
        font-size: 20px;
        color: <?php echo $data["landing_page_title_color"];
        ?>
    }

    h4 {
        color: <?php echo $data["landing_page_title_color"];
        ?>
    }

    @media(min-width:100px) and (max-width:720px) {
        .timer-back {
            position: absolute;
            font-size: 15px;
            font-weight: 100 !important;
            background-color: #000 !important;
            color: #fff;
            border-radius: 10px;
            border: 1px solid #ffffff;
            text-align: center;
            padding: 5px;
            width: 96px;
            background-image: none !important;
            margin-left: -22px;
            z-index: 9999999999;
            margin-top: 66px;
        }

        .score {
            position: absolute;
            font-size: 15px;
            font-weight: 100 !important;
            background-color: #000 !important;
            color: #fff;
            border-radius: 10px;
            border: 1px solid #ffffff;
            padding: 5px;
            width: 86px;
            background-image: none !important;
            margin-left: 28px;
            z-index: 9999999999;
            margin-top: 66px;
            text-align: right;
            border: 1px solid #fff;
            float: right;
            padding: 5px 10px;
        }

        .mtop1 {
            margin-top: 50px;
            font-size: 15px;
            color: <?php echo $data["landing_page_title_color"];
            ?>
        }
    }
    </style>
    <?php include("../actions-default.php");
    back("think-fast.php"); ?>

    <div class="container-fluid mob-margin" style="margin-top:10px;">
        <div class="row ">
            <div class="col-sm-4 col-md-4 col-lg-4 col-xs-8" style="display:inline-flex; ">
                <div class="timer-back" id="timer">Time: 00:00</div>
            </div>

            <div class="col-sm-2 col-md-2 col-lg-2 col-xs-4" style="float:right;">
                <div class="score">Points: <span id="points"><?php echo $score; ?></span></div>
            </div>
        </div>

    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-2 col-md-2 col-lg-2 col-xs-12">
                <!-- <div class="countdown"></div> -->
            </div>
            <div class="row">
                <div class="col-sm-9 col-md-9 col-lg-9 col-xs-10 auto">
                    <div class="col-sm-3 col-md-3 col-lg-3 col-xs-8 auto">
                        <img src="img/chall2.png" class="" alt="" style="width:100%;">
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xs-12 mtop1">

                        <h4 style="font-weight:800;text-align:center;">
                            <?php echo $data["landing_page_title2"]; ?></h4>
                    </div>

                    <?php
                        function is_image($filename) {
                            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                            $image_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                            return in_array($ext, $image_extensions);
                        }

                        $max_questions = $data["questions_02"];
                        $query = "SELECT * FROM pairs WHERE organizationId='$organizationId' AND sessionId='$sessionId' AND themename='$curruntTheme' LIMIT $max_questions";
                        $result = execute_query($query);
                        $rowCount = mysqli_num_rows($result);

                        $question_counter = 1;
                    ?>
                    <form class="" id="idForm" action="" method="post">
                        <input class="timer" name="timer" type="hidden" />
                        <input type="hidden" id="maxQuestions" value="<?php echo $max_questions; ?>" />

                        <?php
                        $question_counter = 0;
                        $total_questions = mysqli_num_rows($result); // Get total number of questions

                        while ($row = mysqli_fetch_assoc($result)) {
                            $item1 = $row['item1'];
                            $item2 = $row['item2']; // Correct answer

                            echo '<div class="col-sm-3 col-md-2 col-lg-3 col-xs-12" style="margin-bottom:20px;">';
                            echo '<div class="row">';
                            
                            // Question (image or text)
                            echo '<div class="col-sm-12 col-md-12 col-lg-12 col-xs-12">';
                            if (is_image($item1)) {
                                echo '<img src="upload_img/' . $item1 . '" class="" alt="question" style="width:100%;margin-top:0px;">';
                            } else {
                                echo '<h3>' . $item1 . '</h3>';
                            }
                            echo '</div>';

                            // Answer input field below the question
                            echo '<div class="col-sm-12 col-md-12 col-lg-12 col-xs-12">';
                            echo '<input type="text" class="form-control login-text" id="ans' . $question_counter . '" name="ans' . $question_counter . '" data-correct-answer="' . $item2 . '" required />';
                            echo '</div>';

                            echo '</div>';
                            echo '</div>';
                            
                            $question_counter++;
                        }

                        ?>
                        <div class="col-md-12 text-center" style="margin-top:20px;">
                            <input type="submit" name="Submit" value="SUBMIT" class="btn btn-login">
                        </div>
                </div>
            </div>


            </form>

        </div>
    </div>
    </div>

    <script>
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
    var sessionId = "<?php echo $sessionId ?>";

    $(document).ready(function() {
        var points = parseInt("<?php echo $score ?>");

        $(document).on('keyup', "input[type='text']", function() {
            this.value = this.value.trimStart();
            var userInput = $(this).val().trim().toLowerCase();
            var correctAnswer = $(this).attr('data-correct-answer');

            if (!correctAnswer) {
                console.log("Missing data-correct-answer for: ", $(this));
                return;
            }

            correctAnswer = correctAnswer.trim().toLowerCase();

            console.log("User input: " + userInput);
            console.log("Correct answer: " + correctAnswer);

            // Check if the answer is correct
            if (userInput === correctAnswer) {
                if (!$(this).data('correct')) {
                    $(this).css({
                        'background-color': 'green',
                        'color': 'white',
                        'border': '2px solid darkgreen'
                    });
                    $(this).prop("readonly", true);
                    $(this).css("pointer-events", "none");
                    $(this).data('correct', true);
                    points += 1;
                    $('#points').text(points);
                }
            } else {
                if (!$(this).data('correct')) {
                    $(this).css({
                        'background-color': 'red',
                        'color': 'white',
                        'border': '2px solid darkred'
                    });
                }
            }

        });

        $("#idForm").submit(function(e) {
            let isValid = true;

            $("input[type='text']").each(function() {
                let val = $(this).val().trim();

                if (val === "") {
                    isValid = false;
                    // Set field to empty so required validation triggers
                    $(this).val("");
                }
            });

            if (!isValid) {
                // Let HTML5 validation trigger native "This field is required" popup
                return;
            }

            e.preventDefault();

            console.log("Form submitted");
            var timer1 = $('.timer').val();
            var question_ans = "";
            var maxQuestions = $('input[id^="ans"]').length;

            for (var i = 0; i < maxQuestions; i++) {
                var ans = $('#ans' + i).val();
                question_ans += i + '-' + ans;
                if (i < maxQuestions - 1) question_ans += ', ';
            }

            console.log("points: " + points);

            $.ajax({
                type: 'post',
                url: 'ajaxcall/question-insert.php',
                data: {
                    question_ans: question_ans,
                    question: 2,
                    timer: timer1,
                    points: points
                },
                success: function(data) {
                    if (data.trim() === "true") {
                        window.location = "think-closer.php";
                    } else {
                        console.log("failed");
                    }
                },
                error: function(xhr, status, error) {
                    console.log("AJAX request failed: " + error);
                }
            });
        });

    });
    </script>
</body>

</html>