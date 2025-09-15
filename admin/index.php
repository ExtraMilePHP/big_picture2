<?php


session_start();
header("Location:themes.php");
$settings=json_decode(file_get_contents("settings.js"),true)[0];
$organizationId=$_SESSION['organizationId'];
$sessionId=$_SESSION['sessionId'];
// echo $organizationId;
// echo $sessionId;

include_once '../dao/config.php';

$_SESSION["gameTitle"]=$settings["gameName"];
mysqli_set_charset( $con, 'utf8');
// echo $_SESSION['adminId']."admin_id";
// if (!$_SESSION['adminId']) {
//     header('Location:../index.php');
// } 
$tabName="Settings";


list($minutes, $seconds) = explode(":", $time);



?>

<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>

<?php include_once("../../admin_assets/common-css.php");?>
<!-- Only unique css classes -->
<style rel="stylesheet" type="text/css">
    .custom-file-input,.custom-file,.custom-file-label,.custom-file-label::after{
    height:auto !important;
    }
    form .form-actions{
      border-top: none !important;
    }

    #formatid{
      position: relative;
    /* top: 50px; */
    }
    input#file {
    height: 40px !important;
    position: relative;
    }
    div#custom_card_height

    {
      height:auto !important;
    }
    .swal2-popup.swal2-modal.swal2-show {
        /* background-image: url(img/correct.png); */
        background-color: transparent !important;
        background-position: center !important;
        background-size: 100% 100% !important;
        background-repeat: no-repeat;
        width: 490px !important;
        height: 370px !important;
    }

    button.swal2-confirm.swal2-styled{
      background-color:transparent !important;
      background-image: url(img/ok.png) !important;
        background-position: center !important;
        background-size: 100% 100% !important;
        background-repeat: no-repeat;
    }
    .swal2-popup .swal2-header,.swal2-popup .swal2-content,.swal2-popup .swal2-actions{
      top: 50px;
        position: relative;
    }


    .custom-grid {
    display: grid;
    grid-template-columns: repeat(6, 50px);
    grid-template-rows: repeat(6, 50px);
    gap: 2px;
}

.custom-grid div {
    width: 50px;
    height: 50px;
    background-color: lightgrey;
    border: 1px solid #ccc;
}

.custom-grid .highlighted {
    background-color: skyblue;
}

.custom-output {
    margin-top: 10px;
    font-size: 16px;
}

