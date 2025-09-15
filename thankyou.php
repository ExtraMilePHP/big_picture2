<?php
session_start();
error_reporting(0);
include_once 'dao/config.php';
$userid = $_SESSION['userId'];
$userId = $_SESSION['userId'];
$organizationId = $_SESSION['organizationId'];
$sessionId = $_SESSION['sessionId'];


$isdemo = ($_SESSION["sessionId"] == "demobypass") ? true : false;
$roles = $_SESSION['roles'];
$hidescore = false;
if ($_SESSION['score'] == "false") {
    if ($_SESSION["score"] != "0") {
        $hidescore = true;
    }
}

include_once 'admin/themes/themesets.php';
include_once 'admin/themes/themeTools.php';

require '../vendor/autoload.php'; // Include the Composer autoload file
include_once "s3/s3_functions.php"; // s3 function for easy upload and get signed urls, still need env file and autoload


$data=fetchThemeData();
$themeName=$data["themeId"];
// print_r($data);

$questions_02 = $data["questions_02"]; 
$totalAdminpoints = 3 + $questions_02;


$getId11 = "SELECT * FROM question WHERE userid='$userid' AND organizationId='$organizationId' AND sessionId='$sessionId'";
$getId11 = execute_query($getId11);
$getId11 = mysqli_fetch_object($getId11);

$score = $getId11->score;
$end_time = $getId11->end_time;



// if (isset($_SESSION["uniqueMsg"])) {
//     $printscore = "Congratulations on completing the BigPicture with score ". $_SESSION["uniqueMsg"]. " out of " .$totalAdminpoints. " in " .$end_time. " minutes.";
// } else {
//     $printscore = "Congratulations on completing the BigPicture with score " . $score. " out of " .$totalAdminpoints. " in " .$end_time. " minutes.";
// }


if (isset($_SESSION["uniqueMsg"])) {
    $printscore = "Well done! You pieced together the Big Picture in just " . $end_time . " minutes.";
} else {
    $printscore = "Well done! You pieced together the Big Picture in just " . $end_time . " minutes.";
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Thank You</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <link href="css/style.css" type="text/css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <script src='js/jquery-ui.min.js'></script>
    <style rel="stylesheet" type="text/css">
@font-face {
    font-family: 'Helvetica Neue';
    src: url('Fonts/HELVET01.TTF');
}

body {
    font-family: 'Helvetica Neue';
}

    html {
        width: 100%;
        height: 100%;

    }

    body {
            font-family: 'Helvetica Neue';
        width: 100%;
        height: 100%;
        overflow: hidden;
        background-color: white;
        background-image: url(<?php echo possibleOnS3("uploads/",$data["background_desk"])?>);
        background-repeat: no-repeat;
        background-size: 100% 100%;
        background-attachment: fixed;
    }

    .words {
        width: 245px;
    }

    .auto {
        text-align: center;
    }

    .welcome-logo {
        width: 100%;
    }

    .container-control {
        margin-top: 40px;
    }

    .mob {
        display: none;
    }

    .desk {
        display: block;
    }

    ul li {
          font-family: 'Helvetica Neue';
        font-size: 20px;
    }

    .begin {
        width: 130px;
        font-size: 18px;
        background: #e9695e;
        margin-top: 10px;
    }

    .thankyou-logo {
        width: 270px;
        margin-top: 60px;
    }

    .score {
        font-size: 20px;
        color: #424242;
        margin-top: 28px;
    }

    .rate,
    .subscribe {
        background: #0078b7;
        color: white;
        font-size: 19px;
        padding: 4px 10px;
        margin-top: 50px;
    }

    .rate:hover,
    .subscribe:hover {
        color: white;
    }


    @media (min-width:100px) and (max-width:768px) {
        body {
            overflow: scroll;
            background-image: url(<?php echo possibleOnS3("uploads/",$data["background_mob"])?>);
            height: 100vh;
            background-size: 100% 100%;
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

        .welcome-logo {
            width: 200px;
            margin-top: 80px;
        }

        .thankyou-logo {
            width: 280px;
        }

        .score {
            font-size: 25px;
        }

        .rate,
        .subscribe {
            margin-top: 20px;
        }
    }

    @media only screen and (max-device-width: 1200px) and (orientation: landscape) {
        body {
            overflow: scroll;
        }

        .thankyou-logo {
            width: 300px;
            margin-top: 0;
        }

        .score {
            font-size: 30px;
            margin-top: 28px;
        }

        .rate,
        .subscribe {
            background: #0078b7;
            color: white;
            font-size: 19px;
            padding: 4px 10px;
            margin-top: 11px;
        }
    }

    @media only screen and (max-device-width: 1000px) and (orientation: landscape) {
        body {
            overflow: scroll;
        }

        .thankyou-logo {
            width: 300px;
            margin-top: 0;
        }

        .score {
            font-size: 30px;
            margin-top: 28px;
        }

        .rate,
        .subscribe {
            background: #0078b7;
            color: white;
            font-size: 19px;
            padding: 4px 10px;
            margin-top: 11px;
        }
    }

    @media only screen and (max-device-width: 900px) and (orientation: landscape) {
        body {
            overflow: scroll;
        }

        .thankyou-logo {
            width: 300px;
            margin-top: 0;
        }

        .score {
            font-size: 30px;
            margin-top: 28px;
        }

        .rate,
        .subscribe {
            background: #0078b7;
            color: white;
            font-size: 19px;
            padding: 4px 10px;
            margin-top: 11px;
        }
    }
    </style>
</head>

<body>

    <?php  include("../actions-default.php");
            back("https://extramileplay.com/"); ?>
    <div class="container-fluid container-control">
        <div class="row">
            <div class="col-md-12 auto">
                <img src="<?php echo possibleOnS3('uploads/', $data['puzzle_image']); ?>" class="thankyou-logo" />
            </div>
            <div class="col-md-12">
                <div class="col-md-8 col-md-offset-2 m-auto" style="    text-align: center;
    font-size: 20px;
    margin-top: 15px;color:<?php echo $data["landing_page_title_color"];?>;">
                    <?php echo $data["custom_text_thank_you_page"];?>
                </div>
            </div>

            <div class="col-md-12 text-center score" style="color:<?php echo $data["landing_page_title_color"];?>;">
                <?php echo $printscore; ?>
            </div>
          

        </div>


    </div>


</body>

</html>