<?php 
include 'header.php'; 
$user_id=$_GET['user_id'];
?>
<div class="span9">
	<div class="content">	
		<div class="module">
			<div class="module-head">
				<h3>Following User List <a class="btn btn-mini btn-inverse pull-right" href="user_detail.php">Back</a></h3>
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
							<th class="center">Name</th>
							<th class="center">Email</th>
							<th class="center">Credits</th>
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
	var user_id='<?php echo $user_id;?>';
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
			url :"following_user_list_detail_grid_data.php", // json datasource
			type: "post",  // method  , by default get
			data:{'user_id':user_id},
			error: function(data)
			{  
				$(".data-grid-error").html("");
				$("#data-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#data-grid_processing").css("display","none");				
			}
		}
	});
}
</script>

