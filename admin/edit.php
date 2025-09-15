<?php
ob_start();
error_reporting(E_ALL);
session_start();
$settings = json_decode(file_get_contents("settings.js"), true)[0];
$organizationId = $_SESSION['organizationId'];
$sessionId = $_SESSION['sessionId'];
include_once '../dao/config.php';
include_once '../../admin_assets/triggers.php';


if (!$_SESSION['adminId']) {
  header('Location:../index.php?save');
}

$tabName = "Edit Question";

$id = $_GET["question_id"];
$setid = $_GET["setid"];

$data = array();

$fetch = "select * from questions_round2 where id='$id'";
$fetch = execute_query( $fetch);
$fetch = mysqli_fetch_object($fetch);
$data[0] = $fetch->question_name;
$data[1] = $fetch->option_one;
$data[2] = $fetch->option_two;
$data[3] = $fetch->option_three;
$data[4] = $fetch->option_four;
$data[5] = $fetch->correct_answer;
$data[6] = $fetch->answer_id;


?>

<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

<head>

  <?php include_once("../../admin_assets/common-css.php"); ?>
  <!-- Only unique css classes -->
  <style rel="stylesheet" type="text/css">

  </style>
  <!-- Only unique css classes -->
</head>


<body class="horizontal-layout horizontal-menu 2-columns" data-open="hover" data-menu="horizontal-menu" data-color="bg-gradient-x-purple-blue" data-col="2-columns">
  <?php include_once("../../admin_assets/common-header.php"); ?>
  <div class="app-content content">
    <div class="content-wrapper">
      <div class="content-wrapper-before"></div>
      <div class="content-header row">
        <div class="content-header-left col-md-4 col-12 mb-2">
          <h3 class="content-header-title"><?php echo $tabName; ?></h3>
        </div>
      </div>
      <div class="content-body">
        <section id="basic-form-layouts">
          <div class="row match-height">
            <!-- add content here -->

            <div class="col-md-6">
              <div class="card" id="custom_card_height">
                <?php cardHeader("Edit Question"); ?>
                <div class="card-content collapse show">
                  <div class="card-body">
                    <form id="myform">
                      <div class="form-body">
                        <div class="row">
                          <div class="col-md-12">
                            <div class="form-group row">
                              <label class="col-md-3 label-control" for="projectinput5">Question</label>
                              <div class="col-md-9">
                                <input type="text" id="projectinput5" class="form-control form-data" placeholder="Email" value="<?php echo $data[0]; ?>" name="email">
                              </div>
                            </div>
                            <div class="form-group row">
                              <label class="col-md-3 label-control" for="projectinput5">Option one</label>
                              <div class="col-md-9">
                                <input type="text" id="projectinput5" class="form-control form-data" placeholder="Email" value="<?php echo $data[1]; ?>" name="email">
                              </div>
                            </div>
                            <div class="form-group row">
                              <label class="col-md-3 label-control" for="projectinput5">Option two</label>
                              <div class="col-md-9">
                                <input type="text" id="projectinput5" class="form-control form-data" placeholder="Email" value="<?php echo $data[2]; ?>" name="email">
                              </div>
                            </div>
                            <div class="form-group row">
                              <label class="col-md-3 label-control" for="projectinput5">Option three</label>
                              <div class="col-md-9">
                                <input type="text" id="projectinput5" class="form-control form-data" placeholder="Email" value="<?php echo $data[3]; ?>" name="email">
                              </div>
                            </div>
                            <div class="form-group row">
                              <label class="col-md-3 label-control" for="projectinput5">Option four</label>
                              <div class="col-md-9">
                                <input type="text" id="projectinput5" class="form-control form-data" placeholder="Email" value="<?php echo $data[4]; ?>" name="email">
                              </div>
                            </div>
                            <div class="form-group row">
                              <label class="col-md-3 label-control" for="projectinput5">Answer id</label>
                              <div class="col-md-9">
                                <input type="text" id="projectinput5" class="form-control form-data" placeholder="Email" value="<?php echo $data[6]; ?>" name="email">
                              </div>
                            </div>
                            <div class="form-group row">
                              <label class="col-md-3 label-control" for="projectinput5" accept=".png, .jpg, .jpeg">Upload Image (as hint)</label>
                              <div class="col-md-9">
                                <div class="custom-file">
                                  <input type="file" id="file" class="custom-file-input" id="inputGroupFile01">
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
                              <div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:70%">
                                70%
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="form-actions">
                          <button class="btn btn-success" type="submit">
                            <i class="ft-upload-cloud"></i> Upload
                          </button>
                        </div>
                    </form>
                    <!-- <div class="col-md-12">
                    <div class="form-group row" >
									<label class="col-md-12 label-control" for="projectinput5">Question</label>
									<div class="col-md-12">
		                            	<input type="text" id="projectinput5" class="form-control form-data" placeholder="" value="<?php echo $data[0]; ?>"  >
		                            </div>
		                        </div>
                                <div class="form-group row" >
									<label class="col-md-12 label-control" for="projectinput5">Option one</label>
									<div class="col-md-12">
		                            	<input type="text" id="projectinput5" class="form-control form-data" placeholder="" value="<?php echo $data[1]; ?>"  >
		                            </div>
		                        </div>
                                <div class="form-group row" >
									<label class="col-md-12 label-control" for="projectinput5">Option two</label>
									<div class="col-md-12">
		                            	<input type="text" id="projectinput5" class="form-control form-data" placeholder="" value="<?php echo $data[2]; ?>"  >
		                            </div>
		                        </div>
                                <div class="form-group row" >
									<label class="col-md-12 label-control" for="projectinput5">Option three</label>
									<div class="col-md-12">
		                            	<input type="text" id="projectinput5" class="form-control form-data" placeholder="" value="<?php echo $data[3]; ?>"  >
		                            </div>
		                        </div>
                                <div class="form-group row" >
									<label class="col-md-12 label-control" for="projectinput5">Option four</label>
									<div class="col-md-12">
		                            	<input type="text" id="projectinput5" class="form-control form-data" placeholder="" value="<?php echo $data[4]; ?>"  >
		                            </div>
		                        </div>
                                <div class="form-group row" >
									<label class="col-md-12 label-control" for="projectinput5">Correct Answer</label>
									<div class="col-md-12">
		                            	<input type="text" id="projectinput5" class="form-control form-data" placeholder="" value="<?php echo $data[5]; ?>"  >
		                            </div>
		                        </div>
                                <div class="form-group row" >
									<label class="col-md-12 label-control" for="projectinput5">Answer id</label>
									<div class="col-md-12">
		                            	<input type="number" id="projectinput5" class="form-control form-data" placeholder="" value="<?php echo $data[6]; ?>"  >
		                            </div>
		                        </div>
                          <button class="btn btn-md btn-success  core-button-color" style="margin-top:15px;" id="save-input-seetings" type="submit" name="submit">Save Changes</button>   
