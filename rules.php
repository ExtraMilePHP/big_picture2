<?php
ob_start();
error_reporting(0);
session_start();
if($_SESSION['token'] == ""){
   header('location:index.php');
}

include_once 'dao/config.php';
$userId = $_SESSION['userId'];
$organizationId = $_SESSION['organizationId'];
$sessionId = $_SESSION['sessionId'];


require '../vendor/autoload.php'; // Include the Composer autoload file
include_once "s3/s3_functions.php"; // s3 function for easy upload and get signed urls, still need env file and autoload

include_once 'admin/themes/themesets.php';
include_once 'admin/themes/themeTools.php';

$data = fetchThemeData();

$curruntTheme = $data["themeName"];
// print_r($data);
$rules = unserialize($data["rules"]);

include_once 'admin/process-questions.php';
include_once 'admin/process-questions1.php';


?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Big Picture</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css" />
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <style>
    .row {
        margin-right: 0px !important;
        margin-left: 0px !important;
    }

    @media screen and (min-width: 768px) {
        body {
            background: url(<?php echo possibleOnS3("uploads/",$data["background_desk"])?>);
            background-size: cover;
            width: 100%;
            background-repeat: no-repeat;
            height: 100%;
            position: relative;
            background-attachment: fixed;
        }
    }

    @media screen and (max-width: 768px) {
        body {
            background: url(<?php echo possibleOnS3("uploads/",$data["background_mob"])?>);
            background-size: cover;
            width: 100%;
            background-repeat: no-repeat;
            height: 100%;
            background-attachment: fixed;
            position: relative;
        }
    }


    .submit-btn {
        color: white;
        padding: 6px 16px 6px;
        text-transform: uppercase;
        border-radius: 5px;
        font-weight: bold;
        background-image: linear-gradient(to right, #E25569, #FB9946);
    }

    #subbtn {
        color: white;
        padding: 6px 16px 6px;
        text-transform: uppercase;
        border-radius: 5px;
        font-weight: bold;
        background-image: linear-gradient(to right, #E25569, #FB9946);
    }


    .logout-btn {
        width: 70%;
    }

    .rule-list li {
        list-style-type: none;
        background-image: url(img/arrow.png);
        background-repeat: no-repeat;
        text-align: left;
        width: 100%;
        padding-bottom: 10px;
        font-size: 17px;
        font-weight: 500;
        padding-left: 40px;
    }

    .auto {
        margin: auto;
        float: none;
    }

    .btn-conteiner {
        display: flex;
        justify-content: center;
        --color-text: #ffffff;
        --color-background: #ff135a;
        --color-outline: #ff145b80;
        --color-shadow: #00000080;
    }

    .btn-content {
        display: flex;
        align-items: center;
        padding: 0px 22px;
        text-decoration: none;
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        font-size: 21px;
        background: var(--color-background);
        transition: 1s;
        box-shadow: 0 0 0.2em 0 var(--color-background);
        text-decoration: none !important;
    }

    .btn-content:hover,
    .btn-content:focus {
        transition: 0.5s;
        -webkit-animation: btn-content 1s;
        animation: btn-content 1s;
        outline: 0.1em solid transparent;
        outline-offset: 0.2em;
        box-shadow: 0 0 0.4em 0 var(--color-background);
    }

    .btn-content .icon-arrow {
        transition: 0.5s;
        margin-right: 0px;
        transform: scale(0.6);
    }

    .btn-content:hover .icon-arrow {
        transition: 0.5s;
        margin-right: 25px;
    }

    .icon-arrow {
        width: 20px;
        margin-left: 15px;
        position: relative;
        top: 6%;
    }

    /* SVG */
    #arrow-icon-one {
        transition: 0.4s;
        transform: translateX(-60%);
    }

    #arrow-icon-two {
        transition: 0.5s;
        transform: translateX(-30%);
    }

    .btn-content:hover #arrow-icon-three {
        animation: color_anim 1s infinite 0.2s;
    }

    .btn-content:hover #arrow-icon-one {
        transform: translateX(0%);
        animation: color_anim 1s infinite 0.6s;
    }

    .btn-content:hover #arrow-icon-two {
        transform: translateX(0%);
        animation: color_anim 1s infinite 0.4s;
    }

    /* SVG animations */
    @keyframes color_anim {
        0% {
            fill: white;
        }

        50% {
            fill: var(--color-background);
        }

        100% {
            fill: white;
        }
    }

    /* Button animations */
    @-webkit-keyframes btn-content {
        0% {
            outline: 0.2em solid var(--color-background);
            outline-offset: 0;
        }
    }

    @keyframes btn-content {
        0% {
            outline: 0.2em solid var(--color-background);
            outline-offset: 0;
        }
    }

    .gradient-button {
        font-size: 25px;
        padding: 10px 20px;
        font-size: 20px;
        font-weight: bold;
        transition: all 0.3s ease;
        text-align: center;
        font-weight: 500;
        text-align: left;
        text-align: center;
        padding: 5px 10px;
        margin-bottom: 10px;
           color: <?php echo $data["landing_page_button_color"];
        ?>;
        background-color: <?php echo $data["landing_page_button_bgcolor"];
        ?>
    }

    .gradient-button:hover {
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
    }

    .btn-login {
       
        width: max-content;
        margin: auto;
        position: relative;
        display: block;
        border: 1px solid black;
           color: <?php echo $data["landing_page_button_color"];
        ?>;
        background-color: <?php echo $data["landing_page_button_bgcolor"];
        ?>
    }
    </style>
</head>

<body class="bg">
    <?php include("../actions-default.php");  back("index.php?save");?>

    <div class="container-fluid" style="margin-top:10px;">

        <div class="row">
            <div class="col-sm-2 col-md-2 col-lg-2 col-xs-10 auto">
                <img src="<?php echo possibleOnS3("uploads/",$data["logo"])?>" class="sangam-logo"
                    style="width:100%;" />
            </div>

        </div>



    </div>
    <div class="container-fluid" style="margin-top:30px;">
        <div class="row">
            <div class="col-sm-1 col-md-1 col-lg-1 col-xs-6 auto">
                <h3 class="gradient-button">Rules</h3>
            </div>

        </div>

    </div>

    <div class="container-fluid" style="margin-top:10px;font-size:20px;font-weight:bold;">

        <div class="row">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xs-12 auto">

                <div class="col-sm-12 col-md-12 col-lg-12 col-xs-12">
                    <ul type="dice" class="rule-list" style="color:<?php echo $data["landing_page_title_color"];?>">
                        <?php
                   
                        foreach ($rules as $key => $value) {
                           echo '<li>'.$value.'</li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="margin-top:20px;">
        <div class="col-sm-4 col-md-3 col-lg-3 col-xs-5 auto">
            <a class="btn-content btn btn-login" href="stage1.php">NEXT </a>
               
        </div>
    </div>

    <script>


    </script>

</body>

</html>