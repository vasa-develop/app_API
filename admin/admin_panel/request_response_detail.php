<?php 
include 'header.php'; 
$query_id=$_GET['query_id'];
?>
<div class="span9">
	<div class="content">	
		<div class="module">
			<div class="module-head">
				<h3>Request Response Detail <a class="btn btn-mini btn-inverse pull-right" href="request_detail.php">Back</a></h3>
			</div>
			<!--div class="module-body" id="result_section" >
		
			</div-->
			<!--div class="module-body">	
			<form class="form-horizontal row-fluid" id="db_entry_form" method="POST" action="">
				<div class="control-group">
					<label class="control-label" for="basicinput">Status</label>
					<div class="controls">
						<select tabindex="1" data-placeholder="Select here.." class="span4"  id="status" name="status"  data_id="status"  >
							<option value="">All</option>												
							<option value="0">Pending</option>							
							<option value="1">Accepted</option>										
							<option value="2">Rejected</option>										
						</select>
						<span class="help-inline" id="error_status"></span>
					</div>
				</div>
				
				<div class="control-group">
					<div class="controls">
						<button type="button" onclick="get_brand_detail();" class="btn btn-inverse">View</button>
					</div>
				</div>
						
			</form>	
			</div>
			<hr/-->
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
							<th class="center">Respose</th>
							<th class="center">Username</th>
							<th class="center"></th>
							<th class="center">Reported</th>
							<th class="center">Up Vote</th>
							<th class="center">Down Vote</th>
							<th class="center">Created&nbsp;Time</th>
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
	get_request_detail();
});		

function get_request_detail()
{
	var query_id='<?php echo $query_id;?>';
	var dataTable = $('#data-grid').DataTable( 
	{
		"lengthMenu": [[25, 50], [ 25, 50]],
		"processing": true,
		"serverSide": true,
		"bDestroy": true,
		"bSort": false,
		"bFilter": false,
		"ajax":
		{
			url :"request_response_detail_grid_data.php", // json datasource
			type: "post",  // method  , by default get
			data:{'query_id':query_id},
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
	data: {'type':'response','id':get_id,'status_value':get_status_value},
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

