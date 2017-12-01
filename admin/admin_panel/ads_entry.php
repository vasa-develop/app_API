<?php
include('functions.php');
require_once "../../include/constant.php";

$ad_id 	= $_POST["ad_id"];

$description 	= $_POST["description"];
$ad_type 		= $_POST["ad_type"];
$youtube_id 	= $_POST["youtube_id"];
$url 			= $_POST["image_file"];
$status 		= $_POST["status"];
$created_date 	= date("Y-m-d H:i:s");


$message = "";
$success = false;
$response = array();


// Update
if($ad_id>0) {

	if($ad_type==1) {
		$ad_type_query = " and youtube_id='$youtube_id'";
	}
	else {
		$ad_type_query = "and url='$url'";
	}

	$query="select * from lokaso_ads where id='$ad_id'";
	$result = mysql_query($query);
	if($result) {
		
		if(mysql_num_rows($result)>0) {
			
			$query0="select * from lokaso_ads where 1=1 $ad_type_query and ad_type=$ad_type and not id='$ad_id'";
			$result0 = mysql_query($query0);

			if($result0) {
				
				if(mysql_num_rows($result0)>0) {
					
					if($ad_type==1) {
						$message = "Ad with this youtube id already exists";					
					}
					else {
						$message = "Ad with this image already exists";							
					}
					$success = false;
				}
				else {
					$query1="update lokaso_ads set message='$description', status='$status', updated_date='$created_date' where id='$ad_id'";
					$result1 = mysql_query($query1);

					if($result1) {			
						$message =  "Ad has been updated";	
						$success = true;
					}
					else {
						$message =  "Failed to updtae Ad";				
						$success = false;
					}	
				}	
			}
			else {

				$message =  "Failed to update Ad ";				
				$success = false;
			}
		}
		else {
			$message = "No such Ad exists";		
			$success = false;
		}
	}
}

// Insert
else {		
	if($ad_type==1) {
		$ad_type_query = " and youtube_id='$youtube_id'";
	}
	else {
		$ad_type_query = "and url='$url'";
	}

	$query="select * from lokaso_ads where 1=1 $ad_type_query and ad_type=$ad_type";
	$result = mysql_query($query);

	if($result) {
		
		if(mysql_num_rows($result)>0) {
			
			if($ad_type==1) {
				$message = "Ad with this youtube id already exists";					
			}
			else {
				$message = "Ad with this image already exists";							
			}
			$success = false;
		}
		else {
			$query1="insert into lokaso_ads(message, ad_type, url, youtube_id, status, created_date, updated_date) 
				values ('$description', '$ad_type', '$url', '$youtube_id', '$status', '$created_date', '$created_date')";
			$result1 = mysql_query($query1);

			if($result1) {			
				$message =  "Ad has been created";	
				$success = true;
			}
			else {
				$message =  "Failed to create Ad";				
				$success = false;
			}	
		}	
	}
	else {

		$message =  "Failed to create Ad";				
		$success = false;
	}
}

$response["message"] = $message;
$response["success"] = $success;

echo json_encode($response);

mysql_close();

?>