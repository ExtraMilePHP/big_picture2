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

include_once 'themes/processThemes.php';    
include_once 'themes/themeTools.php';



require '../../vendor/autoload.php'; // Include the Composer autoload file
include_once "../s3/s3_functions.php"; // s3 function for easy upload and get signed urls, still need env file and autoload


$_SESSION["gameTitle"]=$settings["gameName"];
mysqli_set_charset( $con, 'utf8');
// echo $_SESSION['adminId']."admin_id";
// if (!$_SESSION['adminId']) {
//     header('Location:../index.php');
// } 

// $tabName="Themes";




$tabName=($_SESSION["sessionId"]=="admin")?"Themes ( Superadmin )":"Themes";
$voting=toogles("voting");
$uploads=toogles("uploads");

$multiple_entry=toogles("multiple_entry");

$default_cat=default_data("default_cat");
$custom_cat=default_data("custom_cat");
$title=default_data("title");
$logo=default_data("logo");
$getRows=default_data("rows");
$getCols=default_data("cols");
$time=default_data("time");

list($minutes, $seconds) = explode(":", $time);

$allThemes=fetchAllThemes();
// print_r(json_encode($allThemes));
?>

<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

<head>

    <?php include_once("../../admin_assets/common-css.php");?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="stylesheet" type="text/css" href="themes/themes.css" />
    <!-- Only unique css classes -->
    <style rel="stylesheet" type="text/css">

    </style>
    <!-- Only unique css classes -->
</head>

