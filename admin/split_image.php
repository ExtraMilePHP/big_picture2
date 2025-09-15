<?php
ob_start();
error_reporting(0);
session_start();

$settings = json_decode(file_get_contents("settings.js"), true)[0];

$organizationId = $_SESSION['organizationId'];
$sessionId = $_SESSION['sessionId'];
include_once '../dao/config.php';
include_once '../../admin_assets/triggers.php';
include_once 'themes/processThemes.php';
include_once 'themes/themeTools.php';

$data = fetchThemeData();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['split_image'], $_POST['image_name'])) {
    $splitCount = (int)$_POST['split_image'];
    $originalImagePath = '../uploads/' . $_POST['image_name'];

    $image = imagecreatefromstring(file_get_contents($originalImagePath));
    if (!$image) {
        die("Failed to load image.");
    }

    $width = imagesx($image);
    $height = imagesy($image);
    $savedPieces = [];

    $splitDir = '../uploads/split/';
    if (!is_dir($splitDir)) {
        mkdir($splitDir, 0777, true);
    }

    // Smart square slicing logic
    $minDiff = PHP_INT_MAX;
    for ($cols = 1; $cols <= $splitCount; $cols++) {
        $rows = ceil($splitCount / $cols);
        $sliceRatio = ($width / $cols) / ($height / $rows);
        $diff = abs(1 - $sliceRatio);

        if ($diff < $minDiff) {
            $minDiff = $diff;
            $bestCols = $cols;
            $bestRows = $rows;
        }
    }

    // Do the slicing
    $count = 0;
    $pieceWidth = floor($width / $bestCols);
    $pieceHeight = floor($height / $bestRows);

    for ($row = 0; $row < $bestRows; $row++) {
        for ($col = 0; $col < $bestCols; $col++) {
            if ($count >= $splitCount) break 2;

            $x = $col * $pieceWidth;
            $y = $row * $pieceHeight;

            $dest = imagecreatetruecolor($pieceWidth, $pieceHeight);
            imagecopy($dest, $image, 0, 0, $x, $y, $pieceWidth, $pieceHeight);

            $filename = uniqid('slice_', true) . '.png';
            $savePath = $splitDir . $filename;

            imagepng($dest, $savePath);
            imagedestroy($dest);

            $savedPieces[] = $filename;
            $count++;
        }
    }

    imagedestroy($image);

// echo "<pre>";
// echo "Trying to save split_image: $splitCount\n";
// echo "Trying to save split_image_divide:\n";
// print_r($savedPieces); // See what values you're trying to save
// die();

if (updateThemeData("split_image_divide", json_encode($savedPieces)) &&
    updateThemeData("split_image", $splitCount)) {

//         echo "<pre>";
// print_r($savedPieces);
// echo "</pre>";
// echo "<pre>";
// print_r($splitCount);
// echo "</pre>";
// die();

      header("Location: jigsaw_theme.php");
    //     exit;
} else {
    echo "Failed to update theme data.";
}

}
?>
