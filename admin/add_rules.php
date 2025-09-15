<?php
ob_start();
error_reporting(E_ALL);
session_start();
$settings = json_decode(file_get_contents("settings.js"), true)[0];
$organizationId = $_SESSION['organizationId'];
$sessionId = $_SESSION['sessionId'];
include_once '../dao/config.php';
// include_once '../../admin_assets/triggers.php';
include_once ("userFunction.php");

// if (!$_SESSION['adminId']) {
//     header('Location:../index.php?save');
// }

$tabName = "";
$themeid = $_GET["themeid"]

  ?>

<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

<head>

    <?php include_once ("../../admin_assets/common-css.php"); ?>
    <!-- Only unique css classes -->
    <link href="css_custom/styles.css" type="text/css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style rel="stylesheet" type="text/css">
    .card-body {
        cursor: pointer;
    }

    .form-check-input {
        margin: 0 auto;
        margin-top: 14px;
    }

    .show-image {
        width: 100px;
    }

    .show-image-edit,
    .show-video-edit {
        height: 84px;
        margin: 0 auto;
        margin-top: 16px;
    }

    .show-video {
        width: 100px;
    }

    .show-warning {
        background: #e25c64;
        text-align: center;
        color: white;
        font-size: 14px;
        padding: 9px;
        border-radius: 10px;
    }

    .remove-hint {
        margin-top: 10px;
    }

    #custom_card_height {
        height: auto !important;
    }

    .question-main-title {
        font-family: 'FiraSans-Medium';
    }

    .ruleBox {
        display: flex;
    }

    #ruleContainer {
        width: 90%;
    }

    #addRuleBtn,
    .removeRuleBtn {
        height: 40px;
        margin-left: 10px;
    }
    </style>

    <!-- Only unique css classes -->
</head>


