<?php include 'header.php'; ?>
<div class="span9">
	<div class="content">	
		
		<div class="module">
			<div class="module-head">
				<h3>Users</h3>
			</div>
			<div class="module-body">	
			<form class="form-horizontal row-fluid" id="db_entry_form" method="POST" action="">
				<div class="control-group">
					<label class="control-label" for="basicinput">Status</label>
					<div class="controls">
						<select tabindex="1" data-placeholder="Select here.." class="span4"  id="status" name="status"  data_id="status"  >
							<option value="">All</option>												
							<option value="1">Active</option>							
							<option value="0">InActive</option>										
						</select>
						<span class="help-inline" id="error_status"></span>
					</div>
				</div>
				
				<div class="control-group">
					<div class="controls">
						<button type="button" onclick="get_app_user_detail();" class="btn btn-inverse">View</button>
					</div>
				</div>
						
			</form>	
			</div>
			<hr/>
			<!--div class="module-body" id="result_section" >
		
			</div-->
			<div class="module-body table" id="">
			<style>
			#data-grid 
			{
				overflow-x: scroll;
				max-width: 98%;
				display: block;
			}
			
			/*
			table#data-grid thead th
			{
				text-align:left;
				padding-left:10px;
			}
			*/						
			</style>
			<table id="data-grid"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
					<thead>
						<tr>				
							<th class="center">Name</th>
							<th class="center">Email</th>			
							<th class="center"></th>
							<th class="center">Interest</th>
							<th class="center">Follower</th>
							<th class="center">Following</th>
							<th class="center">Credit</th>
							<th class="center">Spam Reported Count</th>
							<th class="center">Queries&nbsp;Raised</th>
							<th class="center">Response&nbsp;Raised</th>
							<th class="center">Registration&nbsp;Time</th>
						</tr>
					</thead>
			</table>
			</div>
		</div>
		<br />
	</div><!--/.content-->
</div><!--/.span9-->
<?php include 'footer.php'; ?>
	
<script>
	$(document).ready(function() 
	{			
		get_app_user_detail();
	});		
		
	function get_app_user_detail()
	{
		//$("#error_company").text('Please wait.Loading...');
		/*$.ajax({
		type:"POST",
		url: "user_detail_data.php",
		//data:{'company':$("#company").val(),'batch':$("#batch").val()},
		success:function(data)
		{
			$("#result_section").html(data);
			
		}
		});*/
		var dataTable = $('#data-grid').DataTable( 
		{
			"lengthMenu": [[25, 50], [ 25, 50]],
			"processing": true,
			"serverSide": true,
			"bDestroy": true,
			"bSort": false,
			"bFilter": true,
			"language": 
			{
				searchPlaceholder: "Name/Email"
			},
			"ajax":
			{
				url :"user_detail_grid_data.php", // json datasource
				type: "post",  // method  , by default get
				data:{'status':$("#status").val()},
				error: function(data)
				{  
					$(".data-grid-error").html("");
					$("#data-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#data-grid_processing").css("display","none");
					
				}
			}
		});
	}	
	
	function set_user_status(get_id,get_status_value)
	{		
		$.ajax
		({
		type: "POST",
		url: "set_status_entry.php",
		data: {'type':'user','id':get_id,'status_value':get_status_value},
		cache: false,
		dataType:'json',
		success: function(html)
		{
			if(html.query_status==1)
			{					
				if(get_status_value==1)
				{
					$("#status_btn_"+get_id).css('opacity','1');
					$("#status_btn_text_"+get_id).text('Active');
					$("#status_btn_"+get_id).attr("href","javascript:set_user_status("+get_id+",0);");
					
				}
				else
				{
					$("#status_btn_"+get_id).css('opacity','0.3');	
					$("#status_btn_text_"+get_id).text('InActive');
					$("#status_btn_"+get_id).attr("href","javascript:set_user_status("+get_id+",1);");					
				}
				bootbox.alert(html.query_message);	
			}
		} 
		});
	}		
	
	</script>
	
