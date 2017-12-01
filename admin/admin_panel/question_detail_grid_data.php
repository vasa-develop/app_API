<?php
include 'functions.php';
mysql_set_charset('utf8');
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$sub_query="";
$interest=$_POST['interest'];
if($interest!='')
{
	$sub_query=" and interest_id='$interest' ";
}

$columns = array( 
	0 =>'', 
	1=> '',
	2=> '',
	3=> '',
	4=> '',
	5=> '',
	//6=> ''
);

$query1= "SELECT lokaso_question.id,lokaso_question.name as question,lokaso_question.created_date,lokaso_interest.name as interest FROM `lokaso_question` join  lokaso_interest  on interest_id=lokaso_interest.id where 1=1 ".$sub_query;
$res1= mysql_query($query1);
$totalData = mysql_num_rows($res1);
$totalFiltered = $totalData;

$query1 = "SELECT lokaso_question.id,lokaso_question.name as question,lokaso_question.created_date,lokaso_interest.name as interest FROM `lokaso_question` join  lokaso_interest  on interest_id=lokaso_interest.id WHERE 1=1 ".$sub_query;
if( !empty($requestData['search']['value']) ) 
{ 
	// if there is a search parameter, $requestData['search']['value'] contains search parameter
	$query1.=" AND ( name LIKE '%".$requestData['search']['value']."%' ";  
	$query1.="  )";
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
{  // preparing an array
	$nestedData=array();
	
	$created_date="";
	if(date('Y',strtotime($row1['created_date']))>2000)
	{												
		$created_date=date('d-m-Y H:i A',strtotime($row1['created_date']));
	}
	
	$nestedData[] = $row1['question'];
	$nestedData[] = $row1['interest'];
	$nestedData[] = $created_date;
	$nestedData[] = '<a class="btn btn-mini btn-inverse" href="question.php?id='.$row1['id'].'">Edit</a>';
	//$nestedData[] = '<a class="btn btn-mini btn-inverse" href="question.php?id='.$row1['id'].'">Delete</a>';
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