<body class="horizontal-layout horizontal-menu 2-columns" data-open="hover" data-menu="horizontal-menu"
    data-color="bg-gradient-x-purple-blue" data-col="2-columns">
    <?php include_once ("../../admin_assets/common-header.php"); ?>
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-wrapper-before"></div>
            <div class="content-header row">
                <div class="content-header-left col-md-4 col-12 mb-2">
                    <h3 class="content-header-title"><?php //echo $tabName; ?></h3>
                </div>
            </div>
            <div class="content-body">
                <section id="basic-form-layouts">
                    <div class="row match-height">
                        <!-- add content here -->

                        <div class="col-md-12">
                            <div class="card" id="custom_card_height">
                                <?php //cardHeader("Rules"); ?>
                                <div class="card-content collapse show">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="col-md-12">
                                                    <a href="#" id="backButton" class="btn btn-md btn-success">Back </a>
                                                    <?php if($themeid > 2){?>
                                                    <a> <button class="btn btn-md btn-success action-buttons"><i
                                                                class="fas fa-plus"></i>
                                                            Rules</button></a>

                                                    <?php }?>
                                                </div><br>
                                                <div class="table-responsive">
                                                    <table id="example"
                                                        class="table table-striped table-bordered zero-configuration">
                                                        <thead>
                                                            <tr>
                                                                <th>No </th>
                                                                <th>Rules</th>
                                                                <th>Edit</th>
                                                                <?php if($themeid > 2){?>
                                                                <th>Delete</th>
                                                                <?php }?>
                                                                <!-- <th>Delete</th> -->
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                              $sql = "select *  from rules where  themeid='$themeid' AND organizationId='$organizationId' AND sessionId='$sessionId'";
                              //    echo $sql;
                              $sql = execute_query( $sql);
                              $tableCount = 1;
                              while ($get = mysqli_fetch_array($sql)) { ?>
                                                            <?php
                                //  if ($get['status'] == 1) {
                                //   $btntext = "Active";
                                //   $class = "btn-success";
                                // } else {
                                //   $btntext = "Inactive";
                                //   $class = "btn-primary";
                                // }
                                ?>
                                                            <tr>
                                                                <td><?php echo $tableCount; ?></td>
                                                                <td> <?php 
                                  $rules =  $get['rules_text'];
                                  $rulesArray = explode(';', $rules);
                                   $count = 1; 
                                  foreach ($rulesArray as $rule) {
                                    echo $count . ". " . htmlspecialchars($rule) . "<br>";
                                     $count++; 
                                     
                                  }
                                  
                                  
                                  ?>



                                                                </td>

                                                                <td><a href="edit_rules.php?id=<?php echo $get['id']; ?>&themeid=<?php echo $get['themeid']; ?>"
                                                                        class="btn btn-sm btn-info">Edit</a></td>
                                                                <!-- <td>
                                    <button class="btn btn-sm <?php //echo $class; ?> update_status"
                                      id="<?php //echo $btntext; ?>" data-id="<?php //echo $get['id']; ?>"
                                      data-themeid="<?php //echo $get['themeid']; ?>" data-table="rules"
                                      data-status="<?php //echo $get['status']; ?>"><?php //echo $btntext; ?></button> 
                                    </td> -->
                                                                <!-- <td><button class="btn btn-sm btn-danger edit-button" id="<?php echo $get['id']; ?>">Edit</button></td> -->
                                                                <?php if($themeid > 2){?>
                                                                <td>
                                                                    <button class="btn btn-sm btn-danger delete-button"
                                                                        data-id="<?php echo $get['id']; ?>"
                                                                        data-themeid="<?php echo $get['themeid']; ?>"
                                                                        data-table="rules">Delete</button>
                                                                </td>
                                                                <?php }?>

                                                            </tr>


                                                            <?php
                              }

                              ?>

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

    </div>

    <!-- The Modal -->
    <div class="modal" id="myModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header text-center">
                    <h4 class="modal-title question-main-title">Add Rules</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <div class="col-md-12">
                        <form id="myform" enctype="multipart/form-data">
                            <input type="hidden" name="themeid" value="<?php echo $themeid; ?>">
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-12">

                                        <div class="ruleBox">
                                            <div id="ruleContainer">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name="rules[]"
                                                            placeholder="Enter rule">
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-danger removeRuleBtn"
                                                                type="button"><i class="fas fa-minus"></i></button>
                                                        </span>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="col-md-12 option-data" pos="2">
                                                        <button type="button" class="btn btn-success" id="addRuleBtn"><i
                                                                class="fas fa-plus"></i>Add new rule</button>
                                                        <input type="submit" value="Submit" name="submit"
                                                            class="btn btn-success em-color logo-button">
                                                    </div>
                                                </div>
                                                <p>* Keep 6 rules at the most<br>
                                                    * At least 1 rule is required<br>
                                                    * character limit of each rule is 200</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <?php include ("../../admin_assets/footer.php"); ?>
    <?php include_once ("../../admin_assets/common-js.php"); ?>


    <script type="text/javascript">
    $(".action-buttons").click(function() {
        $("#myModal").modal("show");
    });
    $(".edit-buttons").click(function() {
        $("#myModal").modal("show");
    });
    </script>

    <script>
    document.getElementById("backButton").addEventListener("click", function(event) {
        event.preventDefault(); // Prevent the default anchor behavior
        history.back();
    });
    $(document).ready(function() {

        $("#addRuleBtn").click(function() {

            var ruleCount = $("#ruleContainer .form-group").length;
            if (ruleCount >= 6) {
                swal("", "You can't add more than 6 rules", "error");
            } else {
                $("#ruleContainer").append(
                    '<div class="form-group"><div class="input-group"><input type="text" class="form-control" name="rules[]" placeholder="Enter rule" maxlength="100"><span class="input-group-btn"><button class="btn btn-danger removeRuleBtn" type="button"><i class="fas fa-minus"></i></button></span></div></div>'
                );
            }
        });
        $(document).on('click', '.removeRuleBtn', function() {
            $(this).closest('.form-group').remove();
        });

        $('#myform').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url: 'upload_rules.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log("out put---------", response);
                    if (response == "success") {
                        swal("", "Rules added successfully", "success").then(() => {
                            $('#myModal').modal('hide');
                            location.reload();
                        });

                    } else {
                        swal("", "Please check query", "error");
                    }
                }
            });
        });


        $(".delete-button").click(function(event) {
            event.preventDefault(); // Prevent default link behavior

            var id = $(this).data('id');
            var themeid = $(this).data('themeid');
            var table = $(this).data('table');
            var confirmation = confirm("Are you sure you want to delete this record?");
            if (confirmation) {
                $.ajax({
                    type: "POST", // or "GET"
                    url: "delete_data.php", // URL to the delete script
                    data: {
                        id: id,
                        themeid: themeid,
                        table: table // Pass the table name as well
                    },
                    success: function(response) {
                        if (response == "success") {
                            swal("", "Data deleted successful.", "success").then(() => {
                                location.reload();
                            });
                        } else {
                            swal("", "Please check query.", "error");

                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle error
                        alert("Error deleting data");
                    }
                });
            }
        });


        $(".update_status").click(function() {
            var id = $(this).data('id');
            var themeid = $(this).data('themeid');
            var table = $(this).data('table');
            var status = $(this).data('status');

            // Toggle the status (0 to 1 or 1 to 0)
            var newStatus = status == 0 ? 1 : 0;

            // Send AJAX request
            $.ajax({
                type: "POST",
                url: "update_data.php",
                data: {
                    id: id,
                    themeid: themeid,
                    table: table,
                    status: newStatus
                },
                success: function(response) {
                    // Update the button text and data-status attribute
                    if (response == "success") {
                        swal("", "Status updated successful.", "success").then(() => {
                            location.reload();
                        });
                    } else {
                        swal("", "Please check query.", "error");

                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error updating status:", error);
                }
            });
        });


    });
    </script>
</body>

</html>