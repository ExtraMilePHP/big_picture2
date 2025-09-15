<?php
    ob_start();
    // error_reporting(0);
    session_start();
    error_reporting(0);
    if ($_SESSION['token'] == "") {
        header('location:index.php');
    }

    include_once 'dao/config.php';
    include_once '../add_report.php';
    
    $userid = $_SESSION['userId'];
    $email = $_SESSION['email'];
    $organizationId = $_SESSION['organizationId'];
    $sessionId = $_SESSION['sessionId'];
    $roles = $_SESSION['roles'];
    $gameId = $_SESSION['gameId'];
    $fullName = $_SESSION['firstName'] . " " . $_SESSION['lastName'];
    $puzzleimg = rand(1, 9);
   $points = $_POST['points'];
include_once 'admin/themes/themesets.php';
include_once 'admin/themes/themeTools.php';

require '../vendor/autoload.php'; // Include the Composer autoload file
include_once "s3/s3_functions.php"; // s3 function for easy upload and get signed urls, still need env file and autoload

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
    $data = fetchThemeData();
    $curruntTheme=$data["themeName"];
            
    $getId11 = "SELECT * FROM questions WHERE organizationId='$organizationId' AND sessionId='$sessionId' AND themename = '$curruntTheme' ";
    // echo $getId11;
    $getId11 = execute_query($getId11);
    $dataarr = [];
    
    while ($row = mysqli_fetch_assoc($getId11)) {
        $dataarr[] = $row['question_name'];
    }
    
    // Shuffle the questions
    shuffle($dataarr);
    
    $numQuestionsToDisplay = (int) $data["questions_01"];
    
    $dataarr = array_slice($dataarr, 0, $numQuestionsToDisplay);

    $getId12="select * from question where userid='$userid' and organizationId='$organizationId' and sessionId='$sessionId'";
    // echo  $getId11;
    $getId12=execute_query($getId12);
    $getId12=mysqli_fetch_object($getId12);
    $score=$getId12->score;
    $question1=$getId12->question1;
    if(isset($question1)){
        header("Location: think-simple.php");
    }

    ?>
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
    .chan1bg {
        background: url(image/bg.png);
        margin: 0px;
        padding: 0px;
        background-size: 100%;
        background-attachment: fixed;
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
    </style>
 
</head>

<body class="loginbg">
    <style>
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

    .details {

        color: <?php echo $data["landing_page_title_color"];
        ?>;
        font-size: 16px !important;
    }

    .mtop1 {
        margin: 10px 0px;
        text-align:center;
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
        width: 97px;
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
        font-size: 14px;
        font-weight: 100 !important;
   
        border-radius: 10px;
        border: 1px solid #ffffff;
        padding: 5px;
        width: 91px;
        margin-left: 22px;
        z-index: 9999999999;
        margin-top: 66px;
        text-align: right;
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
             text-align:center;
            color: <?php echo $data["landing_page_title_color"];
            ?>
        }
    }
    </style>
    <?php include("../actions-default.php");
    back("rules.php"); ?>
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

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-2 col-md-2 col-lg-2 col-xs-12">
                <!-- <div class="countdown"></div> -->
            </div>
            <div class="col-sm-10 col-md-10 col-lg-10 col-xs-12 auto">
                <div class="row">
                    <div class="col-sm-9 col-md-9 col-lg-9 col-xs-12 auto">
                        <div class="col-sm-3 col-md-3 col-lg-3 col-xs-8 auto">
                              <img src="img/chall2.png" class="" alt="" style="width:100%;">
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xs-12 mtop1">
                            <?php echo htmlspecialchars($data["landing_page_title2"], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <form id="idForm" action="" method="post">
            <input class="timer" name="timer" type="hidden" />
            <input class="question" name="question" value="1" type="hidden" />

            <?php
  
    for ($i = 0; $i < count($dataarr); $i++) {
        $question = $dataarr[$i];
        $answerId = 'ans' . ($i + 1); 
        echo '
        <div class="row">
            <div class="col-sm-10 col-md-10 col-lg-10 col-xs-12 auto">
                <div class="col-sm-8 col-md-8 col-lg-8 col-xs-12">
                    <h4 class="details" ><b>' . ($i + 1) . '. ' . $question . '</b></h4>
                </div>
                <div class="col-sm-4 col-md-4 col-lg-4 col-xs-12">
                    <input type="text" class="form-control login-text" maxlength="200" id="' . $answerId . '" name="' . $answerId . '" required>
                </div>
            </div>
        </div>';
    }
    ?>
            <div class="row" style="margin-top:20px;">
                <div class="col-sm-4 col-md-3 col-lg-3 col-xs-5 auto">
                    <input type="submit" name="Submit" value="SUBMIT" class="btn btn-login">
                </div>
            </div>
        </form>

    </div>

    <script>
          var points = parseInt("<?php echo $score ?>");
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
    var organizationId = "<?php echo $organizationId ?>";
  
    function timer() {
        t = setTimeout(add, 1000);
    }
    timer();
    var sessionId = "<?php echo $sessionId ?>";
    var organizationId = "<?php echo $organizationId ?>";
    $(document).ready(function() {
        $("#idForm").submit(function(e) {
            var isValid = true;

            $('input[type="text"]').each(function() {
                var answer = $(this).val();

                var trimmedAnswer = answer.trim().replace(/\s+/g, ' ');

                if (answer !== trimmedAnswer) {
                    $(this).val(trimmedAnswer);
                    isValid = false;
                }
            });

            if (!isValid) {
                return false;
            }

            var question_ans = [];
            $('input[type="text"]').each(function(index) {
                var answer = $(this).val().trim();

                if (answer !== "") {
                    question_ans.push((index + 1) + '-' + answer);
                }
            });

            if (question_ans.length === 0) {
                alert("Please answer at least one question.");
                return false;
            }
            points += 1;
            $.ajax({
                type: 'post',
                url: 'ajaxcall/question-insert.php',
                data: {
                    question_ans: question_ans.join(", ").trim(),
                    question: 1,
                    points: points,
                    timer: target
                },
                success: function(data) {
                    console.log(data.trim());
                    if (data.trim() === "true") {
                       window.location = "think-closer.php";
                    }
                }
            });

            return false;
        });
    });
    </script>
</body>

</html>