.custom-button {
    margin-top: 10px;
    padding: 5px 10px;
    font-size: 16px;
}

    
.timer-container {
        /* margin-top: 50px; */
    }

    .timer-input {
        width: 75px;
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 5px;
        margin: 0 10px;
        background-color: white;
    }

    .timer-display {
        font-size: 24px;
        margin-top: 20px;
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


		<div class="col-md-4">
			<div class="card" id="custom_card_height">
				<?php cardHeader("Custom title");?>
				<div class="card-content collapse show">
					<div class="card-body">
          <div class="row">
                        <div class="col-md-12">
                
                        <!-- <div class="col-md-12">
                          <h4 class="card-title">Add Custom Rules:</h4>
                        <div class="form-group col-12 mb-2 rules-repeater">

<div data-repeater-list="repeater-group">
  <?php 
//   for($i=0; $i<sizeof($rules); $i++){
//      echo ' <div class="input-group mb-1" data-repeater-item>
    
//      <input type="text" placeholder="Rules" name="rules" value="'.$rules[$i].'" class="form-control" id="example-ql-input" />
//      <span class="input-group-append" id="button-addon2">
//          <button class="btn btn-danger" type="button" data-repeater-delete>
//              <i class="ft-x"></i>
//          </button>
//      </span>
//  </div>';
//   }
  ?>
   
</div>

<button type="button" data-repeater-create class="btn btn-primary em-color">
    <i class="ft-plus"></i> Add new rule
</button>
<p class="text-muted"><code>* Keep 6 rules at the most <br> * At least 1 rule is required <br> * character limit of each rule is 200</code></p>
</div>

                        </div> -->

                        <div class="col-md-12 mb-2">
                          <h4 class="card-title">Add Custom Title:</h4>
                          <input type="text" id="title" maxlength="50" value="<?php echo $title;?>" class="form-control" id="basicInput" ><p style="width:100%;text-align:right;">(maximum 50 characters)</p>
<p class="text-muted"><code>* This title will reflect on the main game page</code></p>
                        </div>


<div class="col-md-12 text-center auto">
                <div class="form-container">
                <button class="btn btn-md btn-success login-button-all core-button-color" style="margin-top:15px;" id="tagButton" type="submit" name="submit">Save</button>           
                </div>
                <div class="col-md-12" style="margin-top:20px;">
                <h4 class="card-title">Timer:</h4>
                <div class="timer-container">
    <label for="minutes">Minutes:</label>
    <input type="number" id="minutes" class="timer-input" min="1" value="<?php echo $minutes;?>" onchange="updateTimer()">

    <label for="seconds">Seconds:</label>
    <input type="number" id="seconds" class="timer-input" min="0" max="59" value="<?php echo $seconds;?>" onchange="updateTimer()">
</div>

<p class="timer-display" id="timer"><?php echo $minutes.":".$seconds?></p>
                </div>
                <div class="col-md-12">
                <button class="btn btn-md btn-success" style="margin-top:15px;" id="saveTime" type="submit" name="submit">Save</button>  
                </div>
            </div>
        <!-- tab content end-->
                        </div>


 
                </div>
                </div>
</div>
</div>
        </div>

        

        <!-- NEW CARD -->

        <div class="col-md-4">
			<div class="card" id="custom_card_height">
				<?php cardHeader("Upload Image");?>
				<div class="card-content collapse show">
					<div class="card-body">
                        <div class="row">
                        
<div class="col-md-12 mb-2">
<form id="logo_upload">
                        <fieldset class="form-group">
                            <div class="custom-file">
                                <input type="file" id="file_logo" class="custom-file-input" id="inputGroupFile01" required>
                                <label class="custom-file-label" for="inputGroupFile01">Choose file</label>
                            </div>
                            <!-- <p class="text-muted"><code><br>* use CSV file only<br>* whitespaces will remove automatically</code></p> -->
                     <div class="form-actions">
                        <div class="row" style="display:block;">
                        <div class="col-md-12">
                            <p class="text-muted" id="formatid">Allowed Photo Formats - JPG,JPEG,PNG </p>
                            <!-- <p class="text-muted" id="formatid">Size of the logo should not exceed 350px</p> -->
                        </div>
                    </div>
                    <button  class="btn btn-success em-color logo-button" type="submit">
                      <i class="ft-upload-cloud"></i> Upload
                    </button>
              </div>  
                        </fieldset>
          </form>
    
</div>
<div class="col-md-12 text-center">
<div class="col-md-10 m-auto">
     <img class="img-fluid" src="../uploads/<?php echo $logo; ?>"/>
</div>

   
					</div>
                </div>
</div>
</div>
        </div>
</div>


<div class="col-md-4">
			<div class="card" id="custom_card_height">
				<?php cardHeader("Grid");?>
				<div class="card-content collapse show">
					<div class="card-body">
                        <div class="row">
                          <div class="col-md-12">
                          <div id="output" class="custom-output text-center"></div>
                          </div>
                          <div class="col-md-9 text-center m-auto">
                          <div id="grid-container" class="custom-grid">
        <!-- 6x6 grid will be generated here by JavaScript -->
    </div>
                          </div>
                          <div class="col-md-12 text-center">
                          <button class="btn btn-md btn-success core-button-color" style="margin-top:15px;" id="save_grid" type="submit" name="submit">Save</button>     
                          <button class="btn btn-md btn-info" style="margin-top:15px;" id="reset-button" type="submit" name="submit">Reset</button>  
                          </div>

    <!-- <button id="reset-button" class="custom-button" style="display:none;">Reset</button> -->
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
  var checkbol=false;
$(document).ready(function() {
    // $('.fav_clr').select2({
		// placeholder: 'colors',
		// width: '100%',
		// border: '1px solid #e4e5e7',
    // });
    
    $(".ft-rotate-cw").click(function(){
      location.reload();
    });

    $("#noofpost").change(function() {
            var seletcedval=$('option:selected', $(this)).text();
            $.ajax({ 
              type: "POST", 
              url: "events.php?events=multiplentry&action="+seletcedval, 
              data: "", 
              success: function(result) { 
                console.log(result);
                      if(result=="true"){
                        swal({
                        title: 'Data Updated',
                        background: '#fff url(img/correct.png)'
                      });
                        // $(".swal2-popup.swal2-modal.swal2-show").css("background-image: url(img/correct.png) !important");
                        // swal('Success', 'Data Updated', '');
                        setTimeout(() => {
                          location.reload();
                        }, 1000);
                      }else{
                        swal({
                        title: result,
                        background: '#fff url(img/wrong.png)'
                      });
                        swal('Error', result, 'error');
                      }
                } 
        }); 
        });
    
});

        $('.fav_clr').on("select2:select", function (e) { 
           var data = e.params.data.text;
           console.log(data+"--------data");
           t = $(".select2").val();
           console.log(t.length+"----------t");
   

              if(data=='all'  && checkbol==false){
                console.log("all checck");
                checkbol=true;
                $(".fav_clr > option").prop("selected","selected");
              
                $(".fav_clr").trigger("change");
                $("#allopn").prop("selected", false);
                checkbol=true;
                    t = $(".select2").val();
                if(t.length>9){
                      flags=true;
                      alert("Maximum Category limit reached");
                      checkbol=false;
                      $(".fav_clr > option").prop("selected", false);
                      $(".fav_clr").trigger("change");
                  }

              }else if(data=='all' && checkbol==true){
                checkbol=false;
                console.log("all de check");
                $(".fav_clr > option").prop("selected", false);
                $(".fav_clr").trigger("change");
              }
          
      });


    function getData(){
      console.log(game_cat.val());
    }

    $('.upload-functionality').hide();
    $('#select-role').change(function(){
     var data= $(this).val();
      if(data=="error"){
        $('.upload-functionality').hide();
       }else{
       $('.upload-functionality').show();
      }
});
      $('#title').keypress(function(event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                var s = $(this).val();
                console.log(s);
                $(this).val(s + "<br>");
            }
        });

    function ValidateImg(file,checkDimensions){
        var img = new Image()
        img.src = window.URL.createObjectURL(file)
        img.onload = () => {
          console.log(img.width);
          console.log(checkDimensions);
            if(img.width =="128" && img.height =="128"){
                checkDimensions=true;
            }else{
                checkDimensions=false;
            }
        }
    }

    function getFileType(file) {
        if(file.type.match('image.*'))
          return 'image';
        if(file.type.match('video.*'))
          return 'video';
        return 'other';
    }


    var myform = document.querySelector('#logo_upload');
