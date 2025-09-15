<?php 
include_once("themesets.php");

function fetchAllThemes(){
    global $sessionId, $organizationId, $con;
    $table=($_SESSION["sessionId"]=="admin")?"themes_superadmin":"themes";
    $themeData_table=($_SESSION["sessionId"]=="admin")?"themesdata_superadmin":"themesdata";
    $fetch_query = "SELECT t1.*, t2.value as themeImage
FROM $table as t1
LEFT JOIN $themeData_table as t2 ON t1.sessionId = t2.sessionId AND t1.organizationId = t2.organizationId AND t1.themename = t2.themename
WHERE t1.sessionId = '$sessionId' AND t1.organizationId = '$organizationId' AND t2.target = 'themeImage'
";
    $result = execute_query( $fetch_query);
    $themes = array();
    while ($row = mysqli_fetch_array($result)) {
        $themes[] = $row;
    }
    return $themes;
}


function fetchThemeData(){
    global $curruntTheme,$sessionId,$organizationId,$con,$themes;

    if (!function_exists('is_serialized')){
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
}

    $table=($_SESSION["sessionId"]=="admin")?"themes_superadmin":"themes";
    $themeData_table=($_SESSION["sessionId"]=="admin")?"themesdata_superadmin":"themesdata";
    if (isset($curruntTheme)) {
        $fetch="select target,value from $themeData_table where themename='$curruntTheme' and sessionId='$sessionId' and organizationId='$organizationId'";
        $result = execute_query( $fetch);
        $data = array();
        while ($row = mysqli_fetch_array($result)) {
            $data[$row["target"]] = $row["value"];
        }
        $data["themeName"]=$curruntTheme;
        return $data;
    } else {
       $check="select * from $table where sessionId='$sessionId' and organizationId='$organizationId' and selected='true'";
       $check=execute_query($check);
       if(mysqli_num_rows($check)==0){


if(isset($_SESSION["target_theme"])){
    $getThemename=$_SESSION["target_theme"];
    $themes_sql = "SELECT * FROM themes_superadmin where themename='$getThemename' order by id asc limit 1";
}else{
    $themes_sql = "SELECT * FROM themes_superadmin where selected='true' order by id asc limit 1";
}

$themes_result =execute_query($themes_sql);

$themeDataArray = [];


if ($themes_result->num_rows > 0) {
    while ($theme = $themes_result->fetch_assoc()) {
        $themename = $theme['themename'];
        
        // Initialize the theme array with fixed values
        $themeDataArray[$themename] = [
            "themeName" => $theme['themename'],
            "selected" => $theme['selected']
        ];
        
        // Fetch theme data from themesdata_superadmin matching themename
        $theme_data_sql = "SELECT * FROM themesdata_superadmin WHERE themename = '$themename'";
        $theme_data_result = execute_query($theme_data_sql);

        if ($theme_data_result->num_rows > 0) {
            while ($data = $theme_data_result->fetch_assoc()) {
                $key_value = $data['target'];
                $value = $data['value'];

                // Unserialize if necessary
                if (is_serialized($value)) {
                    $value = unserialize($value);
                }

                $themeDataArray[$themename][$key_value] = $value;
            }
        }
    }
}

// Function to check if a string is serialized


// Output the result
// print_r($themeDataArray);

// $themes = array_merge($themes, $themeDataArray);

           $themes =  $themeDataArray;

           $firstKey= array_key_first($themes);
           $data = array();
           foreach ($themes[$firstKey] as $key => $value) {
            $key_name=$key;
            $key_value=$value;
            if(is_array($key_value)){
                $key_value=serialize($key_value);
            }
            $data[$key_name] = $key_value;
         }
         $data["themeName"]=$firstKey;
         return $data;
      }else{
         $check=mysqli_fetch_object($check);
         $themename=$check->themename;
         $fetch="select target,value from $themeData_table where themename='$themename' and sessionId='$sessionId' and organizationId='$organizationId'";
         $result = execute_query( $fetch);
         $data = array();
         while ($row = mysqli_fetch_array($result)) {
             $data[$row["target"]] = $row["value"];
         }
         $data["themeName"]=$themename;
         return $data;
      }
    }
}

function updateThemeData($key,$value){
    global $curruntTheme,$sessionId,$organizationId,$con;
    $themeName=$_SESSION['themename'];
    $table=($_SESSION["sessionId"]=="admin")?"themes_superadmin":"themes";
    $themeData_table=($_SESSION["sessionId"]=="admin")?"themesdata_superadmin":"themesdata";
    $update="UPDATE `$themeData_table` SET `value`='$value' where sessionId='$sessionId' and organizationId='$organizationId' and themename='$themeName' and target='$key'";
    if(execute_query($update)){
        return true;
    }else{
        return false;
    }
}

function fetchDefaultValue($key) {
    global $con;
    $themeName = $_SESSION['themename'];
    
    $fetch = "SELECT * FROM themesdata_superadmin WHERE themename='$themeName' AND target='$key'";
 
    
    $result = execute_query( $fetch);
    
    if (mysqli_num_rows($result) > 0) {
        $fetch = mysqli_fetch_object($result);
        return $fetch->value;
    } else {
        // Handle the case where no data is found
        return "default_value"; // Replace with your desired default value or error message
    }
}

function generateUUID() {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $uuid = '';
    for ($i = 0; $i < 32; $i++) {
        $uuid .= $characters[mt_rand(0, strlen($characters) - 1)];
    }
    return $uuid;
}

function createNewTheme($newData,$newDataKeys){
    global $sessionId,$organizationId,$con,$themes,$themesLocal;
    $uid=generateUUID();
    $themeName=$uid;

    // print_r($themesLocal);

    $themeData=$themesLocal["theme0"];

    

    for($i=0; $i<sizeof($newDataKeys); $i++){
        $themeData[$newDataKeys[$i]]=$newData[$i];
    }

    $table=($_SESSION["sessionId"]=="admin")?"themes_superadmin":"themes";
    $themeData_table=($_SESSION["sessionId"]=="admin")?"themesdata_superadmin":"themesdata";

    $insert="INSERT INTO `$table`(`organizationId`, `sessionId`, `themename`, `selected`, `type`) VALUES ('$organizationId','$sessionId','$uid','false','custom')";
    if(execute_query($insert)){
        foreach ($themeData as $key => $value) {
            $key_name=$key;
            $key_value=$value;
            if(is_array($key_value)){
                $key_value=serialize($key_value);
            }
           
            $check="select * from $themeData_table where target='$key_name' and organizationId='$organizationId' and sessionId='$sessionId' and themename='$themeName'";
            $check=execute_query($check);
            $check=mysqli_num_rows($check);
            if($check==0){
                $insert="INSERT INTO `$themeData_table`(`organizationId`, `sessionId`,`themename`,`target`, `value`) VALUES ('$organizationId','$sessionId','$themeName','$key_name','$key_value')";
                execute_query($insert);
            }
      }
        return true;
    }else{
        return false;
    }
}


function selectTheme($themename){
    global $sessionId,$organizationId,$con;
    $table=($_SESSION["sessionId"]=="admin")?"themes_superadmin":"themes";
    $themeData_table=($_SESSION["sessionId"]=="admin")?"themesdata_superadmin":"themesdata";
    $update="UPDATE $table set selected='false' where organizationId='$organizationId' and sessionId='$sessionId'";
    if(execute_query($update)){
        $update="UPDATE $table set selected='true' where organizationId='$organizationId' and sessionId='$sessionId' and themename='$themename'";
        if(execute_query($update)){
            $_SESSION["target_theme"]="";
            return true;
        }else{
            return false;
        }
    }
}

?>