<?php
session_start();
error_reporting(0);
$settings = json_decode(file_get_contents("settings.js"), true)[0];
$organizationId = $_SESSION['organizationId'];
$sessionId = $_SESSION['sessionId'];
// echo $organizationId;
// echo $sessionId;
include_once '../dao/config.php';
include_once '../../admin_assets/triggers.php';

require '../../vendor/autoload.php'; // Include the Composer autoload file
include_once "../s3/s3_functions.php"; // s3 function for easy upload and get signed urls, still need env file and autoload

$curruntTheme = $_GET["name"];
$_SESSION['themename'] = $curruntTheme;
include_once 'themes/processThemes.php';
include_once 'themes/themeTools.php';

$_SESSION["gameTitle"] = $settings["gameName"];
mysqli_set_charset($con, 'utf8');
$settings["pageLinks"] = [
  "Back" => "themeUpdate.php?name=" . $curruntTheme
];


$tabName = "Settings";
$voting = toogles("voting");
$uploads = toogles("uploads");

$multiple_entry = toogles("multiple_entry");

$default_cat = default_data("default_cat");
$custom_cat = default_data("custom_cat");
$title = default_data("title");
$logo = default_data("logo");
$getRows = default_data("rows");
$getCols = default_data("cols");
$time = default_data("time");

$data = fetchThemeData();
// print_r($data);
$rules = unserialize($data["rules"]);
// print_r($rules);

list($minutes, $seconds) = explode(":", $data["timer"]);

$puzzle_img = $data['puzzle_image'];
$puzzle_split  = possibleOnS3("uploads/",$puzzle_img);

// echo $puzzle_img;

// Unserialize the stored array
$img_array = unserialize($data["img_array"]);

// if (!empty($img_array) && is_array($img_array)) {
//     echo '<div style="display: flex; flex-wrap: wrap; gap: 20px;">'; // container with spacing
//     foreach ($img_array as $img) {
//         echo '<div style="flex: 0 0 auto; border: 1px solid #ccc; padding: 10px;">';
//         echo '<img class="img-fluid" src="' . possibleOnS3("uploads/", $img) . '" alt="Image" style="max-width: 100%; max-height: 300px;" />';
//         echo '</div>';
//     }
//     echo '</div>';
// } else {
//     echo "No images found.";
// }



// $img_array = $data['img_array'];
// $img_array_split = possibleOnS3("uploads/",$img_array);

// echo $img_array_split;

?>

<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

