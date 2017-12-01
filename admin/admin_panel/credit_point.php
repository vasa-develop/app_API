<?php 
include "header.php"; 
require_once "../../include/constant.php";

$credit_Signup='';
$credit_Attend_Query='';
$credit_Like_Res='';
$credit_Create_Suggestion='';
$credit_Upload_Pic='';
$credit_Like_Pic='';
$credit_Invite='';
$credit_Ask_Query='';
$credit_Follow='';
$credit_Signup_Invite_from='';
$credit_Signup_Invite_to='';

$type_array=array();

$query1= "SELECT * FROM lokaso_credit   ";
$res1= mysql_query($query1);
if(mysql_num_rows($res1)>0)
{
	while($row1 = mysql_fetch_array($res1,MYSQL_ASSOC))
	{
		$check_type=$row1['name'];
		$get_value=$row1['points'];
		switch($check_type)
		{
			case $TAG_CREDIT_SIGNUP:$credit_Signup=$get_value;break;
			case $TAG_CREDIT_ATTEND_QUERY:$credit_Attend_Query=$get_value;break;
			case $TAG_CREDIT_LIKE_RESPONSE:$credit_Like_Res=$get_value;break;
			case $TAG_CREDIT_SUGGESTION:$credit_Create_Suggestion=$get_value;break;
			case $TAG_CREDIT_SUGGESTION_PICTURE:$credit_Upload_Pic=$get_value;break;
			case $TAG_CREDIT_LIKE_PICTURE:$credit_Like_Pic=$get_value;break;
			case $TAG_CREDIT_USER_INVITE:$credit_Invite=$get_value;break;
			case $TAG_CREDIT_ASK_QUERY:$credit_Ask_Query=$get_value;break;
			case $TAG_CREDIT_FOLLOW:$credit_Follow=$get_value;break;
			case $TAG_CREDIT_SIGNUP_INVITE_FROM:$credit_Signup_Invite_from=$get_value;break;
			case $TAG_CREDIT_SIGNUP_INVITE_TO:$credit_Signup_Invite_to=$get_value;break;
			default:			
		}	
	}
}
?>
<div class="span9">
	<div class="content">

		<div class="module">
			<div class="module-head">
				<h3>Credit Point</h3>
			</div>
			<div class="module-body">				
					<form class="form-horizontal row-fluid" id="db_entry_form">
						<!--input type="hidden" name="entry_id" id="entry_id" value="<?php //echo $id;?>"-->
						<div style="width:">						
								
								<div class="control-group">
									<label class="control-label" for="basicinput">Signup Point</label>
									<div class="controls">
										<input type="text"  name="credit_Signup" id="credit_Signup" placeholder="" class="span5 required" data_id="credit_Signup" value="<?php echo $credit_Signup;?>">
										<span class="help-inline" id="error_credit_Signup"></span>
									</div>
								</div>
						
						
								<div class="control-group">
									<label class="control-label" for="basicinput">Attending Query Point</label>
									<div class="controls">
										<input type="text"  name="credit_Attend_Query" id="credit_Attend_Query" placeholder="" class="span5 required" data_id="credit_Attend_Query" value="<?php echo $credit_Attend_Query;?>">
										<span class="help-inline" id="error_credit_Attend_Query"></span>
									</div>
								</div>
						
								<div class="control-group">
									<label class="control-label" for="basicinput">Like Response Point</label>
									<div class="controls">
										<input type="text"  name="credit_Like_Res" id="credit_Like_Res" placeholder="" class="span5 required" data_id="credit_Like_Res" value="<?php echo $credit_Like_Res;?>">
										<span class="help-inline" id="error_credit_Like_Res"></span>
									</div>
								</div>
								
								<div class="control-group">
									<label class="control-label" for="basicinput">Create Tip Point</label>
									<div class="controls">
										<input type="text"  name="credit_Create_Suggestion" id="credit_Create_Suggestion" placeholder="" class="span5 required" data_id="credit_Create_Suggestion" value="<?php echo $credit_Create_Suggestion;?>">
										<span class="help-inline" id="error_credit_Create_Suggestion"></span>
									</div>
								</div>
								
								<div class="control-group">
									<label class="control-label" for="basicinput">Create Tip With Picture Point</label>
									<div class="controls">
										<input type="text"  name="credit_Upload_Pic" id="credit_Upload_Pic" placeholder="" class="span5 required" data_id="credit_Upload_Pic" value="<?php echo $credit_Upload_Pic;?>">
										<span class="help-inline" id="error_credit_Upload_Pic"></span>
									</div>
								</div>
								
								<div class="control-group">
									<label class="control-label" for="basicinput">Like Picture Point</label>
									<div class="controls">
										<input type="text"  name="credit_Like_Pic" id="credit_Like_Pic" placeholder="" class="span5 required" data_id="credit_Like_Pic" value="<?php echo $credit_Like_Pic;?>">
										<span class="help-inline" id="error_credit_Like_Pic"></span>
									</div>
								</div>
								
								<div class="control-group">
									<label class="control-label" for="basicinput">Invite Point</label>
									<div class="controls">
										<input type="text"  name="credit_Invite" id="credit_Invite" placeholder="" class="span5 required" data_id="credit_Invite" value="<?php echo $credit_Invite;?>">
										<span class="help-inline" id="error_credit_Invite"></span>
									</div>
								</div>
								
								<div class="control-group">
									<label class="control-label" for="basicinput">Ask Query Point</label>
									<div class="controls">
										<input type="text"  name="credit_Ask_Query" id="credit_Ask_Query" placeholder="" class="span5 required" data_id="credit_Ask_Query" value="<?php echo $credit_Like_Pic;?>">
										<span class="help-inline" id="error_credit_Ask_Query"></span>
									</div>
								</div>
								
								<div class="control-group">
									<label class="control-label" for="basicinput">Follow Point</label>
									<div class="controls">
										<input type="text"  name="credit_Follow" id="credit_Follow" placeholder="" class="span5 required" data_id="credit_Follow" value="<?php echo $credit_Follow;?>">
										<span class="help-inline" id="error_credit_Follow"></span>
									</div>
								</div>
								
								<div class="control-group">
									<label class="control-label" for="basicinput">Signup Invite from Point</label>
									<div class="controls">
										<input type="text"  name="credit_Signup_Invite_from" id="credit_Signup_Invite_from" placeholder="" class="span5 required" data_id="credit_Signup_Invite_from" value="<?php echo $credit_Signup_Invite_from;?>">
										<span class="help-inline" id="error_credit_Signup_Invite_from"></span>
									</div>
								</div>
								
								<div class="control-group">
									<label class="control-label" for="basicinput">Signup Invite To Point</label>
									<div class="controls">
										<input type="text"  name="credit_Signup_Invite_to" id="credit_Signup_Invite_to" placeholder="" class="span5 required" data_id="credit_Signup_Invite_to" value="<?php echo $credit_Signup_Invite_to;?>">
										<span class="help-inline" id="error_credit_Signup_Invite_to"></span>
									</div>
								</div>
								
								
								
								
								
							
						</div>
						
						<div style="clear:both;width:100%;">
							<br/><br/>
							<div class="control-group">
								<div class="controls">
									<button type="button" onclick="data_save_entry('credit_point_entry.php','');" class="btn btn-inverse">Submit</button>
								</div>
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
		//$("#error_company").text('Please wait.Loading...');
		$.ajax({
		type:"POST",
		url: get_entry_url,
		data:$("#db_entry_form").serialize(),
		success:function(data)
		{
			alert(data);
			//$("#result_section").html(data);
			/*if(get_exit_url!='')
			{
				window.location.href=get_exit_url;
			}*/
		}
		});
	}
}	
</script>