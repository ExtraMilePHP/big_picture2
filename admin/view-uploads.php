<?php
ob_start();
error_reporting(0);
session_start();
$settings=json_decode(file_get_contents("settings.js"),true)[0];
$organizationId=$_SESSION['organizationId'];
$sessionId=$_SESSION['sessionId'];
include_once '../dao/config.php';
include_once '../../admin_assets/triggers.php';

$_SESSION["gameTitle"]=$settings["gameName"];


require '../../vendor/autoload.php';
include_once "../s3/s3_functions.php"; // s3 function for easy upload and get signed urls, still need env file and autoload



$tabName="Uploads";

include_once 'themes/themesets.php';
include_once 'themes/themeTools.php';

$data=fetchThemeData();

$extraFields=unserialize($data["new_fields"]);

// print_r($extraFields);
?>

<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

<head>
    <?php include_once("../../admin_assets/common-css.php");?>
    <!-- Only unique css classes -->
    <style rel="stylesheet" type="text/css">
    @media (min-width: 576px) {
        .modal-dialog {
            max-width: 790px !important;
            margin: 1.75rem auto;
        }

        video#videoid {
            width: 61%;
            position: relative;
            margin: auto;
        }
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
    </style>
    <!-- Only unique css classes -->
</head>

<body class="horizontal-layout horizontal-menu 2-columns" data-open="hover" data-menu="horizontal-menu"
    data-color="bg-gradient-x-purple-blue" data-col="2-columns">
    <?php include_once("../../admin_assets/common-header.php");?>
    <div class="app-content content">
        <div class="content-wrapper">
            <!-- <div class="content-wrapper-before"></div>
            <div class="content-header row">
                <div class="content-header-left col-md-4 col-12 mb-2">
                    <h3 class="content-header-title"><?php echo $tabName;?></h3>
                </div>
            </div> -->
            <div class="content-body">
                <section id="basic-form-layouts">
                    <div class="row justify-content-center">
                        <div class="col-md-11">
                            <div class="container-fluid ">
                                <!-- Tab Navigation -->
                                <div class="custom-tabs-container ">
                                    <ul class="nav nav-pills custom-tabs justify-content-center">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="tab1" onclick="showCard(1)">1. View
                                                Uploads</a>
                                        </li>
                                     
                                    </ul>
                                </div>

                                <!-- First Card: Add Content -->
                                <div class="card mt-2" id="card1">
                                    <div class="card-body">
                                        <div class="card" id="custom_card_height">
                                            <!-- <?php cardHeader("View Uploads");?> -->
                                            <div class="card-content collapse show">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <a href="csv-download.php" target="_blank" class="btn btn-sm btn-danger" style="    position: relative;float: right;margin-left:20px;">Download
                                                            Csv</a>
                                                    
                                                         
                                                        </div>
                                                    </div>
                                                    <div class="row" style="margin-top:20px;">
                                                        <div class="col-md-12">
                                                            <div class="table-responsive">



                                                                <table
                                                                    class="table table-striped table-bordered zero-configuration">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Name</th>
                                                                            <th>Stage 1</th>
                                                                            <th>Stage 2</th>
                                                                            <th>Stage 3</th>
                                                                            <th>Score</th>
                                                                            <th>Points</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <?php 
                                           $sql="select * from question where organizationId='$organizationId' and sessionId='$sessionId' order by id desc";
                                           $sql=execute_query($sql);
                                           while($get=mysqli_fetch_array($sql)){
                                            
                                            ?>
                                                                    <tr>



                                                                        <td><?php echo $get["name"];?></td>
                                                                        <td><?php echo $get["question5"];?></td>
                                                                       <td><?php echo $get["question1"];?></td>
                                                                        <td><?php echo $get["question3"];?></td>
                                                                        <td><?php echo $get["score"];?></td>
                                                                        <td><?php echo $get["end_time"];?></td>
                                                                    </tr>

                                                                    <?php     }  ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- add content here end -->
                                </div>
                            </div>
                </section>
            </div>
        </div>
    </div>

    <div class="modal" id="rules">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">

                    <button type="button" class="close" id="ruleclose" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="font-size: 31px;">&times;</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <div class="col-sm-11 col-md-11 col-lg-11 col-xs-12 auto" style="margin:auto;float:none;">
                        <img src="" id="opneimg" style="width:100%"></img>
                        <video width="100%" id="videoid" controls>
                            <source src="" type="video/mp4">

                        </video>
                    </div>
                </div>

                <!-- Modal footer -->
                <!-- <div class="modal-footer">
          <button type="button" id="ruleclose" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div> -->

            </div>
        </div>
    </div>



    <?php include("../../admin_assets/footer.php");?>
    <?php include_once("../../admin_assets/common-js.php");?>
    <script>
    function showCard(cardNumber) {
        // Hide all cards
        document.getElementById('card1').style.display = 'none';
        // document.getElementById('card2').style.display = 'none';
        // document.getElementById('card3').style.display = 'none';

        // Remove active class from all tabs
        document.getElementById('tab1').classList.remove('active');
        // document.getElementById('tab2').classList.remove('active');
        // document.getElementById('tab3').classList.remove('active');

        // Show the selected card
        document.getElementById('card' + cardNumber).style.display = 'block';

        // Add active class to the selected tab
        document.getElementById('tab' + cardNumber).classList.add('active');

        // Update the hash in the URL
        window.location.hash = "#tab" + cardNumber;
    }

    // Activate the correct tab on page load based on the URL hash
    window.onload = function() {
        var hash = window.location.hash;
        if (hash) {
            var tabNumber = hash.replace("#tab", "");
            showCard(tabNumber);
        } else {
            showCard(1); // Default to the first tab if no hash is present
        }
    };
    </script>

    <script type="text/javascript">
    $("#opneimg").css("display", "none");
    $("#videoid").css("display", "none");


    $(".em-color").click(function() {
        var currentimg = $(this).attr("data");
        var type = $(this).attr("type");
        if (type == "image") {
            $("#opneimg").attr("src", currentimg);

            $("#opneimg").css("display", "block");
            $("#videoid").css("display", "none");
            $("#rules").css("display", "block");

        } else if (type == "video") {
            $("#videoid").attr("src", currentimg);

            $("#opneimg").css("display", "none");
            $("#videoid").css("display", "block");
            $("#rules").css("display", "block");
        }
        // $("#opneimg").attr("src",currentimg);
        // $("#rules").css("display","block");
    });
    $("#ruleclose").click(function() {

        $("#rules").css("display", "none");
    });
    $(".switchery").click(function() {
        var switchData = $(this).prev().attr("current_id");
        var status = $(this).prev().attr("current_status");
        if (status == "disapprove") {
            var pushData = 1;
            $(this).nextAll('.data-mention:first').html("&nbsp; Approved");
            $(this).prev().attr("current_status", "approve");
        } else {
            var pushData = 0;
            $(this).nextAll('.data-mention:first').html("&nbsp; Disapproved");
            $(this).prev().attr("current_status", "disapprove");
        }
        $.ajax({
            type: "POST",
            url: "events.php?events=approve&action=" + pushData + "&target=" + switchData,
            data: "",
            success: function(result) {
                console.log(result);
            }
        });
    });

    $("#allapproved").click(function() {
        $.ajax({
            type: "POST",
            url: "events.php?events=allapproved&action=" + 1,
            data: "",
            success: function(result) {
                location.reload();
            }
        });
    });
    $("#alldisapproved").click(function() {
        $.ajax({
            type: "POST",
            url: "events.php?events=alldisapproved&action=" + 0,
            data: "",
            success: function(result) {
                location.reload();
            }
        });
    });



    function ToogleData() {


    }
    </script>
</body>

</html>