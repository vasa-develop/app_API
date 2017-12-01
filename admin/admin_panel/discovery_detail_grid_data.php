<?php
include 'functions.php';

mysql_set_charset('utf8');

$status_query="";
$status=$_POST['status'];
if($status!='')
{
	$status_query=" and lokaso_discovery.status='$status' ";
}

$sub_query2="";
$interest=$_POST['interest'];
if($interest!='')
{
	$sub_query2=" and lokaso_discovery.interest_id='$interest' ";
}

// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
	0=> 'Name', 
	1=> 'Request',
	2=> '',
	3=> 'Created Time',
	4=> 'Interest',
	5=> 'Location',
	6=> 'Liked Count',
	7=> 'Spam Count'
);

$query1= "SELECT user_id,lokaso_discovery.id,lokaso_discovery.suggestion,lokaso_discovery.image,lokaso_discovery.location,lokaso_discovery.created_date,lokaso_discovery.status,lokaso_user.name as user,lokaso_interest.name as interest FROM lokaso_discovery join lokaso_user on lokaso_discovery.user_id=lokaso_user.id  join lokaso_interest on lokaso_discovery.interest_id=lokaso_interest.id where 1=1 ".$status_query.$sub_query2;
$res1= mysql_query($query1);
$totalData = mysql_num_rows($res1);
$totalFiltered = $totalData;

$query1 = "SELECT user_id,lokaso_discovery.id,lokaso_discovery.suggestion,lokaso_discovery.image,lokaso_discovery.location,lokaso_discovery.created_date,lokaso_discovery.status,lokaso_user.name as user,lokaso_interest.name as interest FROM lokaso_discovery join lokaso_user on lokaso_discovery.user_id=lokaso_user.id  join lokaso_interest on lokaso_discovery.interest_id=lokaso_interest.id where 1=1 ".$status_query.$sub_query2;
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$query1.=" AND ( lokaso_discovery.suggestion LIKE '%".$requestData['search']['value']."%' ";    
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
	
	$query4= "SELECT count(id) as like_count FROM lokaso_discovery_fav where discovery_id='".$row1['id']."' ";
	$res4= mysql_query($query4);
	$row4 = mysql_fetch_array($res4,MYSQL_ASSOC);
	$like_count=$row4['like_count'];
	
	$query4= "SELECT count(id) as spam_count FROM lokaso_discovery_spam where suggestion_id='".$row1['id']."' ";
	$res4= mysql_query($query4);
	$row4 = mysql_fetch_array($res4,MYSQL_ASSOC);
	$spam_count=$row4['spam_count'];
	
	
	$image=$row1["image"];
	$discovery_image=$lokaso_image_path.$discovery_image_path.$row1["image"];
	
	$nestedData[] = $row1["user"];
	$nestedData[] = $row1["suggestion"];
	//$nestedData[] = '<img src="'.$discovery_image.'" style="width:auto;height:100px;"/>';
	$nestedData[] = '<a id="status_btn_'.$row1['id'].'" class="btn btn-mini btn-success status_btn_'.$row1['id'].'" href="javascript:set_user_status('.$row1['id'].','.$status_value.');" style="'.$status_opacity.'" ><span id="status_btn_text_'.$row1['id'].'">'.$status_text.'</span></a>';
	$nestedData[] = $created_date;
	$nestedData[] = $row1["interest"];
	$nestedData[] = $row1["location"];
	$nestedData[] = $like_count;
	$nestedData[] = $spam_count;
	
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
