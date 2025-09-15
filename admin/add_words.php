<?php

session_start();
error_reporting(0);

$settings=json_decode(file_get_contents("settings.js"),true)[0];
$organizationId=$_SESSION['organizationId'];
$sessionId=$_SESSION['sessionId'];
// echo $organizationId;
// echo $sessionId;
include_once '../dao/config.php';
include_once '../../admin_assets/triggers.php';

require '../../vendor/autoload.php'; // Include the Composer autoload file
include_once "../s3/s3_functions.php"; // s3 function for easy upload and get signed urls, still need env file and autoload

$curruntTheme=$_GET["name"];
$_SESSION['themename']=$curruntTheme;
include_once 'themes/processThemes.php';
include_once 'themes/themeTools.php';

$_SESSION["gameTitle"]=$settings["gameName"];
mysqli_set_charset( $con, 'utf8');
$settings["pageLinks"] = [
  "Back" => "themeUpdate.php?name=" . $curruntTheme
];
$tabName=($_SESSION["sessionId"]=="admin")?"Settings ( Superadmin )":"Settings";

$data=fetchThemeData();


$rules= unserialize($data["rules"]);


$custom_cat=unserialize($data["custom_cat"]);
$default_cat=unserialize($data["default_cat"]);
$new_fields = unserialize($data["new_fields"]);

// print_r($new_fields);

$addAdditionalFields = ($data["additionalFields"] == "true") ? true : false;
// print_r($default_cat);
// print_r($custom_cat);


list($minutes, $seconds) = explode(":", $data["timer"]);

?>

<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

