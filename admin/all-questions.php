<?php
ob_start();
error_reporting(E_ALL);
session_start();
$settings=json_decode(file_get_contents("settings.js"),true)[0];
$organizationId=$_SESSION['organizationId'];
$sessionId=$_SESSION['sessionId'];
include_once '../dao/config.php';
include_once '../../admin_assets/triggers.php';

ini_set("error_log", "/var/www/extramileplay/php/Who-wants-to-be-a-Champion/admin/error.log");

require '../../vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

$s3client = new Aws\S3\S3Client([
    'version' => $version,
    'region'  => $region,
    'credentials' => [
    'key'    => $key,
    'secret' => $secretkey
    ]
    ]);


$_SESSION["gameTitle"]=$settings["gameName"];


if (!$_SESSION['adminId']) {
    header('Location:../index.php?save');
} 
$tabName="";

$setid=$_GET["setid"];
$default=false;
$check="select * from question_sets where id='$setid'";
$check=execute_query($check);
$check=mysqli_fetch_object($check);
$check=$check->type;
if($check=="default"){
  $default=true;
}

?>

<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
<?php include_once("../../admin_assets/common-css.php");?>
<!-- Only unique css classes -->
    <style rel="stylesheet" type="text/css">
   .form-check-input{
        margin: 0 auto;
        margin-top: 14px;
      }
      .show-image{
        width:100px;
      }
    </style>
<!-- Only unique css classes -->
  </head>


<body class="horizontal-layout horizontal-menu 2-columns" data-open="hover" data-menu="horizontal-menu" data-color="bg-gradient-x-purple-blue" data-col="2-columns">
<?php include_once("../../admin_assets/common-header.php");?>
<div class="app-content content">
      <div class="content-wrapper">
        <div class="content-wrapper-before"></div>
        <div class="content-header row">
          <div class="content-header-left col-md-4 col-12 mb-2">
            <h3 class="content-header-title"><?php echo $tabName;?></h3>
          </div>
        </div>
        <div class="content-body">
<section id="basic-form-layouts">
	<div class="row match-height">
<!-- add content here -->


		<div class="col-md-8">
			<div class="card" id="custom_card_height">
				<?php cardHeader("All questions");?>
				<div class="card-content collapse show">
					<div class="card-body">
                        <div class="row">
                        <div class="col-md-12">
                        <div class="table-responsive">
                         <a href="getcsv-admin.php?id=<?php echo $setid;?>"> <button class="btn btn-md em-color mb-1">Download CSV</button></a>
                   <table id="example" class="table table-striped table-bordered zero-configuration">
                                <thead>
                                    <tr>
                                        <th>Question Name</th>
                                        <th>Hint</th>
                                        <th>Option one</th>
                                        <th>Option Two</th>
                                        <th>Option Three</th>
                                        <th>Option Four</th>
                                        <th>Correct Answer</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                        <!-- <th>Delete</th> -->
                                    </tr>
                                </thead>
                                <?php 
                                         
                                           $sql="select *  from questions_round2 where  setid='$setid' order by id desc";
                                           $sql=execute_query($sql);
                                           while($get=mysqli_fetch_array($sql)){
                                             if(!$default){
                                              $deleteButton='<a href="'.PAGE_NAME.'?delete_id='.$get["id"].'&delete_table=questions_round2&fallback='.PAGE_NAME.'?setid='.$setid.'"><button class="btn btn-sm btn-danger">Delete</button></a>';
                                              $editButton='<a href="edit.php?question_id='.$get["id"].'&setid='.$setid.'"><button class="btn btn-sm btn-danger" style="background:black;">Edit</button></a>';
                                             }else{
                                              $deleteButton='disabled';
                                              $editButton='disabled';
                                             }

                                            
                                          

                                            $myImage=$get["question_hint"];
                                             if($get["question_hint"]==""){
                                                 $displayImage='';
                                             }else{
                                              $keyPath = $s3_bucket."whowantstobeachampion/".$myImage;
                                              $bucket_name=$s3_mainbucket;
                                              $fileName = $myImage;
                                              $command = $s3client->getCommand('GetObject', array(
                                                      'Bucket'      => $bucket_name,
                                                      'Key'         => $keyPath,
                                                      'ContentType' => 'image/png',
                                                      'ResponseContentDisposition' => 'attachment; filename="'.$fileName.'"'
                                                  ));
                                                  $signedUrl = $s3client->createPresignedRequest($command, "+6 days"); 
                                                  $presignedUrl = (string)$signedUrl->getUri();
                                                  $displayImage='<img src="'.$presignedUrl.'" class="show-image"/>';
                                             }

                                            ?>   
                                            <tr>
                                               <td><?php echo $get["question_name"];?></td>
                                               <td><?php echo $displayImage;?></td>
                                               <td><?php echo $get["option_one"];?></td>
                                               <td><?php echo $get["option_two"];?></td>
                                               <td><?php echo $get["option_three"];?></td>
                                               <td><?php echo $get["option_four"];?></td>
                                               <td><?php echo $get["correct_answer"];?></td>
                                               <td><?php echo $editButton;?></td>
                                               <td><?php echo $deleteButton;?></td>
                                               <!-- <td><a href=""><button class="btn btn-sm btn-danger">Delete</button></a></td> -->
                                            </tr>

                                       <?php     }  ?>
                                </tbody>
                            </table>
                            </div>
                        </div>
   
					</div>
                </div>
