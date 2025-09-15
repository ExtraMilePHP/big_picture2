<?php
session_start();
error_reporting(0);
include_once 'dao/config.php';
$userid = $_SESSION['userId'];
$userId = $_SESSION['userId'];
$organizationId = $_SESSION['organizationId'];
$sessionId = $_SESSION['sessionId'];
$email = $_SESSION["email"];
$fullName = $_SESSION['firstName'] . " " . $_SESSION['lastName'];
$roles = $_SESSION['roles'];
$timestamp = date('Y-m-d H:i:s');

$settings = json_decode(file_get_contents("admin/settings.js"), true)[0];
include_once '../admin_assets/triggers.php';

include_once 'admin/themes/themesets.php';
include_once 'admin/themes/themeTools.php';

require '../vendor/autoload.php'; 
include_once "s3/s3_functions.php"; 

$data = fetchThemeData();
// print_r($data);

$image = default_data("logo");
$title = default_data("title");
$getRows =  $data["row"];
$getCols =  $data["col"];
$getCols =  $data["col"];

// Assuming $data["img_array"] contains serialized array of image filenames
$puzzle_img = unserialize($data["img_array"]);

// Prepend the correct path to each image
$puzzle_img = array_map(function($img) {
    return possibleOnS3("uploads/", $img);
}, $puzzle_img);

// print_r($puzzle_img);

// For the main image display
$current_img = possibleOnS3("uploads/", $img);






$getId11 = "SELECT * FROM question WHERE userid='$userid' AND organizationId='$organizationId' AND sessionId='$sessionId'";
$getId11 = execute_query($getId11);
$getId11 = mysqli_fetch_object($getId11);

$score = $getId11->score;
$completed = $getId11->completed;
// echo $completed;
if(isset($completed)){
    header("Location: thankyou.php");
}

if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set("Asia/Kolkata");
}
$stage = 4;  
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

