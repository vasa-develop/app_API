<?php
$response=array();
$error = "";
$msg = "";
$image_location = "";
$image_name = "";

$dest_image_height_size = 200;
$fileElementName=$_GET['file_field_name'];
$image_file_name=$_GET['image_file_name'];
$path=$_GET['upload_path']; 

if(!empty($_FILES[$fileElementName]['error']))
{
	$error = 'Error';	
}
else if(empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] == 'none')
{
	$error = 'No file was uploaded..';
}
else 
{
	$fileParts = pathinfo($_FILES[$fileElementName]['name']); 
	$fileTypes = array('jpg','JPG','jpeg','gif','GIF','bmp','BMP','PNG','png'); // File extensions
	//chk for file type
	if(in_array($fileParts['extension'],$fileTypes)) 
	{
		$image_name=$image_file_name.'.'.$fileParts['extension'];
		
		$image_to_upload=$_FILES[$fileElementName]['tmp_name'];
		$location = $path.$image_name; 
		//move_uploaded_file($image_to_upload, $location); //for direct upload
		$sizes = getimagesize($image_to_upload);
		$aspect_ratio = $sizes[1]/$sizes[0]; 
		if ($sizes[1] <= $dest_image_height_size)
		{
			$new_width = $sizes[0];
			$new_height = $sizes[1];
		}
		else
		{
			$new_height = $dest_image_height_size;
			$new_width = abs($new_height/$aspect_ratio);
		}
		$destimg=ImageCreateTrueColor($new_width,$new_height)or die('Problem In Creating image');
		switch ($fileParts['extension'])
		{
			case 'jpeg':
				$srcimg = imagecreatefromjpeg($image_to_upload);
			break;
			case 'jpg':
				$srcimg = imagecreatefromjpeg($image_to_upload);
			break;
			case 'JPG':
				$srcimg = imagecreatefromjpeg($image_to_upload);
			break;
			case 'gif':
				$srcimg = imagecreatefromgif($image_to_upload);
			break;
			case 'GIF':
				$srcimg = imagecreatefromgif($image_to_upload);
			break;
			case 'png':
				$srcimg = imagecreatefrompng($image_to_upload);
				imagealphablending($destimg, false);
				$colorTransparent = imagecolorallocatealpha($destimg, 0, 0, 0, 0x7fff0000);
				imagefill($destimg, 0, 0, $colorTransparent);
				imagesavealpha($destimg, true);
			break;
			case 'PNG':
				$srcimg = imagecreatefrompng($image_to_upload);
				imagealphablending($destimg, false);
				$colorTransparent = imagecolorallocatealpha($destimg, 0, 0, 0, 0x7fff0000);
				imagefill($destimg, 0, 0, $colorTransparent);
				imagesavealpha($destimg, true);
			break;
			case 'bmp':
				$srcimg = imagecreatefromwbmp($image_to_upload);
			break;
			case 'BMP':
				$srcimg = imagecreatefromwbmp($image_to_upload);
			break;
			default:
				die('Error loading '.$image_to_upload.' - File type '.$fileParts['extension'].' not supported');
		}
	
		if(function_exists('imagecopyresampled'))
		{
			imagecopyresampled($destimg,$srcimg,0,0,0,0,$new_width,$new_height,ImageSX($srcimg),ImageSY($srcimg))or die('Problem In resizing');
		}
		else
		{
			Imagecopyresized($destimg,$srcimg,0,0,0,0,$new_width,$new_height,ImageSX($srcimg),ImageSY($srcimg))or die('Problem In resizing');
		}
		//ImageJPEG($destimg,$location,90)or die('Problem In saving');
		//Imagepng($destimg,$location,9)or die('Problem In saving');
	
		switch ($fileParts['extension'])
		{
			case 'png':
				Imagepng($destimg,$location,9)or die('Problem In saving');
			break;
			case 'PNG':
				Imagepng($destimg,$location,9)or die('Problem In saving');
			break;
			default:
				ImageJPEG($destimg,$location,90)or die('Problem In saving');
		}	
		imagedestroy($destimg);		
		$image_location =$location;
	//for security reason, we force to remove all uploaded file
	//@unlink($_FILES['fileToUpload']);	
	}
	else
	{
		$error ='Invalid File type.';
	}
}	

$response['error']=$error;
$response['msg']=$msg;
$response['image_name']=$image_name;
$response['image_location']=$image_location;
echo json_encode($response);
?>

