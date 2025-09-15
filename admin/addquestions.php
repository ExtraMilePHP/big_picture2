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
// echo $_SESSION['themename'];
include_once 'themes/processThemes.php';
include_once 'themes/themeTools.php';

$_SESSION["gameTitle"]=$settings["gameName"];
mysqli_set_charset( $con, 'utf8');

$tabName="Settings";
$title=default_data("title");
$main_title=default_data("main_title");
$Correct_ans_text_popup=default_data("Correct_ans_text_popup");
$end_game_text_popup=default_data("end_game_text_popup");


$data=fetchThemeData();
// print_r($data);

$rules= unserialize($data["rules"]);
// print_r($rules);

$custom_cat=unserialize($data["custom_cat"]);
$default_cat=unserialize($data["default_cat"]);

$cat_icons = unserialize($data["cat_icons"]);

if ($data["show_clue"] == "true") {
    $show_clue = true;
    } else {
    $show_clue = false;
    }


list($minutes, $seconds) = explode(":", $data["timer"]);


$questions_and_answers=(unserialize($data["questions_and_answers"]));
$arraycount = count($questions_and_answers);
for($i=0; $i< $arraycount; $i++ ){
    // print_r($questions_and_answers[$i]['answer']);
    // echo $questions_and_answers[$i]['answer']."<br>";
}

// print_r($questions_and_answers);


// print_r($questions_and_answers);

$question_count = count($questions_and_answers);
// echo "Total questions: " . $question_count;

?>

<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

