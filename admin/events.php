<?php

ob_start();
error_reporting(0);
session_start();
$settings=json_decode(file_get_contents("settings.js"),true)[0];
$organizationId=$_SESSION['organizationId'];
$sessionId=$_SESSION['sessionId'];
include_once '../dao/config.php';
include_once '../../admin_assets/triggers.php';

include_once 'themes/processThemes.php';
include_once 'themes/themeTools.php';

require '../../vendor/autoload.php'; // Include the Composer autoload file
include_once "../s3/s3_functions.php"; // s3 function for easy upload and get signed urls, still need env file and autoload

$data=fetchThemeData();


$events=$_GET["events"];
if(isset($events)){
    if($events=="shuffle"){
        if($launchpos>0){
            echo "Error : unable to shuffle. values are already released. Reset launch first.";
        }else{
            $numbers = range(1, 50);
            shuffle($numbers);
            $serialize=serialize($numbers);
            updateValues("shuffle",$serialize);
            header("Location:index.php");
        }
    }


    if($events=="category_upload"){
        $upload_count = $_POST["upload_count"];
        // echo $upload_count;
        // die();
        updateThemeData("category_upload_count",$upload_count);
        echo "true";
    }


    if($events=="change_theme"){
            $themeNo=$_POST["themeNo"];
            $background_web=$settings["themesets"][$themeNo]["background_web"];
            $background_mob=$settings["themesets"][$themeNo]["background_mob"];
            updateValues("background_mob",$background_web);
            updateValues("background_web",$background_mob);
            echo "true";
    }

    if ($events == "add_cat") {
        // Get the variables from POST
        $words_array = $_POST["variables_array"] ?? [];
        $get_custom_cat = $_POST["get_custom_cat"] ?? [];
    
        // Serialize the variables
        $words_array = serialize($words_array);
        $custom_cat = serialize(json_decode($get_custom_cat, true));
    
        // Escape special characters using mysqli_real_escape_string
        $words_array = mysqli_real_escape_string($con, $words_array);
        $custom_cat = mysqli_real_escape_string($con, $custom_cat);
    
        // Update the database with sanitized values
        if (updateThemeData("default_cat", $words_array) && updateThemeData("custom_cat", $custom_cat)) {
            echo "true";
        }
    }
    


    if($events=="change_limit"){
        $value=$_GET["value"];
        if($launchpos>0){
            echo "Error : unable to update. values are already released. Reset launch first.";
        }else{
            if($value>10){
                echo "Error : unable to update. Max limit is 10";
            }else{
                if($value==0){
                    echo "Error : unable to update. insert number between 1 to 10";
                }else{
                    updateValues("numlimit",$value);
                    header("Location:index.php");
                    echo "true";
                }
            }
        }
    }

    if($events=="approve"){
        $action=$_GET["action"];
        $target=$_GET["target"];
        $update="update uploads SET approval='$action' where id='$target'";
        if(execute_query($update)){
            echo "true";
        }
        // header("Location:view-uploads.php");
    }
    if($events=="allapproved"){
        $action=$_GET["action"];
        // $target=$_GET["target"];
        $update="update uploads SET approval='$action' where organizationId='$organizationId' and sessionId='$sessionId'";
        if(execute_query($update)){
            echo "true";
        }
        // header("Location:view-uploads.php");
    }
    if($events=="allapproved"){
        $action=$_GET["action"];
        // $target=$_GET["target"];
        $update="update uploads SET approval='$action' where organizationId='$organizationId' and sessionId='$sessionId'";
        if(execute_query($update)){
            echo "true";
        }
        // header("Location:view-uploads.php");
    }
    if($events=="alldisapproved"){
        $action=$_GET["action"];
        // $target=$_GET["target"];
        $update="update uploads SET approval='$action' where organizationId='$organizationId' and sessionId='$sessionId'";
        //echo $update;
        if(execute_query($update)){
            echo "true";
        }
        // header("Location:view-uploads.php");
    }
    if($events=="multiplentry"){
        $action=$_GET["action"];
        // $target=$_GET["target"];
        $update="update settings SET value='$action' where target='entrycount' and organizationId='$organizationId' and sessionId='$sessionId'";
        //echo $update;
        if(execute_query($update)){
            echo "true";
        }
        // header("Location:view-uploads.php");
    }
    
    if($events=="release_number"){
        $predict=($launchpos+1)*$numlimit;
        if($predict>50){
            updateValues("launchpos","10");
            updateValues("numlimit","5");
            header("Location:index.php");
        }else{
            $launchpos=$launchpos+1;
            updateValues("launchpos",$launchpos);
            header("Location:index.php");
        }
    }


    function escapeArray($conn, $array) {
        // Escaping each element in the array
        $escapedArray = array();
        foreach ($array as $key => $value) {
            $escapedArray[$key] = mysqli_real_escape_string($conn, $value);
        }
    
        return $escapedArray;
    }

    
    if($events=="add_rules"){
        $rules=$_POST["rules"];
        // $title=mysqli_real_escape_string($con,$_POST["title"]);
        $custom_text_thank_you_page=mysqli_real_escape_string($con,$_POST["custom_text_thank_you_page"]);
        $textColorPicker=$_POST["textColorPicker"];
        $buttonColorPicker = $_POST["buttonColorPicker"];
        $buttonBgColorPicker = $_POST["buttonBgColorPicker"];
        $rules=serialize($rules);
        $rules=mysqli_real_escape_string($con,$rules);
        if(updateThemeData("rules",$rules) && updateThemeData("landing_page_title_color",$textColorPicker)  && updateThemeData("landing_page_button_color",$buttonColorPicker)  && updateThemeData("landing_page_button_bgcolor",$buttonBgColorPicker) &&   updateThemeData("custom_text_thank_you_page",$custom_text_thank_you_page)){
            echo "true";
        }
    }

    if($events=="add_rules_for_challenege1"){
        $title1=mysqli_real_escape_string($con,$_POST["title1"]);
        if( updateThemeData("landing_page_title1",$title1)){
            echo "true";
        }
    }

    if($events=="add_rules_for_challenege2"){
        $title2=mysqli_real_escape_string($con,$_POST["title2"]);
        if( updateThemeData("landing_page_title2",$title2)){
            echo "true";
        }
    }

    if($events=="add_rules_for_challenege3"){
        $title3=mysqli_real_escape_string($con,$_POST["title3"]);
        $sub_title3=mysqli_real_escape_string($con,$_POST["sub_title3"]);
        $title3_eg=mysqli_real_escape_string($con,$_POST["title3_eg"]);
        if(updateThemeData("landing_page_title3",$title3)  &&  updateThemeData("landing_page_title3_question",$sub_title3)  &&  updateThemeData("landing_page_title3_eg",$title3_eg)){
            echo "true";
        }
    }

    if($events=="add_rules_for_challenege4"){
        $title4=mysqli_real_escape_string($con,$_POST["title4"]);
        if( updateThemeData("landing_page_title4",$title4)){
            echo "true";
        }
    }


    if($events=="switch_timer"){
        $use_timeout=$_GET["switch"];
        $update=($_GET["current"]=="true")?"false":"true";
        if(updateThemeData($use_timeout,$update)){
            $previousPage = $_SERVER['HTTP_REFERER'];
            // Redirect back to the previous page
            // Append the tab identifier to the URL (e.g., #tab2)
            $previousPage .= '#tab2'; // Change 'tab2' to the actual ID of your tab
            // Redirect back to the previous page with the tab hash
            header("Location: $previousPage");
            exit(); // Make sure to call exit after header redirect
        }
    }
    if($events=="new_fields"){
        $new_fields=$_POST["new_fields"];
        // print_r($new_fields);
        // $new_fields=mysqli_real_escape_string($con,$new_fields);
        if(updateThemeData("new_fields",serialize($new_fields))){
            echo "true";
        }
    }


    if ($_GET['events'] == "questions_and_answers") {
        // Extract form data from $_POST
        $themename = $_POST['themename'];
        $questions = $_POST['questions'];
        $answers = $_POST['answers'];
   
    
        // Combine questions, answers, and clues into an array
        $questions_and_answers = [];
        foreach ($questions as $index => $question) {
            $questions_and_answers[] = [
                'question' => $question,
                'answer' => $answers[$index],
            ];
        }
    
        // Serialize and escape the data for database storage
        $questions_and_answers = mysqli_real_escape_string($con, serialize($questions_and_answers));
    
        // Save to database or update theme data
        if (updateThemeData("questions_and_answers", $questions_and_answers)) {
            echo "true";
        }
    }
    
    

if($events=="add_grid"){
    $rows = $_POST["rows"];
    $cols = $_POST["cols"];
    $img_count = (int)$_POST["img_count"]; // Ensure this is an integer
    
    // Get current theme data
    $data = fetchThemeData();
    $puzzle_img = $data['puzzle_image'];
    
    // First update the basic data
    if(updateThemeData("row", $rows) && updateThemeData("col", $cols) && updateThemeData("img_count", $img_count)) {
        
        if(!empty($puzzle_img)) {
            if($img_count > 1) {
                $puzzle_split = possibleOnS3("uploads/", $puzzle_img);
                $tempFile = tempnam(sys_get_temp_dir(), 'puzzle');

                try {
                    $imageData = file_get_contents($puzzle_split);
                    if ($imageData === false) {
                        throw new Exception("Failed to download image from S3");
                    }
                    file_put_contents($tempFile, $imageData);
                } catch (Exception $e) {
                    unlink($tempFile);
                    die("Error: " . $e->getMessage());
                }

                $imageInfo = getimagesize($tempFile);
                if (!$imageInfo) {
                    unlink($tempFile);
                    die("Invalid image file");
                }

                $mime = $imageInfo['mime'];
                $ext = strtolower(pathinfo($puzzle_img, PATHINFO_EXTENSION));

                switch ($mime) {
                    case 'image/jpeg':
                        $image = imagecreatefromjpeg($tempFile);
                        break;
                    case 'image/png':
                        $image = imagecreatefrompng($tempFile);
                        imagealphablending($image, false);
                        imagesavealpha($image, true);
                        break;
                    case 'image/gif':
                        $image = imagecreatefromgif($tempFile);
                        break;
                    default:
                        unlink($tempFile);
                        die("Unsupported image type: $mime");
                }

                if(!$image) {
                    unlink($tempFile);
                    die("Failed to create image resource");
                }

                $width = imagesx($image);
                $height = imagesy($image);

                // ✅ Get best row x col combination closest to a square
                function getOptimalGrid($img_count) {
                    $bestRows = 1;
                    $bestCols = $img_count;
                    for ($rows = 1; $rows <= $img_count; $rows++) {
                        if ($img_count % $rows === 0) {
                            $cols = $img_count / $rows;
                            if (abs($cols - $rows) < abs($bestCols - $bestRows)) {
                                $bestRows = $rows;
                                $bestCols = $cols;
                            }
                        }
                    }
                    return [$bestRows, $bestCols];
                }

                list($rowsGrid, $colsGrid) = getOptimalGrid($img_count);
                $sliceWidth = floor($width / $colsGrid);
                $sliceHeight = floor($height / $rowsGrid);

                $slicedImages = array();
                $piecesCreated = 0;

                for ($row = 0; $row < $rowsGrid; $row++) {
                    for ($col = 0; $col < $colsGrid; $col++) {
                        if ($piecesCreated >= $img_count) break;

                        $srcX = $col * $sliceWidth;
                        $srcY = $row * $sliceHeight;
                        $currentSliceWidth = ($col == $colsGrid - 1) ? $width - $srcX : $sliceWidth;
                        $currentSliceHeight = ($row == $rowsGrid - 1) ? $height - $srcY : $sliceHeight;

                        $slice = imagecreatetruecolor($currentSliceWidth, $currentSliceHeight);

                        if ($mime == 'image/png' || $mime == 'image/gif') {
                            imagealphablending($slice, false);
                            imagesavealpha($slice, true);
                            $transparent = imagecolorallocatealpha($slice, 0, 0, 0, 127);
                            imagefill($slice, 0, 0, $transparent);
                        }

                        imagecopyresampled(
                            $slice, $image,
                            0, 0,
                            $srcX, $srcY,
                            $currentSliceWidth, $currentSliceHeight,
                            $currentSliceWidth, $currentSliceHeight
                        );

                        $randomString = bin2hex(random_bytes(5));
                        $sliceFilename = 'puzzle_'.$randomString.'_'.$row.'_'.$col.'.'.$ext;
                        $sliceTempPath = tempnam(sys_get_temp_dir(), 'slice');

                        switch ($mime) {
                            case 'image/jpeg': imagejpeg($slice, $sliceTempPath, 90); break;
                            case 'image/png': imagepng($slice, $sliceTempPath, 9); break;
                            case 'image/gif': imagegif($slice, $sliceTempPath); break;
                        }

                        if (uploadOnS3($sliceFilename, $sliceTempPath)) {
                            $slicedImages[] = $sliceFilename;
                            $piecesCreated++;
                        } else {
                            error_log("Failed to upload slice: $sliceFilename");
                        }

                        imagedestroy($slice);
                        unlink($sliceTempPath);
                    }
                }

                imagedestroy($image);
                unlink($tempFile);

                while(count($slicedImages) < $img_count) {
                    $randomString = bin2hex(random_bytes(5));
                    $sliceFilename = 'puzzle_'.$randomString.'_duplicate_'.count($slicedImages).'.'.$ext;
                    $slicedImages[] = $sliceFilename;
                }

                shuffle($slicedImages);
            } else {
                $slicedImages = array($puzzle_img);
            }

            if (!updateThemeData("img_array", serialize($slicedImages))) {
                echo "Failed to update image array";
            }
        }

        echo "true";
    } else {
        echo "Failed to update grid data";
    }
}


if($events=="add_timer"){
        $time=$_POST["time"];
        if(updateThemeData("timer",$time)){
            echo "true";
        }
     }

     if($events=="add_points_01"){
        $questions=$_POST["questions"];
         $title1=mysqli_real_escape_string($con,$_POST["title1"]);
        if(updateThemeData("questions_01",$questions) && updateThemeData("landing_page_title1",$title1)){
            echo "true";
        }
     }

     

     if($events=="add_points_02"){
        $questions=$_POST["questions"];
        $title2=mysqli_real_escape_string($con,$_POST["title2"]);
        if(updateThemeData("questions_02",$questions) && updateThemeData("landing_page_title2",$title2)){
            echo "true";
        }
     }

     if($events=="select_theme"){
        $themename=$_POST["name"];
        if(selectTheme($themename)){
            echo "true";
        }
     }
 
}

?>