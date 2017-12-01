<?php
include 'functions.php';

mysql_set_charset('utf8');
$status_query="";
$status=$_POST['status'];
if($status!='')
{
	$status_query=" and status='$status' ";
}

// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
	0 =>'Name', 
	1 =>'Email',
	2 => 'Credits',
	3 => 'Queries Raised',
	4 => 'Registration Time',
	5 => '',
	6 => '',
	7 => '',
	8 => ''
);

$query1= "SELECT * FROM lokaso_user where 1=1 ".$status_query;
$res1= mysql_query($query1);
$totalData = mysql_num_rows($res1);
$totalFiltered = $totalData;

$query1 = "SELECT * FROM lokaso_user WHERE 1=1 ".$status_query;
if( !empty($requestData['search']['value']) ) 
{   
	// if there is a search parameter, $requestData['search']['value'] contains search parameter
	$query1.=" AND ( name LIKE '%".$requestData['search']['value']."%' ";    
	//$query1.=" OR lname LIKE '%".$requestData['search']['value']."%' ";
	$query1.=" OR email LIKE '%".$requestData['search']['value']."%' )";
}
//$query=mysqli_query($conn, $sql) or die("employee-grid-data.php: get employees");
$res1= mysql_query($query1);
$totalFiltered = mysql_num_rows($res1); // when there is a search parameter then we have to modify total number filtered rows as per search result. */
$query1.="   order by created_date desc   ";
$query1.="   LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
//$query=mysqli_query($conn, $sql) or die("employee-grid-data.php: get employees");
$res1= mysql_query($query1);

$lokaso_interest_array=array();
$query40= "SELECT id,name FROM lokaso_interest ";
$res40= mysql_query($query40);
while($row40 = mysql_fetch_array($res40,MYSQL_ASSOC))
{	
	$lokaso_interest_array[$row40['id']]=$row40['name'];
}

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
	
	$follwer_count=0;
	$query41= "SELECT count(id) as follwer_count FROM lokaso_user_follow where leader='".$row1['id']."' and status=1";
	$res41= mysql_query($query41);
	$row41 = mysql_fetch_array($res41,MYSQL_ASSOC);
	$follwer_count=$row41['follwer_count'];
	
	$follwing_count=0;
	$query42= "SELECT count(id) as follwing_count FROM lokaso_user_follow where follower='".$row1['id']."'  and status=1";
	$res42= mysql_query($query42);
	$row42 = mysql_fetch_array($res42,MYSQL_ASSOC);
	$follwing_count=$row42['follwing_count'];
	
	$ask_count=0;
	$query4= "SELECT count(id) as ask_count FROM lokaso_ask where user_id='".$row1['id']."' ";
	$res4= mysql_query($query4);
	$row4 = mysql_fetch_array($res4,MYSQL_ASSOC);
	$ask_count=$row4['ask_count'];
	
	$ask_response_count=0;
	$query4= "SELECT count(id) as ask_response_count FROM lokaso_ask_response where user_id='".$row1['id']."' ";
	$res4= mysql_query($query4);
	$row4 = mysql_fetch_array($res4,MYSQL_ASSOC);
	$ask_response_count=$row4['ask_response_count'];	
	
	$user_spam_count=0;
	$query4= "SELECT count(id) as user_spam_count FROM lokaso_ask_user_spam where spam_user_id='".$row1['id']."' ";
	$res4= mysql_query($query4);
	$row4 = mysql_fetch_array($res4,MYSQL_ASSOC);
	$user_spam_count=$row4['user_spam_count'];
	
	$interest_str='';
	$query4= "SELECT * from lokaso_user_interest where user_id='".$row1['id']."' ";
	$res4= mysql_query($query4);
	while($row4 = mysql_fetch_array($res4,MYSQL_ASSOC))
	{
		$interest_str=$interest_str.$lokaso_interest_array[$row4['interest_id']].',';
	}
	
	
	$nestedData[] = $row1["name"];
	$nestedData[] = $row1["email"];
	$nestedData[] = '<a id="status_btn_'.$row1['id'].'" class="btn btn-mini btn-success status_btn_'.$row1['id'].'" href="javascript:set_user_status('.$row1['id'].','.$status_value.');" style="'.$status_opacity.'" ><span id="status_btn_text_'.$row1['id'].'">'.$status_text.'</span></a>';
	$nestedData[] = $interest_str;
	$nestedData[] = '<a class="" href="follower_user_list.php?user_id='.$row1['id'].'">'.$follwer_count.'</a>';
	$nestedData[] = '<a class="" href="following_user_list.php?user_id='.$row1['id'].'">'.$follwing_count.'</a>';
	$nestedData[] = $row1["credits"];
	$nestedData[] = $user_spam_count;
	$nestedData[] = $ask_count;
	$nestedData[] = $ask_response_count;
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
