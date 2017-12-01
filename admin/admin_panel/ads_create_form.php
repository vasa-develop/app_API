<?php 
include "header.php"; 
require_once "../../include/constant.php";

$ad_id = $_GET["ad_id"];

	$title="";	
	$description="";	
	$ad_type=$AD_TYPE_IMAGE;	
	$youtube_id="";	
	$url="";	
	$status="";	
	$created_date="";
	$updated_date="";
	
	$image_name="";
	$image_file="";

	$form_heading="Add New Ad";


$query1= "SELECT * FROM lokaso_ads where id='$ad_id'  ";
$res1= mysql_query($query1);
if(mysql_num_rows($res1)>0)
{
	$row1 = mysql_fetch_array($res1,MYSQL_ASSOC);
	$title=$row1['title'];	
	$description=$row1['message'];	
	$ad_type=$row1['ad_type'];	
	$youtube_id=$row1['youtube_id'];
	$url=$row1['url'];
	$status=$row1['status'];
	$created_date=$row1['created_date'];
	$updated_date=$row1['updated_date'];
	
	if($ad_type==$AD_TYPE_IMAGE) {
		$image_name=$url;		
		$image_file=$AD_PATH.$image_name;		
	}
	
	$form_heading="Edit Ad";	
	
}

?>
<div class="span9">
	<div class="content">

		<div class="module">
			<div class="module-head">
				<h3><?php echo $form_heading?><a class="btn btn-mini btn-inverse pull-right" href="ads_list_form.php">Back</a></h3>
			</div>
			<div class="module-body">
					<form class="form-horizontal row-fluid" id="db_entry_form">
						<input type="hidden" name="ad_id" id="ad_id" value="<?php echo $ad_id;?>">
						<div style="width:">
								
								<div class="control-group">
									<label class="control-label" for="basicinput">Description</label>
									<div class="controls">
										<input type="text"  name="description" id="description" placeholder="" class="span5" data_id="description" value="<?php echo $description;?>">
										<span class="help-inline" id="error_description"></span>
									</div>
								</div>
								
								<div class="control-group">
									<label class="control-label" for="basicinput">Ad Type</label>
									<div class="controls">
									
										<?php if($ad_id!='') {?>
										<select readonly tabindex="1" data-placeholder="Select here.." class="span4"  id="ad_type" name="ad_type"  data_id="ad_type">
										<?php } else { ?>
										<select tabindex="1" data-placeholder="Select here.." class="span4"  id="ad_type" name="ad_type"  data_id="ad_type">
										<?php } ?>
										
											<?php
											if($ad_type==$AD_TYPE_VIDEO)
											{
												?>
													<option value="0">Image</option>
													<option value="1" selected>Video</option>	
												<?php 														
											}
											else
											{															
												?>
													<option value="0" selected>Image</option>
													<option value="1">Video</option>	
												<?php 	
											}
											?>

											
										</select>

										<span class="help-inline" id="error_ad_type"></span>
									</div>
								</div>
								
								<div class="control-group" id="image_layout">
									<label class="control-label" for="basicinput">Image</label>
									<div class="controls">								
										<div id="logo_upload_section" style="<?php echo $upload_section_style;?>">
											<input type="hidden" id="image_file" name="image_file"  value="<?php echo $image_file;?>">
											<div class="col-sm-6">
												<input  id="file_input" type="file" name="file_input"  accept="image/*" class="custom-file-input" onchange="upload_file('<?php echo $ad_upload;?>','image_file','file_input','logo_preview','logo_display_section','logo_upload_section','error_picture','<?php echo $default_image_path;?>');">												
											</div>
										</div>
										<div id="logo_display_section" style="float:left;position:relative;<?php echo $preview_section_style;?>">
											<img id="logo_preview" width="80px" height="80px" src="<?php echo $image_file;?>" >
											<img id="delete_image" src="../images/remove_icon.png" class="delete" onclick="remove_images('logo_display_section','logo_upload_section','error_picture','file_input')">
										</div>
										<span class="help-inline" id="error_picture"></span>
									</div>
								</div>
								
								<div class="control-group" id="youtube_layout">
									<label class="control-label" for="basicinput">Youtube Id</label>
									<div class="controls">
										<input type="text"  name="youtube_id" id="youtube_id" placeholder="" class="span5 required" data_id="youtube_id" value="<?php echo $youtube_id;?>">
										<span class="help-inline" id="error_youtube_id"></span>
									</div>
								</div>
										
								<div class="control-group">
									<label class="control-label" for="basicinput">Status</label>
									<div class="controls">
										<select tabindex="1" data-placeholder="Select here.." class="span4"  id="status" name="status"  data_id="status"  >
																							
											<?php 
											if($status==1)
											{
												?>
													<option value="0">InActive</option>
													<option value="1" selected>Active</option>	
												<?php 														
											}
											else
											{															
												?>
													<option value="0" selected>InActive</option>
													<option value="1">Active</option>	
												<?php 	
											}
											?>
											
										</select>
										<span class="help-inline" id="error_status"></span>
									</div>
								</div>															
						</div>
						
						<div style="clear:both;width:100%;">
							<br/><br/>
							<div class="control-group">
								<div class="controls">
									<button id="save_btn" type="button" onclick="data_save_entry('ads_entry.php','ads_list_form.php');" class="btn btn-inverse">Save</button>
								</div>
							</div>
						</div>
						
					</form>
			</div>
		</div>
	</div><!--/.content-->
