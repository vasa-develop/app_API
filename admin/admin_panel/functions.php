<?php
//ob_start();
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING);
session_start(); //start session

include 'config.php'; //include the config.php file
include '../../include/constant.php'; //include the config.php file

//echo "hello : ".$AD_TYPE_VIDEO;

// connect to data base
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD,DB_DATABASE);
if(!$link) 
{
	die('Failed to connect to the Server: ' . mysqli_error());
}

//Select database
//$db = mysqli_select_db(DB_DATABASE);
if(!$link) 
{
	die("Unable to choose the Database");
}

//login chech function
function loggedin()
{
	if (isset($_SESSION['login_username']) || isset($_COOKIE['login_username']))
	{
		$loggedin = TRUE;
		return $loggedin;
	}
}

function escape_input_str($inp) 
{ 
	if(!empty($inp) && is_string($inp)) 
	{
        return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\0', '\n', '\r', "\'", '\"', '\Z'), $inp); 
    }
    return $inp; 
}

function generateRandomString($length) 
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) 
	{
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

date_default_timezone_set('Asia/Kolkata');
$admin_email='';
//$base_url='http://localhost/admin_panel/';
$base_url='http://localhost/app_panel/admin/admin_panel/';

$lokaso_image_path='http://localhost/app_panel/v1/';
//$lokaso_image_path='http://targetprogress.in/uploads/';
//$lokaso_image_path='http://localhost/lokaso/uploads/';
//$discovery_image_path='discovery/';
$discovery_image_path='discovery_pics/';
$default_image_path='../uploads/default.png';

$ad_upload='../../upload/ad/';

?>