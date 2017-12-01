<?php
require "functions.php";
$response=array();
$id=$_POST['id'];
$status_value=$_POST['status_value'];
$type=$_POST['type'];

if($type=='admin_like_count')
{
	$query="update flauntq_user_flaunts set admin_like_count='$status_value' where id='$id' limit 1";
}
else if($type=='admin_comment_count')
{
	$query="update flauntq_user_flaunts set admin_comment_count='$status_value' where id='$id' limit 1";
}
else
{
	$query="";
}

if(mysql_query($query))
{
	$query_status="1";
	$query_message="Success";
	
	include("update_function.php");
	updatePostScore($id);
}
else
{
	$query_status="0";
	$query_message="Failed";
}

$response['query_status']=$query_status;
$response['query_message']=$query_message;

echo json_encode($response);

