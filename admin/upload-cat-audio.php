<?php

ob_start();
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load settings and session variables
$settings = json_decode(file_get_contents("settings.js"), true)[0];
$organizationId = $_SESSION['organizationId'];
$sessionId = $_SESSION['sessionId'];
$curruntTheme = $_SESSION['themename'];

header('Content-Type: application/json');

// Include database configuration and dependencies
include_once '../dao/config.php';
include_once '../../admin_assets/triggers.php';

require '../../aws/aws-autoloader.php';

// Get form data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $audio = $_FILES['audio'];
    $lyrics = $_FILES['lyrics'];
    $category = htmlspecialchars($_POST['category']);
    $songName = htmlspecialchars($_POST['song_name'], ENT_QUOTES, 'UTF-8'); // Added song_name

    // Check for upload errors
    if ($audio['error'] !== UPLOAD_ERR_OK || $lyrics['error'] !== UPLOAD_ERR_OK) {
        $response['error'] = 'File upload error: ' . ($audio['error'] !== UPLOAD_ERR_OK ? 'Audio file' : 'Lyrics file');
        echo json_encode($response);
        exit;
    }

    // Validate file extensions
    $validAudioExtensions = ['mp3', 'mp4'];
    $validLyricsExtensions = ['lrc', 'pdf'];

    // Extract file extensions
    $audioExt = pathinfo($audio['name'], PATHINFO_EXTENSION);
    $lyricsExt = pathinfo($lyrics['name'], PATHINFO_EXTENSION);

    // Validate file sizes
    if ($audio['size'] > 10 * 1024 * 1024 || $lyrics['size'] > 10 * 1024 * 1024) {
        $response['error'] = 'File size exceeds limit';
        echo json_encode($response);
        exit;
    }

    // Define upload directory and unique file paths
    $uploadDir = 'uploads/';
    $audioPath = $uploadDir . uniqid() . '.' . $audioExt;
    $lyricsPath = $uploadDir . uniqid() . '.' . $lyricsExt;

    // Move files to the server
    if (move_uploaded_file($audio['tmp_name'], $audioPath) && move_uploaded_file($lyrics['tmp_name'], $lyricsPath)) {
        // Update database with song_name
        $update = "INSERT INTO `questions` (`organizationId`, `sessionId`, `themename`, `category`, `audio`, `lyric`, `song_name`) 
                   VALUES ('$organizationId', '$sessionId', '$curruntTheme', '$category', '$audioPath', '$lyricsPath', '$songName')";
      if (execute_query($update)) {
        echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Database update failed']);
        }
    exit;
    
    }
}
?>