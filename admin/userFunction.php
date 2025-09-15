<?php
include_once("../../env.php");
session_start();
error_reporting(E_ALL);

$organizationId = $_SESSION['organizationId'];
$sessionId = $_SESSION['sessionId'];
$userId = $_SESSION['userId'];
function connectToDatabase() {
    // global $servername, $username, $password; 
    global $server_write, $username_write, $password_write; 
    $dbname = "extramileplay_big_picture_new";

    $conn = new mysqli($server_write, $username_write, $password_write, $dbname);
  
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

function checkSessionExists($tableName) {
    global $organizationId,$sessionId,$userId;
   // Start the session
    if (!isset($organizationId) || !isset($sessionId) || !isset($userId)) {
        // If any of the session variables are not set, return false
        return false;
    }

    $conn = connectToDatabase();
   
    $sql = "SELECT COUNT(*) AS count FROM $tableName WHERE organizationId = '$organizationId' AND sessionId = '$sessionId' ";
    echo $sql;
    $result = $conn->query($sql);
    if ($result === FALSE) {
        echo "Error: " . $sql . "<br>" . $conn->error;
        return false;
    }
    $row = $result->fetch_assoc();
    if ($row['count'] > 0) {
        return $row['count'];
    } else {
        return false;
    }
}


// Create operation
function insertRecord($tableName, $data) {
    $conn = connectToDatabase();

    $fields = implode(", ", array_keys($data));
    $values = "'" . implode("', '", array_values($data)) . "'";
    $sql = "INSERT INTO $tableName ($fields) VALUES ($values)";
// echo  $sql;
// die();

    if ($conn->query($sql) === TRUE) {
        // echo "New record created successfully";
        return true;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
        return false;
       
    }
    // $conn->close();
}


function selectFromTable($tableName, $fields, $conditions = array()) {
    $conn = connectToDatabase();

    $fields = implode(", ", $fields);
    $sql = "SELECT $fields FROM $tableName";

    // Construct WHERE clause for multiple conditions
    if (!empty($conditions)) {
        $sql .= " WHERE ";
        $conditionsString = array();
        foreach ($conditions as $column => $value) {
            $conditionsString[] = "$column = '$value'";
        }
        $sql .= implode(" AND ", $conditionsString);
    }
// echo  $sql;
// die();
    $result = $conn->query($sql);
    if ($result === FALSE) {
        echo "Error: " . $sql . "<br>" . $conn->error;
    } else {
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    $conn->close();
}


// Update operation
function updateRecord($tableName, $data, $conditions) {
    $conn = connectToDatabase();
    $setClause = "";
    foreach ($data as $key => $value) {
        $setClause .= "$key = '$value', ";
    }
    $setClause = rtrim($setClause, ", ");

    $whereClause = "";
    foreach ($conditions as $key => $value) {
        $whereClause .= "$key = '$value' AND ";
    }
    $whereClause = rtrim($whereClause, "AND ");

    $sql = "UPDATE $tableName SET $setClause WHERE $whereClause";
    // echo $sql;
    // die();
    if ($conn->query($sql) === TRUE) {
        $conn->close();
        return true; 
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
        $conn->close();
        return false; 
    }
}


// Delete operation
function deleteRecord($tableName, $conditions) {
    $conn = connectToDatabase();
    // Construct WHERE clause with multiple conditions
    $whereClause = "";
    foreach ($conditions as $key => $value) {
        $whereClause .= "$key = '$value' AND ";
    }
    $whereClause = rtrim($whereClause, "AND ");
    $sql = "DELETE FROM $tableName WHERE $whereClause";
    // echo $sql;
    // die();
        if ($conn->query($sql) === TRUE) {
        return true; 
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
        $conn->close();
        return false; 
    }
}
