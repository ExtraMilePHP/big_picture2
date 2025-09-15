<?php
session_start();
include_once '../dao/config.php';
include_once("userFunction.php");

if (!isset($_POST['pairs']) || !is_array($_POST['pairs'])) {
    echo "No data received.";
    exit;
}

$updatedCount = 0;

foreach ($_POST['pairs'] as $id => $pair) {
    $item1 = isset($pair['item1']) ? trim($pair['item1']) : '';
    $item2 = isset($pair['item2']) ? trim($pair['item2']) : '';

    // Get current record from DB
    $checkQuery = "SELECT item1, item2 FROM pairs WHERE id = '$id'";
    $result = mysqli_query($con, $checkQuery);

    if (!$result || mysqli_num_rows($result) == 0) continue;

    $current = mysqli_fetch_assoc($result);
    $currentItem1 = $current['item1'];
    $currentItem2 = $current['item2'];

    // Check for file upload
    $newImageUploaded = isset($_FILES['pairs']['name'][$id]['image1']) && $_FILES['pairs']['error'][$id]['image1'] === UPLOAD_ERR_OK;
    $image1Name = $currentItem1;

    if ($newImageUploaded) {
        $image1 = $_FILES['pairs']['name'][$id]['image1'];
        $tmpName1 = $_FILES['pairs']['tmp_name'][$id]['image1'];

        $uniqueName1 = uniqid() . '_' . basename($image1);
        $imagePath1 = '../upload_img/' . $uniqueName1;
        move_uploaded_file($tmpName1, $imagePath1);

        $image1Name = $uniqueName1; // Update item1 with new image
    }

    // Compare old vs new
    $hasChanges = false;
    $updateData = [];

    if ($newImageUploaded || $item1 !== $currentItem1) {
        $updateData['item1'] = $image1Name;
        $hasChanges = true;
    }

    if ($item2 !== $currentItem2) {
        $updateData['item2'] = $item2;
        $hasChanges = true;
    }

    if ($hasChanges) {
        $where = ["id" => $id];
        updateRecord("pairs", $updateData, $where);
        $updatedCount++;
    }
}

// echo "$updatedCount record(s) updated.";
?>
