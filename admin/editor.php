<?php
ob_start();
error_reporting(0);
session_start();
$settings = json_decode(file_get_contents("settings.js"), true)[0];
$organizationId = $_SESSION['organizationId'];
$sessionId = $_SESSION['sessionId'];
$curruntTheme=$_GET["name"];
// echo $curruntTheme;
$_SESSION['themename']=$curruntTheme;
// echo $_SESSION['themename'];
// echo "fghjk";
include_once '../dao/config.php';
include_once '../../admin_assets/triggers.php';
require '../../vendor/autoload.php'; // Include the Composer autoload file
include_once "../s3/s3_functions.php"; // s3 function for easy upload and get signed urls, still need env file and autoload

include_once 'themes/processThemes.php';
include_once 'themes/themeTools.php';

include_once 'process-questions.php';
// echo $_GET["name"];
// print_r( $question);
// die();
require '../../vendor/autoload.php';
$data=fetchThemeData();

$settings["pageLinks"] = [
  "Back" => "themeUpdate.php?name=" . $curruntTheme
];


use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

$s3client = new Aws\S3\S3Client([
    'version' => $version,
    'region'  => $region,
    'credentials' => [
        'key'    => $key,
        'secret' => $secretkey
    ]
]);

$tabName = "";

// $getAllQuestion = "select * from questions where sessionId='$sessionId' and themename ='$themename' and organizationId='$organizationId'";
$getAllQuestion = "select * from questions where sessionId='$sessionId' and themename ='$curruntTheme' and organizationId='$organizationId'";

// echo $getAllQuestion;
$getAllQuestion = execute_query( $getAllQuestion);
$getAllQuestion = mysqli_num_rows($getAllQuestion);
$total_q = $getAllQuestion;

?>

<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

