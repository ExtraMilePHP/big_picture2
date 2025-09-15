<?php
if ($_GET['events'] == 'get_total_items') {
    require_once 'db_connection.php'; // Ensure your DB connection file is included

    $sql = "SELECT COUNT(*) as total FROM pairs WHERE themename='$curruntTheme' 
            AND organizationId='$organizationId' AND sessionId='$sessionId'";

    $result = execute_query($sql);
    $row = fetch_assoc($result);
    echo $row['total']; // Send the total count back to AJAX
    exit;
}

?>