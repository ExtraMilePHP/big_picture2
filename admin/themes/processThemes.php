<?php 
include_once("themesets.php");

$organizationId=$_SESSION['organizationId'];
$sessionId=$_SESSION['sessionId'];


$themes_sql = "SELECT * FROM themes_superadmin";
$themes_result = $con->query($themes_sql);

$themeDataArray = [];

$themesLocal=$themes;


if ($themes_result->num_rows > 0) {
    while ($theme = $themes_result->fetch_assoc()) {
        $themename = $theme['themename'];

        $isSelected = $theme["selected"];
    
        
        // Initialize the theme array with fixed values
        $themeDataArray[$themename] = [
            "themeName" => $theme['themename'],
            "selected" => $theme['selected']
        ];
        
        // Fetch theme data from themesdata_superadmin matching themename
        $theme_data_sql = "SELECT * FROM themesdata_superadmin WHERE themename = '$themename'";
        $theme_data_result = $con->query($theme_data_sql);

        if ($theme_data_result->num_rows > 0) {
            while ($data = $theme_data_result->fetch_assoc()) {
                $key_value = $data['target'];
                $value = $data['value'];

                // Unserialize if necessary
                if (is_serialized($value)) {
                    $value = unserialize($value);
                }

                if($key_value=="selected"){
                    $value=$isSelected;
                }

                $themeDataArray[$themename][$key_value] = $value;
            }
        }
    }
}

// Function to check if a string is serialized

function is_serialized($data) {
    // If it isn't a string, it isn't serialized
    if (!is_string($data)) {
        return false;
    }
    $data = trim($data);
    if ('N;' == $data) {
        return true;
    }
    if (!preg_match('/^([adObis]):/', $data, $badions)) {
        return false;
    }
    switch ($badions[1]) {
        case 'a':
        case 'O':
        case 's':
            if (preg_match("/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data)) {
                return true;
            }
            break;
        case 'b':
        case 'i':
        case 'd':
            if (preg_match("/^{$badions[1]}:[0-9.E-]+;\$/", $data)) {
                return true;
            }
            break;
    }
    return false;
}


// Output the result
// print_r($themeDataArray);

// $themes = array_merge($themes, $themeDataArray);

$themes =  $themeDataArray;

// print_r($themes);

foreach ($themes as $process_key => $value) {
    $themeName = $process_key; // Use the theme process_key as the theme name
    $selected = $themes[$process_key]["selected"];
    $check="SELECT * FROM themes WHERE organizationId='$organizationId' AND sessionId='$sessionId' AND themename='$themeName'";
    $result = execute_query($check);
    $row = mysqli_num_rows($result);
    if($row == 0){
        $themeInsertQuery = "INSERT INTO themes (organizationId, sessionId, themename,selected,type) 
        VALUES ('$organizationId', '$sessionId', '$themeName','$selected','default')";
        execute_query($themeInsertQuery);
    }
    foreach ($themes[$process_key] as $process_key => $value) {
        $key_name=$process_key;
        $key_value=$value;
        if(is_array($key_value)){
            $key_value=serialize($key_value);
        }

        $key_value=mysqli_real_escape_string($con,$key_value);
        $check="select * from themesdata where target='$key_name' and organizationId='$organizationId' and sessionId='$sessionId' and themename='$themeName'";
        $check=execute_query($check);
        $check=mysqli_num_rows($check);
        if($check==0){
            $insert="INSERT INTO `themesdata`(`organizationId`, `sessionId`,`themename`,`target`, `value`) VALUES ('$organizationId','$sessionId','$themeName','$key_name','$key_value')";
            execute_query($insert);
        }

  }
}

// give updates to superadmin
if($_SESSION["sessionId"]=="admin"){
    foreach ($themesLocal as $process_key => $value) {
        $themeName = $process_key; // Use the theme process_key as the theme name
        $check="SELECT * FROM themes_superadmin";
        $result = execute_query($check);
        $row = mysqli_num_rows($result);
        if($row != 0){
            while($get=mysqli_fetch_array($result)){
                $currentThemeName= $get["themename"];
             foreach ($themesLocal[$process_key] as $process_key_two => $value) {
                $key_name=$process_key_two;
                $key_value=$value;
                if(is_array($key_value)){
                    $key_value=serialize($key_value);
                }
               
                $check="select * from themesdata_superadmin where target='$key_name' and themename='$currentThemeName'";
                $check=execute_query($check);
                $check=mysqli_num_rows($check);
                if($check==0){
                    $insert="INSERT INTO `themesdata_superadmin`(`organizationId`, `sessionId`,`themename`,`target`, `value`) VALUES ('$organizationId','$sessionId','$currentThemeName','$key_name','$key_value')";
                    execute_query($insert);
                }
              }
            }
        }
    }
}else{
    // handle the scenario if superadmin deletes the parent theme in that case the alone theme should recieve update from themesets
    $findAbandoned="SELECT t.*
FROM themes t
LEFT JOIN themes_superadmin ts
ON t.themename = ts.themename
WHERE ts.themename IS NULL
AND t.organizationId = '$organizationId'
AND t.sessionId = '$sessionId'";
$fetchAb=execute_query($findAbandoned);
    while($get=mysqli_fetch_array($fetchAb)){
        $currentThemeName=$get["themename"];
        foreach ($themesLocal as $process_key => $value) {
            foreach ($themesLocal[$process_key] as $process_key_two => $value) {
                $key_name=$process_key_two;
                $key_value=$value;
                if(is_array($key_value)){
                    $key_value=serialize($key_value);
                }
            
                $check="select * from themesdata where target='$key_name' and organizationId='$organizationId' and sessionId='$sessionId' and themename='$currentThemeName'";
                $check=execute_query($check);
                $check=mysqli_num_rows($check);
                if($check==0){
                    $insert="INSERT INTO `themesdata`(`organizationId`, `sessionId`,`themename`,`target`, `value`) VALUES ('$organizationId','$sessionId','$currentThemeName','$key_name','$key_value')";
                    execute_query($insert);
                }
            }
        }
    }
}




?>