<head>

    <?php include_once("../../admin_assets/common-css.php"); ?>
    <!-- Only unique css classes -->

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

    .modal-footer {

        display: flex;
        padding: 0rem !important;
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
    </style>

    <!-- Only unique css classes -->
</head>


<body class="horizontal-layout horizontal-menu 2-columns" data-open="hover" data-menu="horizontal-menu"
    data-color="bg-gradient-x-purple-blue" data-col="2-columns">
    <?php include_once("../../admin_assets/common-header.php"); ?>
    <div class="app-content content">
        <!-- <div class="row"> -->
            <div class="content-header">
                <?php include_once 'header.php'; ?>
            </div>
        <!-- </div> -->
        <div class="content-body">

            <section id="basic-form-layouts">

                <div class="row match-height">
                    <div class="col-md-1">
                    </div>
                  <div class="col-md-10">
    <div class="card" id="custom_card_height">
        <div class="card-body">
            <div class="row">
                <!-- Left Section: Custom Heading -->
                <div class="col-md-6">
                    <h4 class="card-title">Add Custom Heading Title For Challenge 2:</h4>
                    <textarea id="title2" class="form-control" maxlength="150" required><?php echo $data["landing_page_title2"]; ?></textarea>
                    <p class="text-muted mt-2">
                        <code>* This title will reflect on the Challenge 2 of game page<br>
                       * Character limit of text is 150</code>
                    </p>
                </div>

                <div class="col-md-6">
                    <h4 class="card-title">Total Questions</h4>
                    <div class="marks-container mb-2">
                        <!-- <span>Total Questions</span> -->
                        <input type="number" id="questions" class="form-control" min="1" max="10"
                            value="<?php echo $data["questions_02"]; ?>" />
                    </div>
                    <p class="text-muted">
                        <code>* Total number of questions to be displayed</code>
                    </p>
                </div>
            </div>
            <div class="row ">
                <div class="col-12 text-center">
                    <button class="btn btn-md btn-success login-button-all core-button-color"
                        id="update_marks" type="submit" name="submit">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>


                    <!-- Left side: Table Section -->

                    <div class="col-md-12">
                        <div class="card" id="custom_card_height">
                            <div class="card-content collapse show">
                                <div class="card-body">
                                    <!-- Button Actions -->
                                    <div class="mb-3">
                                        <button class="btn btn-md btn-info action-buttons" id="add_question">Add
                                            Question</button>
                                        <button class="btn btn-md btn-warning action-buttons" id="upload_csv">Upload
                                            CSV</button>
                                        <a href="getcsv-admin.php?name=<?php echo $curruntTheme; ?>">
                                            <button class="btn btn-md btn-danger action-buttons">Export CSV</button>
                                        </a>
                                        <a href="questions.csv">
                                            <button class="btn btn-md btn-success action-buttons">Sample
                                                CSV</button>
                                        </a>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="example"
                                            class="table table-striped table-bordered zero-configuration">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Question Name</th>
                                                    <th>Edit</th>
                                                    <th>Delete</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                            // Fetch and display questions from the database
                                            $sql = "SELECT * FROM questions WHERE sessionId='$sessionId' AND themename ='$curruntTheme' AND organizationId='$organizationId'";
                                            $sql = execute_query($sql);
                                            $tableCount = 0;
                                            while ($get = mysqli_fetch_array($sql)) {
                                                $deleteButton = '<button class="btn btn-sm btn-danger delete-button" deleteLink="' . PAGE_NAME . '?delete_id=' . $get["id"] . '&delete_table=questions&fallback=' . PAGE_NAME . '?name=' . $curruntTheme . '">Delete</button>';
                                                $tableCount++;
                                                
                                                $editButton = '<button class="btn btn-sm btn-danger edit-question" pos="' . $get["id"] . '" question_name="' . $get["question_name"] . '" category="' . $get["category"] . '" style="background:black;">Edit</button>';
                                        ?>
                                                <tr>
                                                    <td><?php echo $tableCount; ?></td>
                                                    <td class="data_question"><?php echo $get["question_name"]; ?>
                                                    </td>
                                                    <td><?php echo $editButton; ?></td>
                                                    <td><?php echo $deleteButton; ?></td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right side: Other Content Section -->


                </div>
            </section>
        </div>
        <!-- add content here end -->
    </div>
    </div>
    </section>
    </div>

    <!-- The Modal -->
    <div class="modal" id="myModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header text-center">
                    <h4 class="modal-title question-main-title">Add Question</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <div class="col-md-12">
                        <form id="myform">
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group ">
                                            <label class="col-md-12 label-control" for="projectinput5">Question</label>
                                            <div class="col-md-12">
                                                <textarea type="text" id="projectinput5" class="form-control form-data"
                                                    placeholder="Question" name="email" maxlength="350"></textarea>
                                                <p style="width:100%;text-align:right;">Max length 350 characters</p>
                                            </div>
                                        </div>


                                        <button class="btn btn-success add-question" type="submit">
                                            <i class="ft-upload-cloud"></i> Add Question
                                        </button>

                                    </div>
                                </div>


                        </form>
                    </div>

                </div>

                <!-- Modal footer -->


            </div>
        </div>
    </div>
    </div>






    <div class="modal" id="uploadCSV">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header text-center">
                    <h4 class="modal-title">Upload CSV</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <div class="col-md-12">
                        <form id="myformTwo">
                            <fieldset class="form-group">
                                <div class="custom-file">
                                    <input type="file" id="fileTwo" class="custom-file-input" id="inputGroupFile01"
                                        required>
                                    <label class="custom-file-label" for="inputGroupFile01">Choose file</label>
                                </div>
                                <p class="text-muted mt-2">
                                    <code>* Questions will be overwritten.<br>* Use only CSV files.<br>* Whitespace will be removed automatically. <br>
                                    * When uploading the CSV, the upload format should have only 2 or 4 options for the answers.<br>
                                    * Before uploading, check the sample CSV file for the correct format.     </code>
                                </p>

                              <div class="progress" style="display:none; margin-top:10px;">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0"
                                        aria-valuemax="100" style="width:70%">
                                        0%
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <button class="btn btn-success em-color" type="submit">
                                        <i class="ft-upload-cloud"></i> Upload
                                    </button>
                                </div>
                            </fieldset>
                        </form>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>
    </div>




    <?php include("../../admin_assets/footer.php"); ?>
    <?php include_once("../../admin_assets/common-js.php"); ?>
    <script type="text/javascript">
    // document.getElementById("backButton").addEventListener("click", function(event) {
    //     event.preventDefault(); // Prevent the default anchor behavior
    //     history.back();
    // });
    // document.getElementById("backButton").addEventListener("click", function(event) {
    //     event.preventDefault();
    //     history.back();
    // });
    var action = null;
    var edit_id = null;
    var setid = "<?php echo $curruntTheme; ?>";
    var currentColumn = null;
    var themeName = "<?php echo $curruntTheme; ?>";



    function setColumns(value) {
        currentColumn = (value == 0) ? 4 : 2;
        $(".option-buttons").css({
            "background": "#7f7f7f",
            "color": "white"
        });
        $(".option-buttons").eq(value).css({
            "background": "black",
            "color": "white"
        });
        if (currentColumn == 2) {
            $(".option-data").eq(2).hide();
            $(".option-data").eq(3).hide();
        } else {
            $(".option-data").show();
        }
    }
    var selectedoptionsno;
    $(".option-buttons").click(function() {
        var pos = $(this).attr("pos");
        selectedoptionsno = pos;
        setColumns(pos);
        $(".option-data > input").css({
            "background": "white",
            "color": "black"
        });
        correctAnswer = "";
    });

    setColumns(0);

    $("#add_question").click(function() {
        $(".question-main-title").html("New Question");
        $("#myModal").modal("show");
        action = "add";
        $(".add-question").html('<i class="ft-upload-cloud"></i> Add New Question');
    });

    $("#upload_csv").click(function() {
        $("#uploadCSV").modal("show");
    });






    var procced = false;


    $('#myModal').on('hidden.bs.modal', function() {
        $(".option-data > input").css({
            "background": "white",
            "color": "black"
        });
        $(".form-data").prop("disabled", false);
        $(".form-data").val("");
        $(".file-upload").show();
        $(".add-question").hide();
        procced = false;
        $(".next-question").show();
    })


    $(".edit-question").click(function() {
        $(".question-main-title").html("Edit Question");
        action = "edit";
        $("#myModal").modal("show");
        edit_id = $(this).attr("pos");
        var answer_id = $(this).attr("answer");
        correctAnswer = answer_id;

        var q = $(this).attr("question_name"); // Get the question text
        // var category = $(this).attr("category"); // Get the category

        // Populate the question and category fields
        $(".form-data").eq(0).val(q); // Set the question
        // $(".form-data").eq(1).val(category); // Set the category
        $(".file-upload").show();
        $(".add-question").show();
        answerString = $(".form-data").eq(answer_id).val();
        procced = true;

        $(".option-data > input").eq(answer_id - 1).css({
            "background": "green",
            "color": "white"
        });
        $(".add-question").html('<i class="ft-upload-cloud"></i> Update');
    });




    myform.addEventListener('submit', function(e) {
        e.preventDefault();
        var errorFlag = false;
        var errorMsg = "";

        var question = document.querySelector("#projectinput5").value.trim();
        var formData = new FormData();


        if (!question) {
            errorFlag = true;
            errorMsg = "Question cannot be empty!";
        }

        if (!errorFlag) {
            formData.append('question', question);
            formData.append('action', action);
            formData.append('edit_id', edit_id);

            var request = new XMLHttpRequest();
            request.open('POST', 'add-question-upload.php', true);

            request.onreadystatechange = function() {
                if (request.readyState == 4 && request.status == 200) {
                    console.log(request.responseText);
                    if (request.responseText.trim() === "1010") {
                        swal({
                            position: 'center',
                            type: 'success',
                            title: 'Question Added Successfully!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        swal(request.responseText, "", "error");
                    }
                }
            };
            request.send(formData);
        } else {
            swal(errorMsg, "", "error");
        }
    }, false);
    var totalQuestions = <?php echo $total_q; ?>;
    //  console.log("questions total--q-----", totalQuestions);

    document.getElementById('questions').addEventListener('input', function(e) {
        if (this.value < 1) {
            this.value = 1; // Reset to 0 if a negative number is typed
        }
    });
    $("#update_marks").click(function() {
        // let questions = $("#questions").val();
        // alert(rows);
   var title2 = $("#title2").val();
        let errorFlag = false;
        let errorMsg = "";
        console.log(title2.length);
        if (title2.trim().length === 0 || title2.length > 150) {
            errorFlag = true;
            errorMsg = title2.trim().length === 0 ?
                "The title cannot be empty or contain only spaces." :
                "You exceed the maximum limit of the title.";
        }

        let questions = parseInt($("#questions").val());
       
        if (questions > totalQuestions) {
            errorFlag = true;
            errorMsg = "You cannot enter more than " + totalQuestions + " questions.";
        }

        if (errorFlag) {
            Swal.fire({
                icon: 'error',
                title: 'Limit Exceed',
                text: errorMsg
            }).then(() => {
                location.reload();
            });
            return; // Stop further execution

        }
        $.ajax({
            type: "POST",
            url: "events.php?events=add_points_02",
            data: {
                "questions": questions,
                "title2" : title2
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
    })


    $(".delete-button").click(function() {
        var deletelink = $(this).attr("deletelink");
        let result = confirm("Are you sure you want to delete this item?");
        if (result) {
            location.href = deletelink;
        }
    });


    $('.rules-repeater').repeater({
        show: function() {
            if ($(this).parents(".rules-repeater").find("div[data-repeater-item]").length <= 3) {
                $(this).slideDown();
            } else {
                $(this).remove();
            }
        }
    });

    var rules = [];

    function getValues() {
        rules = [];
        $('input[name*="rules"]').each(function(e) {
            rules.push($(this).val());
        });
    }



    var myform_two = document.querySelector('#myformTwo');
    var inputfile_two = document.querySelector('#fileTwo');
var bar_two = document.getElementsByClassName("progress-bar")[0];
    var progress_two = document.getElementsByClassName("progress")[0];
    var request_two = new XMLHttpRequest();
    request_two.upload.addEventListener('progress', function(e) {
        bar_two.style.width = Math.round(e.loaded / e.total * 100) + '%';
        bar_two.innerHTML = Math.round(e.loaded / e.total * 100) + '% please wait..';
        if (Math.round(e.loaded / e.total * 100) == 100) {
            bar_two.style.width = Math.round(e.loaded / e.total * 100) + '%';
            bar_two.innerHTML = Math.round(e.loaded / e.total * 100) + '% please wait..';
        }
    }, false);

    myform_two.addEventListener('submit', function(e) {
        e.preventDefault();
 progress_two.style.display = "block";
        $(".submit-button").css("display", "none");

        var formData = new FormData();
         formData.append('file', inputfile_two.files[0]);
        request_two.open('post', 'process.php', true);
        request_two.onreadystatechange = function() {
            if (request_two.readyState == 4 && request_two.status == 200) {
                console.log(request_two.responseText);
                if (request_two.responseText == "1010") {
progress_two.style.display = "none";
                    swal("SuccessFully Updated", "SuccessFully Updated All Fields", "success");
                    setTimeout(function() {
                        location.reload();
                    }, 1000)
                } else {
                    swal("Error", request_two.responseText, "error");
                    setTimeout(function() {
                        //location.href="all-categories.php";
                    }, 1000)
                }
                //clearInterval(setID);
            }
        }
        request_two.send(formData);

    }, false);
    </script>
</body>

</html>