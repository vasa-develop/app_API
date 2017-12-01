<?php
include 'functions.php';

mysql_set_charset('utf8');
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$user_id=$_POST['user_id'];
if($user_id!='')
{
	$user_query=" and lokaso_user_follow.leader='$user_id' ";
}

$columns = array( 
	0 =>'', 
	1=> '',
	2=> ''
);

$query1= "SELECT lokaso_user_follow.*,lokaso_user.name,lokaso_user.email,lokaso_user.credits FROM `lokaso_user_follow` join lokaso_user on lokaso_user_follow.follower=lokaso_user.id where 1=1  and lokaso_user_follow.status=1 ".$user_query;
$res1= mysql_query($query1);
$totalData = mysql_num_rows($res1);
$totalFiltered = $totalData;

$query1 = "SELECT lokaso_user_follow.*,lokaso_user.name,lokaso_user.email,lokaso_user.credits FROM `lokaso_user_follow` join lokaso_user on lokaso_user_follow.follower=lokaso_user.id where 1=1  and lokaso_user_follow.status=1 ".$user_query;
if( !empty($requestData['search']['value']) ) 
{
	// if there is a search parameter, $requestData['search']['value'] contains search parameter
	//$query1.=" AND ( name LIKE '%".$requestData['search']['value']."%' ";  
	//$query1.="  )";
}
//$query=mysqli_query($conn, $sql) or die("employee-grid-data.php: get employees");
$res1= mysql_query($query1);
$totalFiltered = mysql_num_rows($res1); // when there is a search parameter then we have to modify total number filtered rows as per search result. */
$query1.="   LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
//$query=mysqli_query($conn, $sql) or die("employee-grid-data.php: get employees");
$res1= mysql_query($query1);


$data = array();
while($row1=mysql_fetch_array($res1)) 
{ 
	// preparing an array
	$nestedData=array();
	$nestedData[] = $row1['name'];
	$nestedData[] = $row1['email'];
	$nestedData[] = $row1['credits'];
	
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