</div>
</div>
        </div>

        <div class="col-md-4">
			<div class="card" id="custom_card_height">
				<?php cardHeader("Add New Question");?>
				<div class="card-content collapse show">
					<div class="card-body">
                        <div class="row">
<div class="col-md-12">
<form id="myform">
							<div class="form-body">
								<div class="row">
									<div class="col-md-12">
                  <div class="form-group row">
                  <label class="col-md-12 label-control" for="projectinput5">Question</label>
									<div class="col-md-12">
		                            	<input type="text" id="projectinput5" class="form-control form-data" placeholder="Question" value="" name="email">
		                            </div>
		                        </div>
                            <div class="form-group row">
                  <div class="col-md-1">
                  <input type="radio" class="form-check-input"  name="optradio" value="1">
                  </div>
									<div class="col-md-11">
		                            	<input type="text" id="projectinput5" class="form-control form-data" placeholder="Option one" value="" name="email">
		                            </div>
		                        </div>
                            <div class="form-group row">
                  <div class="col-md-1">
                  <input type="radio" class="form-check-input"  name="optradio" value="2">
                  </div>
									<div class="col-md-11">
		                            	<input type="text" id="projectinput5" class="form-control form-data" placeholder="Option twp" value="" name="email">
		                            </div>
		                        </div>
                            <div class="form-group row">
                            <div class="col-md-1">
                  <input type="radio" class="form-check-input"  name="optradio" value="3">
                  </div>
									<div class="col-md-11">
		                            	<input type="text" id="projectinput5" class="form-control form-data" placeholder="Option three" value="" name="email">
		                            </div>
		                        </div>
                            <div class="form-group row">
                            <div class="col-md-1">
                  <input type="radio" class="form-check-input"  name="optradio" value="4">
                  </div>
									<div class="col-md-11">
		                            	<input type="text" id="projectinput5" class="form-control form-data"  placeholder="Option four" value="" name="email">
		                            </div>
		                        </div>
								<div class="form-group row">
									<label class="col-md-12 label-control" for="projectinput5">Allowed Format (JPG,PNG)</label>
									<div class="col-md-12">
									<div class="custom-file">
                                <input type="file" id="file" class="custom-file-input" id="inputGroupFile01" accept=".png, .jpg, .jpeg">
                                <label class="custom-file-label" for="inputGroupFile01">Choose file</label>
                            </div>
		                            </div>
		                        </div>

<!-- 
                        <div class="form-group pb-1">
              <input type="checkbox" id="switchery2" class="switchery" data-size="xs" checked disabled/>
              <label for="switchery2" class="font-medium-2 text-bold-600 ml-1">Compression Enabled</label>
            </div> -->
            
                        <div class="progress" style="display:none; margin-top:10px;">
	  <div class="progress-bar" role="progressbar" aria-valuenow="70"
	  aria-valuemin="0" aria-valuemax="100" style="width:70%">
		70%
	  </div>
	</div>
									</div>
							</div>

							<div class="form-actions">
								<button  class="btn btn-success" type="submit">
									<i class="ft-upload-cloud"></i> Add Question
								</button>
							</div>
						</form>
<!-- <div class="form-group row" >
									<label class="col-md-12 label-control" for="projectinput5">New Question</label>
									<div class="col-md-12">
		                            	<input type="text" id="projectinput5" class="form-control form-data-input" placeholder="" value=""  >
		                            </div>
		                        </div>
                            <div class="form-group row" >
									<label class="col-md-12 label-control" for="projectinput5">Option one</label>
                  <div class="col-md-1">
                  <input type="radio" class="form-check-input"  name="optradio" value="1">
                  </div>
									<div class="col-md-11">
		                            	<input type="text" id="projectinput5" class="form-control form-data-input" placeholder="" value=""  >
		                            </div>
		                        </div>
                                <div class="form-group row" >
                                <label class="col-md-12 label-control" for="projectinput5">Option two</label>
                                <div class="col-md-1">
                  <input type="radio" class="form-check-input"  name="optradio" value="2">
                  </div>         
									<div class="col-md-11">
		                            	<input type="text" id="projectinput5" class="form-control form-data-input" placeholder="" value=""  >
		                            </div>
		                        </div>
                                <div class="form-group row" >
									<label class="col-md-12 label-control" for="projectinput5">Option three</label>
                  <div class="col-md-1">
                  <input type="radio" class="form-check-input"  name="optradio" value="3">
                  </div>      
									<div class="col-md-11">
		                            	<input type="text" id="projectinput5" class="form-control form-data-input" placeholder="" value=""  >
		                            </div>
		                        </div>
                                <div class="form-group row" >
									<label class="col-md-12 label-control" for="projectinput5">Option four</label>
                  <div class="col-md-1">
                  <input type="radio" class="form-check-input"  name="optradio" value="4">
                  </div>      
									<div class="col-md-11">
		                            	<input type="text" id="projectinput5" class="form-control form-data-input" placeholder="" value=""  >
		                            </div>
		                        </div>
                            <button class="btn btn-md btn-success  core-button-color" style="margin-top:15px;" id="add-question" type="submit" name="submit">Add Question</button>    -->