<head>

    <?php include_once("../../admin_assets/common-css.php");?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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



    .formtext {
        margin-top: 0px !important;
        padding: 0px !important;
    }

    .formtop {
        margin-top: 15px !important;
    }

    #formatid {
        font-size: 12px;
    }

    .or-text {
        display: block;
        text-align: center;
        width: 100%;
        margin-bottom: 5px;
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

    /* .mt-5,
    .my-5 {
        margin-top: 0rem !important;
    } */

    dl li,
    ol li,
    ul li {
        line-height: 1.8;
        color: black;
    }

    /* new admin ui css */

    .custom-tabs {
        border: 2px solid gray;
        padding: 5px 5px;
    }

    .custom-tabs .nav-link.active {
        color: #fff;
        background-color: black;
        padding: 10px 20px;
    }

    .nav.nav-pills .nav-item .nav-link {
        line-height: normal;
        padding: 10px 15px;
        border-radius: 2rem;
    }

    .sub-tab {
        width: max-content;
        background-color: transparent;
        text-align: center;
        /* padding: 0px 20px; */
        color: black;
        border-radius: 0px;
        margin-left: -1px;
        cursor: pointer;
        border-right: 2px solid black;

    }

    .nav.nav-pills .nav-item .nav-link.active {
        border-radius: 0px;
    }

    .custom-tabs .nav-link {
        font-weight: bold;
        border: none;
        padding: 10px 20px;
        border-radius: 20px;
        /* Rounded corners for a pill-like effect */
        margin: 0 5px;
        /* Adds spacing between the tabs */
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

    .color-drop {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 20px;
        font-size: 17px;
    }

    .buttonspan {
        width: 60%;
    }


    /* add pair css*/


    .addpair {
        width: 60%;
    }

 .preview {
    display: flex;
    justify-content: space-between;
    border-radius: 10px;
    margin-top: 15px;
    padding: 5px;
    max-width: 66%;
}


    /* General styles for responsiveness */

    .pair {
        background-color: #97c5f3;
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 8px;
        box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
    }

    .pair label {
        font-weight: bold;
        color: white;
        min-width: 80px;
        /* Adjust as needed */
    }

    .pair input {
        flex-grow: 1;
        padding: 8px;
        border-radius: 4px;
        border: 1px solid #ccc;
    }

    /* .delete {
        color: red;
        cursor: pointer;
        text-align: right;
        display: block;
    } */
    .delete {
        color: red;
        cursor: pointer;
        text-align: right;
        display: block;
        margin-top: 57px;
        margin-bottom: -8px;
    }

    .addpair {
        /* display: flex; */
        justify-content: center;
        margin-bottom: 20px;
    }

    .btn-save {
        background-color: black;
        /* border-color: #004085; */
        /* background-image: linear-gradient(to right, #E25569, #FB9946); */
        color: white;
    }

    .add-pair {
        border: 2px solid black;
        /* color: black; */
        background-color: transparent;
    }



    .btn-success {
        /* background-color: #28a745;
        border-color: #28a745; */
    }

    .btn-success:hover,
    .btn-primary:hover {
        opacity: 0.8;
    }

    .pair-container {
        display: block;
        margin-bottom: 10px;
        /* Adds some space between the pairs */
    }

    .pair-wrapper {
        display: block;
        /* Ensure each pair takes up the full width */
        margin-bottom: 20px;
        /* Adds space between each pair */
    }

    /* Toggle switch styles */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 55px;
        height: 25px;
        vertical-align: middle;
        margin-left: 30px;
        margin-top: 5px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 24px;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked+.toggle-slider {
        background-color: #E25569;
    }

    input:checked+.toggle-slider:before {
        transform: translateX(26px);
    }

    .toggle-label {
        margin-left: 5px;
        vertical-align: middle;
        font-size: 18px;
    }

    .navbar-dark,
    .navbar-dark.navbar-horizontal {
        background: #2c303b !important;
    }

    .thirdCard {
        border-right: none;
    }

    .em-color {
        background: black;
        color: white;
    }

    .core-button-color {
        width: max-content;
        font-size: 17px;
        padding: 5px 25px;
        font-weight: 200;
        background: black;
        margin-top: 10px;
    }

    #title {
        margin-left: 15px;
        width: 97%;
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

    .btn-md {
        background-color: black !important;
    }

    .page-item.active .page-link {
        background-color: black !important;
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

    .card-header {
        padding: 0;
    }

    .heading-elements {
        display: none;
    }
    </style>

</head>


<body class="horizontal-layout horizontal-menu 2-columns" data-open="hover" data-menu="horizontal-menu"
    data-color="bg-gradient-x-purple-blue" data-col="2-columns">
    <?php include_once("../../admin_assets/common-header.php");?>
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header">
                <?php include_once 'header.php'; ?>
            </div>
            <div class="content-body">
                <section id="basic-form-layouts">
                    <div class="row match-height">


                        <div class="container-fluid">
                            <div class="row justify-content-center">
                                <div class="col-md-10">
                                    <div class="card" id="custom_card_height">
                                        <div class="card-body">
                                            <div class="row">
                                                <!-- Left Section: Custom Heading -->
                                                <div class="col-md-6">
                                                    <h4 class="card-title">Add Custom Heading Title For Challenge 1:
                                                    </h4>
                                                    <textarea id="title1" class="form-control" maxlength="150"
                                                        required><?php echo $data["landing_page_title1"]; ?></textarea>
                                                    <p class="text-muted mt-2">
                                                        <code>* This title will reflect on the Challenge 1 of game page<br>
                                                    * Character limit of text is 150</code>
                                                    </p>
                                                </div>

                                                <div class="col-md-6">
                                                    <h4 class="card-title">Total Questions</h4>
                                                    <div class="marks-container mb-2">
                                                        <!-- <span>Total Questions</span> -->
                                                        <input type="number" id="questions" class="form-control" min="1"
                                                            max="10" value="<?php echo $data["questions_01"]; ?>" />
                                                    </div>
                                                    <p class="text-muted">
                                                        <code>* Total number of questions to be displayed</code>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="row ">
                                                <div class="col-12 text-center">
                                                    <button
                                                        class="btn btn-md btn-success login-button-all core-button-color"
                                                        id="update_marks" type="submit" name="submit">Save</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <!-- third Card: Add Correct Answer Popup -->
                                <div class="card" id="card3">
                                    <div class="card-body">
                                        <div class="card" id="custom_card_height">
                                            <div class="card-content collapse show">
                                                <div class="card-body">
                                                    <div class="row" style="  margin-bottom: 10px;">
                                                        <div class="col-md-6">

                                                            <?php 
                                                                        $show_clue = ($data['show_clue'] == 'true') ? true : false;
                                                                        echo '<a href="events.php?events=switch_timer&switch=show_clue&current='.$data['show_clue'].'"><input type="checkbox" id="switchery2" class="switchery" data-size="md" '.switchery($show_clue).'/></a>';
                                                                    ?>
                                                            <label class="font-medium-2 text-bold-600 ml-1">Show
                                                                Clue</label>
                                                          
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <form method="post" enctype="multipart/form-data" id="myform">
                                                            <input type="hidden" name="themename" id="themename"
                                                                value="<?php echo $curruntTheme; ?>">
                                                            <div class="row">
                                                                <div class="col-md-6 addpair" id="leftColumn"></div>
                                                                <div class="col-md-6 addpair" id="rightColumn">
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-12 text-center">
                                                                    <button type="button" id="addPairButton"
                                                                        class=" btn-md btn btn-save add-pair add_form_field">Add
                                                                        Pair</button>
                                                                    <button type="button" id="saveButton"
                                                                        class="btn btn-md btn btn-save ">Save and
                                                                        Continue</button>
                                                                    <button type="button" id="clearAllButton"
                                                                        class="btn btn-md btn-save">Clear
                                                                        All</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card" id="card2">
                                <div class="card-body">
                                    <div class="card" id="custom_card_height">

                                        <div class="card-content collapse show">
                                            <div class="card-body">
                                                <div class="row">
                                                    <!-- First Column: Add Correct Answer Popup -->
                                                    <div class="col-md-6">
                                                        <div class="col-md-12 mb-2">
                                                            <h4 class="card-title">Add Correct Answer Popup </h4>
                                                            <form id="logo_upload1">
                                                                <fieldset class="form-group">
                                                                    <div class="mb-2">
                                                                        <textarea type="text"
                                                                            id="Correct_ans_text_popup"
                                                                            name="Correct_ans_text_popup"
                                                                            class="form-control"
                                                                            placeholder="Enter Text For Correct Answer Pop Up"
                                                                            maxlength="80"></textarea>


                                                                        <p class="text-muted">
                                                                            <code>* 
                                                                                    Maximum character limit should be 80 characters.</code>
                                                                        </p>
                                                                    </div>
                                                                    <span class="or-text">Or</span>
                                                                    <div class="custom-file mb-2">
                                                                        <input type="file" id="file_logo22"
                                                                            class="custom-file-input">
                                                                        <label class="custom-file-label"
                                                                            for="inputGroupFile01">Choose file</label>
                                                                    </div>
                                                                    <div class="form-actions formtext">
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <p class="text-muted">
                                                                                    <code>* Popup image dimensions must be 435 x 313 px.</code>
                                                                                </p>
                                                                                <p class="text-muted">
                                                                                    <code>* 
                                                                                    Allowed photo formats: JPG, JPEG, PNG, and GIF.</code>
                                                                                </p>
                                                                            </div>
                                                                        </div>

                                                                        <button
                                                                            class="btn btn-success em-color logo-button"
                                                                            onclick="submitForm(textinput, file, 2)"
                                                                            type="submit">
                                                                            Submit
                                                                        </button>
                                                                        <h4 class="card-title mt-2">
                                                                            Preview Section
                                                                        </h4>
                                                                        <div class="col-md-12 text-center">
                                                                            <div class="col-md-10 m-auto">
                                                                                <?php if($data["Correct_ans_img_popup"]!=""){?>
                                                                                <img class="img-fluid"
                                                                                    src="<?php echo possibleOnS3("../uploads/",$data["Correct_ans_img_popup"]); ?>" />
                                                                                <?php }?>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-12 text-center">
                                                                            <div class="col-md-10 m-auto">
                                                                                <?php if($data["Correct_ans_text_popup"] != "") { ?>
                                                                                <textarea id="Correct_ans_text_popup"
                                                                                    name="Correct_ans_text_popup"
                                                                                    readonly
                                                                                    class="form-control"><?php echo $data["Correct_ans_text_popup"];?></textarea>
                                                                                <?php } ?>
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </fieldset>
                                                            </form>
                                                        </div>
                                                    </div>

                                                    <!-- Second Column: Add Pop-Up For Game End -->
                                                    <div class="col-md-6">
                                                        <div class="col-md-12 mb-2">
                                                            <h4 class="card-title">Add end game Popup </h4>
                                                            <form id="logo_upload2">
                                                                <fieldset class="form-group">
                                                                    <div class="mb-2">
                                                                        <textarea type="text" class="form-control"
                                                                            id="end_game_text_popup"
                                                                            name="end_game_text_popup"
                                                                            placeholder="Enter Text For end game Pop Up"
                                                                            maxlength="80"></textarea>
                                                                        <p class="text-muted">
                                                                            <code>* 
                                                                                    Maximum character limit should be 80 characters.</code>
                                                                        </p>
                                                                    </div>

                                                                    <span class="or-text">Or</span>
                                                                    <div class="custom-file">
                                                                        <input type="file" id="file_logo1"
                                                                            class="custom-file-input">
                                                                        <label class="custom-file-label"
                                                                            for="inputGroupFile01">Choose file</label>
                                                                    </div>
                                                                    <div class="form-actions formtext">
                                                                        <div class="row formtop" style="display:block;">
                                                                            <div class="col-md-12">
                                                                                <p class="text-muted mt-2">
                                                                                    <code>* Popup image dimensions must be 435 x 313 px.</code>
                                                                                </p>
                                                                                <p class="text-muted">
                                                                                    <code>* 
                                                                                    Allowed photo formats: JPG, JPEG, PNG, and GIF.</code>
                                                                                </p>
                                                                            </div>
                                                                        </div>

                                                                        <button
                                                                            class="btn btn-success em-color logo-button"
                                                                            id="submitgame" name="submitgame"
                                                                            type="submit"
                                                                            onclick="submitForm(correctAnswer, file, 2)">Submit</button>
                                                                        <h4 class="mt-2 card-title">
                                                                            Preview Section
                                                                        </h4>
                                                                        <div class="col-md-12 text-center">
                                                                            <div class="col-md-10 m-auto">
                                                                                <?php if($data["end_game_img_popup"]!=""){?>
                                                                                <img class="img-fluid"
                                                                                    src="<?php echo possibleOnS3("../uploads/",$data["end_game_img_popup"]); ?>" />
                                                                                <?php }?>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-12 text-center">
                                                                            <div class="col-md-10 m-auto">
                                                                                <?php if($data["end_game_text_popup"] != "") { ?>
                                                                                <textarea id="Correct_ans_text_popup"
                                                                                    name="Correct_ans_text_popup"
                                                                                    readonly
                                                                                    class="form-control"><?php echo $data["end_game_text_popup"];?></textarea>
                                                                                <?php } ?>
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </fieldset>
                                                            </form>
                                                        </div>
                                                    </div>

                                                </div> <!-- End of Row -->
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
    $("#Correct_ans_text_popup, #end_game_text_popup, #new_cat,#projectinput5").on("input", function() {
        $(this).val($(this).val().replace(/^\s+/, ""));
    });

    var checkbol = false;
    $(document).ready(function() {
        // $('.fav_clr').select2({
        // placeholder: 'colors',
        // width: '100%',
        // border: '1px solid #e4e5e7',
        // });

        $(".ft-rotate-cw").click(function() {
            location.reload();
        });

        $("#noofpost").change(function() {
            var seletcedval = $('option:selected', $(this)).text();
            $.ajax({
                type: "POST",
                url: "events.php?events=multiplentry&action=" + seletcedval,
                data: "",
                success: function(result) {
                    console.log(result);
                    if (result == "true") {
                        swal({
                            title: 'Data Updated',
                            background: '#fff url(img/correct.png)'
                        });
                        // $(".swal2-popup.swal2-modal.swal2-show").css("background-image: url(img/correct.png) !important");
                        // swal('Success', 'Data Updated', '');
                        setTimeout(() => {
                            // location.reload();
                        }, 1000);
                    } else {
                        swal({
                            title: result,
                            background: '#fff url(img/wrong.png)'
                        });
                        swal('Error', result, 'error');
                    }
                }
            });
        });

    });

    $(document).ready(function() {
        $('#default-multiple').on('change', function() {
            // Count the number of selected options
            var selectedOptions = $(this).val();
            // If the number of selected options is greater than 10, show an alert
            if (selectedOptions.length > 6) {
                // alert("You can add only 10 words.");
                swal({
                    title: "Limit Reached",
                    text: "Maximum Category limit of 6 reached.",
                    icon: "warning",
                    background: '#fff url(img/wrong.png)',
                    button: "OK",
                });
                // Remove the last selected option to enforce the limit
                // This ensures the last selected option is removed from the selected options
                selectedOptions.pop();
                $(this).val(selectedOptions).trigger('change');
            }
        });
    });


    $('.fav_clr').on("select2:select", function(e) {
        var data = e.params.data.text;
        console.log(data + "--------data");
        t = $(".select2").val();
        console.log(t.length + "----------t");


        if (data == 'all' && checkbol == false) {
            console.log("all checck");
            checkbol = true;
            $(".fav_clr > option").prop("selected", "selected");

            $(".fav_clr").trigger("change");
            $("#allopn").prop("selected", false);
            checkbol = true;
            t = $(".select2").val();
            if (t.length > 9) {
                flags = true;
                alert("Maximum Category limit reached");
                checkbol = false;
                $(".fav_clr > option").prop("selected", false);
                $(".fav_clr").trigger("change");
            }

        } else if (data == 'all' && checkbol == true) {
            checkbol = false;
            console.log("all de check");
            $(".fav_clr > option").prop("selected", false);
            $(".fav_clr").trigger("change");
        }

    });


    function getData() {
        console.log(game_cat.val());
    }

    $('.upload-functionality').hide();
    $('#select-role').change(function() {
        var data = $(this).val();
        if (data == "error") {
            $('.upload-functionality').hide();
        } else {
            $('.upload-functionality').show();
        }
    });
    $('#title').keypress(function(event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            var s = $(this).val();
            console.log(s);
            $(this).val(s + "<br>");
        }
    });

    function ValidateImg(file, checkDimensions) {
        var img = new Image()
        img.src = window.URL.createObjectURL(file)
        img.onload = () => {
            console.log(img.width);
            console.log(checkDimensions);
            if (img.width == "128" && img.height == "128") {
                checkDimensions = true;
            } else {
                checkDimensions = false;
            }
        }
    }

    function getFileType(file) {
        if (file.type.match('image.*'))
            return 'image';
        if (file.type.match('video.*'))
            return 'video';
        return 'other';
    }



    async function validateImageHeight(file, maxHeight) {
        return new Promise((resolve, reject) => {
            const img = new Image();

            img.onload = function() {
                if (this.height <= maxHeight) {
                    resolve(true);
                } else {
                    resolve(false);
                }
            };

            img.onerror = function() {
                reject(new Error('Could not load image'));
            };

            const reader = new FileReader();
            reader.onload = function(event) {
                img.src = event.target.result;
            };

            reader.onerror = function() {
                reject(new Error('Could not read file'));
            };

            reader.readAsDataURL(file);
        });
    }





    var format = /[!@#$%^&*()_+\=\[\]{};':"\\|,.<>\/?]+/;
    //  var format = / ^[A-Za-z0-9 ]+$ /;
    // var format = /^[A-Za-z\s]*$/;
    var custom_cat = <?php echo json_encode($custom_cat ?? []); ?>;
    var default_cat = <?php echo json_encode($default_cat ?? []); ?>;

    $(".login-button").click(function() {
        var t = $(".select2").val();
        var new_cat = $("#new_cat").val();
        new_cat = new_cat.toUpperCase().trim();
        var flags = false;

        // Regular expression to match special characters
        var specialCharPattern = /[^A-Z0-9 ]/;


        if (!t || t.length === 0) {
            swal({
                title: 'Atleast one Category',
                // text: "Category name cannot exceed 20 characters.",
                background: '#fff url(img/wrong.png)'
            }).then(() => {
                setTimeout(() => {
                    location.reload();
                }, 1000);
            });
            return;
            return; // Prevent form submission
        }
        if (new_cat.length > 20) {
            swal({
                title: 'Invalid Category',
                text: "Category name cannot exceed 20 characters.",
                background: '#fff url(img/wrong.png)'
            }).then(() => {
                setTimeout(() => {
                    location.reload();
                }, 1000);
            });
            return;
        }
        if (new_cat !== "") {
            // Check for special characters
            if (specialCharPattern.test(new_cat)) {
                swal({
                    title: 'Invalid Category',
                    text: "Avoid special characters.",
                    background: '#fff url(img/wrong.png)'
                }).then(() => {
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                });
                return;
            }

            // Check for duplicates
            if (custom_cat.includes(new_cat) || default_cat.includes(new_cat)) {
                flags = true;
                swal({
                    title: 'Duplicate Category',
                    text: "This category already exists.",
                    background: '#fff url(img/wrong.png)'
                }).then(() => {
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                });
            } else {
                custom_cat.push(new_cat);
                flags = false;
            }
        }


        if (!flags) {
            $.ajax({
                type: "POST",
                url: "events.php?events=add_cat",
                data: {
                    "get_custom_cat": JSON.stringify(custom_cat),
                    "variables_array": t
                },
                success: function(result) {
                    if (result === "true") {
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
                        swal('Error', result, 'error');
                    }
                }
            });
        }
    });

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
            var ruleValue = $(this).val().trim();
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

    $(document).ready(function() {
        $('#backButton').click(function(event) {
            event.preventDefault();
            window.history.back();
        });

        $('.switchery').change(function() {
            var isChecked = $(this).is(':checked');

            // Update all existing pairs
            $('.pair').each(function() {
                var pairId = $(this).attr('id').split('-')[1];
                var clueLabel = $(this).find('label[for="clue_' + pairId + '"]');
                var clueInput = $(this).find('input[id="clue_' + pairId + '"]');
                var hiddenClueInput = $(this).find('input[type="hidden"][id="clue_' + pairId +
                    '"]');

                if (isChecked) {
                    // If switching to show clues
                    if (hiddenClueInput.length) {
                        // Convert hidden input to visible
                        hiddenClueInput.replaceWith(
                            '<input type="text" maxlength="100" id="clue_' + pairId +
                            '" name="clues[]" value="' + hiddenClueInput.val() +
                            '" required style="grid-column: 2 / 3; width: 100%;">');

                        // Add the clue label if it doesn't exist
                        if (!clueLabel.length) {
                            $(this).append('<label for="clue_' + pairId +
                                '" style="grid-column: 1 / 2;">Clue</label>');
                            $(this).append('<input type="text" maxlength="100" id="clue_' +
                                pairId +
                                '" name="clues[]" value="' + hiddenClueInput.val() +
                                '" required style="grid-column: 2 / 3; width: 100%;">');
                        }
                    }
                    clueLabel.show();
                    clueInput.show();
                } else {
                    // If switching to hide clues
                    if (clueInput.length) {
                        // Convert visible input to hidden
                        clueInput.replaceWith('<input type="hidden" id="clue_' + pairId +
                            '" name="clues[]" value="' + clueInput.val() + '">');
                    }
                    clueLabel.hide();
                }
            });
        });


        var max_fields = 15;
        var x = <?php echo count($questions_and_answers); ?>;
        var question_count = <?php echo $question_count  ?>;
        console.log(question_count);



        // Place existing pairs
        var leftColumn = $('#leftColumn');
        var rightColumn = $('#rightColumn');


        <?php if (is_array($questions_and_answers) && count($questions_and_answers) > 0): ?>
        <?php foreach ($questions_and_answers as $index => $qa): ?>
        var showClue = $('.switchery').is(':checked');
        var pairHtml =
            '<div class="pair" id="pair-<?php echo $index; ?>" style="display: grid; grid-template-columns: auto 1fr auto; align-items: center; gap: 10px;">\
    <label for="question_<?php echo $index; ?>" style="grid-column: 1 / 2;">Question</label>\
    <input type="text" maxlength="100" id="question_<?php echo $index; ?>" name="questions[]" value="<?php echo preg_replace('/[^a-zA-Z0-9\s]/', '', $qa['question']) ?>" required style="grid-column: 2 / 3; width: 100%;">\
    <a href="#" class="delete" onclick="deletePair(<?php echo $index; ?>)" style="grid-column: 3 / 4; justify-self: center;"><i class="far fa-trash-alt"></i></a>\
    <label for="answer_<?php echo $index; ?>" style="grid-column: 1 / 2;">Answer</label>\
    <input type="text" maxlength="100" id="answer_<?php echo $index; ?>" name="answers[]" value="<?php echo preg_replace('/[^a-zA-Z0-9\s]/', '', $qa['answer']) ?>" required style="grid-column: 2 / 3; width: 100%;">';

        if (showClue) {
            pairHtml +=
                '<label for="clue_<?php echo $index; ?>" style="grid-column: 1 / 2;">Clue</label>\
    <input type="text" maxlength="100" id="clue_<?php echo $index; ?>" name="clues[]" value="<?php echo isset($qa['clue']) ? preg_replace('/[^a-zA-Z0-9\s]/', '', $qa['clue']) : '' ?>" required style="grid-column: 2 / 3; width: 100%;">';
        } else {
            pairHtml +=
                '<input type="hidden" id="clue_<?php echo $index; ?>" name="clues[]" value="<?php echo isset($qa['clue']) ? preg_replace('/[^a-zA-Z0-9\s]/', '', $qa['clue']) : '' ?>">';
        }

        pairHtml += '</div>';

        if ((<?php echo $index; ?> % 2) === 0) {
            leftColumn.append(pairHtml);
        } else {
            rightColumn.append(pairHtml);
        }
        <?php endforeach; ?>
        <?php endif; ?>

        // Modify your add pair functionality
        $(document).on('click', '#addPairButton', function(e) {
            e.preventDefault();
            if (x < max_fields) {
                x++;
                var index = x;
                var showClue = $('.switchery').is(':checked');
                var pairHtml;

                pairHtml = '<div class="pair" id="pair-' + index +
                    '" style="display: grid; grid-template-columns: auto 1fr auto; align-items: center; gap: 10px;">\
            <label for="question_' + index + '" style="grid-column: 1 / 2;">Question</label>\
            <input type="text" maxlength="100" id="question_' + index +
                    '" name="questions[]" value="" required style="grid-column: 2 / 3; width: 100%;">\
            <a href="#" class="delete" onclick="deletePair(' + index +
                    ')" style="grid-column: 3 / 4; justify-self: center;"><i class="far fa-trash-alt"></i></a>\
            <label for="answer_' + index + '" style="grid-column: 1 / 2;">Answer</label>\
            <input type="text" maxlength="100" id="answer_' + index +
                    '" name="answers[]" value="" required style="grid-column: 2 / 3; width: 100%;">';

                if (showClue) {
                    pairHtml += '<label for="clue_' + index + '" style="grid-column: 1 / 2;">Clue</label>\
                <input type="text" maxlength="100" id="clue_' + index +
                        '" name="clues[]" value="" required style="grid-column: 2 / 3; width: 100%;">';
                } else {
                    pairHtml += '<input type="hidden" id="clue_' + index + '" name="clues[]" value="">';
                }

                pairHtml += '</div>';

                if ((x % 2) === 0) {
                    rightColumn.append(pairHtml);
                } else {
                    leftColumn.append(pairHtml);
                }
            } else {
                alert('You reached the limit of ' + max_fields + ' pairs');
            }
        });
        // Clear all pairs functionality
        $('#clearAllButton').click(function() {
            $('.pair').remove();
            x = 0;
        });

        // Delete individual pair
        window.deletePair = function(index) {
            $('#pair-' + index).remove();
            x--;
        };

        // Validate and save

        // Update your save functionality to handle clues conditionally
        $('#saveButton').click(function() {
            var isValid = true;
            var charLimit = 200;
            var hasLimitExceeded = false;
            var showClue = $('.switchery').is(':checked');

            // Validate questions and answers
            $('#myform input[name="questions[]"], #myform input[name="answers[]"]').each(function() {
                var value = $(this).val().trim();
                if (value === '') {
                    isValid = false;
                    $(this).css('border', '2px solid red');
                } else if (value.length > charLimit) {
                    hasLimitExceeded = true;
                    $(this).css('border', '2px solid red');
                } else {
                    $(this).css('border', '');
                }
            });

            // Only validate visible clues
            if (showClue) {
                $('#myform input[type="text"][name="clues[]"]').each(function() {
                    var value = $(this).val().trim();
                    if (value === '') {
                        isValid = false;
                        $(this).css('border', '2px solid red');
                    } else if (value.length > charLimit) {
                        hasLimitExceeded = true;
                        $(this).css('border', '2px solid red');
                    } else {
                        $(this).css('border', '');
                    }
                });
            }

            if (hasLimitExceeded) {
                Swal.fire({
                    title: 'Character Limit Exceeded!',
                    text: 'Each input must be 200 characters or fewer.',
                    icon: 'warning',
                    background: '#fff url(img/wrong.png)'
                });
                return;
            }

            if (x < 2) {
                Swal.fire({
                    title: 'Validation Error!',
                    text: 'Please add at least two pairs before saving.',
                    icon: 'warning',
                    background: '#fff url(img/wrong.png)'
                });
            } else if (isValid) {
                var formData = $('#myform').serialize();
                console.log(formData);
                $.ajax({
                    type: 'POST',
                    url: 'events.php?events=questions_and_answers&showClue=' +
                        (showClue ? '1' : '0'),
                    data: formData,
                    success: function(response) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Data has been saved.',
                            icon: 'success',
                            background: '#fff url(img/correct.png)',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location
                                .reload();
                        });
                    },
                    error: function(xhr, status,
                        error) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred while saving data.',
                            background: '#fff url(img/wrong.png)',
                            icon: 'error'
                        });
                    }
                });
            } else {
                Swal.fire({
                    title: 'Validation Error!',
                    text: 'Please fill in all fields.',
                    background: '#fff url(img/wrong.png)',
                    icon: 'warning'
                });
            }
        });

        document.getElementById('questions').addEventListener('input',
            function(e) {
                if (this.value < 1) {
                    this.value =
                        1; // Reset to 0 if a negative number is typed
                }
            });


        $("#update_marks").click(function() {
            let questions = $("#questions").val();
            var title1 = $("#title1").val();
            console.log(questions);
            if (title1.trim().length === 0 || title1.length > 150) {
                errorFlag = true;
                errorMsg = title1.trim().length === 0 ?
                    "The title cannot be empty or contain only spaces." :
                    "You exceed the maximum limit of the title.";
            }

            if (questions > question_count) {
                Swal.fire({
                    title: "Error",
                    text: "Answers required cannot be more than the total number of questions available (" +
                        question_count + ").",
                    background: '#fff url(img/wrong.png)',
                    icon: "error",

                    confirmButtonText: "OK"
                })
                return; // Stop the submission
            }


            // alert(rows);
            $.ajax({
                type: "POST",
                url: "events.php?events=add_points_01",
                data: {
                    "questions": questions,
                    "title1": title1

                },
                success: function(result) {
                    if (result == "true") {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Data has been upated.',
                            icon: 'success',
                            background: '#fff url(img/correct.png)',
                            confirmButtonText: 'OK'
                        })
                        setTimeout(() => {
                            location
                                .reload();
                        }, 1000);
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred while saving data.',
                            background: '#fff url(img/wrong.png)',
                            icon: 'error'

                        });
                    }
                }

            });
        })

    });


    // Answer Popup Form Submission
    var myform = document.querySelector('#logo_upload1');
    var inputfile_logo22 = document.querySelector('#file_logo22');
    var request = new XMLHttpRequest();

    myform.addEventListener('submit', function(e) {
        e.preventDefault();

        var correctAnswer = document.getElementById('Correct_ans_text_popup').value.trim();
        var file = inputfile_logo22.files[0];
        var errorFlag = false;
        var errorMsg = "";

        if ((correctAnswer && file) || (!correctAnswer && !file)) {
            errorFlag = true;
            errorMsg = "You can only upload either text or an image.";
        } else if (file) {
            var validExt = ["jpg", "jpeg", "png", "gif"];
            var extension = file.name.split('.').pop().toLowerCase();
            var fileSize = Math.round((file.size / 1024));
            var maxfileLimit = 5 * 1024; // in KB

            if (validExt.indexOf(extension) === -1) {
                errorFlag = true;
                errorMsg = "Unsupported file format";
            }
            if (fileSize > maxfileLimit) {
                errorFlag = true;
                errorMsg = "Filesize exceeds max limit of 5 MB";
            }
            if (getFileType(file) !== "image") {
                errorFlag = true;
                errorMsg = "Only image files are allowed.";
            }

            if (!errorFlag) {
                var img = new Image();
                img.onload = function() {
                    var width = img.width;
                    var height = img.height;
                    if (width !== 435 || height !== 313) {
                        errorFlag = true;
                        errorMsg = "Image dimensions must be 435 x 313 pixels.";
                        swal({
                            title: errorMsg,
                            background: '#fff url(img/wrong.png)'
                        });
                    } else {
                        submitForm(correctAnswer, file, 2); // Redirect to the second tab
                    }
                };
                img.onerror = function() {
                    errorFlag = true;
                    errorMsg = "Invalid image file.";
                    swal({
                        title: errorMsg,
                        background: '#fff url(img/wrong.png)'
                    });
                };
                img.src = URL.createObjectURL(file);
            }
        } else if (correctAnswer) {
            submitForm(correctAnswer, null, 2); // Redirect to the second tab
        }

        if (errorFlag) {
            swal({
                title: errorMsg,
                background: '#fff url(img/wrong.png)'
            });
        }

        function submitForm(correctAnswer, file, tabNumber) {
            var formData = new FormData();
            if (file) {
                formData.append('file', file);
            } else {
                formData.append('textinput', correctAnswer);
            }

            var request = new XMLHttpRequest();
            request.open('post', 'upload-images.php?&events=upload_logo1', true);

            request.onreadystatechange = function() {
                if (request.readyState == 4 && request.status == 200) {
                    if (request.responseText == "true") {
                        swal({
                            title: 'Successfully Updated',
                            background: '#fff url(img/correct.png)'
                        });
                        setTimeout(() => {
                            window.location.hash = "#tab" +
                                tabNumber; // Set the hash to the second tab
                            setTimeout(() => {
                                location.reload(); // Reload the page
                            }, 500);
                        }, 1000);
                    } else {
                        swal({
                            title: request.responseText,
                            background: '#fff url(img/wrong.png)'
                        });
                        $(".logo-button").show();
                    }
                }
            };
            request.send(formData);
        }
    }, false);




    // End Game Popup Form Submission
    var myform = document.querySelector('#logo_upload2');
    var inputfile_logo1 = document.querySelector('#file_logo1');
    var end_game_text_popup = document.querySelector('#end_game_text_popup');
    var request = new XMLHttpRequest();

    myform.addEventListener('submit', function(e) {
        e.preventDefault();

        var textinput = end_game_text_popup.value.trim();
        var file = inputfile_logo1.files[0];
        var errorFlag = false;
        var errorMsg = "";
        var tabNumber = 2; // Define tabNumber here

        // Validate if both text and file are provided or neither is provided
        if ((textinput && file) || (!textinput && !file)) {
            errorFlag = true;
            errorMsg = "Please upload either an image or enter text, not both.";
        } else if (file) {
            var validExt = ["jpg", "jpeg", "png", "gif"];
            var extension = file.name.split('.').pop().toLowerCase();
            var fileSize = Math.round((file.size / 1024));
            var maxfileLimit = 5 * 1024; // in KB

            // Validate file format
            if (validExt.indexOf(extension) === -1) {
                errorFlag = true;
                errorMsg = "Unsupported file format.";
            }

            // Validate file size
            if (fileSize > maxfileLimit) {
                errorFlag = true;
                errorMsg = "Filesize exceeds max limit of 5 MB.";
            }

            // Validate file type
            if (getFileType(file) !== "image") {
                errorFlag = true;
                errorMsg = "Only image files are allowed.";
            }

            // Check image dimensions
            if (!errorFlag) {
                var img = new Image();
                img.onload = function() {
                    var width = img.width;
                    var height = img.height;
                    if (width !== 435 || height !== 313) {
                        errorFlag = true;
                        errorMsg = "Image dimensions must be 435 x 313 pixels.";
                    }

                    // Show error popup if any validation fails
                    if (errorFlag) {
                        swal({
                            title: errorMsg,
                            background: '#fff url(img/wrong.png)'
                        });
                    } else {
                        submitForm(file, textinput, tabNumber); // Submit the form if no errors
                    }
                };
                img.src = URL.createObjectURL(file);
                return; // Exit the event listener to wait for img.onload
            }
        }

        // Show error popup for any other validation issues
        if (errorFlag) {
            swal({
                title: errorMsg,
                background: '#fff url(img/wrong.png)'
            });
        } else {
            submitForm(file, textinput, tabNumber); // Submit the form if no errors
        }
    }, false);


     // File validation function with dimensions
        function validateFileWithDimensions(file, fieldName, dimension) {
            return new Promise((resolve, reject) => {
                if (!file) {
                    return reject(`${fieldName} is required.`);
                }

                // Check file type
                if (!allowedTypes.includes(file.type)) {
                    return reject(`${fieldName} must be a PNG, JPG, JPEG file.`);
                }

                // Check file size
                if (file.size > maxFileSize) {
                    return reject(`${fieldName} must not exceed 5 MB.`);
                }

                // Check file dimensions
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = new Image();
                    img.onload = function() {
                        if (img.width !== dimension.width || img.height !== dimension.height) {
                            return reject(
                                `${fieldName} must have dimensions ${dimension.width}x${dimension.height}.`
                            );
                        }
                        resolve();
                    };
                    img.onerror = function() {
                        reject(`Failed to load ${fieldName} for dimension validation.`);
                    };
                    img.src = e.target.result;
                };
                reader.onerror = function() {
                    reject(`Failed to read ${fieldName}.`);
                };
                reader.readAsDataURL(file);
            });
        }

          var allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
        var maxFileSize = 5 * 1024 * 1024; // 5 MB
        var dimensions = {
            game1_desk: {
                width: 1920,
                height: 1080,
            },
            game1_mob: {
                width: 414,
                height: 896,
            },
           
        };
   

    function submitForm(file, textinput, tabNumber) {
        var formData = new FormData();
        if (file) {
            formData.append('file', file);
        } else {
            formData.append('textinput', textinput);
        }

        request.open('post', 'upload-images.php?&events=upload_logo2', true);

        request.onreadystatechange = function() {
            if (request.readyState == 4 && request.status == 200) {
                if (request.responseText == "true") {
                    swal({
                        title: 'Successfully Updated',
                        background: '#fff url(img/correct.png)'
                    });
                    setTimeout(() => {
                        window.location.hash = "#tab" + tabNumber; // Redirect to the second tab
                        setTimeout(() => {
                            location.reload(); // Reload the page
                        }, 500);
                    }, 1000);
                } else {
                    swal({
                        title: request.responseText,
                        background: '#fff url(img/wrong.png)'
                    });
                    $(".logo-button").show();
                }
            }
        };
        request.send(formData);
    }


    </script>
</body>

</html>