<?php 
include "header.php"; 
$id=0;
if(isset($_GET['id']))
{
	$id=$_GET['id'];
}

$name='';
$image_file='';
$preview_section_style="display:none";
$upload_section_style="";
$preview_image_path='';
$tag='';
$tag2='';
$position='';

$query1= "SELECT * FROM  flauntq_user where id='$id'  ";
$res1= mysql_query($query1);
if(mysql_num_rows($res1)>0)
{
	$row1 = mysql_fetch_array($res1,MYSQL_ASSOC);
	$fname=$row1['fname'];
	$lname=$row1['lname'];
	$image_file=$row1['image'];
	$preview_image_path=$flaunt_image_path.$image_file;
	$image_file=ltrim($image_file,'challenges/');
	$preview_section_style="";
	$upload_section_style="display:none";	
	$tag=$row1['tags'];	
	$tag2=$row1['tags2'];	
	$position=$row1['position'];	
}
?>
				<div class="span9">
					<div class="content">

						<div class="module">
							<div class="module-head">
								<h3>Challenge</h3>
							</div>
							<div class="module-body">
								
									<form class="form-horizontal row-fluid" id="db_entry_form">
										<input type="hidden" name="entry_id" id="entry_id" value="<?php echo $id;?>">										
																				
										
										<div class="control-group">
											<label class="control-label" for="basicinput">Name</label>
											<div class="controls">
												<input type="text"  name="name" id="name" class="span6" data_id="name" placeholder=""  value="<?php echo $name;?>">
												<span class="help-inline" id="error_name"></span>
											</div>
										</div>	
										
										<div class="control-group">
											<label class="control-label" for="basicinput">Tags1</label>
											<div class="controls">
												<input type="text"  name="tag" id="tag" class="span6" data_id="tag" placeholder=""  value="<?php echo $tag;?>">
												<span class="help-inline" id="error_tag"></span>
											</div>
										</div>							
										
										
										
										<div class="control-group">
											<label class="control-label" for="basicinput">Tags2</label>
											<div class="controls">
												<input type="text"  name="tag2" id="tag2" class="span6" data_id="tag2" placeholder=""  value="<?php echo $tag2;?>">
												<span class="help-inline" id="error_tag2"></span>
											</div>
										</div>	
										
										<div class="control-group">
											<label class="control-label" for="basicinput">Position</label>
											<div class="controls">
												<input type="text"  name="position" id="position" class="span3" data_id="position" placeholder=""  value="<?php echo $position;?>">
												<span class="help-inline" id="error_position"></span>
											</div>
										</div>	
										
																				
										<style type="text/css">
										.delete { position:absolute;top:0;right:0;width:16px;height:16;background:#fff; }
										</style>
										<br/>
										<div class="control-group">
											<label class="control-label" for="basicinput">Image </label>
											<div class="controls">
												
												<div id="logo_upload_section" style="<?php echo $upload_section_style;?>">
													<input type="hidden" id="image_file" name="image_file"  value="<?php echo $image_file;?>"><div class="col-sm-6">
														<!--input  id="file_input" type="file" name="file_input"  accept="image/*" class="custom-file-input" onchange="upload_file('<?php //echo $challenge_upload_path;?>','image_file','file_input','logo_preview','logo_display_section','logo_upload_section','error_file_input','<?php //echo $default_image_path;?>');"-->
														<input  id="file_input" type="file" name="file_input"  accept="image/*" class="custom-file-input" onchange="upload_file('<?php echo $challenge_upload_path;?>','image_file','file_input','logo_preview','logo_display_section','logo_upload_section','error_file_input','<?php echo $default_image_path;?>');">
													</div>
												</div>
												
												<div id="logo_display_section" style="float:left;position:relative;<?php echo $preview_section_style;?>">
													<img id="logo_preview" width="80px" height="80px" src="<?php echo $preview_image_path;?>" >
													<img src="../images/remove_icon.png" class="delete" onclick="remove_dealer_images('logo_display_section','logo_upload_section','error_file_input','image_file')">
												</div>												
												<span class="help-inline" id="error_file_input"></span>
											</div>
										</div>		
										
										
										<div class="control-group">
											<div class="controls">
												<button type="button" onclick="data_save_entry('challenge_entry.php','challenge_detail.php');" class="btn btn-inverse">Submit</button>
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
		//$(".chosen-select").chosen();
		
		/*$( "#user" ).autocomplete(
		{
			source: 'get_user.php',
			
		});
		*/
		var level_id="<?php echo $level_id;?>";
		$("#level_id").val(level_id);
		
		var cache = {};
		$( "#user" ).autocomplete(
		{
			minLength: 1,
			source: function( request, response ) 
			{
				var term = request.term;
				if ( term in cache ) 
				{
					response( cache[ term ] );
					return;
				}

				$.getJSON( "get_user.php", request, function( data, status, xhr ) 
				{
					cache[ term ] = data;
					response( data );
				});
			}
		});

		$('.timepicker').timepicker({
			'showDuration': true,
			'timeFormat': 'H:i',
			'step':1
		});

		$('.datepicker').datepicker({
			'format': 'dd-mm-yy',
			'autoclose': true
		});
		
		
		
	});
	
	
	
	
	/*
	function get_user_by_name()
	{
		var param=$("#user").val();
		var get_entry_url="get_user.php";
		if(param=='')
		{
			$("#user_section").hide();
		}
		else
		{
			$.ajax({
			type:"POST",
			url: get_entry_url,
			data:{'param':param},
			success:function(data)
			{
				console.log(data);
				$("#user_section").html('');
				if(data!='')
				{
					$("#user_section").html(data);
					$("#user_section").show();
				}
				else
				{
					$("#user_section").hide();
				}
			}
			});
		}
	}
	
	function set_user(get_id,get_name)
	{
		//alert(get_id+get_name);
		$("#user_id").val(get_id);
		$("#user").val(get_name);
		$("#user_section").hide();
	}
	*/
		
	function remove_dealer_images(remove_get_display_section_id,remove_get_upload_section_id,get_span_id,get_file_name_id)
	{
		$('#'+remove_get_display_section_id).hide();
		$("#"+remove_get_upload_section_id).show();
		$("#"+get_span_id).text('');
		$("#"+get_file_name_id).val('');
	}