</div><!--/.span9-->
<?php include "footer.php"; ?>
<script src="../scripts/ajaxfileupload.js" type="text/javascript"></script>	
<script>	


$(document).ready(function() 
{		
	var ad_id = '<?php echo $ad_id ?>';
	var ad_type = '<?php echo $ad_type ?>';
		
	show_ad_type(ad_type);
	
	if(ad_id!='') {
		
		//document.getElementById('ad_type').disabled = true;
		document.getElementById('youtube_id').disabled = true;
		console.log('ad_type:'+ad_type);
		$('ad_type').val(''+ad_type);
			
		if(ad_type==<?php echo $AD_TYPE_VIDEO ?>) {
			$('#image_layout').hide();
			$('#youtube_layout').show();
		}
		else {			
			var ad_id = '<?php echo $ad_id ?>';
			var image_name = '<?php echo $image_name ?>';
			var image_location = '<?php echo $image_file ?>';
			var message = "";
						
			console.log('('+ad_id+') ('+image_name+') ('+image_location+') ('+message+')');
			//alert('upload success. Click on save to save ur changes.'+image_file_name); // returns location of uploaded file
			show_image('image_file','logo_display_section','logo_upload_section','logo_preview','error_picture',  image_name, image_location,message);
			
			// The delete image icon should be hidden only for edit mode
			$("#delete_image").hide();
			
		}
	}
	else {
		//document.getElementById('ad_type').disabled = false;
		document.getElementById('youtube_id').disabled = false;
		remove_images('logo_display_section','logo_upload_section','error_picture','file_input');	
	}
	
});

$(function(){
      // bind change event to select
      $('#ad_type').on('change', function () {
          var ad_type = $(this).val(); // get selected value
          show_ad_type(ad_type);
      });
    });


function show_ad_type(ad_type) {

	if(ad_type==<?php echo $AD_TYPE_VIDEO ?>) {
		$('#image_layout').hide();
		$('#youtube_layout').show();
	}
	else {
		$('#image_layout').show();
		$('#youtube_layout').hide();
	}
}

