<?php
require "functions.php";
$response=array();
$id=$_POST['id'];
$youtube_id=$_POST['youtube_id'];

$query="update lokaso_user set youtube_id='$youtube_id', youtube_status='1' where id='$id' limit 1";
if(mysql_query($query))
{
	$query_status="1";
	$query_message="Success";
}
else
{
	$query_status="0";
	$query_message="Failed";
}

$response['query_status']=$query_status;
$response['query_message']=$query_message;

echo json_encode($response);