<head>

    <?php include_once("../../admin_assets/common-css.php");?>
    <!-- Only unique css classes -->
    <style rel="stylesheet" type="text/css">
    .custom-file-input,
    .custom-file,
    .custom-file-label,
    .custom-file-label::after {
        height: auto !important;
    }

    form .form-actions {
        border-top: none !important;
    }

    #formatid {
        position: relative;
        /* top: 50px; */
    }

    input#file {
        height: 40px !important;
        position: relative;
    }

    div#custom_card_height {
        height: auto !important;
    }

    .swal2-popup.swal2-modal.swal2-show {
        /* background-image: url(img/correct.png); */
        background-color: transparent !important;
        background-position: center !important;
        background-size: 100% 100% !important;
        background-repeat: no-repeat;
        width: 490px !important;
        height: 370px !important;
    }

    button.swal2-confirm.swal2-styled {
        background-color: transparent !important;
        background-image: url(img/ok.png) !important;
        background-position: center !important;
        background-size: 100% 100% !important;
        background-repeat: no-repeat;
    }

    .swal2-popup .swal2-header,
    .swal2-popup .swal2-content,
    .swal2-popup .swal2-actions {
        top: 50px;
        position: relative;
    }


    .custom-grid {
        display: grid;
        grid-template-columns: repeat(6, 50px);
        grid-template-rows: repeat(6, 50px);
        gap: 2px;
    }

    .custom-grid div {
        width: 50px;
        height: 50px;
        background-color: lightgrey;
        border: 1px solid #ccc;
    }

    .custom-grid .highlighted {
        background-color: skyblue;
    }

    .custom-output {
        margin-top: 10px;
        font-size: 16px;
    }

    .custom-button {
        margin-top: 10px;
        padding: 5px 10px;
        font-size: 16px;
    }


    .timer-container {
        /* margin-top: 50px; */
    }

    .timer-input {
        width: 75px;
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 5px;
        margin: 0 10px;
        background-color: white;
    }

    .timer-display {
        font-size: 24px;
        margin-top: 20px;
    }

    .color-drop {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 20px;
        font-size: 17px;
    }

    .color-drop input {
        width: 100px;
    }


    .row-container {
        display: flex;
        /* width: 50%; */
        justify-content: center;
        align-items: center;
        gap: 20px;
        font-size: 16px;
        padding: 5px;
    }

    .row-container span {
        width: 80px;
        text-align: left;
    }

    .marks-container {
        display: flex;
        /* width: 50%; */
        justify-content: center;
        align-items: center;
        gap: 10px;
        font-size: 16px;
        padding: 5px;
    }

    .marks-container span {
        width: 100px;
        text-align: left;
    }

    .auto {
        float: none;
        margin: auto;
    }

    /* Custom CSS for Tabs */
    .custom-tabs-container {
        display: flex;
        justify-content: center;
        /* Centers the tabs horizontally */
    }

    .custom-tabs {
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 10px;
    }

    .custom-tabs .nav-link {
        color: #007bff;
        font-weight: bold;
        border: none;
        padding: 10px 20px;
        border-radius: 20px;
        /* Rounded corners for a pill-like effect */
        margin: 0 5px;
        /* Adds spacing between the tabs */
    }

    .custom-tabs .nav-link.active {
        color: #fff;
        background-color: #E25569;
    }

    .card {
        margin-top: 20px;
    }

    .form-control {
        margin-bottom: 10px;
        font-size: 1rem;
        line-height: 1.25;
        display: block;
        width: 100%;
    }

    .add-rule {
        margin-bottom: 10px;
    }

    .mt-5,
    .my-5 {
        margin-top: 0rem !important;
    }

    dl li,
    ol li,
    ul li {
        line-height: 1.8;
        color: black;
    }

    .heading-elements {
        display: none;
    }

    .customize-themes {
        width: maxx;
        width: max-content;
        background: #ee887f;
        padding: 7px 15px;
        border-radius: 5px;
        color: white;
        margin: 0 auto;
        margin-top: 30px;
    }

    .header-navbar.navbar-expand-sm.navbar.navbar-horizontal.navbar-fixed.navbar-dark.navbar-without-dd-arrow.navbar-shadow {
        background-image: none !important;
    }

    .stage {
        width: max-content;
        background-color: transparent;
        text-align: center;
        padding: 10px 20px;
        color: black;
        border-radius: 0px;
        margin-left: -1px;
        cursor: pointer;
        border-right: 2px solid black;
        /* border: 2px solid black; */
    }

    #stage4 {

        border-right: none;
        /* border: 2px solid black; */
    }

    .content-wrapper-before {
        background-image: none !important;
    }

    .centered-container {
        display: flex;
        justify-content: center;
    }

    .stage-container {
        display: flex;
        padding: 5px 5px;
        border: 2px solid gray;
    }

    html body .content .content-wrapper {
        padding: 1.2rem 16.8px;
    }

    .em-color,
    .core-button-color {
        background-color: black;
    }

    code {
        border-radius: 0rem !important;
        border-right: 2px solid red;
    }

    .text-muted code:last-of-type {
        border-right: none;
        margin-right: 0;
        padding-right: 0;
    }

    .buttonspan {
        width: 60%;
    }

    input#marks {
        width: 100px;
    }

    .text-muted {
        margin-top: 10px;
    }

    @media screen and (max-width: 768px) {
        .stage {

            padding: 5px 10px;
            font-size: 10px;

        }
    }

    .active-stage {
    background-color: black !important;
    color: white !important;
}
    
    </style>
    <!-- Only unique css classes -->
</head>