<body class="horizontal-layout horizontal-menu 2-columns" data-open="hover" data-menu="horizontal-menu"
    data-color="bg-gradient-x-purple-blue" data-col="2-columns">
    <?php include_once("../../admin_assets/common-header.php");?>
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-wrapper-before"></div>
            <div class="content-header row">
                <div class="content-header-left col-md-4 col-12 mb-2">
                    <h3 class="content-header-title"><?php echo $tabName;?></h3>
                </div>
            </div>
            <div class="content-body">
                <section id="basic-form-layouts">
                    <div class="row match-height">
                        <!-- add content here -->

                        <div class="col-md-12">
                            <div class="card" id="custom_card_height">
                                <?php cardHeader("Custom title");?>
                                <div class="card-content collapse show">
                                    <div class="card-body">

                                        <div class="theme-container">
                                            <div class="select-theme-grid">


                                                <?php 
                                          for($i=0; $i<sizeof($allThemes); $i++){
                                            $fetchTheme=$allThemes[$i];
                                            
                                            // echo $fetchTheme["themeImage"];
                                            $themeImage=possibleOnS3("../uploads/",$fetchTheme["themeImage"]);

                                            $select=($fetchTheme["selected"]=="true")?'<div class="theme-select"><i class="fa-solid fa-check"></i></div>':"";
                                            echo '<div class="theme-card" name="'.$fetchTheme["themename"].'">
                                            <img src="'.$themeImage.'" class="theme-image"/>
                                            '.$select.'
                                             <div class="icon-container">
                                             <div class="theme-delete" theme='.$fetchTheme["themename"].' >
                                                <i class="fa-solid fa-trash"></i>
                                              </div>
                                             <div class="photo-icon" redirect="themeMedia.php?&name='.$fetchTheme["themename"].'"><i class="fa-regular fa-image"></i></div>
                                             <div class="setting-icon" redirect="themeUpdate.php?&name='.$fetchTheme["themename"].'">
                                                <i class="fa-solid fa-gear"></i>
                                              </div>
                                             </div>
                                            </div>';
                                          }
                                        ?>
                                                <div class="theme-card">
                                                    <div class="create-theme">
                                                        <div class="create-icon">
                                                            <i class="fa-solid fa-plus"></i>
                                                        </div>
                                                        Create Theme
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="build-new-theme" style="display:none;">
                                                <div class="title">Create New Theme</div>
                                                <form id="logo_upload">
                                                    <div class="file-upload-wrapper">
                                                        <input type="file" class="file-upload-input" id="file_logo1">
                                                        <label for="fileUpload" class="file-upload-button">
                                                            <span class="file-upload-text">Theme icon (200x300) </span>
                                                            <i class="fas fa-upload"></i>
                                                        </label>
                                                        <div class="tooltip-container">
                                                            <i class="fas fa-info-circle info-icon"
                                                                aria-hidden="true"></i>
                                                            <div class="tooltip">Required Dimensions: 200x300</div>
                                                        </div>
                                                    </div>
                                                    <div class="file-upload-wrapper">
                                                        <input type="file" class="file-upload-input" id="file_logo2">
                                                        <label for="fileUpload" class="file-upload-button">
                                                            <span class="file-upload-text">Upload Logo (400x300)</span>
                                                            <i class="fas fa-upload"></i>
                                                        </label>
                                                        <div class="tooltip-container">
                                                            <i class="fas fa-info-circle info-icon"
                                                                aria-hidden="true"></i>
                                                            <div class="tooltip">Required Dimensions: 400x300</div>
                                                        </div>
                                                    </div>

                                                    <div class="file-upload-wrapper">
                                                        <input type="file" class="file-upload-input" id="file_logo3">
                                                        <label for="fileUpload" class="file-upload-button">
                                                            <span class="file-upload-text">Desktop Image (1920 x1080)
                                                            </span>
                                                            <i class="fas fa-upload"></i>
                                                        </label>
                                                        <div class="tooltip-container">
                                                            <i class="fas fa-info-circle info-icon"
                                                                aria-hidden="true"></i>
                                                            <div class="tooltip">Required Dimensions: 1920 x1080</div>
                                                        </div>
                                                    </div>

                                                    <div class="file-upload-wrapper">
                                                        <input type="file" class="file-upload-input" id="file_logo4">
                                                        <label for="fileUpload" class="file-upload-button">
                                                            <span class="file-upload-text">Mobile Image (414 x
                                                                896)</span>
                                                            <i class="fas fa-upload"></i>
                                                        </label>
                                                        <div class="tooltip-container">
                                                            <i class="fas fa-info-circle info-icon"
                                                                aria-hidden="true"></i>
                                                            <div class="tooltip">Required Dimensions: 414 x 896</div>
                                                        </div>
                                                    </div>
                                                    <button class="button-create-theme" type="submit">Create
                                                        Theme</button>
                                                </form>
                                            </div>
                                        </div>
                                        <?php
                    if ($_SESSION["onboarding"]) { ?>
                        <div class="text-muted">
                        <p class="text-muted "><br>
                                                <code>* Select one theme from the available options </code></p>
                                            <p><code> * You can create a new theme by clicking "Create Theme" and uploading all the necessary files. </code></p>
                                            <p><code> * After completing all necessary steps, click "Onboarding" to proceed further.</code></p>
                        </div>
                    <?php   } ?>
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

    <?php include("../../admin_assets/footer.php");?>
    <?php include_once("../../admin_assets/common-js.php");?>
    <script type="text/javascript" src="themes/themes.js"></script>
    <script type="text/javascript" src="fileUploadTools.js"></script>
    <script type="text/javascript">
    $(".theme-delete").click(function() {
        event.stopPropagation(); // Stops the event from bubbling up to the outer div
        let theme = $(this).attr("theme");
        swal({
            title: 'Are you sure you want to delete theme',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.value) {
                location.href = ("theme-delete.php?&themename=" + theme);
            } else if (result.dismiss === swal.DismissReason.cancel) {
                console.log("you cancelled");
            }
        });
    });

    $('#main-menu-navigation li').each(function() {
        console.log($(this).find('a span').text().trim());
        if ($(this).find('a span').text().trim() === "All Uploads") {
            console.log("triggering");
            setTimeout(() => {
                console.log(this);
                $(this).remove();
            }, 2000);
        }
    });

    var myform = document.querySelector('#logo_upload');
    var request = new XMLHttpRequest();

    myform.addEventListener('submit', function(e) {
        e.preventDefault();
        var formData = new FormData();
        var validExt = ["jpg", "jpeg", "png"];
        var maxfileLimit = 5; // in MB
        var errorFlag = false;
        var errorMsg = "";
        var fileInputs = [
            document.querySelector('#file_logo1'),
            document.querySelector('#file_logo2'),
            document.querySelector('#file_logo3'),
            document.querySelector('#file_logo4')
        ];

        var fileDimentions = [
            [200, 300],
            [400, 300],
            [1920, 1080],
            [414, 896]
        ];
        var fileInputName = ["Theme Icon", "logo", "Background Desktop", "Background Mobile"];

        async function getImageDimensions(file) {
            return new Promise((resolve, reject) => {
                const img = new Image();
                img.onload = () => resolve([img.width, img.height]);
                img.onerror = reject;
                img.src = URL.createObjectURL(file);
            });
        }


        async function processFiles(fileInputs) {
            const promises = fileInputs.map(async (inputfile_logo, index) => {
                console.log(inputfile_logo.files.length);
                if (inputfile_logo.files.length > 0) {
                    const file = inputfile_logo.files[0];
                    const checkUrl = getFileType(file);
                    const extension = file.name.split('.').pop().toLowerCase();
                    const fileSize = Math.round(file.size / 1024); // in KB

                    if (fileSize > maxfileLimit * 1024) {
                        errorFlag = true;
                        errorMsg = `Filesize exceeds max limit ${maxfileLimit} MB`;
                    }

                    if (validExt.indexOf(extension) === -1) {
                        errorFlag = true;
                        errorMsg = 'Unsupported file format';
                    }

                    if (checkUrl !== 'image') {
                        errorFlag = true;
                        errorMsg = 'Only image files are allowed.';
                    } else {
                        try {
                            const dimensions = await getImageDimensions(file);
                            const width = dimensions[0];
                            const height = dimensions[1];
                            console.log('File dimensions:', width, 'x', height);

                            // Perform your own dimension checks here
                            if (width === fileDimentions[index][0] && height === fileDimentions[
                                    index][1]) {
                                console.log('File dimensions are correct:', width, 'x', height);
                                // File dimensions are correct, proceed with the form submission or other logic
                            } else {
                                $(".tooltlip").eq(index).show();
                                console.log('File dimensions are incorrect:' + " for file " +
                                    index, width, 'x', height);
                                errorFlag = true; // make it true later
                                errorMsg = 'Incorrect Dimensions of ' + fileInputName[index];
                                console.log(index);
                                console.log(errorMsg);
                                // Clear the file input
                                inputfile_logo.value = '';
                            }
                        } catch (error) {
                            console.log(error.message);
                            alert(error.message);
                            // Clear the file input
                            inputfile_logo.value = '';
                            errorFlag = true; // make it true later
                            errorMsg = error.message;
                        }
                    }

                    if (!errorFlag) {
                        formData.append('file' + (index + 1), file);
                    } else {
                        console.error(`Error processing file ${file.name}: ${errorMsg}`);
                    }
                } else {
                    errorFlag = true; // make it true later
                    errorMsg = 'All files are required';
                }

                return {
                    errorFlag,
                    errorMsg
                }; // Return error info to be used later
            });

            const results = await Promise.all(promises);
            const hasError = results.some(result => result.errorFlag);
            console.log(hasError);
        }
        // Call processFiles with your fileInputs array
        processFiles(fileInputs).then(() => {
            console.log(errorFlag);
            console.log('All files processed.');
            if (!errorFlag) {
                request.open('post', 'upload-create-theme.php?&events=upload_logo', true);
                request.onreadystatechange = function() {
                    if (request.readyState == 4 && request.status == 200) {
                        if (request.responseText == "true") {
                            swal({
                                title: 'Successfully Updated',
                                background: '#fff url(img/correct.png)'
                            });
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        } else {
                            console.log(request.responseText);
                            let response = JSON.parse(request.responseText);
                            swal({
                                title: response.error,
                                background: '#fff url(img/wrong.png)'
                            });
                            // Show button again
                            $(".logo-button").show();
                        }
                    }
                };
                request.send(formData);
            } else {
                swal({
                    title: errorMsg,
                    background: '#fff url(img/wrong.png)'
                });
            }
        });





    }, false);

    $(".theme-card").click(function(event) {
        if ($(this).attr('name') !== undefined) {
            let name = $(this).attr('name');
            $.ajax({
                type: "POST",
                url: "events.php?events=select_theme",
                data: {
                    "name": name
                },
                success: function(result) {
                    if (result == "true") {
                        swal({
                            title: 'Theme Selected',
                            background: '#fff url(img/correct.png)'
                        });
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        swal({
                            title: result,
                            background: '#fff url(img/wrong.png)'
                        });
                    }
                }
            });
        }
    });

    $(".setting-icon").click(function() {
        event.stopPropagation(); // Stops the event from bubbling up to the outer div
        let redirect = $(this).attr("redirect");
        location.href = (redirect);
    })

    $(".photo-icon").click(function() {
        event.stopPropagation(); // Stops the event from bubbling up to the outer div
        let redirect = $(this).attr("redirect");
        location.href = (redirect);
    })
    </script>
</body>

</html>