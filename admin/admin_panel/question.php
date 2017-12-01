<?php 
include "header.php"; 
$id=0;
if(isset($_GET['id']))
{
	$id=$_GET['id'];
}

$interest_id='';
$question='';

$query1= "SELECT * FROM lokaso_question where id='$id'  ";
$res1= mysql_query($query1);
if(mysql_num_rows($res1)>0)
{
	$row1 = mysql_fetch_array($res1,MYSQL_ASSOC);
	$interest=$row1['interest_id'];
	$question=$row1['name'];
}
?>
				<div class="span9">
					<div class="content">

						<div class="module">
							<div class="module-head">
								<h3>Add Discovery Question</h3>
							</div>
							<div class="module-body">
								
									<form class="form-horizontal row-fluid" id="db_entry_form">
										<input type="hidden" name="entry_id" id="entry_id" value="<?php echo $id;?>">										
																				
										<div class="control-group">
										<label class="control-label" for="basicinput">Interest</label>
										<div class="controls">
											<select tabindex="1" data-placeholder="Select here.." class="span6 required"  id="interest" name="interest"  data_id="interest"  >
												<option value="">Select here..</option>										
											</select>
											<span class="help-inline" id="error_interest"></span>
											</div>
										</div>																			
									
										<div class="control-group">
											<label class="control-label" for="basicinput">Question Text</label>
											<div class="controls">
												<textarea  name="question" id="question" class="span6" data_id="question" placeholder="" rows=4 style="resize:none"><?php echo $question;?></textarea>
												<span class="help-inline" id="error_question"></span>
											</div>
										</div>								
										
										<div class="control-group">
											<div class="controls">
												<button type="button" onclick="data_save_entry('question_entry.php','question_detail.php');" class="btn btn-inverse">Submit</button>
											</div>
										</div>
									</form>
							</div>
						</div>

						
						
					</div><!--/.content-->
				</div><!--/.span9-->
<?php include "footer.php"; ?>
<script>
	$(document).ready(function() 
	{	
		
		/*var cache = {};
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
		*/
		get_data("interest","interest","","get_interest");		
	});
	
	function get_data(get_input,get_span,get_param,get_url)
	{
		$("#error_"+get_span).text('Please wait.Loading...');
		$.ajax({
		type:"POST",
		url: get_url+".php",
		data:{"param":get_param},
		success:function(data)
		{
			$("#"+get_input).html(data);
			$("#error_"+get_span).text('');
			
			if(get_input=="interest")
			{
				var interest="<?php echo $interest;?>";
				if(interest!='')
				{
					$("#interest").val(interest);		
				}
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