<?php
include 'functions.php';

mysql_set_charset('utf8');
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$sub_query="";
$query_id=$_POST['query_id'];
if($query_id!='')
{
	$sub_query=" and lokaso_ask_response.query_id='$query_id' ";
}

$columns = array( 
	0 =>'', 
	1=> '',
	2=> '',
	3=> '',
	4=> '',
	5=> '',
	6=> ''
);

$query1= "SELECT lokaso_ask_response.id,lokaso_ask_response.status,lokaso_ask_response.created_date,user_id,response,mark_inappropriate,upvotes,downvotes,lokaso_user.name FROM lokaso_ask_response join lokaso_user on user_id=lokaso_user.id where 1=1 ".$sub_query;
$res1= mysql_query($query1);
$totalData = mysql_num_rows($res1);
$totalFiltered = $totalData;

$query1= "SELECT lokaso_ask_response.id,lokaso_ask_response.status,lokaso_ask_response.created_date,user_id,response,mark_inappropriate,upvotes,downvotes,lokaso_user.name FROM lokaso_ask_response join lokaso_user on user_id=lokaso_user.id where 1=1 ".$sub_query;
if( !empty($requestData['search']['value']) ) 
{
	// if there is a search parameter, $requestData['search']['value'] contains search parameter
	//$query1.=" AND ( name LIKE '%".$requestData['search']['value']."%' ";  
	//$query1.="  )";
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
{ 
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

	// preparing an array
	$nestedData=array();
	$nestedData[] = $row1['response'];
	$nestedData[] = $row1['name'];
	$nestedData[] = '<a id="status_btn_'.$row1['id'].'" class="btn btn-mini btn-success status_btn_'.$row1['id'].'" href="javascript:set_user_status('.$row1['id'].','.$status_value.');" style="'.$status_opacity.'" ><span id="status_btn_text_'.$row1['id'].'">'.$status_text.'</span></a>';	
	$nestedData[] = $row1['mark_inappropriate'];
	$nestedData[] = $row1['upvotes'];
	$nestedData[] = $row1['downvotes'];
	$nestedData[] = $created_date;
	
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
