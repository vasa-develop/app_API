<?php
$response=array();
$status = "";
$msg = "";
$image_location = "";
$image_name = "";

$dest_image_height_size = 200;
$fileElementName=$_POST['file_field_name'];
$image_file_name=$_POST['image_file_name'];
$path=$_POST['upload_path']; 

$upload_time=time();

$image_name=$upload_time.basename($_FILES['file_input']["name"]);
$target_dir = $path;
$target_file = $target_dir.$upload_time.basename($_FILES['file_input']["name"]);
$uploadOk = 1;
$status = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

// Check if image file is a actual image or fake image
$check = getimagesize($_FILES['file_input']["tmp_name"]);
if($check !== false) 
{
	if($check[0]==300&&$check[1]==200)
	{
		$msg="File is an image - " . $check["mime"] . ".";			
		$uploadOk = 1;
	}
	else
	{
		$msg="File should be of (300*200) dimesion.";	
		$uploadOk = 0;
	}
} 
else 
{
	$msg="File is not an image.";
	$uploadOk = 0;
}

// Check if file already exists
if (file_exists($target_file)) 
{
    $msg="Sorry, file already exists.";
    $uploadOk = 0;
}
// Check file size
if ($_FILES['file_input']["size"] > 500000) 
{
    $msg="Sorry, your file is too large.";
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) 
{
    $msg="Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0)
{
    //echo "Sorry, your file was not uploaded.";
	$status=0;
} 
else 
{
    if (move_uploaded_file($_FILES['file_input']["tmp_name"], $target_file)) 
	{
        $msg="The file ". basename($_FILES['file_input']["name"]). " has been uploaded.";
		$status=1;
    } 
	else 
	{
        $msg="Sorry, there was an error uploading your file.".$msg;
		$status=0;
    }
	
	/*
	$fileParts = pathinfo($_FILES[$fileElementName]['name']); 
	$fileTypes = array('jpg','JPG','jpeg','gif','GIF','bmp','BMP','PNG','png'); // File extensions
	//chk for file type
	if(in_array($fileParts['extension'],$fileTypes)) 
	{
		//$image_name=$image_file_name.'.'.$fileParts['extension'];
		
		$image_to_upload=$_FILES[$fileElementName]['tmp_name'];
		
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
				Imagepng($destimg,$target_file,9)or die('Problem In saving');
			break;
			case 'PNG':
				Imagepng($destimg,$target_file,9)or die('Problem In saving');
			break;
			default:
				ImageJPEG($destimg,$target_file,90)or die('Problem In saving');
		}	
		imagedestroy($destimg);		
	//for security reason, we force to remove all uploaded file
	//@unlink($_FILES['fileToUpload']);	
	}
	else
	{
		$error ='Invalid File type.';
	}
	
	*/
	
}


/*
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
*/

$response['msg']=$msg;
$response['status']=$status;
$response['image_name']=$image_name;
$response['image_location']=$target_file;
echo json_encode($response);
?>

