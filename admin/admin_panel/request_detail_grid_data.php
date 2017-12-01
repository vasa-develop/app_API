<?php
include 'functions.php';

mysql_set_charset('utf8');
$sub_query="";
$status=$_POST['status'];
if($status!='')
{
	$sub_query=" and lokaso_ask.status='$status' ";
}

$sub_query2="";
$interest=$_POST['interest'];
if($interest!='')
{
	$sub_query2=" and lokaso_ask.interest_id='$interest' ";
}

$date_query='';
if($_POST['from_date']!='' && $_POST['to_date']!='')
{
	$from_date=date('Y-m-d',strtotime($_POST['from_date']));
	$to_date=date('Y-m-d',strtotime($_POST['to_date']));
	$date_query=" and lokaso_ask.created_date between '$from_date' and '$to_date' ";
}


// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
	0=> 'Name', 
	1=> 'Request',
	2=> 'Valid Upto',
	3=> '',
	4=> 'Registration Time',
	5=> 'Interest',
	6=> 'Location',
	7=> ''
);

$query1= "SELECT lokaso_ask.user_id,lokaso_ask.description,lokaso_ask.valid_until,lokaso_ask.location,lokaso_ask.created_date,lokaso_ask.status,lokaso_ask.response_count,lokaso_user.name as user,lokaso_interest.name as interest FROM lokaso_ask join lokaso_user on lokaso_user.id=lokaso_ask.user_id join lokaso_interest on lokaso_ask.interest_id=lokaso_interest.id where 1=1 ".$sub_query.$sub_query2.$date_query;
$res1= mysql_query($query1);
$totalData = mysql_num_rows($res1);
$totalFiltered = $totalData;

$query1 = "SELECT lokaso_ask.id,lokaso_ask.user_id,lokaso_ask.description,lokaso_ask.valid_until,lokaso_ask.location,lokaso_ask.created_date,lokaso_ask.status,lokaso_ask.response_count,lokaso_user.name as user,lokaso_interest.name as interest FROM lokaso_ask join lokaso_user on lokaso_user.id=lokaso_ask.user_id join lokaso_interest on lokaso_ask.interest_id=lokaso_interest.id WHERE 1=1 ".$sub_query.$sub_query2.$date_query;
if( !empty($requestData['search']['value']) ) 
{   
	// if there is a search parameter, $requestData['search']['value'] contains search parameter
	$query1.=" AND ( lokaso_ask.description LIKE '%".$requestData['search']['value']."%' ";    
	$query1.=" OR lokaso_user.name LIKE '%".$requestData['search']['value']."%' ";
	$query1.=" OR lokaso_interest.name LIKE '%".$requestData['search']['value']."%' )";
}
//$query=mysqli_query($conn, $sql) or die("employee-grid-data.php: get employees");
$res1= mysql_query($query1);
$totalFiltered = mysql_num_rows($res1); // when there is a search parameter then we have to modify total number filtered rows as per search result. */
$query1.="   order by created_date desc   ";
$query1.="   LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
//$query=mysqli_query($conn, $sql) or die("employee-grid-data.php: get employees");
$res1= mysql_query($query1);


$data = array();
while($row1=mysql_fetch_array($res1)) 
{  // preparing an array
	$nestedData=array(); 
	
	$valid_date="";
	if(date('Y',strtotime($row1['valid_until']))>2000)
	{												
		$valid_date=date('d-m-Y H:i A',strtotime($row1['valid_until']));
	}
	
	$created_date="";
	if(date('Y',strtotime($row1['created_date']))>2000)
	{												
		$created_date=date('d-m-Y H:i A',strtotime($row1['created_date']));
	}
	
	if($row1['status']==1)
	{
		$status_text="Active";
		$status_opacity="opacity:1";
		$status_value=0;
	}
	else
	{
		$status_text="InActive";
		$status_opacity="opacity:0.3";
		$status_value=1;
	}
	
	
	$query11= "SELECT count(id) as total_no_count FROM lokaso_ask_response where query_id='".$row1['id']."'  ";
	$res11= mysql_query($query11);
	$row11 = mysql_fetch_array($res11,MYSQL_ASSOC);
	$total_no_count=$row11['total_no_count'];
	
	
	
	$nestedData[] = $row1["user"];
	$nestedData[] = $row1["description"];
	$nestedData[] = $valid_date;
	$nestedData[] = '<a id="status_btn_'.$row1['id'].'" class="btn btn-mini btn-success status_btn_'.$row1['id'].'" href="javascript:set_user_status('.$row1['id'].','.$status_value.');" style="'.$status_opacity.'" ><span id="status_btn_text_'.$row1['id'].'">'.$status_text.'</span></a>';
	$nestedData[] = $created_date;
	$nestedData[] = $row1["interest"];
	$nestedData[] = $row1["location"];
	$nestedData[] = '<a class="" href="request_response_detail.php?query_id='.$row1['id'].'">'.$total_no_count.'</a>';
	
	$data[] = $nestedData;
}



$json_data = 
array("draw" => intval( $requestData['draw'] ),    
"recordsTotal" => intval( $totalData ),  
"recordsFiltered" => intval( $totalFiltered ), 
"data" => $data
);

echo json_encode($json_data);  // send data as json format

?>