<body class="horizontal-layout horizontal-menu 2-columns" data-open="hover" data-menu="horizontal-menu"
    data-color="bg-gradient-x-purple-blue" data-col="2-columns">
    <?php include_once("../../admin_assets/common-header.php");?>
     
    <div class="app-content content">
         <div class="content-header">
                <?php include_once 'header.php'; ?>
            </div>  
        <div class="content-body">
            <section id="basic-form-layouts">
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <div class="container ">
                            <div class="card mt-2" id="card1">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <!-- <div class="col-md-6">
                                                <a href="#" id="backButton" class="btn btn-md btn-success">Back</a>
                                            </div><br> -->
                                        </div>
                                    </div>
                                    <div class="card" id="custom_card_height">
                                        <div class="card-content collapse show">
                                            <div class="card-body">
                                                <div class="row">
                                                    <!-- First Column: Title -->
                                                    <div class="col-md-12 mb-2">
                                                        <h4 class="card-title">Add Custom Heading Title For Challenge 3:
                                                        </h4>
                                                        <textarea id="title3" min="5" maxlength="150"
                                                            class="form-control"
                                                            required><?php echo $data["landing_page_title3"];?></textarea>

                                                        <p class="text-muted">
                                                            <code>* This title will reflect on the Challenge3 of game page</code><code>* character limit of text is 150</code>
                                                        </p>
                                                        <h4 class="card-title"></h4>
                                                        <textarea id="sub_title3" min="5" maxlength="300"
                                                            class="form-control"
                                                            required><?php echo $data["landing_page_title3_question"];?></textarea>

                                                        <p class="text-muted">
                                                            <code>* This Question will reflect on the Challenge3 of game page</code><code>* character limit of text is 300</code>
                                                        </p>
                                                        <!-- <div class="col-md-12 mb-2"> -->
                                                            <h4 class="card-title">Add Custom Subtitle Example:</h4>
                                                            <textarea id="title3_eg" min="5" maxlength="150"
                                                                class="form-control"
                                                                required><?php echo $data["landing_page_title3_eg"];?></textarea>

                                                            <p class="text-muted">
                                                                <code>* This subtitle will reflect on the Challenge3 of game page</code><code>* character limit of text is 150</code>
                                                            </p>
                                                        <!-- </div> -->
                                                    </div>

                                                    <!-- Second Column: Subtitle -->

                                                </div>

                                                <div class="col-md-12 text-center mb-2">
                                                    <button class="btn btn-md btn-success core-button-color"
                                                        style="margin-top:15px;"
                                                        id="add_basics_challenege3">Save</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
            </section>
        </div>
    </div>
    </div>

    <?php include("../../admin_assets/footer.php");?>
    <?php include_once("../../admin_assets/common-js.php");?>

    <script type="text/javascript">
    $("#add_basics_challenege3").click(function() {

        var title3 = $("#title3").val();
        var sub_title3 = $("#sub_title3").val();
        var title3_eg = $("#title3_eg").val();
        let errorFlag = false;
        let errorMsg = "";

        if (title3.trim().length === 0 || title3.length > 150) {
            errorFlag = true;
            errorMsg = title3.trim().length === 0 ?
                "The title cannot be empty or contain only spaces." :
                "You exceed the maximum limit of the title.";
        }

        if (sub_title3.trim().length === 0 || sub_title3.length > 300) {
            errorFlag = true;
            errorMsg = sub_title3.trim().length === 0 ?
                "The subtitle cannot be empty or contain only spaces." :
                "You exceed the maximum limit of the subtitle.";
        }

        if (title3_eg.trim().length === 0 || title3_eg.length > 150) {
            errorFlag = true;
            errorMsg = title3_eg.trim().length === 0 ?
                "The title cannot be empty or contain only spaces." :
                "You exceed the maximum limit of the title.";
        }



        if (!errorFlag) {

            $.ajax({
                type: "POST",
                url: "events.php?events=add_rules_for_challenege3",
                data: {
                    "title3": title3,
                    "title3_eg": title3_eg,
                    "sub_title3": sub_title3
                },
                success: function(result) {
                    if (result == "true") {
                        swal({
                            title: 'Data Updated',
                            background: '#fff url(img/correct.png)'
                        });
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        swal({
                            title: result,
                            background: '#fff url(img/wrong.png)'
                        });
                    }
                }
            });

        } else {
            swal({
                title: errorMsg,
                background: '#fff url(img/wrong.png)'
            });
        }
    })
    </script>
</body>

</html>