var inputfile_logo = document.querySelector('#file_logo');
var request = new XMLHttpRequest();
myform.addEventListener('submit',function(e){
	  e.preventDefault();
	  var formData = new FormData();
  	  formData.append('file',inputfile_logo.files[0]);
      var current_cat=$("#select-role").val();
      request.open('post','upload-images.php?&events=upload_logo',true);
        var errorFlag=false;
        var checkUrl=getFileType(inputfile_logo.files[0]);
        var validExt = ["jpg","jpeg","png"];
        var extension = inputfile_logo.files[0].name.split('.').pop();
        var extension = extension.toLowerCase();
        var extensionAllowed = false;
        var maxfileLimit=5; // in MB
        var fileSize=inputfile_logo.files[0].size;
        var fileSize = Math.round((fileSize / 1024));
        if(fileSize>(maxfileLimit*1024)){
            errorFlag=true;
            errorMsg="Filesize exceeed max limit "+maxfileLimit+" Mb";
        }
        if (validExt.indexOf(extension) > -1) {
            extensionAllowed = true;
        }
        if (!extensionAllowed) {
            errorFlag = true;
            errorMsg = "Unsupported file format";
        }
        if(checkUrl!="image") {
            errorFlag = true;
            errorMsg = "only image file allowed.";
        }
        // $(".logo-button").hide();
        console.log(errorFlag);
        if(!errorFlag){
          request.onreadystatechange=function(){
		      if(request.readyState == 4 && request.status == 200){
             if(request.responseText=="true"){
                //swal("SuccessFully Updated", "", "success");
                swal({
                title: 'Successfully Updated',
                background: '#fff url(img/correct.png)'
              });
                setTimeout(function(){
                    location.reload();
                },1000)
             }else{

              swal({
                title: request.responseText,
                background: '#fff url(img/wrong.png)'
              });
                //swal("Error", request.responseText, "error");
                $(".logo-button").show();
             }
		}
	}
	request.send(formData);
   }else{
    swal({
                title: errorMsg,
                background: '#fff url(img/wrong.png)'
              });
      //swal(errorMsg,"","error");
    }
},false);

     var format = /[!@#$%^&*()_+\=\[\]{};':"\\|,.<>\/?]+/;
    //  var format = / ^[A-Za-z0-9 ]+$ /;
    // var format = /^[A-Za-z\s]*$/;
    var custom_cat=<?php echo json_encode($custom_cat); ?>;
    var default_cat=<?php echo json_encode($settings["defaults"]["default_cat"]); ?>;


    $(".login-button").click(function(){
        var t;
        t = $(".select2").val();
        var new_cat=$("#new_cat").val();
        new_cat=new_cat.toUpperCase();
        new_cat=new_cat.trim();
        var flags=false;
        if(new_cat!=""){
          if(format.test(new_cat)){
               flags=true;
               alert("please avoid special character");
           } else {

               if(t.length>9){
                flags=true;
                 alert("Maximum Category limit reached");
               }else{
                 if(new_cat.length>20){
                  flags=true;
                   alert("Maximum Category length reached");
                 }else{
                   for(var i=0; i<custom_cat.length; i++){
                     if(new_cat==custom_cat[i]){
                      flags=true;
                     }
                   }
                   for(var i=0; i<default_cat.length; i++){
                     if(new_cat==default_cat[i]){
                      flags=true;
                     }
                   }
                   if(flags){
                    alert("category already exist");
                   }else{
                   //$('.fav_clr').val(new_cat).trigger("change");
                  flags=false;
                     custom_cat.push(new_cat);
                    //alert("new_cat---"+new_cat);
                    //$('#default-multiple [value='+new_cat+']').attr('selected', 'true');
                    //$('select[name^="checkcat"] option[value='+new_cat+']').attr("selected","selected");
                    // $("#new_cat").prop("selected", false);
                    
                    }
                 }
               }
          }
        }
        if(!flags){
          $.ajax({ 
              type: "POST", 
              url: "events.php?events=add_cat", 
              data: {variables_array : t,"custom_cat":custom_cat}, 
              success: function(result) { 
                      if(result=="true"){
                        swal({
                        title: 'Data Updated',
                        background: '#fff url(img/correct.png)'
                      });
                        // $(".swal2-popup.swal2-modal.swal2-show").css("background-image: url(img/correct.png) !important");
                        // swal('Success', 'Data Updated', '');
                        setTimeout(() => {
                          location.reload();
                        }, 1000);
                      }else{
                        swal({
                        title: result,
                        background: '#fff url(img/wrong.png)'
                      });
                        // swal('Error', result, 'error');
                      }
                } 
        });  
        } 
})


$('.rules-repeater').repeater({
  show: function () {

if( $(this).parents(".rules-repeater").find("div[data-repeater-item]").length <= 6 ){
$(this).slideDown();
} else {
$(this).remove();
}
}
});

var rules=[];
function getValues(){
  $('input[name*="rules"]').each(function(e)
{
      rules.push($(this).val()); 
});
}

$(".login-button-all").click(function(){
      var title=$("#title").val();
      $.ajax({ 
       type: "POST", 
       url: "events.php?events=add_rules", 
       data: {"title":title}, 
       success: function(result) { 
              if(result=="true"){
                swal({
                title: 'Data Updated',
                background: '#fff url(img/correct.png)'
              });
                setTimeout(() => {
                  location.reload();
                }, 1000);
              }else{
                swal({
                title: result,
                background: '#fff url(img/wrong.png)'
              });
            }
           } 
        });  
    });


    $("#save_grid").click(function(){
      $.ajax({ 
       type: "POST", 
       url: "events.php?events=add_grid", 
       data: {"rows":finalrows,"cols":finalcols}, 
       success: function(result) { 
              if(result=="true"){
                swal({
                title: 'Data Updated',
                background: '#fff url(img/correct.png)'
              });
                setTimeout(() => {
                  location.reload();
                }, 1000);
              }else{
                swal({
                title: result,
                background: '#fff url(img/wrong.png)'
              });
            }
           } 
        });  
    })

    let finalrows=3;
    let finalcols=3;


    document.addEventListener('DOMContentLoaded', function() {
    const gridContainer = document.getElementById('grid-container');
    const output = document.getElementById('output');
    const resetButton = document.getElementById('reset-button');

    const rows = 6;
    const cols = 6;
    let isFinalized = false;  // Flag to track if the selection is finalized

    // Generate grid
    for (let i = 0; i < rows; i++) {
        for (let j = 0; j < cols; j++) {
            const cell = document.createElement('div');
            cell.dataset.row = i;
            cell.dataset.col = j;
            gridContainer.appendChild(cell);
        }
    }

    const startRow = 0;
    const startCol = 0;
    let selectedRows = 0;
    let selectedCols = 0;

    function highlightGrid(endRow, endCol) {
        clearHighlight();
        selectedRows = endRow - startRow + 1;
        selectedCols = endCol - startCol + 1;
        output.textContent = `${selectedRows}x${selectedCols}`;
        for (let i = startRow; i <= endRow; i++) {
            for (let j = startCol; j <= endCol; j++) {
                const cell = document.querySelector(`div[data-row='${i}'][data-col='${j}']`);
                if (cell) {
                    cell.classList.add('highlighted');
                }
            }
        }
    }

    function clearHighlight() {
        document.querySelectorAll('.highlighted').forEach(cell => {
            cell.classList.remove('highlighted');
        });
    }

    gridContainer.addEventListener('mouseover', (event) => {
        if (isFinalized) return;  // Prevent changes if selection is finalized
        if (event.target.dataset.row && event.target.dataset.col) {
            const endRow = parseInt(event.target.dataset.row);
            const endCol = parseInt(event.target.dataset.col);
            highlightGrid(endRow, endCol);
        }
    });

    gridContainer.addEventListener('click', () => {
        if (selectedRows > 0 && selectedCols > 0 && !isFinalized) {
            finalrows=selectedRows;
            finalcols=selectedCols;
            isFinalized = true;  // Finalize the selection
        }
    });

    resetButton.addEventListener('click', () => {
        clearHighlight();
        output.textContent = '';
        isFinalized = false;  // Reset the finalized flag
    });

    // Highlight 4x4 grid by default
    highlightGrid(<?php echo $getRows-1;?>, <?php echo $getCols-1;?>);
});

let finalTimer="00:00";
function updateTimer() {
    var minutes = parseInt(document.getElementById('minutes').value);
    var seconds = parseInt(document.getElementById('seconds').value);

    // Ensure minutes are at least 1
    if (minutes < 1) {
        minutes = 1;
        document.getElementById('minutes').value = 1;
    }

    // Ensure seconds are between 0 and 59
    if (seconds < 0) {
        seconds = 0;
        document.getElementById('seconds').value = 0;
    } else if (seconds > 59) {
        seconds = 59;
        document.getElementById('seconds').value = 59;
    }

    // Convert minutes and seconds to strings with leading zeros if necessary
    var formattedMinutes = minutes < 10 ? "0" + minutes : minutes;
    var formattedSeconds = seconds < 10 ? "0" + seconds : seconds;

    // Update the displayed timer
    document.getElementById('timer').innerText = formattedMinutes + ":" + formattedSeconds;
}

$("#saveTime").click(function(){
  let saveTime=$("#timer").html();
  $.ajax({ 
       type: "POST", 
       url: "events.php?events=add_timer", 
       data: {"time":saveTime}, 
       success: function(result) { 
              if(result=="true"){
                swal({
                title: 'Data Updated',
                background: '#fff url(img/correct.png)'
              });
                setTimeout(() => {
                  location.reload();
                }, 1000);
              }else{
                swal({
                title: result,
                background: '#fff url(img/wrong.png)'
              });
            }
           } 
        });  
});



</script>
  </body>
</html>