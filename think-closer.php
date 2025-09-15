<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Big Picture</title>
    <link href="css/bootstrap.min.css" type="text/css" rel="stylesheet">
    <link href="css/style.css" type="text/css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/sweetalert.css" rel="stylesheet">
    <script src="js/jquery.min.js"></script>
    <script src="js/sweetalert.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <style>
  
    </style>
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

require '../vendor/autoload.php'; 
include_once "s3/s3_functions.php"; 

    if (function_exists('date_default_timezone_set')) {
        date_default_timezone_set("Asia/Kolkata");
    }
    $stage = 3;
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
    $userid = $_SESSION['userId'];

    date_default_timezone_set("Asia/Kolkata");
    $timestamp = date('Y-m-d H:i:s');
    
    $sessionId = $_SESSION['sessionId'];
    $organizationId = $_SESSION['organizationId'];
    
    $getId11="select * from question where userid='$userid' and organizationId='$organizationId' and sessionId='$sessionId'";
    // echo  $getId11;
    $getId11=execute_query($getId11);
    $getId11=mysqli_fetch_object($getId11);
    $question3=$getId11->question3;
    $score=$getId11->score;

    $data = fetchThemeData();
if(isset($question3)){
    header("Location: puzzle-game.php");
}

    if(!empty($question3)){
    //   header("Location: puzzle-game.php");
    //    echo  '<script>swal("You have already played", "", "success");
    //    setTimeout(function(){
    //        location.href="CHALLENGE4.php";
    //    },100);</script>
    //    ';
    }


    ?>
</head>

<body class="loginbg">
    <?php include("../actions-default.php");
    back("think-simple.php"); ?>
    <style>
    .score {
        position: absolute;
        text-align: right;
        border: 1px solid #fff;
        float: right;
        padding: 6px 11px;
        border-radius: 10px;
        font-size: 18px;

        background-image: none !important;
        margin-right: 12px;
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
    .details {
        font-size: 24px !important;
        color: <?php echo $data["landing_page_title_color"];
        ?>
    }

    .details-1 {
        font-size: 22px;
        color: <?php echo $data["landing_page_title_color"];
        ?>
    }

    .details-2 {
        font-size: 19px;
        color: <?php echo $data["landing_page_title_color"];
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
        }

        #PointBox {
            position: absolute;
            top: 28px;
            left: 6%;
            width: 15%;
            /* border: 1px solid #fff; */
            font-size: 23px;
            background: black;
            border-radius: 11px;
            float: right;

        }



    }

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

    .bottom-img2 {
        position: relative;
        bottom: 0%;
    }

    .row {
        margin-right: 0px !important;
        margin-left: 0px !important;

    }

    .mtop1 {
        margin: 10px 0px;
        font-size: 20px;
        color: <?php echo $data["landing_page_title_color"];
        ?>
    }

    @media(min-width:100px) and (max-width:720px) {
        .timer-back {
            position: absolute;
            font-size: 15px;
            font-weight: 100 !important;
            border-radius: 10px;
            border: 1px solid #ffffff;
            text-align: center;
            padding: 5px;
            width: 96px;
            margin-left: -22px;
            z-index: 9999999999;
            margin-top: 66px;
            color: <?php echo $data["landing_page_title_color"];
        ?>;
        background-color: <?php echo $data["landing_page_button_bgcolor"];
        ?>
        }

        .score {
            position: absolute;
            font-size: 15px;
            font-weight: 100 !important;
            border-radius: 10px;
            border: 1px solid #ffffff;
            padding: 5px;
            width: 85px;
            background-image: none !important;
            margin-left: 28px;
            z-index: 9999999999;
            margin-top: 66px;
            text-align: right;
            border: 1px solid #fff;
            float: right;
            padding: 5px 10px;
            color: <?php echo $data["landing_page_title_color"];
        ?>;
        background-color: <?php echo $data["landing_page_button_bgcolor"];
        ?>
        }

        .mtop1 {
            margin-top: 50px;
            font-size: 15px;
            color: <?php echo $data["landing_page_title_color"];
            ?>
        }
    }
    </style>
    <div class="container-fluid">

        <div class="container-fluid mob-margin" style="margin-top:10px;">
            <div class="row ">
                <div class="col-sm-4 col-md-4 col-lg-4 col-xs-8" style="display:inline-flex; ">
                    <div class="timer-back" id="timer">Time:00:00</div>
                </div>

                <div class="col-sm-2 col-md-2 col-lg-2 col-xs-4" style="float:right;">
                    <div class="score">Points: <span id="points"><?php echo $score; ?></span></div>
                </div>
            </div>

        </div>
    </div>


    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-2 col-md-2 col-lg-2 col-xs-12">
                <!-- <div class="countdown"></div> -->
            </div>
            <div class="col-sm-10 col-md-10 col-lg-10 col-xs-12">
                <div class="row">
                    <input class="timer" name="timer" type="hidden" />
                    <div class="col-sm-9 col-md-9 col-lg-9 col-xs-12">
                        <div class="col-sm-3 col-md-3 col-lg-3 col-xs-8 auto">
                            <img src="img/chall3.png" class="" alt="" style="width:100%;">
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xs-12 mtop1">

                            <div id="head-logo">
                                <p class="details"> <?php echo $data["landing_page_title3"]; ?></p>
                            </div>
                            <br><br>
                            <p class="details-1">
                                <b><?php echo $data["landing_page_title3_eg"]; ?></b><br>
                            </p>
                            <p class="details-2">
                                <?php echo $data["landing_page_title3_question"]; ?>
                            </p>

                            <form class="" id="idForm" action="" method="post">
                                <div class="form-group">
                                    <input type="hidden" class="form-control login-text" id="question" name="question"
                                        value="3">
                                    <input type="text" class="form-control login-text" id="question_ans"
                                        name="question_ans" placeholder="Insert Your lyrics" required>
                                </div>
                                <div class="sub-btn">
                                    <div class="col-sm-4 col-md-4 col-lg-4 col-xs-6 auto">
                                        <input type="submit" name="Submit" value="SUBMIT" class="btn btn-login">
                                    </div>
                                </div><br>
                            </form>

                        </div>

                    </div>
                </div>
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

    var organizationId = "<?php echo $organizationId ?>";

    var sessionId = "<?php echo $sessionId ?>";
    var points = parseInt("<?php echo $score ?>");
    $(document).ready(function() {
        // Clear custom validity message on input
        $('#question_ans').on('input', function() {
            var inputVal = $(this).val();
            if ($.trim(inputVal) !== "") {
                this.setCustomValidity("");
            }
        });

        $("#idForm").submit(function(e) {
            var inputVal = $('#question_ans').val();

            if ($.trim(inputVal) === "") {
                $("#question_ans")[0].setCustomValidity("This field is required");
                $("#question_ans")[0].reportValidity();
                e.preventDefault();
                return false;
            } else {
                $("#question_ans")[0].setCustomValidity("");
            }

            $('#idForm').css("pointer-events", "none");

            var timer1 = $('.timer').val();
            points += 1;
            $('#points').text(points);

            e.preventDefault();

            $.ajax({
                type: 'post',
                url: 'ajaxcall/question-insert.php',
                data: {
                    question_ans: inputVal,
                    question: 3,
                    timer: timer1,
                    points: points
                },
                success: function(data) {
                    data = data.trim();
                    console.log(data);
                    if (data == "true") {
                        setTimeout(function() {
                            window.location = "puzzle-game.php";
                        }, 1000);
                    }
                }
            });
        });
    });
    </script>
</body>

</html>