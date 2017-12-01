<?php include 'header.php'; ?>
<div class="span9">
	<div class="content">	
		<div class="module">
			<div class="module-head">
				<h3>Discovery Question Detail  <a class="btn btn-mini btn-inverse pull-right" href="question.php">New Question</a></h3>
			</div>
			<div class="module-body">	
			<form class="form-horizontal row-fluid" id="db_entry_form" method="POST" action="">
				<div class="control-group">
					<label class="control-label" for="basicinput">Interest</label>
					<div class="controls">
						<select tabindex="1" data-placeholder="Select here.." class="span4"  id="interest" name="interest"  data_id="interest"  >
							<option value="">All</option>									
						</select>
						<span class="help-inline" id="error_interest"></span>
					</div>
				</div>
				
				<div class="control-group">
					<div class="controls">
						<button type="button" onclick="get_question_detail();" class="btn btn-inverse">View</button>
					</div>
				</div>
						
			</form>	
			</div>
			<hr/>
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
							<th class="center">Question</th>
							<th class="center">Interest</th>
							<th class="center">Created&nbsp;Time</th>
							<th class="center"></th>
							<!--th class="center"></th-->
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
		get_question_detail();
		get_data("interest","interest","","get_interest");	
	});		
		
	function get_question_detail()
	{
		
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
				url :"question_detail_grid_data.php", // json datasource
				type: "post",  // method  , by default get
				data:{'interest':$("#interest").val()},
				error: function(data)
				{  
					$(".data-grid-error").html("");
					$("#data-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#data-grid_processing").css("display","none");
					
				}
			}
		});
	}

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
	
	</script>