<?php 
$themename = $curruntTheme;
// echo "thememname---------",$themename ;
$check="select * from questions where sessionId='$sessionId' and themename ='$themename' and organizationId='$organizationId'";
// echo $check;
// die();
$rows=execute_query($check);
 //give 1 if theme is in table; 
$rows = mysqli_num_rows($rows);
// echo $themename ;
// die(); 
// echo $rows;
if($rows == 0) {
  
  // $fetch = "select * from questions_default";
  // $fetch = "select * from questions_default where themename = '$themename'";
  $fetch = "select * from questions where themename = '$themename' and sessionId='admin'";
  // echo $fetch;
  // die();
  $fetch = execute_query( $fetch);
  while( $result = mysqli_fetch_array($fetch))
  {
    
    $question = $result["question_name"];
   

    $insert = "INSERT INTO `questions`(`organizationId`, `sessionId`, `themeId`, `themename`, `question_name`) VALUES ('$organizationId','$sessionId','','$themename','$question')";
    // echo  $insert;
    if(!execute_query( $insert)){
      echo mysqli_error($con);
    }
  }
}


// $fetch = "select * from questions_default where themename = '$themename'";