</div> -->

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






  <?php include("../../admin_assets/footer.php"); ?>
  <?php include_once("../../admin_assets/common-js.php"); ?>
  <script type="text/javascript">
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
    var progress = document.getElementsByClassName("progress")[0];
    var request = new XMLHttpRequest();
    request.upload.addEventListener('progress', function(e) {
      bar.style.width = Math.round(e.loaded / e.total * 100) + '%';
      bar.innerHTML = Math.round(e.loaded / e.total * 100) + '% please wait..';

    }, false);

    myform.addEventListener('submit', function(e) {
      e.preventDefault();
      progress.style.display = "block";
      $(".submit-button").css("display", "none");
      var errorFlag = false;
      var errorMsg = "";
      var option_question = $(".form-data").eq(0).val();
      var option_one = $(".form-data").eq(1).val();
      var option_two = $(".form-data").eq(2).val();
      var option_three = $(".form-data").eq(3).val();
      var option_four = $(".form-data").eq(4).val();
      var answer_id = $(".form-data").eq(5).val();
      var ques_id = "<?php echo $id; ?>";
      var formData = new FormData();
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
        var appSend = "edit-question.php";
      }
      if (!errorFlag) {
        formData.append('file0', inputfile.files[0]);
        formData.append('option_question', option_question);
        formData.append('option_one', option_one);
        formData.append('option_two', option_two);
        formData.append('option_three', option_three);
        formData.append('option_four', option_four);
        formData.append('answer_id', answer_id);
        formData.append('ques_id', ques_id);

        request.open('post', appSend, true);

        request.onreadystatechange = function() {
          if (request.readyState == 4 && request.status == 200) {
            console.log(request.responseText);
            var getLast = request.responseText.slice(-4);
            console.log(getLast);
            if (getLast == 1010) {
              progress.style.display = "none";
              swal({
                position: 'top-end',
                type: 'success',
                title: 'Question Updated',
                showConfirmButton: false,
                timer: 1500
              });
              setTimeout(function() {
                location.href = "all-questions.php?setid=" + <?php echo $setid; ?>;
              }, 1500);
            } else {
              alert(request.responseText + " or please try to reupload again");
            }
          }

        }
        request.send(formData);
      } else {
        swal(errorMsg, "", "error");

      }


    }, false);

    // $("#save-input-seetings").click(function(){
    //     var data=[];
    //   data[0]= $(".form-data").eq(0).val();
    //   data[1]= $(".form-data").eq(1).val();
    //   data[2]= $(".form-data").eq(2).val();
    //   data[3]= $(".form-data").eq(3).val();
    //   data[4]= $(".form-data").eq(4).val();
    //   data[5]= $(".form-data").eq(5).val();
    //   data[6]= $(".form-data").eq(6).val();
    //   console.log(data);
    //  var setid="<?php echo $setid; ?>";
    //  var ques_id="<?php echo $id; ?>";
    //  $.ajax({ 
    //        type: "POST", 
    //        url: "events.php?events=edit_question", 
    //        data: {data : data,setid:setid,ques_id:ques_id}, 
    //        success: function(result) { 
    //               if(result=="true"){
    //                 swal('Success', 'Data Updated', 'success');
    //                 setTimeout(() => {
    //                   location.href=("all-questions.php?setid="+setid);
    //                 }, 1000);
    //               }else{
    //                 swal('Error', result, 'error');
    //               }
    //         } 
    // });   
    // });
  </script>
</body>

</html>