function data_save_entry(get_entry_url,get_exit_url)
{
	var register_error=0;
	/*
	$('.required').each(function(i)
	{
		if($(this).val()=='')
		{
			register_error=1;
			$("#error_"+$(this).attr('data_id')).html('<font color="red"> This Field Is Required. </font>');
			$(this).focus();
			return false;
		}
		else
		{
			$("#error_"+$(this).attr('data_id')).html('');
		}
	});
	*/
	var ad_type = $("#ad_type").val();	
	if(ad_type==<?php echo $AD_TYPE_VIDEO ?>) {
		var youtube_id = $('#youtube_id').val();
		if(youtube_id.length==0) {
			register_error=1;
			$("#error_youtube_id").html('<font color="red"> The youtube id is required. </font>');
			$("#youtube_id").focus();
		}
	}
	else {
		var image_name = $('#image_file').val();
		if(image_name.length==0) {
			register_error=1;
			$("#error_picture").html('<font color="red"> Image is required. </font>');	
		}	
		
		//$('#image_layout').show();
		//$('#youtube_layout').hide();
	}

	if(register_error==0)
	{
          var ad_type = $('#ad_type').val(); // get selected value
		console.log('ad_typeb4 :'+ad_type);
		//$("#error_company").text('Please wait.Loading...');
		$.ajax({
		type:"POST",
		url: get_entry_url,
		data:$("#db_entry_form").serialize(),
		success:function(data)
		{
			console.log(data);
			var json = $.parseJSON(data);
			var message = json.message;
			bootbox.alert(message);
			if(json.success && get_exit_url!='')
			{
				window.location.href=get_exit_url;
			}
		},
		error: function (data, status, e)
		{
					alert(e);
			//$("#save_btn").attr('disabled',false);
			//$("#"+get_picture_span_id).text('Image Upload Error.');
		}
		});
	}
}

function remove_images(remove_get_display_section_id,remove_get_upload_section_id,get_span_id,get_file_name_id)
{
	console.log('('+remove_get_display_section_id+') ('+remove_get_upload_section_id+') ('+get_span_id+') ('+get_file_name_id+')');
	$('#'+remove_get_display_section_id).hide();
	$("#"+remove_get_upload_section_id).show();
	$("#"+get_span_id).text('');
	$("#"+get_file_name_id).text('');
	$("#"+get_file_name_id).val('');
}

function show_image(get_image_name_field, get_display_section_id, get_upload_Section_id, get_display_image_id, get_picture_span_id,   image_name, image_location, message)
{
	$('#'+get_image_name_field).val(image_name);
	$("#"+get_display_section_id).show();
	$("#"+get_upload_Section_id).hide();
	$("#"+get_display_image_id).attr('src',image_location);
	
	$("#"+get_picture_span_id).text(message);
	
	$("#delete_image").show();
}

function upload_file(get_upload_path,get_image_name_field,get_file_field_name,get_display_image_id,get_display_section_id,get_upload_Section_id,get_picture_span_id, default_image_path)
	{
		var image_file_time=new Date().getTime();
		var image_file_name=image_file_time;
		
		var image_upload_path=get_upload_path;
		var content_type=$("#content_type").val();		
		
		console.log('('+image_file_time+') ('+image_file_name+') ('+image_upload_path+') ('+content_type+') ('+get_upload_Section_id+') ('+get_file_field_name+') ('+get_picture_span_id+') ('+default_image_path+')');
		
		$("#"+get_picture_span_id).show();
		$("#"+get_picture_span_id).text('Uploading your file.');
		$('#save_btn').attr('disabled',true);
		
		$.ajaxFileUpload
		(
		{
		url:'upload_file.php?image_file_name='+image_file_name+'&upload_path='+image_upload_path+'&file_field_name='+get_file_field_name,
		secureuri:false,
		fileElementId:get_file_field_name,
		dataType: 'json',
		success: function (data, status)
		{
			if(typeof(data.error) != 'undefined')
			{
				if(data.error != '')
				{
					alert(data.error);
				}
				else
				{
					var image_name = data.image_name;
					var image_location = data.image_location;
					var message = "Upload success. Click on save to save your changes.";
					//alert('upload success. Click on save to save ur changes.'+image_file_name); // returns location of uploaded file
					show_image(get_image_name_field, get_display_section_id, get_upload_Section_id, get_display_image_id, get_picture_span_id, image_name, image_location, message);
					/*
					$('#'+get_image_name_field).val(data.image_name);
					$("#"+get_display_section_id).show();
					$("#"+get_upload_Section_id).hide();
					$("#"+get_display_image_id).attr('src',data.image_location);
					
					$("#"+get_picture_span_id).text("Upload success. Click on save to save your changes.");
					*/
				}
			}
			$("#save_btn").attr('disabled',false);
		},
		error: function (data, status, e)
		{
					alert(e);
			//$("#save_btn").attr('disabled',false);
			$("#"+get_picture_span_id).text('Image Upload Error.');
		}
		}
		)
		return false;
	}		


</script>