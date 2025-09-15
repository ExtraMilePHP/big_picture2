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
// echo $_SESSION['adminId']."admin_id";
// if (!$_SESSION['adminId']) {
//     header('Location:../index.php');
// } 
$tabName=($_SESSION["sessionId"]=="admin")?"Settings ( Superadmin )":"Settings";

$data=fetchThemeData();

// print_r($data);

$rules= unserialize($data["rules"]);


// print_r($rules);

$custom_cat=unserialize($data["custom_cat"]);
$default_cat=unserialize($data["default_cat"]);
$new_fields = unserialize($data["new_fields"]);

// print_r($new_fields);

$addAdditionalFields = ($data["additionalFields"] == "true") ? true : false;
// print_r($default_cat);

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

    .stage {
        width: max-content;
        background: black;
        text-align: center;
        padding: 10px;
        color: white;
        border-radius: 10px;
        margin-left: -1px;
        cursor: pointer;
        border: 1px solid white;
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
        <?php include 'header.php'; ?>
        <!-- <div class="content-wrapper">
            <div class="content-header row centered-container">
                <div class="stage-container">
                    <a href="themeUpdate.php?name=<?php echo $curruntTheme; ?>">
                        <div class="stage" style="background-color:black;color:white;">Home</div>
                    </a>
                    <a href="editor.php?name=<?php echo $_GET['name'];?>">
                        <div class="stage" id="stage1">Stage 1</div>
                    </a>
                    <a href="addquestions.php?name=<?php echo $_GET['name'];?>">
                        <div class="stage" id="stage2">Stage 2</div>
                    </a>
                    <a href="add_words.php?name=<?php echo $_GET['name'];?>">
                        <div class="stage" id="stage3">Stage 3</div>
                    </a>
                    <a href="jigsaw_theme.php?name=<?php echo $_GET['name'];?>">
                        <div class="stage" id="stage4">Stage 4</div>
                    </a>
                </div>
            </div>
        </div> -->

        <div class="content-body">
            <section id="basic-form-layouts">
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <div class="container ">
                            <!-- Tab Navigation -->
                            <!-- <div class="custom-tabs-container ">
                                <ul class="nav nav-pills custom-tabs justify-content-center">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="tab1" onclick="showCard(1)">1. Add Content</a>
                                    </li>


                                </ul>
                            </div> -->

                            <!-- First Card: Add Content -->
                            <div class="card mt-2" id="card1">
                                <div class="card-body">
                                    <div class="card" id="custom_card_height">
                                        <!-- <?php cardHeader("Add Content");?> -->
                                        <div class="card-content collapse show">
                                            <div class="card-body">
                                                <div class="row">
                                                    <!-- First Column -->
                                                    <div class="col-md-12">

                                                        <div class="col-md-12 mb-2">
                                                            <h4 class="card-title">Add Custom thankyou text:</h4>
                                                            <textarea id="custom_text_thank_you_page" min="5"
                                                                maxlength="300" class="form-control" required
                                                                id="basicInput"><?php echo $data["custom_text_thank_you_page"];?></textarea>


                                                            <p class="text-muted">
                                                                <code>* This text will reflect on the thankyou page</code><code>  * character limit of text is 300</code>
                                                            </p>
                                                        </div>



                                                    </div>

                                                    <!-- Second Column -->
                                                    <div class="col-md-12">
                                                        <!-- Right Column -->
                                                        <h4 class="card-title">Add Custom Rules:</h4>
                                                        <div class="form-group rules-repeater">
                                                            <div data-repeater-list="repeater-group">
                                                                <?php
                                         for($i=0; $i<sizeof($rules); $i++){
                                            echo ' <div class="input-group mb-1" data-repeater-item>
                                            <textarea type="text" placeholder="Rules" name="rules" class="form-control" id="example-ql-input">'.htmlentities($rules[$i]).'</textarea>
                                            <span class="input-group-append" id="button-addon2">
                                                <button class="btn btn-danger" type="button" data-repeater-delete>
                                                    <i class="ft-x"></i>
                                                </button>
                                            </span>
                                        </div>';
                                        }
                                            ?>
                                                            </div>
                                                            <button type="button" data-repeater-create
                                                                class="btn btn-primary em-color">
                                                                <i class="ft-plus"></i> Add new rule
                                                            </button>
                                                            <p class="text-muted">
                                                            <p><code>* Keep 6 rules at the most </code><code> * At least 1 rule is required </code><code>  * character limit of each rule is 200</code>
                                                            </p>

                                                        </div>


                                                    </div>

                                                </div>
                                                <div class="row text-center mb-2">
                                                    <div class="col-md-4">
                                                        <div class="color-drop">
                                                            <span>Text Color</span>
                                                            <input type="color" id="textColorPicker"
                                                                name="textColorPicker"
                                                                value="<?php echo $data['landing_page_title_color']; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="color-drop">
                                                            <span>Button Text Color</span>
                                                            <input type="color" id="buttonColorPicker"
                                                                name="buttonColorPicker"
                                                                value="<?php echo $data['landing_page_button_color']; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="color-drop">
                                                            <span>Button Background Color</span>
                                                            <input type="color" id="buttonBgColorPicker"
                                                                name="buttonBgColorPicker"
                                                                value="<?php echo $data['landing_page_button_bgcolor']; ?>">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-12 text-center mb-2">
                                                    <button class="btn btn-md btn-success core-button-color"
                                                        style="margin-top:15px;" id="add_all_rules">Save</button>
                                                </div>
                                                <!-- tab content end-->
                                            </div>
                                            <div class="col-md-12 text-center auto" style="display:none;">
                                                <div class="form-group pb-1">
                                                    <?php

                        $timeout = ($data["use_timeout"] == "true") ? true : false;
                        echo '<a href="events.php?events=switch_timer&switch=use_timeout&current=' . $data["use_timeout"] . '"><input type="checkbox" id="switchery2" class="switchery" data-size="md" ' . switchery($timeout) . '/></a>';

                        ?>

                                                    <label class="font-medium-2 text-bold-600 ml-1">Enable
                                                        Timeout</label>
                                                </div>
                                                <div class="col-md-12"
                                                    style="margin-top:20px; <?php echo (!$timeout) ? "display:none" : ""; ?>">
                                                    <h4 class="card-title">Timer:</h4>
                                                    <div class="timer-container">
                                                        <label for="minutes">Minutes:</label>
                                                        <input type="number" id="minutes" class="timer-input" min="1"
                                                            value="<?php echo $minutes; ?>" onchange="updateTimer()">

                                                        <label for="seconds">Seconds:</label>
                                                        <input type="number" id="seconds" class="timer-input" min="0"
                                                            max="59" value="<?php echo $seconds; ?>"
                                                            onchange="updateTimer()">
                                                    </div>

                                                    <p class="timer-display" id="timer">
                                                        <?php echo $minutes . ":" . $seconds ?></p>
                                                    <button class="btn btn-md btn-success" style="margin-top:15px;"
                                                        id="saveTime" type="submit" name="submit">Save</button>
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
    $('.rules-repeater').repeater({
        show: function() {

            if ($(this).parents(".rules-repeater").find("div[data-repeater-item]").length <= 6) {
                $(this).slideDown();
            } else {
                $(this).remove();
            }
        }
    });

    var rules = [];

    function getValues() {
        rules = []; // Clear the rules array before pushing new values
        $('textarea[name*="rules"]').each(function(e) {
            var ruleValue = $(this).val();
            console.log("ruleValue", ruleValue);
            if (ruleValue.trim() !== "") { // Check if the rule is not empty
                rules.push(ruleValue);
            }
        });
        console.log("merules", rules);
    }


    function checkRulesLength() {
        let rulesFlag = false;
        $('textarea[name*="rules"]').each(function(e) {
            let rulesData = $(this).val();
            if (rulesData.length > 200) {
                rulesFlag = true;
            }
        });
        return rulesFlag;
    }


    $("#add_all_rules").click(function() {
        if ($('.rules-repeater').find("div[data-repeater-item]").length != 0) {

            getValues();
            // var title = $("#title").val();
            var custom_text_thank_you_page = $("#custom_text_thank_you_page").val();
            var textColorPicker = $("#textColorPicker").val();
            var buttonColorPicker = $("#buttonColorPicker").val();
            var buttonBgColorPicker = $("#buttonBgColorPicker").val();

            let errorFlag = false;
            let errorMsg = "";


            if (custom_text_thank_you_page.trim().length === 0 || custom_text_thank_you_page.length > 300) {
                errorFlag = true;
                errorMsg = sub_title.trim().length === 0 ?
                    "The Thankyou Text cannot be empty or contain only spaces." :
                    "You exceed the maximum limit of the Thankyou page text.";
            }

            if (!errorFlag) {
                if (!checkRulesLength()) {
                    $.ajax({
                        type: "POST",
                        url: "events.php?events=add_rules",
                        data: {
                            "rules": rules,
                            "textColorPicker": textColorPicker,
                            "buttonColorPicker": buttonColorPicker,
                            "buttonBgColorPicker": buttonBgColorPicker,
                            "custom_text_thank_you_page": custom_text_thank_you_page
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
                        title: "Rules must below 200 characters.",
                        background: '#fff url(img/wrong.png)'
                    });
                }
            } else {
                swal({
                    title: errorMsg,
                    background: '#fff url(img/wrong.png)'
                });
            }


        } else {
            alert("at least 1 rule required.");
        }
    })



    let finalTimer = "00:00";

    function updateTimer() {
        var minutes = parseInt(document.getElementById('minutes').value);
        var seconds = parseInt(document.getElementById('seconds').value);

        // Ensure minutes are at least 1
        if (minutes < 1) {
            minutes = 1;
            document.getElementById('minutes').value = 1;
        }

        // Ensure seconds are between 0 and 59
        if (seconds < 0) {
            seconds = 0;
            document.getElementById('seconds').value = 0;
        } else if (seconds > 59) {
            seconds = 59;
            document.getElementById('seconds').value = 59;
        }

        // Convert minutes and seconds to strings with leading zeros if necessary
        var formattedMinutes = minutes < 10 ? "0" + minutes : minutes;
        var formattedSeconds = seconds < 10 ? "0" + seconds : seconds;

        // Update the displayed timer
        document.getElementById('timer').innerText = formattedMinutes + ":" + formattedSeconds;
    }

    $("#saveTime").click(function() {
        let saveTime = $("#timer").html();
        $.ajax({
            type: "POST",
            url: "events.php?events=add_timer",
            data: {
                "time": saveTime
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
    });



    $("#thankyou_title_button").click(function() {
        var title = $("#thankyou_title").val();
        $.ajax({
            type: "POST",
            url: "events.php?events=thankyou_title",
            data: {
                "title": title
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
    });
    </script>
</body>

</html>