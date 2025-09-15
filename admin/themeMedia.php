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

$tabName=($_SESSION["sessionId"]=="admin")?"Settings ( Superadmin )":"Settings";

$data=fetchThemeData();






?>

<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

<head>

    <?php include_once("../../admin_assets/common-css.php");?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="stylesheet" type="text/css" href="themes/themes.css" />
    <!-- Only unique css classes -->
    <style rel="stylesheet" type="text/css">
        html body .content .content-wrapper {
    padding: 1.2rem !important;
}
.mob-bg {
    height: 204px;
    width: 150px;
}
.card .card-title {
    font-size: 1rem;
    font-weight: 700;
    letter-spacing: 0.05rem;
    font-family: 'FiraSans-Medium';
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
                    <div class="row match-height">
                        <!-- add content here -->


                        <div class="col-md-12">
                            <div class="card" id="custom_card_height">
                                <!-- <?php cardHeader("Update images");?> -->
                                <div class="card-content collapse show">
                                    <div class="card-body">
                                        <div class="col-md-12">
                                            <form id="logo_upload">
                                                <div class="container">
                                                    <div class="row">
                                                        <!-- Left Column: Theme Thumbnail and Logo -->
                                                        <div class="col-md-6">
                                                            <h4 class="card-title" style="margin-bottom:0px">Add Theme Thumbnail (200x300) and Logo (400x300)</h4>
                                                            <div class="image-container type-vertical">
                                                                <div class="overlay">
                                                                    <label for="file_logo1" class="upload-button">Upload
                                                                        Image</label>
                                                                    <input type="file" id="file_logo1" />
                                                                </div>
                                                                <img src="<?php echo possibleOnS3('../uploads/', $data['themeImage']); ?>"
                                                                    class="container-background-image" />
                                                                <div class="tooltip-container-update">
                                                                    <i class="fas fa-info-circle info-icon-update"
                                                                        aria-hidden="true"></i>
                                                                    <div class="tooltip-update">Required Dimensions:
                                                                        200x300</div>
                                                                </div>
                                                            </div>
                                                            <div class="image-container type-square">
                                                                <div class="overlay">
                                                                    <label for="file_logo2" class="upload-button">Upload
                                                                        Image</label>
                                                                    <input type="file" id="file_logo2" />
                                                                </div>
                                                                <img src="<?php echo possibleOnS3('../uploads/', $data['logo']); ?>"
                                                                    class="container-background-image" />
                                                                <div class="tooltip-container-update">
                                                                    <i class="fas fa-info-circle info-icon-update"
                                                                        aria-hidden="true"></i>
                                                                    <div class="tooltip-update">Required Dimensions:
                                                                        400x300</div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Right Column: Background Images of Mobile and Desktop -->
                                                        <div class="col-md-6">
                                                            <h4 class="card-title" style="margin-bottom:0px">Add Mobile Background (414x896) and Desktop Background (1920x1080)</h4>
                                                            <div class="image-container type-vertical mob-bg"
                                                               >
                                                                <div class="overlay">
                                                                    <label for="file_logo3" class="upload-button">Upload
                                                                        Image</label>
                                                                    <input type="file" id="file_logo3" />
                                                                </div>
                                                                <img src="<?php echo possibleOnS3('../uploads/', $data['background_mob']); ?>"
                                                                    class="container-background-image" />
                                                                <div class="tooltip-container-update">
                                                                    <i class="fas fa-info-circle info-icon-update"
                                                                        aria-hidden="true"></i>
                                                                    <div class="tooltip-update">Required Dimensions: 414
                                                                        x 896</div>
                                                                </div>
                                                            </div>
                                                            <div class="image-container type-horizontal">
                                                                <div class="overlay">
                                                                    <label for="file_logo4" class="upload-button">Upload
                                                                        Image</label>
                                                                    <input type="file" id="file_logo4" />
                                                                </div>
                                                                <img src="<?php echo possibleOnS3('../uploads/', $data['background_desk']); ?>"
                                                                    class="container-background-image" />
                                                                <div class="tooltip-container-update">
                                                                    <i class="fas fa-info-circle info-icon-update"
                                                                        aria-hidden="true"></i>
                                                                    <div class="tooltip-update">Required Dimensions:
                                                                        1920 x 1080</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Centered Update Button -->
                                                    <div class="row">
                                                        <div class="col-md-12 text-center">
                                                            <button class="button-create-theme btn-md btn-save" type="submit"
                                                                style="margin-top:15px;">Update Images</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>

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

    <?php include("../../admin_assets/footer.php");?>
    <?php include_once("../../admin_assets/common-js.php");?>
    <script type="text/javascript" src="themes/themes.js"></script>

    <script type="text/javascript">
    function getFileType(file) {
        if (file.type.match('image.*'))
            return 'image';
        if (file.type.match('video.*'))
            return 'video';
        return 'other';
    }


    function getImageDimensions(file) {
        return new Promise((resolve, reject) => {
            if (file && file.type.match('image.*')) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    var img = new Image();

                    img.onload = function() {
                        var dimensions = [img.width, img.height];
                        resolve(dimensions);
                    };

                    img.onerror = function() {
                        reject(new Error('Failed to load image'));
                    };

                    img.src = e.target.result;
                };

                reader.onerror = function() {
                    reject(new Error('Failed to read file'));
                };

                reader.readAsDataURL(file);
            } else {
                reject(new Error('Selected file is not an image'));
            }
        });
    }



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
            [414, 896],
            [1920, 1080]
        ];
        var fileInputName = ["Theme Icon", "logo", "Background Mobile","Background Desktop"];

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
                    console.log(file);
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
                                console.log(index);
                                errorMsg = 'Incorrect Dimensions of ' + fileInputName[index];
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
                        formData.append('file' + (index + 1), "");
                        console.error(`Error processing file ${file.name}: ${errorMsg}`);
                    }
                } else {
                    errorFlag = false; // make it true later
                    formData.append('file' + (index + 1), "");
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
                request.open('post', 'upload-create-theme-update.php', true);
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
                console.log(formData);
                request.send(formData);
            } else {
                swal({
                    title: errorMsg,
                    background: '#fff url(img/wrong.png)'
                });
            }
        });
    }, false);
    </script>
</body>

</html>