<head>

    <?php include_once("../../admin_assets/common-css.php"); ?>

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
        <div class="content-wrapper">
            <div class="content-header">
                <?php include_once 'header.php'; ?>
            </div>
            <div class="content-body">
                <section id="basic-form-layouts">
                    <div class="row match-height">
                        <!-- add content here -->
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-10">
                            <div class="card" id="custom_card_height">

                                <div class="card-content collapse show">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12 mb-2">
                                                <h4 class="card-title">Add Custom Title:</h4>
                                                <input type="text" id="title4" maxlength="150"
                                                    value="<?php echo $data["landing_page_title4"]; ?>"
                                                    class="form-control" id="basicInput">

                                                <p class="text-muted">
                                                    <code>* This title will reflect on the Challenege 4 of game page</code>
                                                    <code>* Character limit of the text is 150 characters.</code>
                                                </p>

                                            </div>
                                            <div class="col-md-12 text-center mb-2">
                                                <button class="btn btn-md btn-success  core-button-color"
                                                    style="margin-top:15px;" id="add_all_rules">Save</button>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>

            <div class="col-md-12">
                <div class="card" id="custom_card_height">
                    <?php cardHeader("Add Jigsaw Image"); ?>

                    <div class="card-content collapse show">
                        <div class="card-body">
                            <div class="col-md-12">
                                <div class="row">
                                    <!-- Upload Form -->
                                    <div class="col-md-6 mb-2">
                                        <form id="logo_upload">
                                            <fieldset class="form-group">
                                                <div class="custom-file mb-2">
                                                    <input type="file" id="file_logo" class="custom-file-input"
                                                        required>
                                                    <label class="custom-file-label" for="file_logo">Choose
                                                        file</label>
                                                </div>

                                                <p class="text-muted">
                                                    <code>*  Allowed Photo Formats - JPG, JPEG,
                                                                    PNG</code><code>*  Allowed Dimensions - 1920x1080</code>
                                                </p>

                                                <div class="col-md-12 text-center mb-2">
                                                    <button class="btn btn-success em-color logo-button" type="submit">
                                                        <i class="ft-upload-cloud"></i> Upload
                                                    </button>
                                                </div>
                                            </fieldset>
                                        </form>
                                        <?php cardHeader("Select Grid Size"); ?>
                                        <div class="row">
                                            <div class="col-md-12 ">
                                                <div class="row-container">
                                                    <span class="">Row</span>
                                                    <input type="number" id="row" min="2" max="12"
                                                        value="<?php echo $data["row"]; ?>" />
                                                </div>
                                                <div class="row-container">
                                                    <span class="">Column</span>
                                                    <input type="number" id="col" min="2" max="12"
                                                        value="<?php echo $data["col"]; ?>" />
                                                </div>
                                                <div class="row-container">
                                                    <span class="">Image count</span>
                                                    <input type="number" id="img_count" min="2" max="8"
                                                        value="<?php echo $data["img_count"]; ?>" />
                                                </div>
                                                <div class="col-md-12 text-center mb-2">
                                                    <button
                                                        class="btn btn-md btn-success login-button-all core-button-color"
                                                        style="margin-top:15px;" id="update_grid" type="submit"
                                                        name="submit">Save</button>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <!-- Image Preview -->
                                    <div
                                        class="col-md-6 mb-2 text-center d-flex align-items-center justify-content-center">
                                        <img class="img-fluid"
                                            src="<?php echo possibleOnS3('../uploads/', $data['puzzle_image']); ?>"
                                            alt="Uploaded Logo Preview" style="max-width: 100%; max-height: 300px;" />


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


    </section>

    <?php include("../../admin_assets/footer.php"); ?>
    <?php include_once("../../admin_assets/common-js.php"); ?>
    <script type="text/javascript">
    var checkbol = false;
    $(document).ready(function() {


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



    async function validateImageDimensions(file, maxWidth, maxHeight) {
        return new Promise((resolve, reject) => {
            const img = new Image();

            img.onload = function() {
                if (this.width === maxWidth && this.height === maxHeight) {
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



    var myform = document.querySelector('#logo_upload');
    var inputfile_logo = document.querySelector('#file_logo');
    var request = new XMLHttpRequest();
    myform.addEventListener('submit', function(e) {
        e.preventDefault();
        var formData = new FormData();
        formData.append('file', inputfile_logo.files[0]);
        var current_cat = $("#select-role").val();
        request.open('post', 'upload-images.php?&events=upload_logo', true);
        var errorFlag = false;
        var checkUrl = getFileType(inputfile_logo.files[0]);
        var validExt = ["jpg", "jpeg", "png"];
        var extension = inputfile_logo.files[0].name.split('.').pop();
        var extension = extension.toLowerCase();
        var extensionAllowed = false;
        var maxfileLimit = 5; // in MB
        var fileSize = inputfile_logo.files[0].size;
        var fileSize = Math.round((fileSize / 1024));
        if (fileSize > (maxfileLimit * 1024)) {
            errorFlag = true;
            errorMsg = "Filesize exceeed max limit " + maxfileLimit + " Mb";
        }
        if (validExt.indexOf(extension) > -1) {
            extensionAllowed = true;
        }
        if (!extensionAllowed) {
            errorFlag = true;
            errorMsg = "Unsupported file format";
        }
        if (checkUrl != "image") {
            errorFlag = true;
            errorMsg = "only image file allowed.";
        }
        // $(".logo-button").hide();

        if (errorFlag) {
            swal({
                title: errorMsg,
                background: '#fff url(img/wrong.png)'
            });
        } else {
            validateImageDimensions(inputfile_logo.files[0], 1920, 1080)
                .then(isValid => {
                    if (isValid) {
                        console.log('Image height is valid.');
                    } else {
                        errorFlag = true;
                        errorMsg = "Image dimension must be 1920x1080";
                        console.log('Image height exceeds the limit.');
                    }
                    console.log(errorFlag);
                    // console.log(errorMsg);
                    console.log(errorFlag);
                    if (!errorFlag) {
                        request.onreadystatechange = function() {
                            if (request.readyState == 4 && request.status == 200) {

                                console.log(request.responseText);
                                if (request.responseText == "true") {
                                    //swal("SuccessFully Updated", "", "success");
                                    swal({
                                        title: 'Successfully Updated',
                                        background: '#fff url(img/correct.png)'
                                    });
                                    setTimeout(function() {
                                        location.reload();
                                    }, 1000)
                                } else {

                                    swal({
                                        title: request.responseText,
                                        background: '#fff url(img/wrong.png)'
                                    });
                                    //swal("Error", request.responseText, "error");
                                    $(".logo-button").show();
                                }
                            }
                        }
                        request.send(formData);
                    } else {
                        swal({
                            title: errorMsg,
                            background: '#fff url(img/wrong.png)'
                        });
                        //swal(errorMsg,"","error");
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    }, false);

    $("#add_all_rules").click(function() {

        var title4 = $("#title4").val();

        let errorFlag = false;
        let errorMsg = "";

        if (title4.length > 150) {
            errorFlag = true;
            errorMsg = "You exceed the maximum limit! ";
        }


        if (!errorFlag) {
            $.ajax({
                type: "POST",
                url: "events.php?events=add_rules_for_challenege4",
                data: {
                    "title4": title4,
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
        }
    })

    var puzzle_img = "<?php echo isset($puzzle_img) ? $puzzle_img : ''; ?>";
    console.log("this is main img", puzzle_img);

    // Verify image exists before allowing updates
    if (!puzzle_img) {
        console.error("No puzzle image defined");
        $("#update_grid").prop("disabled", true);
    }

    $("#update_grid").click(function() {
        let rows = $("#row").val();
        let cols = $("#col").val();
        let img_count = parseInt($("#img_count").val(), 10);

        // ✅ Validate img_count: allow 1 or even numbers up to 8
        if (isNaN(img_count) || (img_count !== 1 && (img_count % 2 !== 0 || img_count > 8))) {
            swal({
                title: "Invalid Image Count",
                text: "Please enter either 1 or an even number up to 8.",
                icon: "error",
                background: '#fff url(img/wrong.png)'
            });
            return; // ❌ Stop further execution
        }
        // Add loading indicator
        $(this).prop("disabled", true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');

        $.ajax({
            type: "POST",
            url: "events.php?events=add_grid",
            data: {
                "rows": rows,
                "cols": cols,
                "img_count": img_count
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
                    $("#update_grid").prop("disabled", false).text('Save');
                }
            },
            error: function(xhr, status, error) {
                swal({
                    title: "Error: " + error,
                    background: '#fff url(img/wrong.png)'
                });
                $("#update_grid").prop("disabled", false).text('Save');
            }
        });
    });


    $("#update_marks").click(function() {
        let marks = $("#marks").val();
        // alert(rows);
        $.ajax({
            type: "POST",
            url: "events.php?events=add_points",
            data: {
                "marks": marks
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

    let finalrows = 3;
    let finalcols = 3;


    document.addEventListener('DOMContentLoaded', function() {
        const gridContainer = document.getElementById('grid-container');
        const output = document.getElementById('output');
        const resetButton = document.getElementById('reset-button');

        const rows = 6;
        const cols = 6;
        let isFinalized = false; // Flag to track if the selection is finalized

        // Generate grid
        for (let i = 0; i < rows; i++) {
            for (let j = 0; j < cols; j++) {
                const cell = document.createElement('div');
                cell.dataset.row = i;
                cell.dataset.col = j;
                gridContainer.appendChild(cell);
            }
        }

        const startRow = 0;
        const startCol = 0;
        let selectedRows = 0;
        let selectedCols = 0;

        function highlightGrid(endRow, endCol) {
            clearHighlight();
            selectedRows = endRow - startRow + 1;
            selectedCols = endCol - startCol + 1;
            output.textContent = `${selectedRows}x${selectedCols}`;
            for (let i = startRow; i <= endRow; i++) {
                for (let j = startCol; j <= endCol; j++) {
                    const cell = document.querySelector(`div[data-row='${i}'][data-col='${j}']`);
                    if (cell) {
                        cell.classList.add('highlighted');
                    }
                }
            }
        }

        function clearHighlight() {
            document.querySelectorAll('.highlighted').forEach(cell => {
                cell.classList.remove('highlighted');
            });
        }

        gridContainer.addEventListener('mouseover', (event) => {
            if (isFinalized) return; // Prevent changes if selection is finalized
            if (event.target.dataset.row && event.target.dataset.col) {
                const endRow = parseInt(event.target.dataset.row);
                const endCol = parseInt(event.target.dataset.col);
                highlightGrid(endRow, endCol);
            }
        });

        gridContainer.addEventListener('click', () => {
            if (selectedRows > 0 && selectedCols > 0 && !isFinalized) {
                finalrows = selectedRows;
                finalcols = selectedCols;
                isFinalized = true; // Finalize the selection
            }
        });

        resetButton.addEventListener('click', () => {
            clearHighlight();
            output.textContent = '';
            isFinalized = false; // Reset the finalized flag
        });

        // Highlight 4x4 grid by default
        highlightGrid(<?php echo $data["row"] - 1; ?>, <?php echo $data["col"] - 1; ?>);
    });
    </script>
</body>

</html>