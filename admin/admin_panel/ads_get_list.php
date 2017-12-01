<?php
include 'functions.php';

mysql_set_charset('utf8');
$sub_query="";
$status=$_POST['status'];
if($status!='')
{
	$sub_query=" and lokaso_ads.status='$status' ";
}

$sub_query2="";
$ad_type=$_POST['ad_type'];
if($ad_type!='')
{
	$sub_query2=" and lokaso_ads.ad_type='$ad_type' ";
}

$date_query='';
if($_POST['from_date']!='' && $_POST['to_date']!='')
{
	$from_date=date('Y-m-d',strtotime($_POST['from_date']));
	$to_date=date('Y-m-d',strtotime($_POST['to_date']));
	$date_query=" and lokaso_ads.created_date between '$from_date' and '$to_date' ";
}


// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$query1= "SELECT * FROM lokaso_ads where 1=1 ".$sub_query.$sub_query2.$date_query;
$res1= mysql_query($query1);
$totalData = mysql_num_rows($res1);
$totalFiltered = $totalData;

$query1= "SELECT * FROM lokaso_ads where 1=1 ".$sub_query.$sub_query2.$date_query;
if( !empty($requestData['search']['value']) ) 
{   
	$query1.=" AND ( lokaso_ads.message LIKE '%".$requestData['search']['value']."%' ";   
	$query1.=" OR lokaso_ads.youtube_id LIKE '%".$requestData['search']['value']."%' ";
	$query1.=" OR lokaso_ads.title LIKE '%".$requestData['search']['value']."%' )";
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
	
	if($row1["ad_type"]==$AD_TYPE_VIDEO) {
		$youtube_id = $row1["youtube_id"];
		$image_url = "https://img.youtube.com/vi/".$youtube_id."/mqdefault.jpg";
		$ad_type = "Video";
	}
	else {
		$youtube_id = "";
		$image_url = "../../upload/ad/".$row1["url"];
		$ad_type = "Image";
	}
	
	$nestedData[] = '<a id="edit_btn_'.$row1['id'].'" class="btn btn-mini btn-edit edit_btn_'.$row1['id'].'" href="ads_create_form.php?ad_id='.$row1['id'].'" ><span id="edit_btn_text_'.$row1['id'].'">Edit</span></a>';
	$nestedData[] = $row1["id"];
	$nestedData[] = '<img src="'.$image_url.'" alt="Image" />';
	$nestedData[] = $ad_type;
	$nestedData[] = $row1["message"];
	$nestedData[] = $youtube_id;
	$nestedData[] = '<a id="status_btn_'.$row1['id'].'" class="btn btn-mini btn-success status_btn_'.$row1['id'].'" href="javascript:set_user_status('.$row1['id'].','.$status_value.');" style="'.$status_opacity.'" ><span id="status_btn_text_'.$row1['id'].'">'.$status_text.'</span></a>';
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
