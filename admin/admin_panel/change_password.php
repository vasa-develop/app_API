<?php 
include "header.php"; 
$id=$_GET['id'];
$name='';
/*
$query1= "SELECT * FROM flauntq_main_category where id='$id'  ";
$res1= mysql_query($query1);
if(mysql_num_rows($res1)>0)
{
	$row1 = mysql_fetch_array($res1,MYSQL_ASSOC);
	$name=$row1['name'];
}
*/
?>
				<div class="span9">
					<div class="content">

						<div class="module">
							<div class="module-head">
								<h3>Change Password</h3>
							</div>
							<div class="module-body">								
									<form class="form-horizontal row-fluid" id="db_entry_form">
										<input type="hidden" name="entry_id" id="entry_id" value="<?php echo $id;?>">
										
										<div class="control-group">
											<label class="control-label" for="basicinput">Current Password</label>
											<div class="controls">
												<input type="password"  name="current_password" id="current_password" placeholder="" class="span6 required" data_id="current_password" value="">
												<span class="help-inline" id="error_current_password"></span>
											</div>
										</div>
										
										<div class="control-group">
											<label class="control-label" for="basicinput">New Password</label>
											<div class="controls">
												<input type="password"  name="new_password" id="new_password" placeholder="" class="span6 required" data_id="new_password" value="">
												<span class="help-inline" id="error_new_password"></span>
											</div>
										</div>
												
												
										
										<div class="control-group">
											<div class="controls">
												<span id="page_error"></span><br/><br/>
												<button type="button" onclick="data_save_entry('change_password_entry.php','');" class="btn btn-inverse">Submit</button>
											</div>
										</div>
									</form>
							</div>
						</div>

						
						
					</div><!--/.content-->
				</div><!--/.span9-->
<?php include "footer.php"; ?>
<script>	
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
		$("#page_error").text('');
		$.ajax({
		type:"POST",
		url: get_entry_url,
		dataType:'json',
		data:$("#db_entry_form").serialize(),
		success:function(data)
		{
			//alert(data);
			$("#page_error").html(data.query_message);
			/*if(get_exit_url!='')
			{
				window.location.href=get_exit_url;
			}*/
		}
		});
	}	

}	
</script>