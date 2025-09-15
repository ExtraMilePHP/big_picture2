<?php
ob_start();
session_start();
error_reporting(0);

$organizationId = $_SESSION['organizationId'];
$sessionId = $_SESSION['sessionId'];
$curruntTheme = $_GET["name"];
$_SESSION['themename'] = $curruntTheme;

include_once '../dao/config.php';
$id = $_GET["id"];

// Query to fetch questions
$sql = "SELECT question_name FROM questions WHERE themename='$curruntTheme'";
$qur = execute_query($sql);

// Set headers to enable file download
$filename = "questions.csv";
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Content-Type: text/csv; charset=utf-8");

// Open output stream
$display = fopen("php://output", 'w');

// Add BOM for Excel Compatibility (Optional but Recommended for UTF-8)
fwrite($display, "\xEF\xBB\xBF");

// Handle special characters and export data
$flag = false;
while ($row = mysqli_fetch_assoc($qur)) {
    // Decode HTML entities and ensure UTF-8 encoding
    foreach ($row as $key => $value) {
        $row[$key] = mb_convert_encoding(html_entity_decode($value, ENT_QUOTES | ENT_HTML5), 'UTF-8');
    }

    if (!$flag) {
        // Display field/column names as first row
        fputcsv($display, array_keys($row), ",", '"');
        $flag = true;
    }
    // Write row data
    fputcsv($display, array_values($row), ",", '"');
}

fclose($display);
?>
