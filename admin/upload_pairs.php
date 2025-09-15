<?php
session_start();
include_once '../dao/config.php';
include_once("userFunction.php");
$themename = $_POST['themename']; // The theme name

// Check if POST data is present
if (isset($_POST["themename"])) {
    $themeid = $_POST['themeid']; // Retrieve themeid from POST data

    $selectvalue1 = $_POST['selectvalue1']; 

    // Iterate through the pairs data
    foreach ($_POST['pairs'] as $index => $pair) {
        // Extract pair items
        $item1 = $pair['item1']; // Dynamic value from frontend
        $item2 = $pair['item2']; // Dynamic value from frontend

        // Handle file uploads (optional, you can skip this if no file is uploaded)
        $imagePath1 = '';
        if (isset($_FILES['pairs']['name'][$index]['image1'])) {
            $image1 = $_FILES['pairs']['name'][$index]['image1'];
            $tmpName1 = $_FILES['pairs']['tmp_name'][$index]['image1'];

            $uniqueName1 = uniqid() . '_' . basename($image1);
            $imagePath1 = '../upload_img/' . $uniqueName1;
            move_uploaded_file($tmpName1, $imagePath1);
        }

        // If no image is uploaded, set the item1 to the unique name
        if (empty($item1)) {
            $item1 = $uniqueName1;
        }

        // Prepare data for insertion
        $data = array(
            "userid" => $_SESSION['userId'],
            "organizationId" => $_SESSION['organizationId'],
            "sessionId" => $_SESSION['sessionId'],
            "themeid" => $themeid, // Include themeid in the data array
            "themename" => $themename, // The theme name
            "type1" => $selectvalue1,
            "item1" => $item1,
            "item2" => $item2 // Save the dynamic value of item2
        );

        // Insert pair into the database
        if (insertRecord("pairs", $data)) {
            echo "Pair added successfully.";
        } else {
            echo "Error occurred while adding pair.";
        }
    }
} else {
    echo "No data received.";
}
?>