// echo "Elapsed Time: $hours hours, $minutes minutes, $seconds seconds";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Big Picture</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="css/style.css" type="text/css" rel="stylesheet">
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <script src="js/sweetalert.min.js"></script>
    <link rel="stylesheet" href="css/puzzle.css">

    <!-- <script type="text/javascript" src="js/jquery.ui.touch-punch.min.js"></script>
    <script type="text/javascript" src="js/jquery.ui.touch-punch.js"></script> -->

    <style type="text/css">
    html {
        height: 100%;
    }

    body {
        overflow: hidden;
    }

    .chall {
        top: -100px;
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

    @media screen and (min-width: 768px) {
        .chan2bg {
            background: url(<?php echo possibleOnS3("uploads/", $data["background_desk"]) ?>);
            background-size: cover;
            width: 100%;
            background-repeat: no-repeat;
            height: 100%;
            position: relative;
            background-attachment: fixed;
            background-size: cover;
            background-position: 100% 100%;
        }

        #puzzle1 {
            margin-top: 160px !important;
        }

        .col-sm-3.col-md-3.col-lg-3.col-xs-12.auto.puzzleimg1 {
            max-width: 30% !important;
            flex: 0 0 30% !important;
        }

        .maingame {
            position: absolute;
            width: 100%;
            top: 150px;
        }

        body {
            /* overflow: scroll; */
        }

        .maskat {
            width: 100%;
            margin-top: 10px;
        }

        .mainHead {
            text-align: center;
            top: -75px;
            text-align: center;
            font-weight: bold;
            font-size: 20px;
            color: <?php echo $data["landing_page_title_color"];

            ?>
        }
    }

    @media screen and (max-width: 768px) {
        .chan2bg {
            background: url(<?php echo possibleOnS3("uploads/", $data["background_mob"]) ?>);
            background-size: cover;
            width: 100%;
            background-repeat: no-repeat;
            height: 100%;
            position: relative;
            background-attachment: fixed;
            background-size: cover;
            background-position: 100% 100%;
        }

        .maskat {
            width: 71%;
            left: 84px;
        }

        .mainHead {
            font-weight: bold;
            text-align: center;
            color: <?php echo $data["landing_page_title_color"];
            ?>
        }


    }

    .custom-popup {
        position: fixed;
        top: 0;
        width: 100%;
        height: 100vh;
        font-size: 20px;
        text-align: center;
        z-index: 999;
        background: #00000070;
        display: none;
    }

    .popup-top {
        height: auto;
        background-size: 100% 100%;
        background-repeat: no-repeat;
        width: 60%;
        padding: 50px 0px;
        color: white;
        border-radius: 20px;
        padding-top: 10px;
        text-align: center;
        margin: 0 auto;
        margin-top: -300px;
        transition: 1s all;
        position: relative;
        margin-top: 14% !important;
        width: 20% !important;
    }

    .auto {
        margin: auto;
        float: none;
    }

    .custom-popup {
        position: fixed;
        top: 0;
        width: 100%;
        height: 100vh;
        font-size: 20px;
        text-align: center;
        z-index: 999;
        background: #00000070;
        display: none;
    }

    .popup-top {
        height: auto;
        background-size: 100% 100%;
        background-repeat: no-repeat;
        width: 60%;
        padding: 50px 0px;
        color: white;
        border-radius: 20px;
        padding-top: 10px;
        text-align: center;
        margin: 0 auto;
        margin-top: -300px;
        transition: 1s all;
        position: relative;

    }

    .timer {
        background: #e9695e;
        color: white;
        font-size: 18px;
        padding: 1px 8px;
        border: none;
        margin-left: 10px;
        margin-right: 15px;
        margin-top: 0px;
        font-weight: bold;
        border-radius: 5px;
        width: 80px;
        text-align: center;
        position: absolute;
        top: 35px;
        right: 15px;
    }

    .desk {
        display: block;
    }

    .mob {
        display: none;
    }

    @media(min-width:100px) and (max-width:768px) {
        .timer {
            top: 0px;
            margin-top: -258px;
        }

        .desk {
            display: none;
        }

        .mob {
            display: block;
        }

        .popup-top {
            margin-top: 46% !important;
            width: 60% !important;
        }

        body {
            overflow: auto;
        }

        .chall {
            top: 0px;
        }
    }

    @media(min-width:721px) and (max-width:1368px) {
        body {
            overflow: auto;
        }

    }

    body::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1;
    }

    body * {
        position: relative;
        z-index: 2;
    }

    html,
    body {
        height: 100%;
    }

    .container-fluid.upperaction {
        margin-top: 0px !important;
        padding-top: 10px;
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

    .mtop1 {
        margin: 10px 0px;
        top: -112px;
        font-size: 20px;
        color: <?php echo $data["landing_page_title_color"];
        ?>
    }

    /* Add this to your CSS */
    .frame {
        width: 100% !important;
        max-width: 800px !important;
        /* Adjust this value as needed */
        margin: 0 auto;
        max-height: 481px !important;
    }

    #puzzle1 {
        width: 100%;
        max-width: 800px;
        /* Match the frame width */
        margin: 20px auto;
    }


    @media(min-width:100px) and (max-width:720px) {
        .timer-back {
            position: absolute;
            font-size: 15px;
            font-weight: 100 !important;

            border-radius: 10px;
            border: 1px solid #ffffff;
            text-align: center;
            padding: 7px;
            width: 101px;
            margin-left: -4px;
            z-index: 9999999999;
            margin-top: 14px;
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
            width: 88px;
            margin-left: 18px;
            z-index: 9999999999;
            margin-top: 14px;
            text-align: right;
            float: right;
            padding: 5px 10px;
            color: <?php echo $data["landing_page_title_color"];
            ?>;
            background-color: <?php echo $data["landing_page_button_bgcolor"];
            ?>
        }


        .mtop1 {
            margin-top: 103px;
            font-size: 15px;
            color: <?php echo $data["landing_page_title_color"];
            ?>
        }
    }

      @media (min-width: 600px) and (max-width: 1200px) {

        .mtop1 {
            margin: 10px 0px;
            font-size: 14px;
            text-align: center;
        }
    .mainHead {
        font-size: 15px;
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

    }
    </style>
</head>

<body class="chan2bg">
    <?php

    ob_start();
    error_reporting(0);
    session_start();
    if ($_SESSION['token'] == "") {
        header('location:index.php');
    }

    include_once 'dao/config.php';

    $userid = $_SESSION['userId'];

    $organizationId = $_SESSION['organizationId'];
    $sessionid = $_SESSION['sessionId'];
    // $find = "select * from stat where userid='$userid'";

    // $find = execute_query($find);
    // $find1 = mysqli_num_rows($find);
    // $find2 = mysqli_fetch_object($find);

    // $img = possibleOnS3("uploads/", $data["puzzle_image"])





    ?>
    <?php include("../actions-default.php");
    back("rules.php"); ?>

    <div class="container-fluid mob-margin" style="margin-top:10px;">
        <div class="row ">
            <div class="col-sm-4 col-md-4 col-lg-4 col-xs-8">
                <div class="timer-back" id="timer">Time:00:00</div>
            </div>
            <div class="col-sm-2 col-md-2 col-lg-2 col-xs-4" style="float:right;">
                <div class="score">Points: <span id="points"><?php echo $score; ?></span></div>
            </div>
        </div>
    </div>
    <div class="container-fluid maingame">
        <div class="row">
            <div class="col-sm-9 col-md-9 col-lg-9 col-xs-10 auto">
                <div class="col-sm-3 col-md-3 col-lg-3 col-xs-8 auto">
                    <img src="img/chall4.jpg" class="chall" alt="" style="width:100%;">
                </div>

                <p class="mainHead">
                    <?php echo $data["landing_page_title4"]; ?></p>
            </div>
            <div class="col-sm-2 col-md-2 col-lg-2 col-xs-7 text-center">
                <img src="<?php echo $current_img; ?>" alt="" class="maskat" style=""><br><br>
            </div>
            <div class="col-sm-10 col-md-10 col-lg-10 col-xs-5 text-center">
            </div>
        </div>
    </div>
    <div class="container-fluid" style="padding-bottom:20px;">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12 col-xs-12">

                <div class="row">
                    <?php
                  
                        echo '<div class="col-sm-4 col-md-4 col-lg-4 col-xs-12 auto">';
                    
                    ?>
                    <input type="checkbox" name="options" id="options" style="display: none;">
                    <div id="puzzle1"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="custom-popup" style="z-index: 9999;">
        <div class="popup-top">
            <div class="popup-content">
                <img src="" style="width:100%;" class="customimg" />
            </div>
        </div>
    </div>

    <script>
    const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);

    var organizationId;
    var sessionId;
    $(document).ready(function() {
        organizationId = "<?php echo $organizationId; ?>";
        sessionId = "<?php $_SESSION['sessionId']; ?>";

    });



    var sessionId = '<?php echo $sessionId; ?>';
    console.log("sessionId--" + sessionId);
    var target,
        seconds = <?php echo $seconds ?>,
        minutes = <?php echo $minutes; ?>;
    hours = <?php echo $hours; ?>;

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

        target = (minutes ? (minutes > 9 ?
            minutes :
            "0" +
            minutes) : "00") + ":" + (seconds > 9 ? seconds : "0" + seconds);
        $("#timer").html("Time: " + target);
        $("#timer").val(target);
        // console.log("Timer ", target);

        timer();
    }

    function timer() {
        t = setTimeout(add, 1000);
    }
    timer();


    document.addEventListener('DOMContentLoaded', function() {

        var isMobile = false;
        if (window.innerWidth < 850) {
            isMobile = true;
        }


        var points = parseInt("<?php echo $score ?>");

        function closePopup() {
            $(".popup-top").css({
                "margin-top": "-1000px"
            });
            setTimeout(() => {
                $(".custom-popup").hide();
                points += 1;
                window.location.href = "thankyou.php";
            }, 500);

        }

        var isMobile = false;
        if (window.innerWidth < 850) {
            isMobile = true;
        }

        function openPopup() {
            setTimeout(() => {
                $(".popup-top").css({
                    "margin-top": "10%",
                    "width": "40%",
                });
            }, 500);

            $(".customimg").attr("src", "img/Awesome-min.gif");
            setTimeout(() => {
                closePopup();

            }, 3500);
            $(".custom-popup").show();
        }


        function sendData() {
            //alert(target);
            $.ajax({
                type: 'post',
                url: 'ajaxcall/question-insert.php',
                data: {
                    question: 4,
                    timer: target,
                    points: points
                },
                success: function(data) {
                    console.log(data.trim());
                    if (data.trim() === "true") {
                      //  window.location = "thankyou.php";
                         setTimeout(function() {
                    window.location = "thankyou.php";
                }, 10000);
                    }
                }
            });
        }

        var puzzle_img = <?php echo json_encode($puzzle_img); ?>;
        console.log("puzzle_img--", puzzle_img);

        function getRandomImage() {
            if (puzzle_img && puzzle_img.length > 0) {
                const randomIndex = Math.floor(Math.random() * puzzle_img.length);
                return puzzle_img[randomIndex];
            }
            return "<?php echo $current_img; ?>"; // fallback to the original image
        }
        window.addEventListener('touchmove', function(e) {
            if (window.scrollY <= 0) {
                e.preventDefault();
            }
        }, {
            passive: false
        });

        function getURLParameter(sParam) {
            const sPageURL = decodeURIComponent(window.location.search.substring(1)),
                sURLVariables = sPageURL.split('&');
            let sParameterName,
                i;

            for (i = 0; i < sURLVariables.length; i++) {
                sParameterName = sURLVariables[i].split('=');

                if (sParameterName[0] === sParam) {
                    return sParameterName[1] === undefined ? true : sParameterName[1];
                }
            }
        }

        const opts = {};
        const image = (getURLParameter('image')) ? getURLParameter('image') : getRandomImage();
        // Update the displayed image to match the puzzle
        document.querySelector('.maskat').src = image;
        const puzzle1 = document.querySelector('#puzzle1');
        puzzle1.pz = new Puzzle({
            el: puzzle1,
            image: image,
            difficulty: "expert",
            dragenter: function(evt) {
                onDragEnter(evt)
            },
            dragleave: function(evt) {
                onDragLeave(evt)
            },
            mousedown: function(evt) {
                onMouseDown(evt)
            },
            mouseup: function(evt) {
                onMouseUp(evt)
            },
            finished: function(evt) {
                puzzleFin(evt)
            }
        }).init();

        function onDragEnter(evt) {
            if (!evt.target.draggable) {
                evt.target.classList.add('highlight');
            }
        }

        function onDragLeave(evt) {
            if (!evt.target.draggable) {
                evt.target.classList.remove('highlight');
            }
        }

        function onMouseDown(evt) {
            evt.target.children[0].classList.add('highlight');
        }

        function onMouseUp(evt) {
            evt.target.children[0].classList.remove('highlight');
        }

        function puzzleFin(evt) {
            setTimeout(function() {
                Object.assign(evt.self.fullImg.style, {
                    'opacity': 1,
                    'z-index': 1
                });
            }.bind(evt), 300);

            setTimeout(function() {
                points += 1;
                sendData();
            }, 1500);

        }

        // Update buttons
        document.querySelectorAll('div[data-attr="reset"]').forEach(function(item) {
            item.addEventListener('click', function(evt) {
                updatePuzzle(evt);
            });
        });

        // Make sure number inputs are between 1 and 10
        document.querySelectorAll('input[type="number"]').forEach(function(item) {
            item.addEventListener('change', function() {
                if (Number(this.value) > 10) {
                    this.value = 10;
                } else if (Number(this.value) <= 0) {
                    this.value = 1;
                }
            });
        });

        // Update puzzle and reinitialize
        function updatePuzzle() {
            document.querySelectorAll('.options input').forEach(function(item) {
                if (item.value.length && item.type === "text" || item.type === "number") {
                    opts[item.name] = item.value;
                }

                if (item.value.length && item.type === "radio" && item.checked) {
                    opts[item.name] = item.value;
                }
            });
            puzzle1.pz.usropts = opts;
            puzzle1.pz.init();
        }
    });
    document.addEventListener('DOMContentLoaded', function() {
        // ForEach Nodelist Polyfill IE9
        // https://developer.mozilla.org/en-US/docs/Web/API/NodeList/forEach#Polyfill
        if (window.NodeList && !NodeList.prototype.forEach) {
            NodeList.prototype.forEach = function(callback, thisArg) {
                thisArg = thisArg || window;
                for (let i = 0; i < this.length; i++) {
                    callback.call(thisArg, this[i], i, this);
                }
            };
        }

        this.Puzzle = function(opts) {
            const instance = this;
            this.container = null;
            this.fullImg = new Image();
            this.grid = null;
            this.key = null;
            this.mouseX = null;
            this.mouseY = null;
            this.offsetX = null;
            this.offsetY = null;
            this.touchSlot = null;
            this.clientX = null;
            this.clientY = null;
            this.clientX = null;
            this.clientY = null;
            this.lastPlace = null;
            this.gridSize = null;
            this.usropts = opts;
            this.difficulty = null;
            this.settings = {
                el: null,
                image: '<?php echo $current_img; ?>',
                fullImg: null,
                numcolumns: <?php echo $getCols; ?>,
                numrows: <?php echo $getRows; ?>,
                difficulty: "normal",
                dragstart: function() {},
                dragenter: function() {},
                drag: function() {},
                dragover: function() {},
                dragleave: function() {},
                dragend: function() {},
                drop: function() {},
                touchstart: function() {},
                touchmove: function() {},
                touchhover: function() {},
                touchend: function() {},
                mousedown: function() {},
                mouseup: function() {},
                correct: function() {},
                finished: function() {},
                debug: false
            };


            // Public Methods
            this.init = function() {
                instance.setOpts(instance.usropts);
                instance.setDifficulty(instance.settings.difficulty);

                // set container
                if (instance.settings.el) {
                    instance.container = instance.settings.el;
                } else {
                    console.error(
                        'No "el" option detected. Please specify an element to attach puzzle to.');
                    return false;
                }

                // execute remaining functions after image loads
                let img = new Image();
                let width, height;
                img.onload = function() {
                    // set max height to viewport height
                    if (this.height > window.innerHeight) {
                        height = window.innerHeight;
                        width = height * (this.width / this.height);
                    } else {
                        width = this.width;
                        height = this.height;
                    }

                    // set grid height/width based on image dimensions
                    instance.settings.width = width;
                    instance.settings.height = height;

                    // insert html into DOM
                    instance.grid = buildGrid(instance.settings.numcolumns, instance.settings
                        .numrows);
                    instance.correctTiles();
                    instance.container.innerHTML = "";
                    instance.container.appendChild(instance.grid, instance.container.children[0]);
                    setFrameDimensions(instance.grid, instance.container);
                    setEventHandlers();
                };
                img.src = instance.settings.image;

                return instance;
            };

            this.setOpts = function() {
                // set user options
                Object.keys(instance.usropts).forEach(function(key) {
                    (instance.settings[key] !== (undefined || null || '')) ? instance.settings[
                        key] = instance.usropts[key]: '';
                });

                return instance;
            };

            this.setDifficulty = function(difficulty) {
                switch (difficulty) {
                    case "easy":
                        instance.difficulty = .25;
                        break;

                    case "normal":
                        instance.difficulty = .50;
                        break;

                    case "hard":
                        instance.difficulty = .75;
                        break;

                    case "expert":
                        instance.difficulty = 1;
                        break;

                    default:
                        instance.difficulty = .50;
                }

                instance.usropts.difficulty = difficulty;

                return instance;
            };

            this.setGridSize = function(obj) {
                Object.keys(obj).forEach(function(value) {
                    if (value === "numrows" && typeof Number(obj[value]) == "number") {
                        instance.usropts.numrows = obj[value];
                    }

                    if (value === "numcolumns" && typeof Number(obj[value]) == "number") {
                        instance.usropts.numcolumns = obj[value];
                    }
                });

                return instance;
            };

            this.setImage = function(src) {
                let tmpImg = new Image();
                tmpImg.onload = function() {
                    instance.usropts.image = src;
                };
                tmpImg.src = src;

                return instance;
            };

            this.isSorted = function(array) {
                array = (array) ? array : instance.getTiles();
                let i = 0;
                let keys = Object.keys(array);
                let totalelements = array.length;

                while (totalelements > 1) {
                    // Compare current index against original index
                    if (Number(keys[i]) === Number(array[i][0])) {
                        i++;
                        totalelements--;
                    } else {
                        return false;
                    }
                }

                return true;
            };

            this.getTiles = function() {
                let array = [];

                instance.grid.childNodes.forEach(function(child, index) {
                    if (child.nodeType !== 3) {
                        let arr = [];
                        arr[0] = Number(child.children[0].dataset.position) - 1;
                        arr[1] = child;
                        array[index] = arr;
                    }
                });

                return array;
            };

            this.correctTiles = function(array) {

                array = (array) ? array : instance.getTiles();
                let i = 0;
                let keys = Object.keys(array);
                let totalelements = array.length;
                let number_correct = 0;

                while (totalelements > 0) {
                    // Compare current index against original index
                    if (Number(keys[i]) === Number(array[i][0])) {
                        array[i][1].style.pointerEvents = "none";
                        array[i][1].dataset.inplace = "true";
                        number_correct++;
                    } else {
                        array[i][1].children[0].dataset.inplace = '';
                        array[i][1].style.pointerEvents = "";
                    }

                    i++;
                    totalelements--;
                }

                return number_correct;
            };

            // Private Methods
            function setEventHandlers() {
                let slots = instance.grid.children;

                // Define mouse position while dragging tile
                document.addEventListener('dragover', function(evt) {
                    instance.mouseX = evt.clientX;
                    instance.mouseY = evt.clientY;
                });

                // Reset mouse position after tile has been let go
                document.addEventListener('mousemove', function(evt) {
                    instance.mouseX = evt.clientX;
                    instance.mouseY = evt.clientY;
                });

                // Reset animation class
                document.addEventListener('transitionend', function(evt) {
                    evt.target.classList.remove('animate');
                });

                // Use document move event to hover over other elements
                document.addEventListener('touchmove', function(evt) {
                    if (instance.touchSlot) {
                        if (evt.touches[0].clientY > instance.offsetY &&
                            evt.touches[0].clientX > instance.offsetX &&
                            evt.touches[0].clientY < document.body.offsetHeight - (instance
                                .touchSlot.offsetHeight - instance.offsetY) &&
                            evt.touches[0].clientX < document.body.offsetWidth - (instance.touchSlot
                                .offsetWidth - instance.offsetX)) {

                            // noinspection JSValidateTypes
                            instance.touchSlot.style.zIndex = 10;
                            instance.touchSlot.style.pointerEvents = "none";
                            instance.touchSlot.style.transform = "translate(" + (evt.touches[0]
                                .clientX - instance.clientX) + "px," + (evt.touches[0].clientY -
                                instance.clientY) + "px" + ")";

                            let params = {
                                self: instance,
                                event: evt,
                                target: evt.target
                            };

                            // Map out touchpoints for other dropzones
                            Object.keys(slots).forEach(function(index) {
                                let top = slots[index].getBoundingClientRect().top;
                                let bottom = slots[index].getBoundingClientRect().bottom;
                                let right = slots[index].getBoundingClientRect().right;
                                let left = slots[index].getBoundingClientRect().left;

                                // Do something when hovering over dropzone
                                if (evt.touches[0].clientX > left &&
                                    evt.touches[0].clientX < right &&
                                    evt.touches[0].clientY > top &&
                                    evt.touches[0].clientY < bottom &&
                                    slots[index] !== instance.touchSlot) {

                                    if (!slots[index].style.pointerEvents) {
                                        // Clear last highlighted element
                                        Object.keys(instance.grid.children).forEach(
                                            function(key) {
                                                instance.grid.children[key].classList
                                                    .remove('highlight');
                                            });

                                        slots[index].classList.add('highlight');
                                    }

                                    // user callback
                                    if (instance.settings.touchhover &&
                                        typeof instance.settings.touchhover === "function"
                                    ) {
                                        instance.settings.touchhover(params);
                                    }
                                }

                                instance.clientX = evt.touches[0].clientX;
                                instance.clientY = evt.touches[0].clientY;
                            });
                        }
                    }
                });

                Object.keys(slots).forEach(function(index) {
                    let isIE11 = !!window.MSInputMethodContext && !!document.documentMode;

                    // Set X,Y position for when slot is dragged
                    slots[index].addEventListener('mousemove', function(evt) {
                        instance.offsetX = evt.offsetX;
                        instance.offsetY = evt.offsetY;
                        instance.clientX = evt.clientX;
                        instance.clientY = evt.clientY;

                        let params = {
                            self: instance,
                            event: evt,
                            target: evt.target
                        };

                        // user callback
                        if (instance.settings.mousemove &&
                            typeof instance.settings.mousemove === "function") {
                            instance.settings.mousemove(params);
                        }
                    });

                    // Mouse events

                    slots[index].addEventListener('mousedown', function(evt) {
                        // Enable drag
                        this.draggable = true;

                        // Show ghost image
                        addAfterImage(this);

                        let params = {
                            self: instance,
                            event: evt,
                            target: evt.target
                        };

                        // user callback
                        if (instance.settings.mousedown &&
                            typeof instance.settings.mousedown === "function") {
                            instance.settings.mousedown(params);
                        }
                    });

                    slots[index].addEventListener('mouseup', function(evt) {
                        this.style.transform = "";
                        this.removeAttribute('draggable');

                        let params = {
                            self: instance,
                            event: evt,
                            target: evt.target
                        };

                        // user callback
                        if (instance.settings.mouseup &&
                            typeof instance.settings.mouseup === "function") {
                            instance.settings.mouseup(params);
                        }
                    });

                    // Drag events

                    slots[index].addEventListener('dragstart', function(evt) {
                        let dt = evt.dataTransfer;

                        if (isIE11) {

                        } else {
                            dt.setDragImage(new Image(), 0,
                                0);
                            dt.setData('key',
                                '');
                        }

                        instance.key = index;

                        let params = {
                            self: instance,
                            event: evt,
                            target: evt.target
                        };

                        // user callback
                        if (instance.settings.dragstart &&
                            typeof instance.settings.dragstart === "function") {
                            instance.settings.dragstart(params);
                        }
                    });

                    slots[index].addEventListener('drag', function(evt) {
                        let x;
                        let y;
                        // Set coordinates
                        let mouseX = instance.mouseX;
                        let mouseY = instance.mouseY;
                        let offsetX = instance.offsetX;
                        let offsetY = instance.offsetY;
                        let clientX = instance.clientX;
                        let clientY = instance.clientY;
                        // Declare borders
                        let topBorder = mouseY < offsetY;
                        let leftBorder = mouseX < offsetX;
                        let rightBorder = mouseX > document.body.offsetWidth - (evt.target
                            .offsetWidth - offsetX);
                        let bottomBorder = mouseY > document.body.offsetHeight - (evt.target
                            .offsetHeight - offsetY);

                        if (!leftBorder && !topBorder && !rightBorder && !bottomBorder) {
                            evt.target.style.zIndex = 10;
                            evt.target.style.pointerEvents = "none";
                            x = mouseX - clientX;
                            y = mouseY - clientY;
                            evt.target.style.transform = "translate(" + x + "px," + y +
                                "px)";
                        }

                        // Hitting top of screen
                        if (topBorder && !rightBorder && !leftBorder) {
                            x = mouseX - clientX;
                            y = clientY - offsetY;
                            evt.target.style.transform = "translate(" + x + "px," + -y +
                                "px)";
                        }

                        // Hitting bottom of screen
                        if (bottomBorder && !rightBorder && !leftBorder) {
                            x = mouseX - clientX;
                            y = document.body.offsetHeight - (clientY + (evt.target
                                .offsetHeight - offsetY));
                            evt.target.style.transform = "translate(" + x + "px," + y +
                                "px)";
                        }

                        // Hitting left side of screen
                        if (leftBorder && !bottomBorder && !topBorder) {
                            x = clientX - offsetX;
                            y = mouseY - clientY;
                            evt.target.style.transform = "translate(" + -x + "px," + y +
                                "px)";
                        } else if (leftBorder && bottomBorder) {
                            x = clientX - offsetX;
                            y = document.body.offsetHeight - (clientY + (evt.target
                                .offsetHeight - offsetY));
                            evt.target.style.transform = "translate(" + -x + "px," + y +
                                "px)";
                        } else if (leftBorder && topBorder) {
                            x = clientX - offsetX;
                            y = clientY - offsetY;
                            evt.target.style.transform = "translate(" + -x + "px," + -y +
                                "px)";
                        }

                        // Hitting right side of screen
                        if (rightBorder && !bottomBorder && !topBorder) {
                            x = document.body.offsetWidth - (clientX + (evt.target
                                .offsetWidth - offsetX));
                            y = mouseY - clientY;
                            evt.target.style.transform = "translate(" + x + "px," + y +
                                "px)";
                        } else if (rightBorder && topBorder) {
                            x = document.body.offsetWidth - (clientX + (evt.target
                                .offsetWidth - offsetX));
                            y = clientY - offsetY;
                            evt.target.style.transform = "translate(" + x + "px," + -y +
                                "px)";
                        } else if (rightBorder && bottomBorder) {
                            x = document.body.offsetWidth - (clientX + (evt.target
                                .offsetWidth - offsetX));
                            y = document.body.offsetHeight - (clientY + (evt.target
                                .offsetHeight - offsetY));
                            evt.target.style.transform = "translate(" + x + "px," + y +
                                "px)";
                        }

                        let params = {
                            self: instance,
                            event: evt,
                            target: evt.target
                        };

                        // user callback
                        if (instance.settings.drag &&
                            typeof instance.settings.drag === "function") {
                            instance.settings.drag(params);
                        }
                    });

                    slots[index].addEventListener('dragend', function(evt) {
                        // If out of place
                        if (!evt.target.dataset.inplace) {
                            // Enable pointer events
                            evt.target.style.pointerEvents = "";
                        }

                        // Slight delay to smoothly move piece
                        setTimeout(function() {
                            evt.target.classList.add('animate');
                            evt.target.style.transform = "translate(0px,0px)";
                        }, 100);

                        let params = {
                            self: instance,
                            event: evt,
                            target: evt.target
                        };

                        // user callback
                        if (instance.settings.dragend &&
                            typeof instance.settings.dragend === "function") {
                            instance.settings.dragend(params);
                        }
                    });

                    // Drop events

                    slots[index].addEventListener('dragenter', function(evt) {
                        evt.preventDefault(); // enables drop event

                        let params = {
                            self: instance,
                            event: evt,
                            target: evt.target
                        };

                        // user callback
                        if (instance.settings.dragenter &&
                            typeof instance.settings.dragenter === "function") {
                            instance.settings.dragenter(params);
                        }
                    });

                    slots[index].addEventListener('dragover', function(evt) {
                        evt.preventDefault(); // enables drop event

                        let params = {
                            self: instance,
                            event: evt,
                            target: evt.target
                        };

                        // user callback
                        if (instance.settings.dragover &&
                            typeof instance.settings.dragover === "function") {
                            instance.settings.dragover(params);
                        }
                    });

                    slots[index].addEventListener('dragleave', function(evt) {
                        let params = {
                            self: instance,
                            event: evt,
                            target: evt.target
                        };

                        // user callback
                        if (instance.settings.dragleave &&
                            typeof instance.settings.dragleave === "function") {
                            instance.settings.dragleave(params);
                        }
                    });

                    slots[index].addEventListener('drop', function(evt) {
                        let slot = this;
                        let dragSlot = slots[instance.key];
                        let tile = slots[index].children[0];
                        let dragTile = dragSlot.children[0];

                        // Remove highlights
                        dragTile.classList.remove('highlight');
                        slots[index].classList.remove('highlight');

                        // Disable drag
                        dragSlot.removeAttribute('draggable');
                        slots[index].removeAttribute('draggable');

                        // Reset element
                        dragSlot.style.zIndex = 10;
                        instance.lastPlace.remove();

                        // Swap tiles
                        slots[index].appendChild(dragTile);
                        dragSlot.appendChild(tile);

                        // Check correct number of tiles
                        instance.correctTiles();

                        // Run callback functions
                        runCallBacks(slot, dragSlot, tile, dragTile, evt);

                        // debug output
                        if (instance.settings.debug) {
                            console.info(instance);
                            console.info(tile[0]);
                            console.info(slot);
                            console.info("Dropped tile #" + (Number(tile[0].dataset
                                .position)) + " in slot #" + (Array.from(instance
                                .grid.children).indexOf(slot) + 1));
                        }
                    });

                    // Touch events for mobile

                    slots[index].addEventListener('touchstart', function(evt) {
                        instance.touchSlot = evt.target;
                        instance.offsetY = Math.round(evt.touches[0].clientY - evt.target
                            .getBoundingClientRect().top);
                        instance.offsetX = Math.round(evt.touches[0].clientX - evt.target
                            .getBoundingClientRect().left);
                        instance.clientX = evt.touches[0].clientX;
                        instance.clientY = evt.touches[0].clientY;

                        this.children[0].classList.add('highlight');

                        // Show ghost image
                        addAfterImage(this);

                        this.style.zIndex = 10;
                        instance.touchSlot = this;

                        let params = {
                            self: instance,
                            event: evt,
                            target: evt.target
                        };

                        // user callback
                        if (instance.settings.touchstart &&
                            typeof instance.settings.touchstart === "function") {
                            instance.settings.touchstart(params);
                        }
                    });

                    slots[index].addEventListener('touchend', function(evt) {
                        // Reset element
                        evt.target.style.pointerEvents = "";
                        instance.grid.querySelectorAll('.highlight').forEach(function(el) {
                            el.classList.remove('highlight')
                        });
                        instance.lastPlace.remove();

                        // Slight delay to smoothly move slot back in place
                        setTimeout(function() {
                            evt.target.classList.add('animate');
                            evt.target.style.transform = "translate(0px,0px)";
                        }, 100);

                        Object.keys(slots).forEach(function(index) {
                            let top = slots[index].getBoundingClientRect().top;
                            let bottom = slots[index].getBoundingClientRect()
                                .bottom;
                            let right = slots[index].getBoundingClientRect().right;
                            let left = slots[index].getBoundingClientRect().left;

                            if (instance.clientX > left &&
                                instance.clientX < right &&
                                instance.clientY > top &&
                                instance.clientY < bottom &&
                                slots[index] !== instance.touchSlot &&
                                !slots[index].style.pointerEvents) {

                                let slot = slots[index];
                                let dragSlot = evt.target;
                                let tile = slot.children[0];
                                let dragTile = dragSlot.children[0];

                                dragTile.classList.remove('highlight');
                                slot.classList.remove('highlight');

                                dragSlot.removeAttribute('draggable');
                                slot.removeAttribute('draggable');

                                dragSlot.style.zIndex = 10;

                                slot.appendChild(dragTile);
                                dragSlot.appendChild(tile);

                                instance.correctTiles();

                                runCallBacks(slot, dragSlot, tile, dragTile, evt);

                                if (!slots[index].draggable) {

                                    Object.keys(instance.grid.children).forEach(
                                        function(key) {
                                            instance.grid.children[key]
                                                .classList.remove('highlight');
                                        });
                                }
                            }
                        });

                        instance.touchSlot = null;
                    });

                    slots[index].addEventListener('transitionend', function(evt) {

                        if (evt.target.style.transform === "translate(0px, 0px)") {
                            if (instance.lastPlace !== undefined) {
                                instance.lastPlace.remove();
                            }
                            this.children[0].classList.remove('highlight');
                            this.style.zIndex = "";
                            this.style.transform = "";
                        }
                    });
                });

                window.addEventListener('resize', function() {
                    setFrameDimensions(instance.grid, instance.container);
                });
            }

            function addAfterImage(el) {
                if (instance.lastPlace) {
                    instance.lastPlace.remove();
                }

                instance.lastPlace = el.cloneNode(true);

                Object.assign(instance.lastPlace.style, {
                    'position': 'absolute',
                    'opacity': '.4',
                    'top': (el.getBoundingClientRect().top - el.parentNode.getBoundingClientRect()
                        .top) + "px",
                    'left': (el.getBoundingClientRect().left - el.parentNode.getBoundingClientRect()
                        .left) + "px",
                    'zIndex': '-2'
                });

                el.parentNode.appendChild(instance.lastPlace);
            }

            function runCallBacks(slot, dragSlot, tile, dragTile, evt) {
                let tileInPlace =
                    (Array.from(instance.grid.children).indexOf(slot) === Number(tile.dataset.position) -
                        1);
                let prevTileInPlace =
                    (Array.from(instance.grid.children).indexOf(dragSlot.parentNode) === Number(dragTile
                        .dataset.position) - 1);
                let params = {
                    self: instance,
                    event: evt,
                    target: evt.target,
                    dropped: {
                        el: tile,
                        position: tile.dataset.position,
                        inPlace: tileInPlace
                    },
                    dragged: {
                        el: dragTile,
                        position: dragTile.dataset.position,
                        inPlace: prevTileInPlace
                    }
                };

                if (instance.settings.drop &&
                    typeof instance.settings.drop === "function") {
                    instance.settings.drop(params);
                }

                if (instance.isSorted(instance.getTiles())) {

                    instance.grid.appendChild(instance.fullImg);

                    if (instance.settings.finished &&
                        typeof instance.settings.finished === "function") {
                        instance.settings.finished(params);
                    }
                }

                if (tileInPlace || prevTileInPlace) {
                    if (instance.settings.correct &&
                        typeof instance.settings.correct === "function") {
                        instance.settings.correct(params);
                    }
                }
            }

            function setFrameDimensions(grid, container) {
                let containerWidth = container.offsetWidth;

                let padding = 0;
                let paddingArr = [
                    window.getComputedStyle(container).paddingRight,
                    window.getComputedStyle(container).paddingLeft
                ];
                paddingArr.forEach(function(value) {
                    padding += parseInt(value);
                });

                containerWidth = containerWidth - padding;

                Object.assign(grid.style, {
                    'max-width': instance.settings.width + 'px',
                    'max-height': instance.settings.height + 'px',
                    'height': 'calc(' + containerWidth + "px * " + "(" + instance.settings.height +
                        "/" + instance.settings.width + ")" + ')'
                });
            }

            function buildGrid(numcolumns, numrows) {
                let gridArr = [];
                let i = 0;
                let currentRow = 0;
                let currentColumn = 1;
                instance.grid = document.createElement('ul');
                instance.gridSize = numcolumns * numrows;

                while (i < instance.gridSize) {

                    let tmpLi = document.createElement('li');
                    let tmpDiv = document.createElement('div');
                    let tmpImg = document.createElement('img');
                    let tmpArr = [];

                    Object.assign(tmpLi.style, {
                        'height': (100 / numrows) + '%',
                        'max-width': (100 / numcolumns) + '%',
                        'flex': '1 0 ' + (100 / numcolumns) + '%'
                    });

                    tmpDiv.dataset.position = i + 1;

                    tmpImg.src = instance.settings.image;
                    tmpImg.style.position = "relative";
                    tmpImg.style.width = 100 * numcolumns + "%";

                    if (((i + 1) % (Math.floor(instance.gridSize / numrows))) === 0) {
                        tmpImg.style.top = -(100 * currentRow) + "%";
                        currentRow++;
                    } else {
                        tmpImg.style.top = -(100 * currentRow) + "%";
                    }

                    if ((i) % numcolumns !== 0) {
                        tmpImg.style.left = -(100 * currentColumn) + "%";
                        currentColumn++;
                    } else {
                        currentColumn = 1;
                    }

                    tmpDiv.appendChild(tmpImg);
                    tmpLi.appendChild(tmpDiv);
                    tmpArr[0] = i;
                    tmpArr[1] = tmpLi;
                    gridArr.push(tmpArr);
                    i++;
                }

                instance.grid.classList.add("frame");
                instance.grid.classList.add("no-select");

                gridArr = shuffleArr(gridArr);
                gridArr.forEach(function(piece) {
                    instance.grid.appendChild(piece[1]);
                });

                instance.fullImg.src = (instance.settings.fullImg) ? instance.settings.fullImg : instance
                    .settings.image;
                instance.fullImg.classList.add('full-img');
                instance.fullImg.style.opacity = 0;
                instance.fullImg.style.zIndex = -1;

                return instance.grid;
            }

            function shuffleArr(array) {
                let shuffle_limit = array.length - Math.ceil(array.length * instance.difficulty);
                let m = array.length,
                    t, i;

                shuffle_limit = (shuffle_limit < (m - 1)) ? shuffle_limit : 0;

                m = (m - shuffle_limit > 0) ? m - Math.abs(shuffle_limit) : 2;

                while (m) {

                    i = Math.floor(Math.random() * (m - 1) + 1);

                    if (m < 0) {
                        break;
                    } else {
                        m--;
                    }

                    t = array[m];
                    array[m] = array[i];
                    array[i] = t;
                }

                if (instance.isSorted(array)) {
                    shuffleArr(array);
                }

                if (instance.difficulty === 1 && instance.correctTiles(array)) {
                    array.forEach(function(arr) {
                        arr[1].dataset.inplace = "";
                    });
                    shuffleArr(array);
                }

                return array;
            }

            return this;
        }
    }());
    </script>
    <script>
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
    </script>

</body>

</html>