/*
	function upload_file(get_upload_path,get_image_name_field,get_file_field_name,get_display_image_id,get_display_section_id,get_upload_Section_id,get_picture_span_id)
	{
		var image_file_time=new Date().getTime();
		var image_file_name=image_file_time;
		
		var image_upload_path=get_upload_path;
		var content_type=$("#content_type").val();		
		
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
					//alert(data.error);
				}
				else
				{
					//alert('upload success. Click on save to save ur changes.'+image_file_name); // returns location of uploaded file
					$('#'+get_image_name_field).val(data.image_name);
					$("#"+get_display_section_id).show();
					$("#"+get_upload_Section_id).hide();
					$("#"+get_display_image_id).attr('src',data.image_location);
					
					$("#"+get_picture_span_id).text("Upload success. Click on save to save your changes.");
				}
			}
			$("#save_btn").attr('disabled',false);
		},
		error: function (data, status, e)
		{
			//$("#save_btn").attr('disabled',false);
			$("#"+get_picture_span_id).text('Image Upload Error.');
		}
		}
		)
		return false;
	}

	*/
		
		
	function upload_file(get_upload_path,get_image_name_field,get_file_field_name,get_display_image_id,get_display_section_id,get_upload_Section_id,get_picture_span_id)
	{
		var image_file_time=new Date().getTime();
		var image_file_name=image_file_time;	
		var data;/* = new FormData($('input[name^="file_input"]'));     
		jQuery.each($('input[name^="file_input"]')[0].files, function(i, file) {
		data.append(i, file);
		});*/
		//console.log($( '#file_input' )[0].files[0]);
		data = new FormData();
		data.append('file_input',$('#file_input' )[0].files[0]);
		data.append('image_file_name',image_file_name);
		data.append('upload_path',get_upload_path);
		data.append('file_field_name',get_file_field_name);
		console.log(data);
		
		
		jQuery.ajax(
		{
			url: 'upload_file2.php',
			data: data,
			cache: false,
			contentType: false,
			processData: false,
			type: 'POST',
			dataType:'json',
			success: function(data)
			{
				console.log(data);
				$("#"+get_picture_span_id).text(data.msg);
				if(data.status==1)
				{
					$('#'+get_image_name_field).val(data.image_name);
					$("#"+get_display_section_id).show();
					$("#"+get_upload_Section_id).hide();
					$("#"+get_display_image_id).attr('src',data.image_location);					
				}
			}
		});
		
		
	}	
		
		
	function data_save_entry(get_entry_url,get_exit_url)
	{
		var register_error=0;
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
	
	
		if(register_error==0)
		{		
			//$("#error_company").text('Please wait.Loading...');
			$.ajax({
			type:"POST",
			url: get_entry_url,
			data:$("#db_entry_form").serialize(),
			success:function(data)
			{
				//alert(data);
				//$("#result_section").html(data);
				if(get_exit_url!='')
				{
					window.location.href=get_exit_url;
				}
			}
			});
		}	
	
	}	
</script>