</div>
                </div>
</div>
</div>
        </div>
</div>





 <!-- add content here end -->     
          </div>
       </div>
          </section>
        </div>
      </div>
    </div>



    


<?php include("../../admin_assets/footer.php");?>
<?php include_once("../../admin_assets/common-js.php");?>
<script type="text/javascript">

  <?php
  if($default){
    echo 'var table = $("#example").DataTable();
    table.column(6).visible( false );
    table.column(7).visible( false );';
  }
  
  ?>


$(document).ready(function() {
  $("#example").dataTable().fnDestroy();
    var table = $('#example').DataTable( {
      searching: false, 
      paging: false, 
      info: false,
        scrollY:        "500px",
        scrollX:        true,
        scrollCollapse: true,
        paging:         false,
        fixedColumns:   {
            leftColumns: 0,
            rightColumns: 2
        }
    } );
} );


function getFileType(file) {

if (file.type.match('image.*'))
    return 'image';

if (file.type.match('video.*'))
    return 'video';

return 'other';
}

var myform = document.querySelector('#myform');
var inputfile = document.querySelector('#file');
var bar = document.getElementsByClassName("progress-bar")[0];
var progress=document.getElementsByClassName("progress")[0];
var request = new XMLHttpRequest();
  request.upload.addEventListener('progress',function(e){
  bar.style.width=Math.round(e.loaded/e.total * 100)+'%';
  bar.innerHTML=Math.round(e.loaded/e.total * 100)+'% please wait..';

},false);

myform.addEventListener('submit',function(e){
    e.preventDefault();
    var radio =$('input[name="optradio"]:checked').val();
    if(radio==undefined){
         alert("Please select correct answer");
    }else{
      progress.style.display="block";
    $(".submit-button").css("display","none");
        var errorFlag = false;
        var errorMsg = "";
      var option_question=$(".form-data").eq(0).val();
      var option_one=$(".form-data").eq(1).val();
      var option_two=$(".form-data").eq(2).val();
      var option_three=$(".form-data").eq(3).val();
      var option_four=$(".form-data").eq(4).val();
      var setid=<?php echo $setid;?>;
	var formData = new FormData();

  if (inputfile.files.length === 0){
    var appSend = "add-question-upload.php";
  }else{

    var checkUrl = getFileType(inputfile.files[0]);
        console.log(checkUrl);

  
	var validExt = ["png", "jpg", "jpeg"];
        var extension = inputfile.files[0].name.split('.').pop();
        var extension = extension.toLowerCase();
        var extensionAllowed = false;
        if (validExt.indexOf(extension) > -1) {
            extensionAllowed = true;
        }
        if (!extensionAllowed) {
            errorFlag = true;
            errorMsg = "Unsupported file format";
        } else {
            errorFlag = false;
        }
        if (checkUrl == "video") {
            // var appSend = "upload-video.php";
            // var maxfileLimit = 5; // in MB
            // var fileSize = inputfile.files[0].size;
            // var fileSize = Math.round((fileSize / 1024));
            // if (fileSize > (maxfileLimit * 1024)) {
            //     errorFlag = true;
            //     errorMsg = "Filesize exceeed max limit " + maxfileLimit + " Mb";
            //     console.log(errorMsg);
            // }
			    errorFlag = true;
                errorMsg = "Video Files Not allowed";
        } else if (checkUrl == "image") {
            var appSend = "add-question-upload.php";
        }
  }
		if (!errorFlag) {
  formData.append('file0',inputfile.files[0]);
  formData.append('option_question',option_question);
  formData.append('option_one',option_one);
  formData.append('option_two',option_two);
  formData.append('option_three',option_three);
  formData.append('option_four',option_four);
  formData.append('answer_id',radio);
  formData.append('setid',setid);
    request.open('post',appSend,true);

	request.onreadystatechange=function(){
		if(request.readyState == 4 && request.status == 200){
      console.log(request.responseText);
      var getLast=request.responseText.slice(-4);
      console.log(getLast);
			   if(getLast == 1010){
              progress.style.display="none";
              swal({
            position: 'top-end',
            type: 'success',
            title: 'Question Updated',
            showConfirmButton: false,
            timer: 1500
        });
        setTimeout(function(){
         location.href="all-questions.php?setid="+<?php echo $setid;?>;
        },1500);
			   }else{
			        alert(request.responseText);
			   }
		}

	}
	request.send(formData);
    } else {
            swal(errorMsg, "", "error");
        }

    }
},false);


</script>
  </body>
</html>