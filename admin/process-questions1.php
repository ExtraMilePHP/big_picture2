<?php 
$themename = $curruntTheme;
// echo $themename ;
$check="select * from pairs where sessionId='$sessionId' and themename ='$themename' and organizationId='$organizationId'";
// echo "page2---",$check;
// die();
$rows=execute_query($check);
 //give 1 if theme is in table; 
$rows = mysqli_num_rows($rows);
// echo $themename ;
// die(); 
// echo $rows;
if($rows == 0) {
  $fetch = "select * from pairs where themename = '$themename' and sessionId='admin'";
  // echo $fetch;
  // die();
  $fetch = execute_query( $fetch);
  while( $result = mysqli_fetch_array($fetch))
  {
    
    $item1 = $result["item1"];
    $item2 = $result["item2"];
   
    $insert = "INSERT INTO `pairs`(`organizationId`, `sessionId`, `themeId`, `themename`, `item1`, `item2`) VALUES ('$organizationId','$sessionId','','$themename','$item1','$item2')";
    if(!execute_query( $insert)){
      echo mysqli_error($con);
    }
  }
}


// $fetch = "select * from questions_default where themename = '$themename'";