<?php
error_reporting(0);
session_start();
$organizationId = $_SESSION['organizationId'];
$sessionId = $_SESSION['sessionId'];
include_once '../dao/config.php';

// Query from question table (assuming you have fullname + email also stored here)
$sql = "SELECT 
            email,
            name,
            question5 AS Stage1,
            question1 AS Stage2,
            question3 AS Stage3,
            score,
            end_time AS Points
        FROM question
        WHERE organizationId = '$organizationId' 
          AND sessionId = '$sessionId'
        ORDER BY id DESC";

$qur = mysqli_query($con, $sql);

// File name
$filename = "Question-data.csv";

// Headers for download
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Content-Type: text/csv");

// Open output stream
$display = fopen("php://output", 'w');

// CSV headers
$headers = [
    'Email',
    'Name',
    'Stage 1',
    'Stage 2',
    'Stage 3',
    'Score',
    'Points'
];
fputcsv($display, $headers);

// Write rows
while ($row = mysqli_fetch_assoc($qur)) {
    $rowData = [
        $row['email'],
        $row['name'],
        $row['Stage1'],
        $row['Stage2'],
        $row['Stage3'],
        $row['score'],
        $row['Points']
    ];
    fputcsv($display, $rowData);
}

fclose($display);
exit;
?>
