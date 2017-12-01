<?php

require_once '../include/db_connect.php';
require '../libs/Slim/Slim.php';
//require 'db.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

function authenticate( $token) {    
		include("../include/constant.php");
        if ( $token != $APP_TOKEN ) {
            $app = \Slim\Slim::getInstance();
			
			$response[$TAG_CODE] 	= 400;
			$response[$TAG_MESSAGE]	= "Bad Authentication Token";
			
            echoRespnse(201, $response);
			return false;
        }  

		return true;
		
};


/** Sign up
 * NAME
 * url - /users/signup
 * method - POST
 * params - TAG_USER_ID
 */


$app->get('/get111', function() use ($app) {

    $response = "working";
    echo "working!";
    return;

});

$app->post('/post111', function() use ($app) {

    include("../include/constant.php");
    $user_id            = $app->request->post('user_id');
    echo $user_id;
    return;

});



$app->post('/users/signup', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$user_id 				= $app->request->post('user_id');
				$name 					= $app->request->post('name');
				$fname 					= $app->request->post('fname');
				$lname 					= $app->request->post('lname');
				$email 					= $app->request->post('email');
				$password 				= $app->request->post('password');
				$image 					= $app->request->post('image');
				$num_asks 				= $app->request->post('num_asks');
				$num_responses 			= $app->request->post('num_responses');
				$about_me 				= $app->request->post('about_me');
				$profession_id 			= $app->request->post('profession_id');
				$location 				= $app->request->post('location');
				$current_lat 			= $app->request->post('current_lat');
				$current_lng 			= $app->request->post('current_lng');
				$provider 				= $app->request->post('provider');
				$facebook_id 			= $app->request->post('facebook_id');
				$notification_flag 		= $app->request->post('notification_flag');
				$credits 				= $app->request->post('credits');
				$refer_code 			= $app->request->post('refer_code');
				
				$device_id 				= $app->request->post('device_id');
				$device_os 				= $app->request->post('device_os');
				$device_name 			= $app->request->post('device_name');
				$device_os_version 		= $app->request->post('device_os_version');
				$app_version_code 		= $app->request->post('app_version_code');
				$app_version_name 		= $app->request->post('app_version_name');
				
				// Escape
				$name = escape($name);
				$location = escape($location);
				$password = escape($password);
				
				// Encrypt
				$password 		= md5($password);
								
				$response = array();
				$response[$TAG_DETAILS] = array();
				
				$created_date 	= date("Y-m-d H:i:s");
				
				$gen_refer_code = getRandomString(7);
				
				if($user_id==null) {
					$user_id = -1;
				}
				
				
				if($fname==null && $lname==null) {
					$nameArr = explode(" ", $name);
					$fname = $nameArr[0];
					$lname = $nameArr[1];
				}
								
				// First check if user present using user_id. This means that it is sign-up completion after fb login.				
				$query	= "SELECT id FROM $TABLE_USER WHERE id = '$user_id'";
				$result = mysql_query($query);
				
				if($result) {
					
					if (mysql_num_rows($result) > 0) {
							
						$row = mysql_fetch_array($result);
						$id = $row['id'];		

								
						//Check if email entered already exists
						$query2	= "SELECT id FROM $TABLE_USER WHERE email = '$email'";
						$result2 = mysql_query($query2);
						
						if($result2) {
							if (mysql_num_rows($result2) > 0) {
								
								$response[$TAG_SUCCESS] = false;
								$response[$TAG_MESSAGE] = "A user with this email already exists.";
								echoRespnse(200, $response);
								
								return;
								
							}
						}
										
						$provider_clause = "";
						// This check is if 	
						if(strpos($image, "http") !== false) {
							$provider_clause = ",`provider` = 'facebook'";							
						}
						else {
							if(!empty($image)) {
								$image = _upload_file($image, $USER_PICS);
								$provider_clause = ",`provider` = 'email'";
							}							
						}
						
						
						$query3	= "UPDATE $TABLE_USER SET 
									`facebook_id` = '$facebook_id' 
									,`image` = '$image' 
									,`email` = '$email' 
									,`fname` = '$fname' 
									,`lname` = '$lname' 
									,`current_lat` = '$current_lat' 
									,`current_lng` = '$current_lng' 
									,`location` = '$location' 
									,`about_me` = '$about_me' 
									,`profession_id` = '$profession_id'
									$provider_clause
									,`provider_facebook` = 'facebook'
									WHERE `id` = '$id'";
						$result3 = mysql_query($query3);

						if ($result3) {

																					
							updateDeviceDetails($app, $id);
							
							$detail = array();
							$detail[$TAG_ID] 				= $id;
							$detail[$TAG_NAME]   			= $row['name'];
							if ($row['provider'] == $TAG_EMAIL) {
								$detail[$TAG_IMAGE_URL]  	= $USER_PIC_PATH . $row['image'];
							} else {
								$detail[$TAG_IMAGE_URL]  	= $row['image'];
							}
							$detail[$TAG_REFER_CODE] 		= $row['refer_code'];
							
							
							// get Profile
							$profile = getProfile($id);
							$detail[$TAG_PROFILE] = $profile;
							
																												
							array_push($response[$TAG_DETAILS], $detail);
							$success = true;

							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "Facebook user updated";
							echoRespnse(200, $response);
							
							return;

						} else {
						    $response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Some error occured.";
							echoRespnse(200, $response);
							
							return;
						}
					}
				}
				
				
				//CODE
				$query	= "SELECT id FROM $TABLE_USER WHERE email = '$email'";
				$result = mysql_query($query);
				
				if($result) {
					if (mysql_num_rows($result) == 0) {
						$profile_pic = "";
						
						if ($provider == $TAG_EMAIL) {
							if(!empty($image)) {
								$profile_pic = _upload_file($image, $USER_PICS);
							}
						}
						else {
							$profile_pic = $image;
						}
						
						$query2 = "INSERT INTO $TABLE_USER (name, email, password, image, num_asks, num_responses, about_me, profession_id, location, current_lat, current_lng, provider, facebook_id, notification_flag, credits, refer_code, created_date)
						VALUES ('$name', '$email', '$password', '$profile_pic', '$num_asks', '$num_responses', '$about_me', '$profession_id', '$location', '$current_lat', '$current_lng', '$provider', '$facebook_id', '$notification_flag', '$credits', '$gen_refer_code', '$created_date')";
						$result2 = mysql_query($query2);
						
						if ($result2) {
							$detail = array();		
							$id = mysql_insert_id();
							
							updateDeviceDetails($app, $id);
							
							$detail[$TAG_ID] 		= $id;
							getCreditValue($TAG_CREDIT_SIGNUP, $id);
							$detail[$TAG_NAME] 		= $name;

							if ($provider == $TAG_EMAIL) {
								$detail[$TAG_IMAGE_URL]  	= $USER_PIC_PATH . $profile_pic;
							} else {
								$detail[$TAG_IMAGE_URL]  	= $profile_pic; //$user_image;
							}
							
							$detail[$TAG_REFER_CODE] 		= $gen_refer_code;
							
							// Give Credits for user using referral code							
							giveReferCodePoint($refer_code, $id);
							
							// Create a chat room
							createFirstChat($id);
							
							// get Profile
							$profile = getProfile($id);
							$detail[$TAG_PROFILE] = $profile;
							
							$detail[$TAG_IS_REGISTER] = true;
							
							
							array_push($response[$TAG_DETAILS], $detail);
							$success = true;

							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "New user created";		
							echoRespnse(200, $response);

						} else {
						    $response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Some error occured";
							echoRespnse(200, $response);
						}

					} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "User exists";
							$response[$TAG_QUERY] = $query;
							
							echoRespnse(200, $response);
					}
				}
				else {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured";
					echoRespnse(200, $response);
				}
			}
        });

/** Sign up with Facebook
 * NAME
 * url - /users/signupFbCheck
 * method - POST
 * params - TAG_USER_ID
 */
 $app->post('/users/signupFbCheck', function() use ($app) {
	// check for required params
	include("../include/constant.php");
	$db = new DB_CONNECT();
	
	$token = $app->request()->headers->get($TAG_TOKEN);	
	if(authenticate($token)){
		
		mysql_set_charset('utf8');
		// reading post params
		$name 			= $app->request->post('name');
		$email 			= $app->request->post('email');
		$password 		= $app->request->post('password');
		$image 			= $app->request->post('image');
		$num_asks 		= $app->request->post('num_asks');
		$num_responses 	= $app->request->post('num_responses');
		$about_me 		= $app->request->post('about_me');
		$profession_id 	= $app->request->post('profession_id');
		$location 		= $app->request->post('location');
		$current_lat 	= $app->request->post('current_lat');
		$current_lng 	= $app->request->post('current_lng');
		$provider 		= $app->request->post('provider');
		$facebook_id 	= $app->request->post('facebook_id');
		$notification_flag = $app->request->post('notification_flag');
		$credits 		= $app->request->post('credits');
		
		$refer_code 	= $app->request->post('refer_code');
						
		// Escape
		$name = escape($name);
		$location = escape($location);
		$password = escape($password);
		
		// Encrypt
		$password 		= md5($password);
		
		$response = array();
		$response[$TAG_DETAILS] = array();
		
		$created_date 	= date("Y-m-d H:i:s");
		
		$gen_refer_code = getRandomString(7);
		
		if(strlen($email)==0) {
			$email = "".$facebook_id;
			//$email = "xDswDE3eW37@hgtfqd54.org";
		}
		
		//CODE
		$query	= "SELECT * FROM $TABLE_USER WHERE email = '$email' || facebook_id='$facebook_id'";
		$result = mysql_query($query);
		
		if($result) {
			
			// No user registered with this email or facebook_id
			if (mysql_num_rows($result) == 0) {
				
				$response[$TAG_IS_NEW_USER] = true;
				
				$response[$TAG_SUCCESS] = true;
				$response[$TAG_MESSAGE] = "New Facebook User";
				echoRespnse(200, $response);
				
			} else if (mysql_num_rows($result) > 0) {
				
				$row = mysql_fetch_array($result);
				$email_user = $row['email'];
				$id = $row['id'];
				
				updateDeviceDetails($app, $id);
				
				$detail = array();
				$detail[$TAG_ID] 				= $id;
				$detail[$TAG_NAME]   			= $row['name'];
				if ($row['provider'] == $TAG_EMAIL) {
					$detail[$TAG_IMAGE_URL]  	= $USER_PIC_PATH . $row['image'];
				} else {
					$detail[$TAG_IMAGE_URL]  	= $row['image'];
				}
				$detail[$TAG_REFER_CODE] 		= $row['refer_code'];
				
				// get Profile
				$profile = getProfile($id);
				$detail[$TAG_PROFILE] = $profile;
				
				array_push($response[$TAG_DETAILS], $detail);
							
				// If email contains @, then this user already exists and has updated his details.. LOGIN USER
				if(strpos($email_user, "@")) {
					
					// If email present, then update user details and LOGIN	
					$query3	= "UPDATE $TABLE_USER SET `facebook_id` = '$facebook_id' ,`provider_facebook` = 'facebook' WHERE `email` = '$email'";
					$result3 = mysql_query($query3);
															
					$response[$TAG_SUCCESS] = true;
					$response[$TAG_MESSAGE] = "Facebook user updated";
					echoRespnse(200, $response);
				}
				// Details not updated. So update details
				else {
						
					$response[$TAG_IS_NEW_USER] = true;
					$response[$TAG_IS_NEW_USER_UPDATE] = true;

					$response[$TAG_SUCCESS] = true;
					$response[$TAG_MESSAGE] = "New Facebook User";
					echoRespnse(200, $response);
				}				
			}
		}
		else 
		{
			$response[$TAG_SUCCESS] = false;
			$response[$TAG_MESSAGE] = "Some error occured.";
			echoRespnse(200, $response);
		}
	}
});	

		
/** Sign up
 * NAME
 * url - /users/signup
 * method - POST
 * params - TAG_USER_ID
 */
$app->post('/users/signup1', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();
			
			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
				
				mysql_set_charset('utf8');
				// reading post params
				$name 			= $app->request->post('name');
				$email 			= $app->request->post('email');
				$password 		= $app->request->post('password');
				$image 			= $app->request->post('image');
				$num_asks 		= $app->request->post('num_asks');
				$num_responses 	= $app->request->post('num_responses');
				$about_me 		= $app->request->post('about_me');
				$profession_id 	= $app->request->post('profession_id');
				$location 		= $app->request->post('location');
				$current_lat 	= $app->request->post('current_lat');
				$current_lng 	= $app->request->post('current_lng');
				$provider 		= $app->request->post('provider');
				$facebook_id 	= $app->request->post('facebook_id');
				$notification_flag = $app->request->post('notification_flag');
				$credits 		= $app->request->post('credits');
				$refer_code 	= $app->request->post('refer_code');
				
				$device_id 			= $app->request->post('device_id');
				$device_os 			= $app->request->post('device_os');
				$device_name 			= $app->request->post('device_name');
				$device_os_version 			= $app->request->post('device_os_version');
				$app_version_code 			= $app->request->post('app_version_code');
				$app_version_name 			= $app->request->post('app_version_name');
				
				// Escape
				$name = escape($name);
				$location = escape($location);
				$password = escape($password);
				
				// Encrypt
				$password 		= md5($password);
				
				
				$response = array();
				$response[$TAG_DETAILS] = array();
				
				$created_date 	= date("Y-m-d H:i:s");
				
				$gen_refer_code = getRandomString(7);
				
				
				//CODE
				$query	= "SELECT id FROM $TABLE_USER WHERE email = '$email'";
				$result = mysql_query($query);
				
				if($result) {
					if (mysql_num_rows($result) == 0) {
						$profile_pic = "";
						if(!empty($image)) {
							$profile_pic = _upload_file($image, $USER_PICS);
						}
						
						$query2 = "INSERT INTO $TABLE_USER (name, email, password, image, num_asks, num_responses, about_me, profession_id, location, current_lat, current_lng, provider, facebook_id, notification_flag, credits, refer_code, created_date)
						VALUES ('$name', '$email', '$password', '$profile_pic', '$num_asks', '$num_responses', '$about_me', '$profession_id', '$location', '$current_lat', '$current_lng', '$provider', '$facebook_id', '$notification_flag', '$credits', '$gen_refer_code', '$created_date')";
						$result2 = mysql_query($query2);
						
						if ($result2) {
							$detail = array();		
							$id = mysql_insert_id();
							
							updateDeviceDetails($app, $id);
							
							$detail[$TAG_ID] 		= $id;
							getCreditValue($TAG_CREDIT_SIGNUP, $id);
							$detail[$TAG_NAME] 		= $name;

							if ($provider == $TAG_EMAIL) {
								$detail[$TAG_IMAGE_URL]  	= $USER_PIC_PATH . $profile_pic;
							} else {
								$detail[$TAG_IMAGE_URL]  	= $user_image;
							}
							
							$detail[$TAG_REFER_CODE] 		= $gen_refer_code;
							
							// Give Credits for user using referral code							
							giveReferCodePoint($refer_code, $id);
							
							// Create a chat room
							createFirstChat($id);
							
							// get Profile
							$profile = getProfile($id);
							$detail[$TAG_PROFILE] = $profile;
							
							$detail[$TAG_IS_REGISTER] = true;
							
							
							array_push($response[$TAG_DETAILS], $detail);
							$success = true;

							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "New user created";		
							echoRespnse(200, $response);

						} else {
						    $response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Some error occured";
							echoRespnse(200, $response);
						}

					} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "User exists";
							$response[$TAG_QUERY] = $query;
							
							echoRespnse(200, $response);
					}
				}
				else {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured";
					echoRespnse(200, $response);
				}
			}
        });	


		

/** DEPRECATED : Used only for old apps 
 * Sign up with Facebook
 * NAME
 * url - /users/signupFb
 * method - POST
 * params - TAG_USER_ID
 */
 $app->post('/users/signupFb', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$name 			= $app->request->post('name');
				$email 			= $app->request->post('email');
				$password 		= $app->request->post('password');
				$image 			= $app->request->post('image');
				$num_asks 		= $app->request->post('num_asks');
				$num_responses 	= $app->request->post('num_responses');
				$about_me 		= $app->request->post('about_me');
				$profession_id 	= $app->request->post('profession_id');
				$location 		= $app->request->post('location');
				$current_lat 	= $app->request->post('current_lat');
				$current_lng 	= $app->request->post('current_lng');
				$provider 		= $app->request->post('provider');
				$facebook_id 	= $app->request->post('facebook_id');
				$notification_flag = $app->request->post('notification_flag');
				$credits 		= $app->request->post('credits');
				
				
				$refer_code 	= $app->request->post('refer_code');
								
				// Escape
				$name = escape($name);
				$location = escape($location);
				$password = escape($password);
				
				// Encrypt
				$password 		= md5($password);
				
				$response = array();
				$response[$TAG_DETAILS] = array();
				
				$created_date 	= date("Y-m-d H:i:s");
				
				$gen_refer_code = getRandomString(7);
				
				if(strlen($email)==0) {
					$email = "".$facebook_id;
				}
				
				//CODE
				$query	= "SELECT * FROM $TABLE_USER WHERE email = '$email'";
				$result = mysql_query($query);
				
				if($result) {
					if (mysql_num_rows($result) == 0) {
						$query2	= "INSERT INTO $TABLE_USER (name, email, password, image, num_asks, num_responses, about_me, profession_id, location, current_lat, current_lng, provider, facebook_id, notification_flag, credits, refer_code, created_date) VALUES ('$name', '$email', '$password', '$image', '$num_asks', '$num_responses', '$about_me', '$profession_id', '$location', '$current_lat', '$current_lng', '$provider', '$facebook_id', '$notification_flag', '$credits', '$gen_refer_code', '$created_date')";
						$result2 = mysql_query($query2);

						if ($result2) {
							$detail = array();		
							$id = mysql_insert_id();
							
							updateDeviceDetails($app, $id);

							$detail[$TAG_ID] 		= $id;
							getCreditValue($TAG_CREDIT_SIGNUP, $id);
							$detail[$TAG_NAME] 		= $name;	
							
							$detail[$TAG_REFER_CODE] 		= $gen_refer_code;
							
							
							// Create a chat room
							createFirstChat($id);
							
							// get Profile
							$profile = getProfile($id);
							$detail[$TAG_PROFILE] = $profile;
														
							$detail[$TAG_IS_REGISTER] = true;
							
							
							array_push($response[$TAG_DETAILS], $detail);
							$success = true;

							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "Facebook new user saved";		
							echoRespnse(200, $response);

						} else {
						    $response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Some error occured.";
							echoRespnse(200, $response);
						}

					} else if (mysql_num_rows($result) == 1) {
						$query3	= "UPDATE $TABLE_USER SET `facebook_id` = '$facebook_id' ,`image` = '$image' WHERE `email` = '$email'";
						$result3 = mysql_query($query3);

						if ($result3) {

							$row = mysql_fetch_array($result);
														
							$id = $row['id'];
														
							updateDeviceDetails($app, $id);
							
							$detail = array();
							$detail[$TAG_ID] 				= $id;
							$detail[$TAG_NAME]   			= $row['name'];
							if ($row['provider'] == $TAG_EMAIL) {
								$detail[$TAG_IMAGE_URL]  	= $USER_PIC_PATH . $row['image'];
							} else {
								$detail[$TAG_IMAGE_URL]  	= $row['image'];
							}
							$detail[$TAG_REFER_CODE] 		= $row['refer_code'];
							
							
							// get Profile
							$profile = getProfile($id);
							$detail[$TAG_PROFILE] = $profile;
																					
							array_push($response[$TAG_DETAILS], $detail);
							$success = true;

							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "Facebook user updated";
							echoRespnse(200, $response);

						} else {
						    $response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Some error occured.";
							echoRespnse(200, $response);
						}
					}
				}
				else 
				{
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured.";
					echoRespnse(200, $response);
				}
			}
        });	

 
 


/** login
 * NAME
 * url - /users/login
 * method - POST
 * params - TAG_USER_ID
 */
$app->post('/users/login', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$email 		= $app->request->post('email');
				$password 	= $app->request->post('password');
				
				
				// Escape
				$password = escape($password);
				
				// Encrypt
				$password 		= md5($password);
							
				
			
				$response = array();
				$response[$TAG_DETAILS] = array();
				
				//CODE
				$query	= "SELECT * FROM $TABLE_USER WHERE email = '$email' AND password = '$password'";
				$result = mysql_query($query);
				
				if($result)
				{
					if (mysql_num_rows($result) > 0) {
						    while ($row = mysql_fetch_array($result)) {
							
								$detail = array();		
								
								$id = $row['id'];
								
								updateDeviceDetails($app, $id);
								
								
								$detail[$TAG_ID] 		= $id;					
								$detail[$TAG_NAME] 		= $row['name'];

								if ($row['provider'] == $TAG_EMAIL) {
									$detail[$TAG_IMAGE_URL]  	= $USER_PIC_PATH . $row['image'];
								} else {
									$detail[$TAG_IMAGE_URL]  	= $row['image'];
								}
								$detail[$TAG_REFER_CODE] 		= $row['refer_code'];
							
								// get Profile
								$profile = getProfile($id);
								$detail[$TAG_PROFILE] = $profile;
							
								array_push($response[$TAG_DETAILS], $detail);
								$success = true;
							}

							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "Successfully Signed In.";		
							echoRespnse(200, $response);

					} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Invalid username or password";
							echoRespnse(200, $response);
					}
				}
				else
				{
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Failed to sign-in.";
					echoRespnse(200, $response);
				}
			}
        });	



/** check User using email
 * NAME
 * url - /users/checkUser
 * method - POST
 * params - TAG_USER_ID
 */
$app->post('/users/checkUser', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$email 		= $app->request->post('email');
			
				$response = array();
				
				//CODE
				$query	= "SELECT * FROM $TABLE_USER WHERE email = '$email'";
				$result = mysql_query($query);
				
				if($result)
				{
					if (mysql_num_rows($result) > 0) {
						   /* while ($row = mysql_fetch_array($result)) {
							
								$detail = array();		
								
								$detail[$TAG_ID] 		= $row['id'];					
								$detail[$TAG_NAME] 		= $row['name'];

								if ($row['provider'] == $TAG_EMAIL) {
									$detail[$TAG_IMAGE_URL]  	= $USER_PIC_PATH . $row['image'];
								} else {
									$detail[$TAG_IMAGE_URL]  	= $row['image'];
								}
							
								array_push($response[$TAG_DETAILS], $detail);
								$success = true;
							}*/

							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "Successful";		
							echoRespnse(200, $response);

					} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "User not found";
							echoRespnse(200, $response);
					}
				}
				else
				{
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured.";
					echoRespnse(200, $response);
				}
			}
        });	


/** Gcm register
 * url - /gcm_register
 * method - POST
 */
$app->post('/gcm_register', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$user_id = $app->request->post('user_id');
				$gcm_token = $app->request->post('gcm_token');
				$device_id = $app->request->post('device_id');
			
				$response = array();

				$query	= "SELECT id FROM $TABLE_GCM WHERE user_id = '$user_id'";
				$result = mysql_query($query);
				
				if($result)
				{

					if (mysql_num_rows($result) == 0) {

						$query2	= "INSERT INTO $TABLE_GCM (user_id, gcm_token, device_id) VALUES ('$user_id', '$gcm_token', '$device_id')";
						$result2 = mysql_query($query2);

						if ($result2) {
							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "GCM registered successfully";
							echoRespnse(200, $response);

						} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Some error occured";
							echoRespnse(200, $response);
						}

					} else if (mysql_num_rows($result) == 1) {
						$query3	= "UPDATE $TABLE_GCM SET user_id = '$user_id', gcm_token = '$gcm_token', device_id = '$device_id' WHERE user_id = '$user_id'";
						$result3 = mysql_query($query3);

						if ($result3) {
							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "GCM updated successfully";
							echoRespnse(200, $response);

						} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Some error occured";
							echoRespnse(200, $response);
						}

					} else {
						$response[$TAG_SUCCESS] = false;
						$response[$TAG_MESSAGE] = "Some error occured";
						echoRespnse(200, $response);
					}

				} else {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured";
					echoRespnse(200, $response);
				}
			}
        });


		
/** Update Notification id
 * NAME
 * url - /users/notification
 * method - POST
 * params - TAG_USER_ID
 */
$app->post('/users/notification', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
				
				mysql_set_charset('utf8');
				// reading post params
				$user_id 			= $app->request->post('user_id');
				$notification_flag 	= $app->request->post('notification_flag');
			
				$response = array();
				
				//CODE
				$query	= "UPDATE $TABLE_USER SET `notification_flag` = '$notification_flag' WHERE id = '$user_id'";
				$result = mysql_query($query);

				if ($result) {
					$response[$TAG_SUCCESS] = true;
					$response[$TAG_MESSAGE] = "Notification flag updated";
					echoRespnse(200, $response);

				} else {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured.";
					echoRespnse(200, $response);
				}
			}
        });

/**
*  Get Settings
*/
$app->post('/getSetting', function() use ($app) {
			echo 'hello';
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();
			
			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
				
				mysql_set_charset('utf8');
				
				$user_id 			= $app->request->post('user_id');
				
				$response = array();
				$response[$TAG_DETAILS] = array();
				
				updateDeviceDetails($app, $user_id);
				
				$query	= "SELECT * FROM $TABLE_SETTING";
				$result = mysql_query($query);
				
				if($result)
				{
					if (mysql_num_rows($result) > 0) {
										
						$row = mysql_fetch_array($result);
						
						$detail = array();		
						
						$detail[$TAG_VERSION_CODE] 			= $row['version_code'];					
						$detail[$TAG_VERSION_NAME] 			= $row['version_name'];	
						$detail[$TAG_VERSION_UPDATE_TYPE] 	= $row['version_update_type'];
						
						$response[$TAG_DETAILS] =  $detail;
												
						$success = true;

						$response["q"] = $query1;
						$response[$TAG_SUCCESS] = true;
						$response[$TAG_MESSAGE] = "Successful";		
						echoRespnse(200, $response);

					} else {
						$response["q"] = $query1;
						$response[$TAG_SUCCESS] = false;
						$response[$TAG_MESSAGE] = "Settings are empty";
						echoRespnse(200, $response);
					}
				}
				else
				{
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured.";
					echoRespnse(200, $response);
				}
			}
        }); 

		
		
		
/**
*  Get Professions
*/
$app->post('/profession', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				
				
				$updated_date 		= $app->request->post('updated_date');
				
				$created_date 	= date("Y-m-d H:i:s");
				
				
				$updated_clause = "";
				
				if($updated_date!=null && strlen($updated_date)>0) {
					$updated_clause = "where updated_date>'$updated_date'";
				}
			
				$response = array();
				$response[$TAG_DETAILS] = array();
				
				//CODE
				$query	= "SELECT * FROM $TABLE_PROFESSION ".$updated_clause;
				$result = mysql_query($query);
				
				if($result)
				{
					if (mysql_num_rows($result) > 0) {
						    while ($row = mysql_fetch_array($result)) {
							
								$detail = array();		
								
								$detail[$TAG_ID] 		= $row['id'];					
								$detail[$TAG_NAME] 		= $row['name'];			
								$detail[$TAG_IMAGE_URL] 	= ""; //$INTEREST_PIC_PATH . $row['image'];
								$detail[$TAG_CREATED_DATE] 	= $row['created_date'];	
								$detail[$TAG_UPDATED_DATE] 	= $row['updated_date'];	
								$detail[$TAG_STATUS] 		= $row['status'];	
							
								array_push($response[$TAG_DETAILS], $detail);
								$success = true;
							}

							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "Successful";		
							echoRespnse(200, $response);

					} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Professions are empty";
							echoRespnse(200, $response);
					}
				}
				else
				{
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured.";
					echoRespnse(200, $response);
				}
			}
        }); 

		
		
	
/**
*  Get Interests
*/
$app->post('/interests', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
				
				$updated_date 		= $app->request->post('updated_date');
				
				$created_date 	= date("Y-m-d H:i:s");
				
				
				$updated_clause = "";
				
				if($updated_date!=null && strlen($updated_date)>0) {
					$updated_clause = "where updated_date>'$updated_date'";
				}
							
				mysql_set_charset('utf8');
			
				$response = array();
				$response[$TAG_DETAILS] = array();
				
				//$query	= "SELECT * FROM $TABLE_INTEREST order by name asc";
				$query	= "SELECT * FROM $TABLE_INTEREST  ".$updated_clause;
				$result = mysql_query($query);
				
				if($result)
				{
					if (mysql_num_rows($result) > 0) {
						    while ($row = mysql_fetch_array($result)) {
							
								$detail = array();		
								
								$detail[$TAG_ID] 			= $row['id'];					
								$detail[$TAG_NAME] 			= $row['name'];	
								$detail[$TAG_IMAGE_URL] 	= $INTEREST_PIC_PATH . $row['image'];
								$detail[$TAG_DISPLAY_ORDER] = $row['display_order'];	
								$detail[$TAG_CREATED_DATE] 	= $row['created_date'];	
								$detail[$TAG_UPDATED_DATE] 	= $row['updated_date'];	
								$detail[$TAG_STATUS] 		= $row['status'];	
							
								array_push($response[$TAG_DETAILS], $detail);
								$success = true;
							}

						
							$response["q"] = $query;
							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "Successful";		
							echoRespnse(200, $response);

					} else {
						
						
							$response["q"] = $query;
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Interests are empty";
							echoRespnse(200, $response);
					}
				}
				else
				{
						
					$response["q"] = $query;
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured.";
					echoRespnse(200, $response);
				}
			}
        }); 



/** Add or edit User Interests
 * url - /users/interests
 * method - POST
 * params - TAG_USER_ID
 */
$app->post('/users/interests', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			//$db = getDB();
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');

				$response = array();
				$response[$TAG_DETAILS] = array();

				// reading post params
				$user_id 		= $app->request->post('user_id');
				$interest_ids 	= $app->request->post('interest_ids');
			
				$response = array();
				$response[$TAG_DETAILS] = array();		

				$interest_ids = rtrim($interest_ids, ",");
				
				$interests = explode(",", $interest_ids);
				
				$success = false;
				
				// First Delete all user interests
				$query1 = "DELETE FROM $TABLE_USER_INTEREST WHERE user_id='$user_id'";
				$result1 = mysql_query($query1);
				
				// Insert user interest
				foreach($interests as $interest) {
					$query = "INSERT INTO $TABLE_USER_INTEREST (user_id, interest_id) values ('$user_id', '$interest')";
					$result = mysql_query($query);
					if($result) {
						$success = true;
					}
				}
				
				if($success) {					
						$response[$TAG_DETAILS] = getUserInterest($TAG_DETAILS, $user_id);
						$response[$TAG_SUCCESS] = true;
						$response[$TAG_MESSAGE] = "Successful";		
						echoRespnse(200, $response);
				}
				else {					
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured.";
					echoRespnse(200, $response);
				}								
			}
        });	


/** Add Queries
 * NAME
 * url - /add_ask
 * method - POST
 * params - TAG_USER_ID
 */
$app->post('/asks', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$user_id 		= $app->request->post('user_id');
				$description 	= $app->request->post('description');
				$valid_until 	= $app->request->post('valid_until');
				$lat 			= $app->request->post('lat');
				$lng 			= $app->request->post('lng');
				$location 		= $app->request->post('location');
				$interest_id 	= $app->request->post('interest_id');
				
				// Escape
				$description = escape($description);
				$location = escape($location);
								
				$created_date 	= date("Y-m-d H:i:s");
			
				$response = array();
				//$response[$TAG_DETAILS] = array();
				
				
				$isUserInactive = isUserInactive($user_id);
				if($isUserInactive[$TAG_USER_INACTIVE]) {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = $isUserInactive[$TAG_MESSAGE];
					echoRespnse(200, $response);
					return;
				}
				
				
				$query1	= "SELECT id FROM $TABLE_ASK where user_id = '$user_id' and description='$description'";
				$result1 = mysql_query($query1);
				
				if($result1)
				{
					if (mysql_num_rows($result1) == 0) {
						
						$query	= "INSERT INTO $TABLE_ASK (user_id, description, valid_until, lat, lng, location, interest_id, created_date) VALUES ('$user_id', '$description', '$valid_until', '$lat', '$lng', '$location', '$interest_id', '$created_date')";
						$result = mysql_query($query);

						if ($result) {
							
							$query_id = mysql_insert_id();
							
							
							getCreditValue($TAG_CREDIT_ASK_QUERY, $user_id);
							
							$range = $RANGE_VALUE_QUERY;
							
							$nearby_user_id_list = "";
							$notify_response = "";
																	
							$query1 = "SELECT *, (1.609344 * 3956 * acos( cos( radians($lat) ) * cos( radians(current_lat) ) * cos( radians(current_lng) - radians($lng) ) + sin( radians($lat) ) * sin( radians(current_lat) ) ) ) as distance FROM $TABLE_USER HAVING distance < '$range' 
							 and status>0";
							$result1 = mysql_query($query1);
							if($result1) {
								
								if(mysql_num_rows($result1)>0) {
																		
									while ($row = mysql_fetch_array($result1)) {
																
										$nearby_user_id 		= $row['id'];		
										$nearby_user_id_list = $nearby_user_id_list.$nearby_user_id.",";
									}

									$nearby_user_id_list = rtrim($nearby_user_id_list, ",");
																									
									// Send Notification to all users who are nearby the query.
									$notification_type = $TAG_NOTIFICATION_QUERY_NEARBY;
									$to_user_id = $nearby_user_id_list;
									$from_user_id = $user_id;
									$content_id = $query_id;
									$other_data = "Nearby Query";
									$notify_response = createNotification($to_user_id, $from_user_id, $content_id, $other_data, $notification_type);										
								}
							}
							
							//$response["notify_response"] = $notify_response;
							//$response["notify_users"] = $nearby_user_id_list;
							//$response["q"] = $query1;
							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "Query posted successfully";		
							echoRespnse(200, $response);

							
						} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Failed to ask query. Please try again.";
							echoRespnse(200, $response);
						}				
					}
					else {
						$response[$TAG_SUCCESS] = false;
						$response[$TAG_MESSAGE] = "Same Query has been already asked by you previously. You may extend its time.";
						echoRespnse(200, $response);					
					}
				}
				else {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured";
					echoRespnse(200, $response);					
				}
			}
        });




/** Spam Query
 * url - /query/spam
 * method - POST
 */
$app->post('/query/spam', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$action 	= $app->request->post('action');
				$query_id 	= $app->request->post('query_id');
				$user_id 	= $app->request->post('user_id');
				
			
				$response = array();
				
				$isUserInactive = isUserInactive($user_id);
				if($isUserInactive[$TAG_USER_INACTIVE]) {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = $isUserInactive[$TAG_MESSAGE];
					echoRespnse(200, $response);
					return;
				}
				
				$isQueryInactive = isQueryInactive($query_id);
				if($isQueryInactive[$TAG_INACTIVE]) {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = $isQueryInactive[$TAG_MESSAGE];
					echoRespnse(200, $response);
					return;
				}
				$query	= "SELECT * FROM $TABLE_ASK_SPAM WHERE query_id = '$query_id' AND user_id = '$user_id'";
				$result = mysql_query($query);				
				
				if (mysql_num_rows($result) == 0) {
					$query1	= "INSERT INTO $TABLE_ASK_SPAM (query_id, user_id) VALUES ('$query_id', '$user_id')";
					$result1 = mysql_query($query1);

					if ($result1) {
						$response[$TAG_ACTION] = getAction($query_id, 1);
						$response[$TAG_SUCCESS] = true;
						$response[$TAG_MESSAGE] = "Query spammed successfully";
						echoRespnse(200, $response);

					} else {
						$response[$TAG_SUCCESS] = false;
						$response[$TAG_MESSAGE] = "Failed to spam";
						echoRespnse(200, $response);
					}
				}
				else {
					$query2	= "DELETE FROM $TABLE_ASK_SPAM WHERE query_id = '$query_id' AND user_id = '$user_id'";
					$result2 = mysql_query($query2);

					if ($result2) {
						$response[$TAG_ACTION] = getAction($query_id, 0);
						$response[$TAG_SUCCESS] = true;
						$response[$TAG_MESSAGE] = "Query unspammed successfully";
						echoRespnse(200, $response);

					} else {
						$response[$TAG_SUCCESS] = false;
						$response[$TAG_MESSAGE] = "Failed to unspam";
						echoRespnse(200, $response);
					}
				}
			}
        });



/** Spam User
 * url - /user/spam
 * method - POST
 */
$app->post('/user/spam', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$action 		= $app->request->post('action');
				$spam_user_id 	= $app->request->post('spam_user_id');
				$user_id 		= $app->request->post('user_id');
			
				$response = array();
								
				$isUserInactive = isUserInactive($user_id);
				if($isUserInactive[$TAG_USER_INACTIVE]) {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = $isUserInactive[$TAG_MESSAGE];
					echoRespnse(200, $response);
					return;
				}
				
				$query1	= "SELECT * FROM $TABLE_ASK_USER_SPAM WHERE spam_user_id = '$spam_user_id' AND user_id = '$user_id'";
				$result1 = mysql_query($query1);				

				if (mysql_num_rows($result1) == 0) {
					$query1	= "INSERT INTO $TABLE_ASK_USER_SPAM (spam_user_id, user_id) VALUES ('$spam_user_id', '$user_id')";
					$result1 = mysql_query($query1);

					if ($result1) {
															
						$response[$TAG_ACTION] = getAction($spam_user_id, 1);
						
						$response[$TAG_SUCCESS] = true;
						$response[$TAG_MESSAGE] = "User spammed successfully";
						echoRespnse(200, $response);

					} else {
						$response[$TAG_SUCCESS] = false;
						$response[$TAG_MESSAGE] = "Some error occured";
						echoRespnse(200, $response);
					}

				} else {
					$query2	= "DELETE FROM $TABLE_ASK_USER_SPAM WHERE spam_user_id = '$spam_user_id' AND user_id = '$user_id'";
					$result2 = mysql_query($query2);

					if ($result2) {
												
						$response[$TAG_ACTION] = getAction($spam_user_id, 0);
						
						$response[$TAG_SUCCESS] = true;
						$response[$TAG_MESSAGE] = "User unspammed successfully";
						echoRespnse(200, $response);

					} else {
						$response[$TAG_SUCCESS] = false;
						$response[$TAG_MESSAGE] = "Some error occured";
						echoRespnse(200, $response);
					}
				}
			}
        });




/** Add Query Response
 * url - /query/response
 * method - POST
 */
$app->post('/query/response', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);
			if(authenticate($token)) {
				
				mysql_set_charset('utf8');
				// reading post params
				$response_comment 	= $app->request->post('response');
				$query_id 			= $app->request->post('query_id');
				$query_user_id 		= $app->request->post('query_user_id');
				$user_id 			= $app->request->post('user_id');
				
				
				// Escape
				$response_comment = escape($response_comment);
				
				$current_date		= date("Y-m-d H:i:s");
				
				$response = array();
				
				$isUserInactive = isUserInactive($user_id);
				if($isUserInactive[$TAG_USER_INACTIVE]) {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = $isUserInactive[$TAG_MESSAGE];
					echoRespnse(200, $response);
					return;
				}				

				$query_m	= "SELECT * FROM $TABLE_ASK WHERE id = '$query_id'";
				$result_m = mysql_query($query_m);

				if($result_m) {
					if (mysql_num_rows($result_m) > 0) {
						while ($row_m = mysql_fetch_array($result_m)) {

							$admin_id = $row_m["user_id"];
						
							$timestamp1 = strtotime($row_m['valid_until']);
							$timestamp2 = strtotime($current_date);

							if($timestamp1 >= $timestamp2) {
								$query	= "INSERT INTO $TABLE_ASK_RESPONSE (query_id, user_id, response) VALUES ('$query_id', '$user_id', '$response_comment')";
								$result = mysql_query($query);

								if ($result) {
									$id = mysql_insert_id();
									getCreditValue($TAG_CREDIT_ATTEND_QUERY, $user_id);
									if($query_user_id != $user_id) {
																									
										// Send notification
										$notification_type = $TAG_NOTIFICATION_MY_QUERY_RESPONSE;
										$to_user_id = $admin_id;
										$from_user_id = $user_id;
										$content_id = $query_id;
										$other_data = $response_comment;
										createNotification($to_user_id, $from_user_id, $content_id, $other_data, $notification_type);
										
									}

									$query2	= "SELECT * FROM $TABLE_USER_QUERY WHERE query_id = '$query_id'";
									$result2 = mysql_query($query2);
									
									if($result2) {
										if (mysql_num_rows($result2) > 0) {
											while ($row2 = mysql_fetch_array($result2)) {
												$query_follow_user_id = $row2['user_id'];
												if($query_follow_user_id != $user_id) {
																																										
													// Send notification
													$notification_type = $TAG_NOTIFICATION_FOLLOWED_QUERY_RESPONSE;
													$to_user_id = $query_follow_user_id;
													$from_user_id = $user_id;
													$content_id = $query_id;
													$other_data = $response_comment;
													createNotification($to_user_id, $from_user_id, $content_id, $other_data, $notification_type);
													
												}
											}
										}
									}

									$response[$TAG_SUCCESS] = true;
									$response[$TAG_MESSAGE] = "" . $id;
									echoRespnse(200, $response);

								} else {
									$response[$TAG_SUCCESS] = false;
									$response[$TAG_MESSAGE] = "Some error occured";
									echoRespnse(200, $response);
								}
							} else {
								$response[$TAG_SUCCESS] = false;
								$response[$TAG_MESSAGE] = "Validity of Query expired";
								echoRespnse(200, $response);
							}
						}
					}
				} else {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Query not found";
					echoRespnse(200, $response);
				}
			}
        });




/** Update Query Response
 * url - /query/updateResponse
 * method - POST
 */
$app->post('/query/updateResponse', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)) {
							
				mysql_set_charset('utf8');
				// reading post params
				$response_comment = $app->request->post('response');
				$response_id = $app->request->post('response_id');
				$user_id = $app->request->post('user_id');
				
				
				// Escape
				$response_comment = escape($response_comment);
			
				$response = array();

				$query2	= "UPDATE $TABLE_ASK_RESPONSE SET response = '$response_comment' WHERE id = '$response_id' AND user_id = '$user_id'";
				$result2 = mysql_query($query2);

				if ($result2) {
					$response[$TAG_SUCCESS] = true;
					$response[$TAG_MESSAGE] = "Response updated";
					echoRespnse(200, $response);

				} else {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured";
					echoRespnse(200, $response);
				}
			}
        });



/** Add Response Upvotes
 * url - /response/votes
 * method - POST
 */
$app->post('/response/votes', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$action 		= $app->request->post('action');
				$response_id 	= $app->request->post('response_id');
				$user_id 		= $app->request->post('user_id');
			
				$response = array();
				
				$isUserInactive = isUserInactive($user_id);
				if($isUserInactive[$TAG_USER_INACTIVE]) {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = $isUserInactive[$TAG_MESSAGE];
					echoRespnse(200, $response);
					return;
				}


				$query = "SELECT * FROM $TABLE_ASK_RESPONSE_ACTION WHERE response_id = '$response_id' AND user_id = '$user_id'";
				$result = mysql_query($query);
				
				if($result) {
					if (mysql_num_rows($result) == 0) {
						$query1	= "INSERT INTO $TABLE_ASK_RESPONSE_ACTION (response_id, user_id, action) VALUES ('$response_id', '$user_id', '$action')";
						$result1 = mysql_query($query1);

						if ($result1) {
							$response[$TAG_SUCCESS] = true;
							if($action == 1) {
								$response[$TAG_MESSAGE] = "1";
								
								$credit_user_id = getResponseUserId($response_id);
								getCreditValue($TAG_CREDIT_LIKE_RESPONSE, $credit_user_id);
								
								// Send notification
								$notification_type = $TAG_NOTIFICATION_QUERY_RESPONSE_LIKE;
								$to_user_id = $credit_user_id;
								$from_user_id = $user_id;
								$content_id = $response_id;
								$other_data = "Favorite";
								createNotification($to_user_id, $from_user_id, $content_id, $other_data, $notification_type);
								
							} else {
								$response[$TAG_MESSAGE] = "1";
							}
							echoRespnse(200, $response);

						} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Some error occured";
							echoRespnse(200, $response);
						}

					} else {
						while ($row = mysql_fetch_array($result)) {
							if($row['action'] != $action) {
								$query2	= "UPDATE $TABLE_ASK_RESPONSE_ACTION SET action = '$action' WHERE response_id = '$response_id' AND user_id = '$user_id'";
								$result2 = mysql_query($query2);
								
								if ($result2) {
									$response[$TAG_SUCCESS] = true;
									if($action == 1) {
										$response[$TAG_MESSAGE] = "Response upvote successful";
									} else {
										$response[$TAG_MESSAGE] = "Response downvote successful";
									}
									echoRespnse(200, $response);

								} else {
									$response[$TAG_SUCCESS] = false;
									$response[$TAG_MESSAGE] = "Some error occured";
									echoRespnse(200, $response);
								}

							} else {
								$response[$TAG_SUCCESS] = false;
								$response[$TAG_MESSAGE] = "Some error occured";
								echoRespnse(200, $response);
							}
						}
					}

				} else {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured";
					echoRespnse(200, $response);
				}
			}
        });




/** Spam Users of Response
 * url - /response/spam
 * method - POST
 */
$app->post('/response/spam', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$action 		= $app->request->post('action');
				$response_id 	= $app->request->post('response_id');
				$user_id 		= $app->request->post('user_id');
			
				$response = array();

				$isUserInactive = isUserInactive($user_id);
				if($isUserInactive[$TAG_USER_INACTIVE]) {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = $isUserInactive[$TAG_MESSAGE];
					echoRespnse(200, $response);
					return;
				}

				$query1	= "SELECT * FROM $TABLE_ASK_RESPONSE_SPAM WHERE response_id = '$response_id' AND user_id = '$user_id'";
				$result1 = mysql_query($query1);				

				if (mysql_num_rows($result1) == 0) {
					$query1	= "INSERT INTO $TABLE_ASK_RESPONSE_SPAM (response_id, user_id) VALUES ('$response_id', '$user_id')";
					$result1 = mysql_query($query1);

					if ($result1) {
						$response[$TAG_ACTION] = getAction($response_id, 1);
						$response[$TAG_SUCCESS] = true;
						$response[$TAG_MESSAGE] = "Response of user spammed successfully";
						echoRespnse(200, $response);

					} else {
						$response[$TAG_SUCCESS] = false;
						$response[$TAG_MESSAGE] = "Some error occured";
						echoRespnse(200, $response);
					}

				} else {
					$query2	= "DELETE FROM $TABLE_ASK_RESPONSE_SPAM WHERE response_id = '$response_id' AND user_id = '$user_id'";
					$result2 = mysql_query($query2);

					if ($result2) {
						$response[$TAG_ACTION] = getAction($response_id, 0);
						$response[$TAG_SUCCESS] = true;
						$response[$TAG_MESSAGE] = "Response of user unspammed successfully";
						echoRespnse(200, $response);

					} else {
						$response[$TAG_SUCCESS] = false;
						$response[$TAG_MESSAGE] = "Some error occured";
						echoRespnse(200, $response);
					}
				}
			}
        });




/** Get Query Response
 * NAME
 * url - /query/getQueryResponse
 * method - POST
 */
$app->post('/query/getQueryResponse', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$query_id 	= $app->request->post('query_id');
				$user_id 	= $app->request->post('user_id');
			
				$response = array();
				$response[$TAG_DETAILS] = array();
				
				//CODE
			 	$query = "SELECT * FROM $TABLE_ASK_RESPONSE WHERE query_id = '$query_id' and status=1 ORDER BY created_date ASC";
				$result = mysql_query($query);
				
				if($result) {
					if (mysql_num_rows($result) > 0) {
						    while ($row = mysql_fetch_array($result)) {
							
								$detail = array();
								$detail[$TAG_ID] 		 		= $row['id'];
								$detail[$TAG_QUERY_ID]   		= $row['query_id'];
								$detail[$TAG_RESPONSE] 			= $row['response'];
								$detail[$TAG_USER_FAV]   		= isResponseUserFav($row['id'], $user_id);
								$detail[$TAG_SPAM]   			= isResponseUserSpammed($row['id'], $user_id);
								$detail[$TAG_MARK_INAPPROPRIATE]= $row['mark_inappropriate'];
								$detail[$TAG_COMMENT_COUNT]		= getResponseCommentCount($row['id']);
								$detail[$TAG_UPVOTES] 			= getUpvotes($row['id']);
								$detail[$TAG_DOWNVOTES] 		= getDownvotes($row['id']);
								$detail[$TAG_CREATED_DATE] 		= $row['created_date'];
																
								$current_user_id = $row['user_id'];								
								$detail[$TAG_PROFILE] = getProfileView($current_user_id, $user_id);

								array_push($response[$TAG_DETAILS], $detail);
								$success = true;
							}

							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "Successful";		
							echoRespnse(200, $response);

					} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Query Responses not found";
							echoRespnse(200, $response);
					}

				} else {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured";
					echoRespnse(200, $response);
				}
			}
        });



/** Add query response Favourite
 * url - /query/response/favourite
 * method - POST
 */
/*$app->post('/query/response/favourite', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$action = $app->request->post('action');
				$response_id = $app->request->post('response_id');
				$user_id = $app->request->post('user_id');
			
				$response = array();
				
				//CODE
				$query	= "SELECT * FROM lokaso_ask_response_action WHERE response_id = '$response_id' AND user_id = $user_id";
				$result = mysql_query($query);
				
				if($result)
				{
					if (mysql_num_rows($result) == 0) {
				
						if ($action == 1) {

							$query2	= "INSERT INTO lokaso_ask_response_action (response_id, user_id) VALUES ('$response_id', '$user_id')";
							$result2 = mysql_query($query2);

							if ($result2) {
								addUserContentNotification("Response favourited", $user_id, $response_id, $TAG_NOTIFICATION_RESPONSE_FAV);
								$credit_user_id = getResponseUserId($response_id);
								getCreditValue($TAG_CREDIT_LIKE, $credit_user_id);
								$response[$TAG_SUCCESS] = true;
								$response[$TAG_MESSAGE] = "Response favourited";
								echoRespnse(200, $response);

							} else {
								$response[$TAG_SUCCESS] = false;
								$response[$TAG_MESSAGE] = "Some error occured";
								echoRespnse(200, $response);
							}

						} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Some error occured";
							echoRespnse(200, $response);
						}

					}  else if (mysql_num_rows($result) > 0) {
						if ($action == 0) {
							$sql_delete = "DELETE FROM lokaso_ask_response_action WHERE response_id = '$response_id' AND user_id = '$user_id'";
							$sql_result = mysql_query($sql_delete);

							if ($sql_result) {
								$response[$TAG_SUCCESS] = true;
								$response[$TAG_MESSAGE] = "Response unfavourited";
								echoRespnse(200, $response);
							} else {
								$response[$TAG_SUCCESS] = false;
								$response[$TAG_MESSAGE] = "Some error occured";
								echoRespnse(200, $response);
							}

						} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Some error occured";
							echoRespnse(200, $response);
						}
					}

				} else {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured";
					echoRespnse(200, $response);
				}
			}
        });*/




/** Add query response comment
 * url - /query/response/comment
 * method - POST
 */
$app->post('/query/response/comment', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$comment 		= $app->request->post('comment');
				$response_id 	= $app->request->post('response_id');
				$user_id 		= $app->request->post('user_id');
				
				// Escape
				$comment = escape($comment);
				
				$created_date   = date("Y-m-d H:i:s");
			
				$response = array();
				
				$isUserInactive = isUserInactive($user_id);
				if($isUserInactive[$TAG_USER_INACTIVE]) {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = $isUserInactive[$TAG_MESSAGE];
					echoRespnse(200, $response);
					return;
				}
				
				$admin_id = 0;
			 	$query = "SELECT * FROM $TABLE_ASK_RESPONSE WHERE id = '$response_id' ";
				$result = mysql_query($query);
				
				if($result) {
					if (mysql_num_rows($result) > 0) {
						$row = mysql_fetch_array($result);
						$admin_id = $row["user_id"];
						
					}
				}

				$query	= "INSERT INTO $TABLE_ASK_RESPONSE_COMMENT (response_id, user_id, comment, created_date) VALUES ('$response_id', '$user_id', '$comment', '$created_date')";
				$result = mysql_query($query);

				if ($result) {
						
					// Send notification
					$notification_type = $TAG_NOTIFICATION_QUERY_RESPONSE_COMMENT;
					$to_user_id = $admin_id;
					$from_user_id = $user_id;
					$content_id = $response_id;
					$other_data = $comment;
					createNotification($to_user_id, $from_user_id, $content_id, $other_data, $notification_type);
					
					//getCreditValue($TAG_CREDIT_COMMENT, $user_id);
					$response[$TAG_SUCCESS] = true;
					$response[$TAG_MESSAGE] = $created_date;
					echoRespnse(200, $response);

				} else {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured";
					echoRespnse(200, $response);
				}
			}
        });



/** Get query response comment
 * NAME
 * url - /query/response/getComments
 * method - POST
 */
$app->post('/query/response/getComments', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$user_id = $app->request->post('user_id');
				$response_id = $app->request->post('response_id');
			
				$response = array();
				$response[$TAG_DETAILS] = array();
				
				//CODE
			 	$query = "SELECT * FROM $TABLE_ASK_RESPONSE_COMMENT WHERE response_id = '$response_id' ORDER BY created_date DESC";
				$result = mysql_query($query);
				
				if($result) {
					if (mysql_num_rows($result) > 0) {
						    while ($row = mysql_fetch_array($result)) {
							
								$detail = array();
								$detail[$TAG_ID] 		 	= $row['id'];
								$detail[$TAG_RESPONSE_ID]   = $row['response_id'];			
								$detail[$TAG_COMMENT] 		= $row['comment'];
								$detail[$TAG_CREATED_DATE] 	= $row['created_date'];
								
								$current_user_id 		 	= $row['user_id'];
								$detail[$TAG_PROFILE] 		= getProfileView($current_user_id, $user_id);
								
								array_push($response[$TAG_DETAILS], $detail);
								$success = true;
							}

							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "Successful";		
							echoRespnse(200, $response);

					} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Response comments not found";
							echoRespnse(200, $response);
					}

				} else {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured.";
					echoRespnse(200, $response);
				}
			}
        });



/** Follow/Unfollow query
 * url - /query/follow
 * method - POST
 */
$app->post('/query/follow', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
				
				mysql_set_charset('utf8');
				// reading post params
				$action 	= $app->request->post('action');
				
				$query_id 	= $app->request->post('query_id');
				$user_id 	= $app->request->post('user_id');
				
				$response = array();
				
				$isUserInactive = isUserInactive($user_id);
				if($isUserInactive[$TAG_USER_INACTIVE]) {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = $isUserInactive[$TAG_MESSAGE];
					echoRespnse(200, $response);
					return;
				}
				
				$query	= "SELECT * FROM $TABLE_USER_QUERY WHERE query_id = '$query_id' AND user_id = $user_id";
				$result = mysql_query($query);
				
				if($result)
				{
					if (mysql_num_rows($result) == 0) {
								
						$dateNow   = date("Y-m-d H:i:s");
						
						$row = mysql_fetch_array($result);
						$admin_id = $row["user_id"];
						$description = $row["description"];
								
						$query1	= "SELECT * FROM $TABLE_ASK WHERE id = '$query_id' AND valid_until >= '$dateNow'";
						$result1 = mysql_query($query1);
						if (mysql_num_rows($result1) > 0) {
							
							$query2	= "INSERT INTO $TABLE_USER_QUERY (query_id, user_id) VALUES ('$query_id', '$user_id')";
							$result2 = mysql_query($query2);

							if ($result2) {
								//getCreditValue($TAG_CREDIT_FOLLOW, $user_id);
								
								$response[$TAG_ACTION] = getAction($query_id, 1);
								
										
								// Send notification
								$notification_type = $TAG_NOTIFICATION_FOLLOW_QUERY;
								$to_user_id = $admin_id;
								$from_user_id = $user_id;
								$content_id = $query_id;
								$other_data = $description;
								createNotification($to_user_id, $from_user_id, $content_id, $other_data, $notification_type);
								
								
								$response[$TAG_SUCCESS] = true;
								$response[$TAG_MESSAGE] = "Query Followed";
								echoRespnse(200, $response);

							} else {
								$response[$TAG_SUCCESS] = false;
								$response[$TAG_MESSAGE] = "Failed to follow query";
								echoRespnse(200, $response);
							}
						}
						else {							
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Sorry this query is already expired.";
							echoRespnse(200, $response);
						}
						

					}  else if (mysql_num_rows($result) > 0) {
					
						$sql_delete = "DELETE FROM $TABLE_USER_QUERY WHERE query_id = '$query_id' AND user_id = '$user_id'";
						$sql_result = mysql_query($sql_delete);

						if ($sql_result) {
																					
							$response[$TAG_ACTION] = getAction($query_id, 0);
							
							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "Query Unfollowed";
							echoRespnse(200, $response);
						} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Failed to Unfollow query";
							echoRespnse(200, $response);
						}
						
					}
				} else {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured";
					echoRespnse(200, $response);
				}
			}
        });	





/** Questions for Interests
 * NAME
 * url - /questions
 * method - POST
 * params - TAG_USER_ID
 */
$app->post('/questions', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$interest_id = $app->request->post('interest_id');
			
				$response = array();
				$response[$TAG_DETAILS] = array();
				
				//CODE
				$query	= "SELECT id, name FROM $TABLE_QUESTION WHERE interest_id = '$interest_id'";
				$result = mysql_query($query);
				
				if($result)
				{
					if (mysql_num_rows($result) > 0) {
						    while ($row = mysql_fetch_array($result)) {
							
								$detail = array();		
								
								$detail[$TAG_ID] 		= $row['id'];					
								$detail[$TAG_NAME] 		= $row['name'];	
							
								array_push($response[$TAG_DETAILS], $detail);
								$success = true;
							}

							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "Successful";		
							echoRespnse(200, $response);

					} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Questions not found for the selected interest";
							echoRespnse(200, $response);
					}
				}
				else
				{
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured.";
					echoRespnse(200, $response);
				}
			}
        });



/** Create Suggestion
 * NAME
 * url - /suggestion
 * method - POST
 * params - TAG_USER_ID
 */
$app->post('/suggestion', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				/*$user_id 		= $app->request->post('user_id');
				$location 		= $app->request->post('location');
				$interest_id 	= $app->request->post('interest_id');
				$suggestion 	= $app->request->post('suggestion');
				$image 			= $app->request->post('image');
				$caption 		= $app->request->post('caption');
				$lat 			= $app->request->post('lat');
				$lng 			= $app->request->post('lng');*/

				$uploaddir 		= $SUGGESTION_PICS;
				$file 			= basename($_FILES['image']['name']);
				$tmp_filename 	= $_FILES['image']['tmp_name'];
				
				$location 		= $_REQUEST['location'];
				$interest_id 	= $_REQUEST['interest_id'];
				$suggestion 	= $_REQUEST['suggestion'];
				$caption 		= $_REQUEST['caption'];
				$lat 			= $_REQUEST['lat'];
				$lng 			= $_REQUEST['lng'];
				$placeId        = $_REQUEST['placeId'];
				$google_biz     = $_REQUEST['google_biz'];
				
				// Escape
				$caption = escape($caption);
				$suggestion = escape($suggestion);
				$location = escape($location);
				
				$created_date 	= date("Y-m-d H:i:s");
				
				$response = array();
				
				$isUserInactive = isUserInactive($user_id);
				if($isUserInactive[$TAG_USER_INACTIVE]) {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = $isUserInactive[$TAG_MESSAGE];
					echoRespnse(200, $response);
					return;
				}
				
				
				$query1 = "SELECT id FROM $TABLE_SUGGESTION WHERE user_id = '$user_id' AND interest_id = '$interest_id' AND location = '$location'";
				$result1 = mysql_query($query1);
				
				if($result1){
					if (mysql_num_rows($result1) == 0) {
						$suggestion_pic = "";
						if(!empty($file)) {
							$suggestion_pic = uploadImage($user_id, $uploaddir, $file, $tmp_filename);
						}
						$query = "INSERT INTO $TABLE_SUGGESTION (suggestion, user_id, image, caption, location, lat, lng, placeId,google_biz , interest_id, created_date) VALUES ('$suggestion', '$user_id', '$suggestion_pic', '$caption', '$location', '$lat', '$lng','$placeId','$google_biz' ,'$interest_id', '$created_date')";
							$result = mysql_query($query);

						if ($result) {
							$discovery_id = mysql_insert_id();
							
							$point = 0;
							if(strlen($suggestion_pic)>0) {
								$point = getCreditValue($TAG_CREDIT_SUGGESTION_PICTURE, $user_id);
							}
							else {
								$point = getCreditValue($TAG_CREDIT_SUGGESTION, $user_id);
							}
							
							$detail = array();
							$detail["point"] = $point;
							$detail["title"] = "Congratulations..!!";
							$detail["message"] = "You have earned ".$point." points. Keep it flowing.";
							
							$response[$TAG_DETAILS] = $detail;
							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "Suggestion created successfully";
							echoRespnse(200, $response);

						} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Some error occured q";
							$response[$TAG_QUERY] = $query;
							
							echoRespnse(200, $response);
						}

					} else {
						$response[$TAG_SUCCESS] = false;
						$response[$TAG_MESSAGE] = "You have already added suggestion for this location";
						echoRespnse(200, $response);
					}

				} else {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured r";
					echoRespnse(200, $response);
				}
			}
        });	




/** Edit Suggestion
 * NAME
 * url - /suggestion/edit
 * method - POST
 * params - TAG_USER_ID
 */
$app->post('/suggestion/edit', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				$uploaddir 		= $SUGGESTION_PICS;
				$file 			= basename($_FILES['image']['name']);
				$tmp_filename 	= $_FILES['image']['tmp_name'];

				$user_id 	= $_REQUEST["user_id"];
				$suggestion_id 	= $_REQUEST["suggestion_id"];
				$location 		= $_REQUEST['location'];
				$interest_id 	= $_REQUEST['interest_id'];
				$suggestion 	= $_REQUEST['suggestion'];
				$caption 		= $_REQUEST['caption'];
				$lat 			= $_REQUEST['lat'];
				$lng 			= $_REQUEST['lng'];
                $placeId        = $_REQUEST['placeId'];
                $google_biz     = $_REQUEST['google_biz'];
				
				// Escape
				$caption = escape($caption);
				$suggestion = escape($suggestion);
				$location = escape($location);
				
				/*
				$image_content 			= $_REQUEST['image'];
				
				
							
				if(strlen($image_content)>0) {
						
					$binary=base64_decode($image_content);
					
					$path_to_folder = '/testproject/motovert'; // On dev server
					//$path_to_folder = ''; // On prod server		
					
					$upload_folder = "upload";
					$vehicle_folder = $upload_folder."/vehicle";
					$image_path = $vehicle_folder."/".$image_name;
					
					$target_file = $_SERVER['DOCUMENT_ROOT'].$path_to_folder.'/'.$vehicle_folder;
					$target_folder = $target_file.'/';
					//$target_folder = "profile/"; 
					
					
					if (!file_exists($target_file)) {
						mkdir($target_file, 0777, true);
						//echo "Folder created ".$target_file." - ";
					}
					else {
						//echo "Folder exist ".$target_file." - ";
					}
					
					$filename = $target_folder.$image_name;		
					
					//echo $filename." , ".$target_file." , ".$image_path;
					
					if(!file_put_contents($filename, $binary)) {
						$response['success'] = false;
						$response['message'] = "Failed to add vehicle. Image upload failed." ;
						echoResponse(200, $response);	
						return;
					}
				}
			*/
				$response = array();
				
				$query1 = "SELECT * FROM $TABLE_SUGGESTION WHERE id = '$suggestion_id'";
				$result1 = mysql_query($query1);
				
				if($result1){
					if (mysql_num_rows($result1) > 0) {
						
						$row = mysql_fetch_array($result1);
							
								
						$user_id_old 			= $row['user_id'];	
						$interest_id_old 		= $row['interest_id'];	
						$location_old 			= $row['location'];	
						
						if($user_id==$user_id_old && $interest_id==$interest_id_old && $location_old==$location_old) {
						
						}
								
						// Check if user has created suggestion at this place
						$query2 = "SELECT id FROM $TABLE_SUGGESTION WHERE user_id = '$user_id' AND interest_id = '$interest_id' AND location = '$location' and not id = '$suggestion_id'";
						$result2 = mysql_query($query2);
						
						if($result2){
							if (mysql_num_rows($result2) > 0) {
									
								$response[$TAG_SUCCESS] = false;
								$response[$TAG_MESSAGE] = "You have already added suggestion for this location";
								echoRespnse(200, $response);
								return;
							}
						}
				
						
						
						$suggestion_pic = "";

						if(!empty($file)) {
							$suggestion_pic = uploadImage($user_id, $uploaddir, $file, $tmp_filename);

							$query = "UPDATE $TABLE_SUGGESTION SET suggestion = '$suggestion', image = '$suggestion_pic', caption = '$caption', location = '$location', lat = '$lat', lng = '$lng',placeId = '$placeId',google_biz = '$google_biz', interest_id = '$interest_id' WHERE id = '$suggestion_id'";

						} else {
							$query = "UPDATE $TABLE_SUGGESTION SET suggestion = '$suggestion', caption = '$caption', location = '$location', lat = '$lat', lng = '$lng',placeId = '$placeId',google_biz = '$google_biz', interest_id = '$interest_id' WHERE id = '$suggestion_id'";
						}

						$result = mysql_query($query);

						if ($result) {
							$response["q"] = $query2;
							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "Suggestion edited successfully";
							echoRespnse(200, $response);

						} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Some error occured";
							echoRespnse(200, $response);
						}

					} else {
						$response[$TAG_SUCCESS] = false;
						$response[$TAG_MESSAGE] = "Suggestion does not exist";
						echoRespnse(200, $response);
					}

				} else {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured";
					echoRespnse(200, $response);
				}
			}
        });



/** Create Answers
 * NAME
 * url - /answer
 * method - POST
 * params - TAG_USER_ID
 */
$app->post('/answer', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$discovery_id = $app->request->post('discovery_id');
			
				$response = array();
				$response[$TAG_DETAILS] = array();
				
				//CODE
				$query	= "SELECT * FROM $TABLE_ANSWER WHERE discovery_id = '$discovery_id'";
				$result = mysql_query($query);
				
				if($result)
				{
					if (mysql_num_rows($result) > 0) {
						    while ($row = mysql_fetch_array($result)) {
							
								$question_id = $row['question_id'];
								$query2	= "SELECT * FROM $TABLE_QUESTION WHERE id = '$question_id'";
								$result2 = mysql_query($query2);
												
								if($result2)
								{
									if (mysql_num_rows($result2) > 0) {
										while ($row2 = mysql_fetch_array($result2)) {
											$detail = array();
											$detail[$TAG_ID] 			= $row['id'];					
											$detail[$TAG_DISCOVERY_ID] 	= $row['discovery_id'];	
											$detail[$TAG_QUESTION] 		= $row2['name'];	
											$detail[$TAG_ANSWER_NAME] 	= $row['answer_name'];	
											$detail[$TAG_CREATED_DATE] 	= $row['created_date'];	
										
											array_push($response[$TAG_DETAILS], $detail);
											$success = true;
										}
									}/* else {
										$response[$TAG_SUCCESS] = false;
										$response[$TAG_MESSAGE] = "Questions not found";
										echoRespnse(200, $response);
									}*/
								}
							}

							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "Successful";		
							echoRespnse(200, $response);

					} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Suggestion not found";
							echoRespnse(200, $response);
					}
				}
				else
				{
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured.";
					echoRespnse(200, $response);
				}
			}
        });	


function setAnswers($discovery_id, $question_id, $answer_name){
	include("../include/constant.php");
	$query	= "INSERT INTO $TABLE_ANSWER (discovery_id, question_id, answer_name) VALUES ('$discovery_id', '$question_id', '$answer_name')";
	$result = mysql_query($query);

	if ($result) {
		$success = true;
	} else {
		$success = false;
	}

	return $success;
}



/** Set Ad View
 * NAME
 * url - /adView
 * method - POST
 */
$app->post('/adView', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$user_id = $app->request->post('user_id');
				$ad_id = $app->request->post('ad_id');
				$type = $app->request->post('type');
				$status = $app->request->post('status');
				$data = $app->request->post('data');
				
				$created_date 	= date("Y-m-d H:i:s");
				
				$response = array();
				$response[$TAG_DETAILS] = array();
				
				$query	= "INSERT INTO $TABLE_ADS_VIEW (user_id, ad_id, ad_type, status, data, created_date) VALUES ('$user_id', '$ad_id', '$type', '$status', '$data', '$created_date')";
				$result = mysql_query($query);

				if($result) {			
					$detail = array();
					$response[$TAG_DETAILS]= $detail;
			
					$response[$TAG_SUCCESS] = true;
					$response[$TAG_MESSAGE] = "Successful";		
					echoRespnse(200, $response);
				}
				else {					
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "User not found";
					echoRespnse(200, $response);					
				}				
			}
        });





/** Get Suggestions based on filters
 * Filters applied : 
 * Distance, Location, Interest selected, and sorted by distance or latest
 * NAME
 * url - /getSuggestion
 * method - POST
 */
$app->post('/getSuggestion', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();
			
			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
				
				mysql_set_charset('utf8');
				// reading post params
				$lat 		= $app->request->post('lat');
				$lng 		= $app->request->post('lng');
				$placeId    = $app->request->post('placeId');
				$google_biz = $app->request->post('google_biz');
				$range 		= $app->request->post('range');
				$user_id 	= $app->request->post('user_id');
				$limit 		= $app->request->post('limit');
				$position 	= $app->request->post('position');
				
				$filter_by = $app->request->post('filter_by');
				
				$interest_ids = $app->request->post('interest_ids');
				
				$search_type = $app->request->post('search_type');
				
				$display_items = 10;
				
				if($position==null) {
					$position = "xxx";
				}
				else {
					$limit = $position * $display_items;
				}
				/*
				if($interest_ids==null) {
					
					$query	= "SELECT * FROM $TABLE_USER_INTEREST WHERE user_id = '$user_id'";
					$result = mysql_query($query);
				
					if($result)
					{
						if (mysql_num_rows($result) > 0) {
							$interest_ids = "";
						    while ($row = mysql_fetch_array($result)) {
							
								$interest_id = $row['interest_id'];
								$interest_ids = $interest_ids.$interest_id.",";
							}
						}
					}
				}
			*/
				if($interest_ids==null || ($interest_ids!=null && strlen($interest_ids)==0)) {
					$interest_ids = "1,2,3,4,5,6,";
				}
			
				$interest_ids = rtrim($interest_ids, ",");
				
				
				if($search_type==null) {
					$search_type = $TYPE_SEARCH_NORMAL;
				}
				$range = $RANGE_VALUE_TIP;
				if($search_type==$TYPE_SEARCH_ALL) {
					$range = $RANGE_VALUE_TIP_ALL;
				}
				
				
				
				if($filter_by!=null) {
					if($filter_by==2) { // By distance
					
						$order_by = " ORDER BY distance ASC ";	
																	
						$query = "SELECT *, (1.609344 * 3956 * acos( cos( radians($lat) ) * cos( radians(lat) ) * cos( radians(lng) - radians($lng) ) + sin( radians($lat) ) * sin( radians(lat) ) ) ) as distance FROM $TABLE_SUGGESTION HAVING distance < '$range' 
						 and status>0  
						 and interest_id in (".$interest_ids.") 
						 ".$order_by."
						 LIMIT $limit, $display_items ";													
					}
					else { // By latest
						$order_by = " ORDER BY created_date DESC ";		
					
						$query = "SELECT *, (1.609344 * 3956 * acos( cos( radians($lat) ) * cos( radians(lat) ) * cos( radians(lng) - radians($lng) ) + sin( radians($lat) ) * sin( radians(lat) ) ) ) as distance FROM $TABLE_SUGGESTION HAVING distance < '$range' 
						 and status>0  
						 and interest_id in (".$interest_ids.") 
						 ".$order_by."
						 LIMIT $limit, $display_items ";
					}
				}
				else {
					$order_by = " ORDER BY created_date DESC ";		
				
					$query = "SELECT *, (1.609344 * 3956 * acos( cos( radians($lat) ) * cos( radians(lat) ) * cos( radians(lng) - radians($lng) ) + sin( radians($lat) ) * sin( radians(lat) ) ) ) as distance FROM $TABLE_SUGGESTION HAVING distance < '$range' 
					 and status>0  
					 and interest_id in (".$interest_ids.") 
					 ".$order_by."
					 LIMIT $limit, $display_items ";		
				}
				
							
			
				$response = array();
				$response[$TAG_DETAILS] = array();
				$response[$TAG_ADS_LIST] = array();
				$response[$TAG_AD_POSITION] = $AD_POSITION;
							
				
				$result = mysql_query($query);
				
				if($result)
				{
					if (mysql_num_rows($result) > 0) {
						    while ($row = mysql_fetch_array($result)) {
							
								$detail = array();
								$detail[$TAG_ID] 		 		= $row['id'];
								$detail[$TAG_FAV_COUNT]  		= getDiscoveryFavCount($row['id']);
								$detail[$TAG_COMMENT_COUNT] 	= getDiscoveryCommentCount($row['id']);
								$detail[$TAG_USER_FAV]   		= isDiscoveryUserFav($row['id'], $user_id);
								$detail[$TAG_SUGGESTION_SPAM]	= isSuggestionUserSpam($row['id'], $user_id);
								$detail[$TAG_USER_DISC]   		= isUserDiscovery($row['id'], $user_id);
								$detail[$TAG_IMAGE_URL]  		= $DISCOVERY_PIC_PATH . $row['image'];
								$detail[$TAG_CAPTION]			= $row['caption'];
								$detail[$TAG_SUGGESTION]		= $row['suggestion'];

								$discovery_user_id 				= $row['user_id'];
																
								$detail[$TAG_PROFILE] 			= getProfile($discovery_user_id, $user_id);
																
								$interest_id = $row['interest_id'];
								$detail[$TAG_INTEREST_ID] 	= $interest_id;
								
								$query3 = "SELECT * FROM $TABLE_INTEREST WHERE id = '$interest_id'";
								$result3 = mysql_query($query3);
								if (mysql_num_rows($result3) > 0) {
									$row3 = mysql_fetch_array($result3);																		
									$interestObj = array();											
									$interestObj[$TAG_ID] 				= $row3['id'];					
									$interestObj[$TAG_NAME] 			= $row3['name'];	
									$interestObj[$TAG_IMAGE_URL] 		= $INTEREST_PIC_PATH . $row3['image'];
									$interestObj[$TAG_CREATED_DATE] 	= $row3['created_date'];										
									$detail[$TAG_INTEREST] = $interestObj;
								}
								
								$detail[$TAG_LOCATION]   	= $row['location'];
								$detail[$TAG_LAT] 		 	= $row['lat'];
								$detail[$TAG_LNG] 		 	= $row['lng'];
								$detail[$TAG_PLACEID]       = $row['placeId'];
								$detail[$TAG_GOOGLE_BIZ]    = $row['google_biz'];
								$detail[$TAG_CREATED_DATE] 	= $row['created_date'];
								
								$dist = distance($lat, $lng, $row['lat'], $row['lng']);
								$distance = $row['distance'];
								$detail[$TAG_DISTANCE]  	= $dist;
								$detail["dist"]  			= $distance;
								$detail[$TAG_DISTANCE_DISPLAY]  	= getDistanceDisplay($distance);
								
								array_push($response[$TAG_DETAILS], $detail);
								$success = true;
							}
							
							
							// Now send the ads
							
							$query1 = "select * from $TABLE_ADS where status=1 order by updated_date desc";
							$result1 = mysql_query($query1);
							
							if($result1)
							{
								while ($row = mysql_fetch_array($result1)) {
									$detail = array();
									$detail[$TAG_ID] 		 		= $row['id'];
									$detail[$TAG_TITLE] 		 	= $row['title'];
									$detail[$TAG_MESSAGE] 		 	= $row['message'];
									
									$ad_type = (int)$row['ad_type'];
									$detail[$TAG_TYPE] 		 	= $ad_type;
									$url = $row['url'];
									
									$youtube_id = $row['youtube_id'];
									if($ad_type==$AD_TYPE_VIDEO) {
										$url = "https://img.youtube.com/vi/".$youtube_id."/mqdefault.jpg";
									}
									else {
										$url = $AD_PATH.$url;
									}
									$detail[$TAG_URL] 		 		= $url;
									$detail[$TAG_YOUTUBE_ID] 		= $row['youtube_id'];
									$detail[$TAG_STATUS] 		 	= $row['status'];
									$detail[$TAG_CREATED_DATE] 		= $row['created_date'];
									$detail[$TAG_UPDATED_DATE] 		= $row['updated_date'];
									array_push($response[$TAG_ADS_LIST], $detail);
								}
							}
							
							
							$response["q"] = $query;
							$response["position"] = $position;
							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "Successful";		
							echoRespnse(200, $response);

					} else {
							$response["q"] = $query;
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Suggestions not found";
							echoRespnse(200, $response);
					}
				}
				else
				{
					$response["q"] = $query;
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured.";
					echoRespnse(200, $response);
				}
			}
        });


/** Search Suggestions
 * NAME
 * url - /suggestion/search
 * method - POST
 */
$app->post('/suggestion/search', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$lat 		= $app->request->post('lat');
				$lng 		= $app->request->post('lng');
				$placeId    = $app->request->post('placeId');
				$google_biz = $app->request->post('google_biz');
				$user_id 	= $app->request->post('user_id');
				$search 	= $app->request->post('search');
				
				// Escape
				$search = escape($search);
				
				$search 	= "%" . $search . "%";
			
				$response 	= array();
				$response[$TAG_DETAILS] = array();
				
				
			 	$query 	= "SELECT * FROM $TABLE_SUGGESTION WHERE suggestion LIKE '$search' and status>0 ORDER BY created_date DESC";
				$result = mysql_query($query);
				
				if($result)
				{
					if (mysql_num_rows($result) > 0) {
						    while ($row = mysql_fetch_array($result)) {
							
								$detail = array();
								$detail[$TAG_ID] 		 		= $row['id'];
								$detail[$TAG_FAV_COUNT]  		= getDiscoveryFavCount($row['id']);
								//$detail[$TAG_COMMENT_COUNT] 	= getDiscoveryCommentCount($row['id']);
								$detail[$TAG_USER_FAV]   		= isDiscoveryUserFav($row['id'], $user_id);
								$detail[$TAG_SUGGESTION_SPAM]	= isSuggestionUserSpam($row['id'], $user_id);
								$detail[$TAG_USER_DISC]   		= isUserDiscovery($row['id'], $user_id);
								$detail[$TAG_IMAGE_URL]  		= $DISCOVERY_PIC_PATH . $row['image'];
								$detail[$TAG_CAPTION]			= $row['caption'];
								$detail[$TAG_SUGGESTION]		= $row['suggestion'];
								
								$current_user_id 				= $row['user_id'];
								$detail[$TAG_PROFILE] 			= getProfile($current_user_id, $user_id);

								$interest_id = $row['interest_id'];
								$detail[$TAG_INTEREST_ID] 	= $interest_id;
								
								$query3 = "SELECT * FROM $TABLE_INTEREST WHERE id = '$interest_id'";
								$result3 = mysql_query($query3);
								if (mysql_num_rows($result3) > 0) {
									$row3 = mysql_fetch_array($result3);																		
									$interestObj = array();											
									$interestObj[$TAG_ID] 				= $row3['id'];					
									$interestObj[$TAG_NAME] 			= $row3['name'];	
									$interestObj[$TAG_IMAGE_URL] 		= $INTEREST_PIC_PATH . $row3['image'];
									$interestObj[$TAG_CREATED_DATE] 	= $row3['created_date'];										
									$detail[$TAG_INTEREST] = $interestObj;
								}
								

								$detail[$TAG_LOCATION]   	= $row['location'];
								$detail[$TAG_LAT] 		 	= $row['lat'];
								$detail[$TAG_LNG] 		 	= $row['lng'];
								$detail[$TAG_PLACEID]       = $row['placeId'];
                                $detail[$TAG_GOOGLE_BIZ]       = $row['google_biz'];
								$detail[$TAG_CREATED_DATE] 	= $row['created_date'];
								$detail[$TAG_DISTANCE]  	= distance($lat, $lng, $row['lat'], $row['lng']);
							
								array_push($response[$TAG_DETAILS], $detail);
								$success = true;
							}

							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "Successful";		
							echoRespnse(200, $response);

					} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Suggestions not found";
							echoRespnse(200, $response);
					}
				}
				else
				{
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured.";
					echoRespnse(200, $response);
				}
			}
        });

		
/** Get My Suggestions
 * NAME
 * url - /users/getMyDiscovery
 * method - POST
 */
$app->post('/users/getMyDiscovery', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();
			
			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
				
				mysql_set_charset('utf8');
				// reading post params
				$user_id = $app->request->post('user_id');
				$session_user_id = $app->request->post('session_user_id');
				
				$response = array();
				$response[$TAG_DETAILS] = array();
				
				//CODE
			 	$query = "SELECT * FROM $TABLE_SUGGESTION WHERE user_id = '$user_id' ORDER BY created_date DESC";
				$result = mysql_query($query);
				
				if($result) {
					if (mysql_num_rows($result) > 0) {
						    while ($row = mysql_fetch_array($result)) {
								
								$detail = array();
								$detail[$TAG_ID] 		 	= $row['id'];
								$detail[$TAG_FAV_COUNT]  	= getDiscoveryFavCount($row['id']);
								$detail[$TAG_COMMENT_COUNT] = getDiscoveryCommentCount($row['id']);
								$detail[$TAG_USER_FAV]   	= isDiscoveryUserFav($row['id'], $session_user_id);
								$detail[$TAG_USER_DISC]   	= isUserDiscovery($row['id'], $session_user_id);
								$detail[$TAG_IMAGE_URL]  	= $DISCOVERY_PIC_PATH . $row['image'];
								$detail[$TAG_CAPTION]		= $row['caption'];
								$detail[$TAG_SUGGESTION]	= $row['suggestion'];
								
								$current_user_id = $row['user_id'];								
								$detail[$TAG_PROFILE] 		= getProfileView($current_user_id, $user_id);
																
								$interest_id = $row['interest_id'];
								$detail[$TAG_INTEREST_ID] 	= $interest_id;
								
								$query3 = "SELECT * FROM $TABLE_INTEREST WHERE id = '$interest_id'";
								$result3 = mysql_query($query3);
								if (mysql_num_rows($result3) > 0) {
									$row3 = mysql_fetch_array($result3);																		
									$interestObj = array();											
									$interestObj[$TAG_ID] 				= $row3['id'];					
									$interestObj[$TAG_NAME] 			= $row3['name'];	
									$interestObj[$TAG_IMAGE_URL] 		= $INTEREST_PIC_PATH . $row3['image'];
									$interestObj[$TAG_CREATED_DATE] 	= $row3['created_date'];										
									$detail[$TAG_INTEREST] = $interestObj;
								}								

								$detail[$TAG_LOCATION]   	= $row['location'];
								$detail[$TAG_LAT] 		 	= $row['lat'];
								$detail[$TAG_LNG] 		 	= $row['lng'];
								$detail[$TAG_PLACEID]       = $row['placeId'];
                                $detail[$TAG_GOOGLE_BIZ]       = $row['google_biz'];
								$detail[$TAG_CREATED_DATE] 	= $row['created_date'];
								$detail[$TAG_DISTANCE]  	= distance($lat, $lng, $row['lat'], $row['lng']);
							
								array_push($response[$TAG_DETAILS], $detail);
								$success = true;
							}

							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "Successful";		
							echoRespnse(200, $response);

					} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Suggestions not found";
							echoRespnse(200, $response);
					}
				}
				else
				{
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured";
					echoRespnse(200, $response);
				}
			}
        });


		
/** Get My Suggetion Bookmark list
 * NAME
 * url - /users/getDiscovery
 * method - POST
 */
$app->post('/users/getDiscovery', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$user_id = $app->request->post('user_id');
			
				$response = array();
				$response[$TAG_DETAILS] = array();
				
				//CODE
			 	$query = "SELECT d.id, d.image, d.suggestion, d.location, d.lat, d.lng, d.placeId,d.google_biz, d.interest_id, d.created_date, d.suggestion, d.user_id FROM $TABLE_USER_SUGGESTION ud LEFT JOIN $TABLE_SUGGESTION d ON ud.discovery_id = d.id WHERE ud.user_id = '$user_id' ORDER BY d.created_date DESC";

				$result = mysql_query($query);
				
				if($result)
				{
					if (mysql_num_rows($result) > 0) {
						    while ($row = mysql_fetch_array($result)) {
							
								$detail = array();
								$detail[$TAG_ID] 		 	= $row['id'];
								$detail[$TAG_FAV_COUNT]  	= getDiscoveryFavCount($row['id']);
								// $detail[$TAG_COMMENT_COUNT] = getDiscoveryCommentCount($row['id']);
								$detail[$TAG_USER_FAV]   	= isDiscoveryUserFav($row['id'], $user_id);
								$detail[$TAG_USER_DISC]   	= isUserDiscovery($row['id'], $user_id);
								$detail[$TAG_IMAGE_URL]  	= $DISCOVERY_PIC_PATH . $row['image'];
								$detail[$TAG_CAPTION]		= $row['caption'];
								$detail[$TAG_SUGGESTION]	= $row['suggestion'];

								$current_user_id = $row['user_id'];
								$detail[$TAG_PROFILE] 		= getProfileView($current_user_id, $user_id);
								
								$interest_id = $row['interest_id'];
								$detail[$TAG_INTEREST_ID] 	= $interest_id;
								
								$query3 = "SELECT * FROM $TABLE_INTEREST WHERE id = '$interest_id'";
								$result3 = mysql_query($query3);
								if (mysql_num_rows($result3) > 0) {
									$row3 = mysql_fetch_array($result3);																		
									$interestObj = array();											
									$interestObj[$TAG_ID] 				= $row3['id'];					
									$interestObj[$TAG_NAME] 			= $row3['name'];	
									$interestObj[$TAG_IMAGE_URL] 		= $INTEREST_PIC_PATH . $row3['image'];
									$interestObj[$TAG_CREATED_DATE] 	= $row3['created_date'];										
									$detail[$TAG_INTEREST] = $interestObj;
								}
								
								$detail[$TAG_LOCATION]   	= $row['location'];
								$detail[$TAG_LAT] 		 	= $row['lat'];
								$detail[$TAG_LNG] 		 	= $row['lng'];
								$detail[$TAG_PLACEID]       = $row['placeId'];
                                $detail[$TAG_GOOGLE_BIZ]    = $row['google_biz'];
								$detail[$TAG_CREATED_DATE] 	= $row['created_date'];
								$detail[$TAG_DISTANCE]  	= distance($lat, $lng, $row['lat'], $row['lng']);
							
								array_push($response[$TAG_DETAILS], $detail);
								$success = true;
							}

							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "Successful";		
							echoRespnse(200, $response);

					} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Suggestions not found";
							echoRespnse(200, $response);
					}
				}
				else
				{
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured";
					echoRespnse(200, $response);
				}
			}
        });



/** Add Discovery Favourite
 * url - /discovery/favourite
 * method - POST
 */
$app->post('/discovery/favourite', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$action 		= $app->request->post('action');
				$discovery_id 	= $app->request->post('discovery_id');
				$user_id 		= $app->request->post('user_id');
			
				$response = array();
				
				$isUserInactive = isUserInactive($user_id);
				if($isUserInactive[$TAG_USER_INACTIVE]) {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = $isUserInactive[$TAG_MESSAGE];
					echoRespnse(200, $response);
					return;
				}
				
				$isSuggestionInactive = isSuggestionInactive($discovery_id);
				if($isSuggestionInactive[$TAG_INACTIVE]) {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = $isSuggestionInactive[$TAG_MESSAGE];
					echoRespnse(200, $response);
					return;
				}
				
				$query	= "SELECT * FROM $TABLE_SUGGESTION_FAV WHERE discovery_id = '$discovery_id' AND user_id = $user_id";
				$result = mysql_query($query);
				
				if($result)
				{
					if (mysql_num_rows($result) == 0) {
				
						$query2	= "INSERT INTO $TABLE_SUGGESTION_FAV (discovery_id, user_id,status) VALUES ('$discovery_id', '$user_id', 1)";
						$result2 = mysql_query($query2);
						
						if ($result2) {
							$credit_user_id = getDiscoveryUserId($discovery_id);								
							if($user_id==$credit_user_id) {
								// Don't give points for like if its my discovery
							}
							else {
								getCreditValue($TAG_CREDIT_LIKE_PICTURE, $credit_user_id);
							}
							
							// Send notification
							$notification_type = $TAG_NOTIFICATION_SUGGESTION_LIKE;
							$to_user_id = $credit_user_id;
							$from_user_id = $user_id;
							$content_id = $discovery_id;
							$other_data = "Favorited";
							createNotification($to_user_id, $from_user_id, $content_id, $other_data, $notification_type);
							
							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "New Suggestion favourited";
							echoRespnse(200, $response);

						} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Some error occured";
							echoRespnse(200, $response);
						}

					}  else if (mysql_num_rows($result) > 0) {
						
						$row = mysql_fetch_array($result);
						$status = (int)$row["status"];
						
						// If already following
						if($status==1) {
							
							// Unfollow
							$sql_delete = "UPDATE $TABLE_SUGGESTION_FAV SET status = '0' WHERE discovery_id = '$discovery_id' AND user_id = '$user_id'";
							$sql_result = mysql_query($sql_delete);

							if ($sql_result) {
								$response[$TAG_SUCCESS] = true;
								$response[$TAG_MESSAGE] = "Suggestion unfavourited";
								echoRespnse(200, $response);
							} else {
								$response[$TAG_SUCCESS] = false;
								$response[$TAG_MESSAGE] = "Some error occured";
								echoRespnse(200, $response);
							}
						}
						else {
							// Follow
							$sql_up = "UPDATE $TABLE_SUGGESTION_FAV SET status = '1' WHERE discovery_id = '$discovery_id' AND user_id = '$user_id'";
							$sql_resultup = mysql_query($sql_up);

							if ($sql_resultup) {
								$response[$TAG_SUCCESS] = true;
								$response[$TAG_MESSAGE] = "Suggestion favourited";
								echoRespnse(200, $response);
							} else {
								$response[$TAG_SUCCESS] = false;
								$response[$TAG_MESSAGE] = "Some error occured";
								echoRespnse(200, $response);
							}
						}
					}

				} else {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured";
					echoRespnse(200, $response);
				}
			}
        });	



/** Add Discovery Comment
 * url - /discovery/comment
 * method - POST
 */
$app->post('/discovery/comment', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$comment 		= $app->request->post('comment');
				$discovery_id 	= $app->request->post('discovery_id');
				$user_id 		= $app->request->post('user_id');
				
				
				// Escape
				$comment = escape($comment);
								
				$created_date   = date("Y-m-d H:i:s");
			
				$response = array();
				
				$isUserInactive = isUserInactive($user_id);
				if($isUserInactive[$TAG_USER_INACTIVE]) {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = $isUserInactive[$TAG_MESSAGE];
					echoRespnse(200, $response);
					return;
				}
				
				$isSuggestionInactive = isSuggestionInactive($discovery_id);
				if($isSuggestionInactive[$TAG_INACTIVE]) {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = $isSuggestionInactive[$TAG_MESSAGE];
					echoRespnse(200, $response);
					return;
				}
				

				$query	= "INSERT INTO $TABLE_SUGGESTION_COMMENT (discovery_id, user_id, comment, created_date) VALUES ('$discovery_id', '$user_id', '$comment', '$created_date')";
				$result = mysql_query($query);

				if ($result) {
					
					$credit_user_id = getDiscoveryUserId($discovery_id);								
					if($user_id==$credit_user_id) {
						// Don't give points for comment if its my discovery
					}
					else {
						// Current No points to be given for commenting
						//getCreditValue($TAG_CREDIT_LIKE_PICTURE, $credit_user_id);
					}
					
					// Send notification
					$notification_type = $TAG_NOTIFICATION_SUGGESTION_COMMENT;
					$to_user_id = $credit_user_id;
					$from_user_id = $user_id;
					$content_id = $discovery_id;
					$other_data = "Comment";
					createNotification($to_user_id, $from_user_id, $content_id, $other_data, $notification_type);
					
					$response[$TAG_SUCCESS] = true;
					$response[$TAG_MESSAGE] = $created_date;
					echoRespnse(200, $response);

				} else {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured";
					echoRespnse(200, $response);
				}
			}
        });



/** Get Dicovery comments
 * NAME
 * url - /getDiscoveryComments
 * method - POST
 */
$app->post('/discovery/getComments', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$discovery_id = $app->request->post('discovery_id');
			
				$created_date   = date("Y-m-d H:i:s");
				
				$response = array();
				$response[$TAG_DETAILS] = array();
				
			 	$query = "SELECT * FROM $TABLE_SUGGESTION_COMMENT WHERE discovery_id = '$discovery_id' ORDER BY created_date ASC";
				$result = mysql_query($query);
				
				if($result) {
					if (mysql_num_rows($result) > 0) {
						    while ($row = mysql_fetch_array($result)) {
							
								$detail = array();
								$detail[$TAG_ID] 		 	= $row['id'];
								$detail[$TAG_SUGGESTION_ID] = $row['discovery_id'];
								$detail[$TAG_COMMENT] 		= $row['comment'];
								$detail[$TAG_CREATED_DATE] 	= $row['created_date'];
								
								$current_user_id 		 	= $row['user_id'];
								$detail[$TAG_PROFILE] 		= getProfileView($current_user_id, $user_id);
															
								array_push($response[$TAG_DETAILS], $detail);
								$success = true;
							}

							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "Successful";		
							echoRespnse(200, $response);

					} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Dicovery comments not found";
							echoRespnse(200, $response);
					}

				} else {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured.";
					echoRespnse(200, $response);
				}
			}
        });


/** Spam Suggestion Query
 * url - /suggestion/spam
 * method - POST
 */
$app->post('/suggestion/spam', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();
			
			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$action 		= $app->request->post('action');
				$suggestion_id 	= $app->request->post('suggestion_id');
				$user_id 		= $app->request->post('user_id');
				
				
				$created_date   = date("Y-m-d H:i:s");
				
				$response 		= array();
				
				$isUserInactive = isUserInactive($user_id);
				if($isUserInactive[$TAG_USER_INACTIVE]) {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = $isUserInactive[$TAG_MESSAGE];
					echoRespnse(200, $response);
					return;
				}
				
				$isSuggestionInactive = isSuggestionInactive($suggestion_id);
				if($isSuggestionInactive[$TAG_INACTIVE]) {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = $isSuggestionInactive[$TAG_MESSAGE];
					echoRespnse(200, $response);
					return;
				}
				
				$query	= "SELECT * FROM $TABLE_SUGGESTION_SPAM WHERE suggestion_id = '$suggestion_id' AND user_id = '$user_id'";
				$result = mysql_query($query);				
				
				if (mysql_num_rows($result) == 0) {
					$query1	= "INSERT INTO $TABLE_SUGGESTION_SPAM (suggestion_id, user_id) VALUES ('$suggestion_id', '$user_id')";
					$result1 = mysql_query($query1);
					
					if ($result1) {
						$response[$TAG_ACTION] = getAction($suggestion_id, 1);
						$response[$TAG_SUCCESS] = true;
						$response[$TAG_MESSAGE] = "Local Tip spammed successfully";
						echoRespnse(200, $response);
						
					} else {
						$response[$TAG_SUCCESS] = false;
						$response[$TAG_MESSAGE] = "Some error occured";
						echoRespnse(200, $response);
					}
					
				} else {
					$query2	= "DELETE FROM $TABLE_SUGGESTION_SPAM WHERE suggestion_id = '$suggestion_id' AND user_id = '$user_id'";
					$result2 = mysql_query($query2);

					if ($result2) {
						$response[$TAG_ACTION] = getAction($suggestion_id, 0);
						$response[$TAG_SUCCESS] = true;
						$response[$TAG_MESSAGE] = "Local Tip unspammed successfully";
						echoRespnse(200, $response);

					} else {
						$response[$TAG_SUCCESS] = false;
						$response[$TAG_MESSAGE] = "Some error occured";
						echoRespnse(200, $response);
					}
				}
			}
        });


/** Share Suggestion Query
 * url - /suggestion/share
 * method - POST
 */
$app->post('/suggestion/share', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();
			
			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$suggestion_id 	= $app->request->post('suggestion_id');
				$user_id 		= $app->request->post('user_id');
				
				$created_date   = date("Y-m-d H:i:s");
				
				$response 		= array();
				
				$isUserInactive = isUserInactive($user_id);
				if($isUserInactive[$TAG_USER_INACTIVE]) {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = $isUserInactive[$TAG_MESSAGE];
					echoRespnse(200, $response);
					return;
				}
				
				$isSuggestionInactive = isSuggestionInactive($suggestion_id);
				if($isSuggestionInactive[$TAG_INACTIVE]) {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = $isSuggestionInactive[$TAG_MESSAGE];
					echoRespnse(200, $response);
					return;
				}
				
				$status = ($isSuggestionInactive[$TAG_INACTIVE]==true) ? 2 : (($isUserInactive[$TAG_USER_INACTIVE]==true) ? 0 : 1);
				
			
				$query1	= "INSERT INTO $TABLE_SUGGESTION_SHARE (suggestion_id, user_id, status, created_at) VALUES ('$suggestion_id', '$user_id', '$status', '$created_date')";
				$result1 = mysql_query($query1);
				
				if ($result1) {
					$response[$TAG_ACTION] = getAction($suggestion_id, 1);
					$response[$TAG_SUCCESS] = true;
					$response[$TAG_MESSAGE] = "Local Tip shared successfully";
					echoRespnse(200, $response);
					
				} else {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured";
					echoRespnse(200, $response);
				}
			}
        });



/** Bookmark a Discovery
 * url - /user/discovery
 * method - POST
 */
$app->post('/user/discovery', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$action = $app->request->post('action');
				$discovery_id = $app->request->post('discovery_id');
				$user_id = $app->request->post('user_id');
			
				$response = array();
				
				$isUserInactive = isUserInactive($user_id);
				if($isUserInactive[$TAG_USER_INACTIVE]) {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = $isUserInactive[$TAG_MESSAGE];
					echoRespnse(200, $response);
					return;
				}
				
				$isSuggestionInactive = isSuggestionInactive($discovery_id);
				if($isSuggestionInactive[$TAG_INACTIVE]) {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = $isSuggestionInactive[$TAG_MESSAGE];
					echoRespnse(200, $response);
					return;
				}
				
				$query	= "SELECT * FROM $TABLE_USER_SUGGESTION WHERE discovery_id = '$discovery_id' AND user_id = $user_id";
				$result = mysql_query($query);

				if($result) {
					if (mysql_num_rows($result) == 0) {
						$query2	= "INSERT INTO $TABLE_USER_SUGGESTION (discovery_id, user_id) VALUES ('$discovery_id', '$user_id')";
						$result2 = mysql_query($query2);

						if ($result2) {
							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "Suggestion bookmarked";
							echoRespnse(200, $response);

						} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Some error occured";
							echoRespnse(200, $response);
						}

					}  else if (mysql_num_rows($result) > 0) {
						
						$sql_delete = "DELETE FROM $TABLE_USER_SUGGESTION WHERE discovery_id = '$discovery_id' AND user_id = '$user_id'";
						$sql_result = mysql_query($sql_delete);

						if ($sql_result) {
							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "Suggestion bookmark removed";
							echoRespnse(200, $response);

						} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Some error occured";
							echoRespnse(200, $response);
						}
					}

				} else {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured";
					echoRespnse(200, $response);
				}
			}
        });


/** Get Folks
 * NAME
 * url - /getFolks
 * method - POST
 */
$app->post('/getFolks', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$lat = $app->request->post('lat');
				$lng = $app->request->post('lng');
				$range = $app->request->post('range');
				$user_id = $app->request->post('user_id');
				$limit = $app->request->post('limit');
			
				$response = array();
				$response[$TAG_DETAILS] = array();
				
				$range = $RANGE_VALUE_FOLK;
				
				//CODE
			 	$query = "SELECT *, (1.609344 * 3956 * acos( cos( radians($lat) ) * cos( radians(current_lat) ) * cos( radians(current_lng) - radians($lng) ) + sin( radians($lat) ) * sin( radians(current_lat) ) ) ) as distance FROM $TABLE_USER WHERE id != '$user_id' HAVING distance < '$range' and status=1 ORDER BY credits DESC LIMIT $limit, 10";

				$result = mysql_query($query);
				
				if($result)
				{
					if (mysql_num_rows($result) > 0) {
						    while ($row = mysql_fetch_array($result)) {
							
								$current_user_id  = $row['id'];
								$detail = getProfileItem($row, $current_user_id, $user_id);
							
							/*
								$detail = array();
								$detail[$TAG_ID] 				= $row['id'];
								$detail[$TAG_NAME]   			= $row['name'];
								$detail[$TAG_EMAIL] 			= $row['email'];
								$detail[$TAG_NUM_ASKS] 			= $row['num_asks'];
								// $detail[$TAG_NUM_RESPONSES] 		= $row['num_responses'];
								$detail[$TAG_NUM_RESPONSES] 	= getQueriesAttended($row['id']);
								$detail[$TAG_ABOUT_ME] 			= $row['about_me'];
								$detail[$TAG_PROFESSION]   		= getProfession($row['profession_id']);
								$detail[$TAG_LOCATION] 			= $row['location'];
								$detail[$TAG_PROVIDER] 			= $row['provider'];
								$detail[$TAG_FACEBOOK_ID] 	 	= $row['facebook_id'];
								$detail[$TAG_USER_FOLLOWED] 	= isUserFollowed($row['id'], $user_id);

								if ($row['provider'] == $TAG_EMAIL) {
									$detail[$TAG_IMAGE_URL]  	= $USER_PIC_PATH . $row['image'];
								} else {
									$detail[$TAG_IMAGE_URL]  	= $row['image'];
								}
								
								$detail[$TAG_CREATED_DATE] 		= $row['created_date'];
								$detail[$TAG_NOTIFICATION_FLAG]	= $row['notification_flag'];
								$detail[$TAG_CREDITS] 			= $row['credits'];
								$detail[$TAG_DISTANCE]			= distance($lat, $lng, $row['current_lat'], $row['current_lng']);
							*/
								array_push($response[$TAG_DETAILS], $detail);
								$success = true;
							}

							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "Successful";		
							echoRespnse(200, $response);

					} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Folks not found";
							echoRespnse(200, $response);
					}
				}
				else
				{
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured.";
					echoRespnse(200, $response);
				}
			}
        });





/** Search Folks
 * NAME
 * url - /folks/search
 * method - POST
 */
$app->post('/folks/search', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$lat 		= $app->request->post('lat');
				$lng 		= $app->request->post('lng');
				$user_id 	= $app->request->post('user_id');
				$search 	= $app->request->post('search');
				
				// Escape
				$search = escape($search);
				
				$search 	= "%" . $search . "%";
				
					
				if($lat==0 && $lng==0) {
					
					// Find users lat lon
					$profile_location = getProfileLocation($user_id);
					$lat = $profile_location[$TAG_CURRENT_LAT];
					$lng = $profile_location[$TAG_CURRENT_LNG];
				}		
			
				$response = array();
				$response[$TAG_DETAILS] = array();
				
				
			 	$query1 = "SELECT * FROM $TABLE_USER WHERE user_id = '$user_id'";
				$result1 = mysql_query($query1);
				
				if (mysql_num_rows($result1) > 0) {
					$row1 = mysql_fetch_array($result1);
					$lat				= $row1['current_lat'];
					$lng				= $row1['current_lng'];
				}
				
			 	$query = "SELECT * FROM $TABLE_USER WHERE name LIKE '$search' AND id != $user_id and status=1 ORDER BY credits DESC";
				$result = mysql_query($query);
				
				if($result)
				{
					if (mysql_num_rows($result) > 0) {
						    while ($row = mysql_fetch_array($result)) {
							
								$current_user_id  = $row['id'];
								$detail = getProfileItem($row, $current_user_id, $user_id);
								
								/*
								$detail = array();
								$detail[$TAG_ID] 				= $row['id'];
								$detail[$TAG_NAME]   			= $row['name'];
								$detail[$TAG_EMAIL] 			= $row['email'];
								$detail[$TAG_NUM_ASKS] 			= $row['num_asks'];
								// $detail[$TAG_NUM_RESPONSES] 		= $row['num_responses'];
								$detail[$TAG_NUM_RESPONSES] 	= getQueriesAttended($row['id']);
								$detail[$TAG_ABOUT_ME] 			= $row['about_me'];
								$detail[$TAG_PROFESSION]   		= getProfession($row['profession_id']);
								$detail[$TAG_LOCATION] 			= $row['location'];
								$detail[$TAG_PROVIDER] 			= $row['provider'];
								$detail[$TAG_FACEBOOK_ID] 	 	= $row['facebook_id'];
								$detail[$TAG_USER_FOLLOWED] 	= isUserFollowed($row['id'], $user_id);

								if ($row['provider'] == $TAG_EMAIL) {
									$detail[$TAG_IMAGE_URL]  	= $USER_PIC_PATH . $row['image'];
								} else {
									$detail[$TAG_IMAGE_URL]  	= $row['image'];
								}
								
								$detail[$TAG_CREATED_DATE] 		= $row['created_date'];
								$detail[$TAG_NOTIFICATION_FLAG]	= $row['notification_flag'];
								$detail[$TAG_CREDITS] 			= $row['credits'];
								$detail[$TAG_DISTANCE]			= distance($lat, $lng, $row['current_lat'], $row['current_lng']);
							*/
								array_push($response[$TAG_DETAILS], $detail);
								$success = true;
							}

							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "Successful";		
							echoRespnse(200, $response);

					} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Folks not found";
							echoRespnse(200, $response);
					}
				}
				else
				{
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured.";
					echoRespnse(200, $response);
				}
			}
        });


/** Get list of users who have liked a suggestion
 * NAME
 * url - /users/getSuggestionLike
 * method - POST
 */
$app->post('/users/getSuggestionLike', function() use ($app) {
		// check for required params
		include("../include/constant.php");
		$db = new DB_CONNECT();

		$token = $app->request()->headers->get($TAG_TOKEN);	
		if(authenticate($token)){
						
			mysql_set_charset('utf8');
			// reading post params
			$user_id = $app->request->post('user_id');
			$suggestion_id = $app->request->post('suggestion_id');
			$position = $app->request->post('position');
		
			$response = array();
			$response[$TAG_DETAILS] = array();
			
			
			//CODE
			$query = "SELECT * FROM $TABLE_USER WHERE id in (SELECT user_id FROM `lokaso_discovery_fav` where discovery_id='$suggestion_id')";
			
			$result = mysql_query($query);
			
			if($result)
			{
				if (mysql_num_rows($result) > 0) {
						while ($row = mysql_fetch_array($result)) {
							
							$current_user_id  = $row['id'];
							$detail = getProfileItem($row, $current_user_id, $user_id);
								
							array_push($response[$TAG_DETAILS], $detail);
							$success = true;
						}

						$response["q"] = $query;
						$response[$TAG_SUCCESS] = true;
						$response[$TAG_MESSAGE] = "Successful";		
						echoRespnse(200, $response);

				} else {
					
						$response["q"] = $query;
						$response[$TAG_SUCCESS] = false;
						$response[$TAG_MESSAGE] = "Folks not found";
						echoRespnse(200, $response);
				}
			}
			else
			{
				$response["q"] = $query;
				$response[$TAG_SUCCESS] = false;
				$response[$TAG_MESSAGE] = "Some error occured.";
				echoRespnse(200, $response);
			}
		}
	});

		

/** Follow/Unfollow user
 * url - /users/follow
 * method - POST
 */
$app->post('/users/follow', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$action = $app->request->post('action');
				$leader = $app->request->post('leader');
				$follower = $app->request->post('follower');
			
				$response = array();

				
				$created_date 	= date("Y-m-d H:i:s");
				
				$isUserInactive = isUserInactive($leader);
				if($isUserInactive[$TAG_USER_INACTIVE]) {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = $isUserInactive[$TAG_MESSAGE];
					echoRespnse(200, $response);
					return;
				}
								
				if ($leader == $follower) {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "User cannot follow himself";
					echoRespnse(200, $response);
					return;
				}
				
				$query	= "SELECT * FROM $TABLE_USER_FOLLOW WHERE leader = '$leader' AND follower = $follower";
				$result = mysql_query($query);
				
				if($result)
				{
					if (mysql_num_rows($result) > 0) {
						
						$row = mysql_fetch_array($result);
						$status = (int)$row["status"];
						
						// If already following
						if($status==1) {
							
							// Unfollow
							$sql_delete = "UPDATE $TABLE_USER_FOLLOW SET status = '0', modified_date='$created_date' WHERE leader = '$leader' AND follower = '$follower'";
							$sql_result = mysql_query($sql_delete);

							if ($sql_result) {
								$response[$TAG_SUCCESS] = true;
								$response[$TAG_MESSAGE] = "User Unfollowed";
								echoRespnse(200, $response);

							} else {
								$response[$TAG_SUCCESS] = false;
								$response[$TAG_MESSAGE] = "Some error occured";
								echoRespnse(200, $response);
							}
						}
						else {
							// Follow
							$sql_up = "UPDATE $TABLE_USER_FOLLOW SET status = '1', modified_date='$created_date' WHERE leader = '$leader' AND follower = '$follower'";
							$sql_resultup = mysql_query($sql_up);

							if ($sql_resultup) {
								$response[$TAG_SUCCESS] = true;
								$response[$TAG_MESSAGE] = "User Unfollowed";
								echoRespnse(200, $response);

							} else {
								$response[$TAG_SUCCESS] = false;
								$response[$TAG_MESSAGE] = "Some error occured";
								echoRespnse(200, $response);
							}
						}
					}
					else {
											
						$query2	= "INSERT INTO $TABLE_USER_FOLLOW (leader, follower, created_date) VALUES ('$leader', '$follower', '$created_date')";
						$result2 = mysql_query($query2);

						if ($result2) {
							$user_name = getUserName($follower);
																
							// Send notification
							$notification_type = $TAG_NOTIFICATION_USER_FOLLOW;
							$to_user_id = $leader;
							$from_user_id = $follower;
							$content_id = $leader;
							$other_data = $user_name;
							createNotification($to_user_id, $from_user_id, $content_id, $other_data, $notification_type);
							
							getCreditValue($TAG_CREDIT_FOLLOW, $leader);
							
							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "User Followed";
							echoRespnse(200, $response);

						} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Some error occured";
							echoRespnse(200, $response);
						}
					}

				} else {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured";
					echoRespnse(200, $response);
				}
			}
        });	




/** Get Queries
 * NAME
 * url - /getQueries
 * method - POST
 */
$app->post('/getQueries', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$user_id = $app->request->post('user_id');
				$lat = $app->request->post('lat');
				$lng = $app->request->post('lng');
				$range = $app->request->post('range');
				$limit = $app->request->post('limit');
				$position = $app->request->post('position');
				$filter_by = $app->request->post('filter_by');
				$current_date = date("Y-m-d H:i:s");
				
				
				$interest_ids = $app->request->post('interest_ids');
				
				
				$display_items = 10;
				
				if($position==null) {
					$position = "xxx";
				}
				else {
					$limit = $position * $display_items;
				}
				
				if($interest_ids==null) {
					
					$query	= "SELECT * FROM $TABLE_USER_INTEREST WHERE user_id = '$user_id'";
					$result = mysql_query($query);
				
					if($result)
					{
						if (mysql_num_rows($result) > 0) {
							$interest_ids = "";
						    while ($row = mysql_fetch_array($result)) {
							
								$interest_id = $row['interest_id'];
								$interest_ids = $interest_ids.$interest_id.",";
							}
						}
					}
				}
			
				if($interest_ids==null || ($interest_ids!=null && strlen($interest_ids)==0)) {
					$interest_ids = "1,2,3,4,5,6,";
				}
			
				$interest_ids = rtrim($interest_ids, ",");
								
				$range = $RANGE_VALUE_QUERY;
				
				///////
				
				
				 
				if($filter_by!=null) {
					if($filter_by==2) { // By distance			
					
						$order_by = " ORDER BY distance ASC ";	
									
						$query = "SELECT *, (1.609344 * 3956 * acos( cos( radians($lat) ) * cos( radians(lat) ) * cos( radians(lng) - radians($lng) ) + sin( radians($lat) ) * sin( radians(lat) ) ) ) as distance  
							 FROM $TABLE_ASK HAVING distance < '$range' and status = 1 
						 and interest_id in (".$interest_ids.") 
							 ".$order_by." LIMIT $limit, 10";	
					}
					else if($filter_by==3) { // By expiring
						$order_by = " ORDER BY is_valid DESC, valid_until ASC ";	
									
									
						$query = "SELECT *, (1.609344 * 3956 * acos( cos( radians($lat) ) * cos( radians(lat) ) * cos( radians(lng) - radians($lng) ) + sin( radians($lat) ) * sin( radians(lat) ) ) ) as distance,
						CASE 
						WHEN valid_until > '$current_date'
						   THEN 1 
						   ELSE 0 
						END as is_valid  
							 FROM $TABLE_ASK HAVING distance < '$range' and status = 1 
						 and interest_id in (".$interest_ids.") 
						 AND valid_until >= '$current_date'
							 ".$order_by." LIMIT $limit, 10";
		
					}
					else { // By latest
						$order_by = " ORDER BY created_date DESC ";		
						$query = "SELECT *, (1.609344 * 3956 * acos( cos( radians($lat) ) * cos( radians(lat) ) * cos( radians(lng) - radians($lng) ) + sin( radians($lat) ) * sin( radians(lat) ) ) ) as distance  
							 FROM $TABLE_ASK HAVING distance < '$range' and status = 1 
						 and interest_id in (".$interest_ids.") 
							 ".$order_by." LIMIT $limit, 10";					
					}
				}
				else {
					$order_by = " ORDER BY valid_until ASC ";	
						$query = "SELECT *, (1.609344 * 3956 * acos( cos( radians($lat) ) * cos( radians(lat) ) * cos( radians(lng) - radians($lng) ) + sin( radians($lat) ) * sin( radians(lat) ) ) ) as distance  
							 FROM $TABLE_ASK HAVING distance < '$range' and status = 1 
						 and interest_id in (".$interest_ids.") 
							 ".$order_by." LIMIT $limit, 10";	
				}
						
			
				$response = array();
				$response[$TAG_DETAILS] = array();
				
				
				
				
				//CODE
			 	//$query = "SELECT *, (1.609344 * 3956 * acos( cos( radians($lat) ) * cos( radians(lat) ) * cos( radians(lng) - radians($lng) ) + sin( radians($lat) ) * sin( radians(lat) ) ) ) as distance FROM $TABLE_ASK HAVING distance < '$range' AND valid_until >= '$current_date' and status = 1 ORDER BY valid_until ASC LIMIT $limit, 10";
				
				// Now even expired queries will be fetched.
				/*
				$query = "SELECT *, (1.609344 * 3956 * acos( cos( radians($lat) ) * cos( radians(lat) ) * cos( radians(lng) - radians($lng) ) + sin( radians($lat) ) * sin( radians(lat) ) ) ) as distance  
				 FROM $TABLE_ASK HAVING distance < '$range' and status = 1 
				 ORDER BY valid_until ASC LIMIT $limit, 10";
*/
				$result = mysql_query($query);
				
				if($result)
				{
					if (mysql_num_rows($result) > 0) {
						    while ($row = mysql_fetch_array($result)) {
							
								$detail = array();
								$detail[$TAG_ID] 				= $row['id'];
								$detail[$TAG_RESPONSE_COUNT] 	= getResponseCount($row['id']);
								$detail[$TAG_QUERY_FAV]   		= isQueryFav($row['id'], $user_id);
								$detail[$TAG_DESCRIPTION]   	= $row['description'];
								$detail[$TAG_VALID_DATE] 	 	= $row['valid_until'];
								$detail[$TAG_FLAG] 				= isQueryFlagged($row['id'], $user_id);

								if($user_id != $row['user_id']) {
									$detail[$TAG_USER_FOLLOWED] = isUserFollowed($row['user_id'], $user_id);
								}
								
								$current_user_id 		 	= $row['user_id'];
								$detail[$TAG_PROFILE] 		= getProfileView($current_user_id, $user_id);
								

								$valid_until 					= $row['valid_until'];
								$date_valid_until 				= findRemainingDate($valid_until); 

								$detail[$TAG_VALID_UNTIL] 		= $date_valid_until;
								$detail[$TAG_CREATED_DATE] 		= $row['created_date'];
								$detail[$TAG_LOCATION] 			= $row['location'];
								$detail[$TAG_INTEREST_ID] 		= $row['interest_id'];
								$detail[$TAG_FLAG_SOLVED] 	 	= $row['flag_solved'];
								$detail[$TAG_MARKED_INAPPROPRIATE] = $row['marked_inappropriate'];
								$detail[$TAG_DISTANCE]			= distance($lat, $lng, $row['lat'], $row['lng']);
							
								array_push($response[$TAG_DETAILS], $detail);
								$success = true;
							}

							$response["q"] = $filter_by. " ". $query;
							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "Successful";		
							echoRespnse(200, $response);

					} else {
						$response[$TAG_SUCCESS] = false;
						$response[$TAG_MESSAGE] = "Queries not found";
						echoRespnse(200, $response);
					}
				}
				else
				{
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured.";
					echoRespnse(200, $response);
				}
			}
        });




/** Search Queries
 * NAME
 * url - /getQueries
 * method - POST
 */
$app->post('/query/search', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$lat 		= $app->request->post('lat');
				$lng 		= $app->request->post('lng');
				$user_id 	= $app->request->post('user_id');
				$search 	= $app->request->post('search');
						
				if($lat==0 && $lng==0) {
					
					// Find users lat lon
					$profile_location = getProfileLocation($user_id);
					$lat = $profile_location[$TAG_CURRENT_LAT];
					$lng = $profile_location[$TAG_CURRENT_LNG];
				}		
				
				// Escape
				$search = escape($search);
				
				$search 	= "%" . $search . "%";
			
				$response = array();
				$response[$TAG_DETAILS] = array();
				
				//CODE
			 	$query = "SELECT * FROM $TABLE_ASK WHERE description LIKE '$search' and status=1 ORDER BY valid_until ASC";
				$result = mysql_query($query);
				
				if($result)
				{
					if (mysql_num_rows($result) > 0) {
						    while ($row = mysql_fetch_array($result)) {
							
								$detail = array();
								$detail[$TAG_ID] 				= $row['id'];
								$detail[$TAG_RESPONSE_COUNT] 	= getResponseCount($row['id']);
								$detail[$TAG_QUERY_FAV]   		= isQueryFav($row['id'], $user_id);
								$detail[$TAG_DESCRIPTION]   	= $row['description'];
								$detail[$TAG_VALID_DATE] 	 	= $row['valid_until'];
								$detail[$TAG_FLAG] 				= isQueryFlagged($row['id'], $user_id);
								
								$current_user_id = $row['user_id'];
								$detail[$TAG_PROFILE] = getProfileView($current_user_id, $user_id);
								
								if($user_id != $current_user_id) {
									$detail[$TAG_USER_FOLLOWED] 	= isUserFollowed($current_user_id, $user_id);
								}
																
								$valid_until 					= $row['valid_until'];
								$date_valid_until 				= findRemainingDate($valid_until);

								$detail[$TAG_VALID_UNTIL] 		= $date_valid_until;
								$detail[$TAG_CREATED_DATE] 		= $row['created_date'];
								$detail[$TAG_LOCATION] 			= $row['location'];
								$detail[$TAG_INTEREST_ID] 		= $row['interest_id'];
								$detail[$TAG_FLAG_SOLVED] 	 	= $row['flag_solved'];
								$detail[$TAG_MARKED_INAPPROPRIATE] = $row['marked_inappropriate'];
								$distance			= distance($lat, $lng, $row['lat'], $row['lng']);
								$detail[$TAG_DISTANCE]			= $distance;
								$detail[$TAG_DISTANCE_DISPLAY]  	= getDistanceDisplay($distance);
							
								array_push($response[$TAG_DETAILS], $detail);
								$success = true;
							}

							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "Successful";		
							echoRespnse(200, $response);

					} else {
						$response[$TAG_SUCCESS] = false;
						$response[$TAG_MESSAGE] = "Queries not found";
						echoRespnse(200, $response);
					}
				}
				else
				{
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured.";
					echoRespnse(200, $response);
				}
			}
        });





/** Get Profile
 * NAME
 * url - /getProfile
 * method - POST
 */
$app->post('/getProfile', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$user_id = $app->request->post('user_id');
				$current_user_id = $app->request->post('current_user_id');
			
				$response = array();
				$response[$TAG_DETAILS] = array();
				
				$detail = getProfileView($user_id, $current_user_id);
				if($detail==null) {
			
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "User not found";
					echoRespnse(200, $response);
				}
				else {
					array_push($response[$TAG_DETAILS], $detail);
			
					$response[$TAG_SUCCESS] = true;
					$response[$TAG_MESSAGE] = "Successful";		
					echoRespnse(200, $response);
					
					
				}				
			}
        });



/** Update Profile
 * NAME
 * url - /users/updateProfile
 * method - POST
 * params - TAG_USER_ID
 */
$app->post('/users/updateProfile', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();
			
			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				
				// reading post params
				$user_id = $app->request->post('user_id');
				$name = $app->request->post('name');
				$image = $app->request->post('image');
				$about_me = $app->request->post('about_me');
				$profession_id = $app->request->post('profession_id');
				$location = $app->request->post('location');
				$current_lat = $app->request->post('current_lat');
				$current_lng = $app->request->post('current_lng');
				$notification_flag = $app->request->post('notification_flag');
								
				// Escape
				$name = escape($name);
				$about_me = escape($about_me);
				$location = escape($location);
				
			
				$response = array();
				
				
				$query	= "SELECT id FROM $TABLE_USER WHERE id = '$user_id'";
				$result = mysql_query($query);
				
				if($result) {

					if (mysql_num_rows($result) == 1) {
						$profile_pic = "";
						if(!empty($image)) {
							$profile_pic = _upload_file($image, $USER_PICS);
							$query2	= "UPDATE $TABLE_USER SET name = '$name', image = '$profile_pic', provider='email', about_me = '$about_me', profession_id = '$profession_id', location = '$location', current_lat = '$current_lat', current_lng = '$current_lng', notification_flag = '$notification_flag' WHERE id = '$user_id'";
							$result2 = mysql_query($query2);
							//echo "x : ".$query2;

						} else {
							$query2	= "UPDATE $TABLE_USER SET name = '$name', about_me = '$about_me', profession_id = '$profession_id', location = '$location', current_lat = '$current_lat', current_lng = '$current_lng', notification_flag = '$notification_flag' WHERE id = '$user_id'";
							
							//echo $query2;
							$result2 = mysql_query($query2);
						}

						if ($result2) {
							/*$response[$TAG_DETAILS]	= getUserImage($user_id);*/
							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "Profile Saved";
							echoRespnse(200, $response);

						} else {
						    $response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Some error occured q";
							echoRespnse(200, $response);
						}

					} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "User donot exist";
							echoRespnse(200, $response);
					}

				} else {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured.";
					echoRespnse(200, $response);
				}
			}
        });







/** Get Queries
 * NAME
 * url - /getQueries
 * method - POST
 */
$app->post('/users/getQueries', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$user_id = $app->request->post('user_id');
				$lat = $app->request->post('lat');
				$lng = $app->request->post('lng');
				$current_date = date("Y-m-d H:i:s");
			
				$response = array();
				$response[$TAG_DETAILS] = array();
				
				//CODE
			 	$query = "SELECT * FROM $TABLE_ASK WHERE user_id = '$user_id' ORDER BY created_date DESC";
				$result = mysql_query($query);
				
				if($result)
				{
					if (mysql_num_rows($result) > 0) {
						    while ($row = mysql_fetch_array($result)) {
							
								$detail = array();
								$query_id = $row['id'];
								$detail[$TAG_ID] 				= $query_id;
								
								$current_user_id = $row['user_id'];
								$detail[$TAG_PROFILE] = getProfileView($current_user_id, $user_id);
																
								$detail[$TAG_DESCRIPTION]   	= $row['description'];
								$detail[$TAG_VALID_DATE] 	 	= $row['valid_until'];

								$valid_until 					= $row['valid_until'];
								// if(strtotime($valid_until) >= strtotime($current_date) {
								$date_valid_until 				= findRemainingDate($valid_until);
								$detail[$TAG_VALID_UNTIL] 		= $date_valid_until;

								/*} else {
									$detail[$TAG_VALID_UNTIL] 		= "0";
								}*/

								$detail[$TAG_RESPONSE_COUNT] 	= getQueryResponseCount($query_id); //$row['response_count'];
								$detail[$TAG_CREATED_DATE] 		= $row['created_date'];
								$detail[$TAG_LOCATION] 			= $row['location'];
								$detail[$TAG_INTEREST_ID] 		= $row['interest_id'];
								$detail[$TAG_FLAG_SOLVED] 	 	= $row['flag_solved'];
								$detail[$TAG_MARKED_INAPPROPRIATE] = $row['marked_inappropriate'];
								$detail[$TAG_FLAG] 				= isQueryFlagged($row['id'], $user_id);
								$distance			= distance($lat, $lng, $row['lat'], $row['lng']);
								$detail[$TAG_DISTANCE]			= $distance;
								$detail[$TAG_DISTANCE_DISPLAY]  	= getDistanceDisplay($distance);
							
								array_push($response[$TAG_DETAILS], $detail);
								$success = true;
							}

							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "Successful";		
							echoRespnse(200, $response);

					} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Queries not found";
							echoRespnse(200, $response);
					}
				}
				else
				{
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured.";
					echoRespnse(200, $response);
				}
			}
        });

$app->post('/users/getQueryLocation', function() use ($app) {
    // check for required params
    include("../include/constant.php");
    $db = new DB_CONNECT();
    $token = $app->request()->headers->get($TAG_TOKEN);
    if(authenticate($token)){

        mysql_set_charset('utf8');
        // reading post params
        $user_id = $app->request->post('user_id');
        $lat = $app->request->post('lat');
        $lng = $app->request->post('lng');
        //$current_date = date("Y-m-d H:i:s");

        $response = array();
        $response[$TAG_DETAILS] = array();

        //CODE
        $query = "SELECT * FROM $TABLE_ASK WHERE user_id = '$user_id' ORDER BY created_date DESC";
        $result = mysql_query($query);

        if($result)
        {
            if (mysql_num_rows($result) > 0) {
                while ($row = mysql_fetch_array($result)) {

                    $detail = array();
                    $query_id = $row['id'];
                    $detail[$TAG_ID] 				= $query_id;

                    $current_user_id = $row['user_id'];
                    $detail[$TAG_PROFILE] = getProfileView($current_user_id, $user_id);

                    //$detail[$TAG_DESCRIPTION]   	= $row['description'];
                    $detail[$TAG_VALID_DATE] 	 	= $row['valid_until'];

                    $valid_until 					= $row['valid_until'];
                    // if(strtotime($valid_until) >= strtotime($current_date) {
                    $date_valid_until 				= findRemainingDate($valid_until);
                    $detail[$TAG_VALID_UNTIL] 		= $date_valid_until;

                    /*} else {
                        $detail[$TAG_VALID_UNTIL] 		= "0";
                    }*/

                    //$detail[$TAG_RESPONSE_COUNT] 	= getQueryResponseCount($query_id); //$row['response_count'];
                    $detail[$TAG_CREATED_DATE] 		= $row['created_date'];
                    $detail[$TAG_LOCATION] 			= $row['location'];
                    $detail[$TAG_INTEREST_ID] 		= $row['interest_id'];
                    $detail[$TAG_FLAG_SOLVED] 	 	= $row['flag_solved'];
                    $detail[$TAG_MARKED_INAPPROPRIATE] = $row['marked_inappropriate'];
                    $detail[$TAG_FLAG] 				= isQueryFlagged($row['id'], $user_id);
                    $distance			= distance($lat, $lng, $row['lat'], $row['lng']);
                    $detail[$TAG_DISTANCE]			= $distance;
                    $detail[$TAG_DISTANCE_DISPLAY]  	= getDistanceDisplay($distance);

                    array_push($response[$TAG_DETAILS], $detail);
                    $success = true;
                }

                $response[$TAG_SUCCESS] = true;
                $response[$TAG_MESSAGE] = "Successful";
                echoRespnse(200, $response);

            } else {
                $response[$TAG_SUCCESS] = false;
                $response[$TAG_MESSAGE] = "Queries not found";
                echoRespnse(200, $response);
            }
        }
        else
        {
            $response[$TAG_SUCCESS] = false;
            $response[$TAG_MESSAGE] = "Some error occured.";
            echoRespnse(200, $response);
        }
    }
});


/** Get Followers
 * NAME
 * url - /users/getFollowers
 * method - POST
 */
$app->post('/users/getFollowers', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$user_id = $app->request->post('user_id');
				$lat = $app->request->post('lat');
				$lng = $app->request->post('lng');
				
				
				if($lat==0 && $lng==0) {
					
					// Find users lat lon
					$profile_location = getProfileLocation($user_id);
					$lat = $profile_location[$TAG_CURRENT_LAT];
					$lng = $profile_location[$TAG_CURRENT_LNG];
				}		
				
			
				$response = array();
				$response[$TAG_DETAILS] = array();
				
				
			 	$query = "SELECT * FROM $TABLE_USER_FOLLOW uf LEFT JOIN $TABLE_USER u ON  uf.follower = u.id WHERE uf.leader = '$user_id' and uf.status=1 ORDER BY u.created_date DESC";

				$result = mysql_query($query);
				
				if($result)
				{
					if (mysql_num_rows($result) > 0) {
						    while ($row = mysql_fetch_array($result)) {
							
								$detail = array();
								$detail[$TAG_ID] 			= $row['id'];
								$detail[$TAG_NAME]   		= $row['name'];
								$detail[$TAG_EMAIL] 		= $row['email'];
								$detail[$TAG_NUM_ASKS] 		= $row['num_asks'];
								$detail[$TAG_NUM_RESPONSES] = $row['num_responses'];
								$detail[$TAG_ABOUT_ME] 		= $row['about_me'];
								$detail[$TAG_PROFESSION]   	= getProfession($row['profession_id']);
								$detail[$TAG_LOCATION] 		= $row['location'];
								$detail[$TAG_PROVIDER] 		= $row['provider'];
								$detail[$TAG_FACEBOOK_ID] 	= $row['facebook_id'];
								$detail[$TAG_USER_FOLLOWED] = isUserFollowed($row['id'], $user_id);

								if ($row['provider'] == $TAG_EMAIL) {
									$detail[$TAG_IMAGE_URL] = $USER_PIC_PATH . $row['image'];
								} else {
									$detail[$TAG_IMAGE_URL] = $row['image'];
								}
								
								$detail[$TAG_CREATED_DATE] 	= $row['created_date'];
								$detail[$TAG_NOTIFICATION_FLAG]= $row['notification_flag'];
								$detail[$TAG_CREDITS] 		= $row['credits'];
								$distance		= distance($lat, $lng, $row['current_lat'], $row['current_lng']);
								$detail[$TAG_DISTANCE]		= $distance;
								$detail[$TAG_DISTANCE_DISPLAY]  	= getDistanceDisplay($distance);
							
								array_push($response[$TAG_DETAILS], $detail);
								$success = true;
							}

							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "Successful";		
							echoRespnse(200, $response);

					} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Currently you donot have followers";
							echoRespnse(200, $response);
					}
				}
				else
				{
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured.";
					echoRespnse(200, $response);
				}
			}
        });




/** Create Chat room
 * url - /createChatroom
 * method - POST
 */
$app->post('/createChatroom', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$from_user_id = $app->request->post('from_user_id');
				$to_user_id = $app->request->post('to_user_id');
			
			
				$created_date = date("Y-m-d H:i:s");
			
				$response = array();
				$response[$TAG_DETAILS] = array();
				
				
				$isUserInactive = isUserInactive($from_user_id);
				if($isUserInactive[$TAG_USER_INACTIVE]) {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = $isUserInactive[$TAG_MESSAGE];
					echoRespnse(200, $response);
					return;
				}
				

				$query	= "SELECT * FROM $TABLE_CHAT_ROOM WHERE from_user_id = '$from_user_id' AND to_user_id = '$to_user_id' OR from_user_id = '$to_user_id' AND to_user_id = '$from_user_id'";
				$result = mysql_query($query);
				
				if($result) {
					if (mysql_num_rows($result) == 0) {
						$query2	= "INSERT INTO $TABLE_CHAT_ROOM (from_user_id, to_user_id, created_date, updated_date) 
									VALUES ('$from_user_id', '$to_user_id', '$created_date', '$created_date')";
						$result2 = mysql_query($query2);

						if ($result2) {
							$id = mysql_insert_id();
							$response[$TAG_DETAILS] = $id;

							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "Chat room created successfully";
							echoRespnse(200, $response);

						} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Some error occured";
							echoRespnse(200, $response);
						}

					} else {
						while ($row = mysql_fetch_array($result)) {
							$response[$TAG_DETAILS] = $row['id'];
						}

						$response[$TAG_SUCCESS] = true;
						$response[$TAG_MESSAGE] = "Chat room is already present";
						echoRespnse(200, $response);
					}

				} else {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured";
					echoRespnse(200, $response);
				}
			}
        });



/** Get Users Chat rooms
 * url - /users/chatroom
 * method - POST
 */
$app->post('/users/chatroom', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$user_id = $app->request->post('user_id');
				
				$response = array();
				$response[$TAG_DETAILS] = array();

				//$query	= "SELECT * FROM $TABLE_CHAT_ROOM WHERE from_user_id = '$user_id' OR to_user_id = '$user_id'";
				
				$query	= "select cr.*, c.message, c.from_user_id as chat_from_user_id, c.created_date as chat_created_date, c.to_user_seen from $TABLE_CONVERSATION as c 
				left join $TABLE_CHAT_ROOM as cr on cr.id = c.chat_id 
				where c.chat_id in (SELECT id FROM $TABLE_CHAT_ROOM WHERE from_user_id = '$user_id' OR to_user_id = '$user_id') 
				group by c.chat_id order by cr.updated_date desc, c.created_date desc";
				
				$result = mysql_query($query);
				
				if($result) {
					if (mysql_num_rows($result) > 0) {
						while ($row = mysql_fetch_array($result)) {
							$detail = array();
							$detail[$TAG_ID] 			= $row['id'];
							
							$from_user_id = $row['from_user_id'];
							$to_user_id = $row['to_user_id'];
							
							$detail[$TAG_FROM_USER_ID]  = $from_user_id;
							$detail[$TAG_TO_USER_ID] 	= $to_user_id;
							$detail[$TAG_CREATED_DATE] 	= $row['created_date'];
							
							// These are the chat details of the latest chat
							$detail[$TAG_MESSAGE] 				= $row['message'];
							$detail[$TAG_TO_USER_SEEN]   		= $row['to_user_seen'];
							$detail[$TAG_CHAT_FROM_USER_ID] 	= $row['chat_from_user_id'];
							$detail[$TAG_CHAT_CREATED_DATE] 	= $row['chat_created_date'];
							
													
							$other_user_id = $to_user_id;
							if($user_id==$to_user_id) {
								$other_user_id = $from_user_id;								
							}
							
							$unread_count				= getChatUnreadCount($user_id, $other_user_id);

							if ($unread_count == null) {
								$unread_count = 0;
							}
							$detail[$TAG_UNREAD_COUNT] 	= $unread_count;

							if($user_id == $row['from_user_id']) {
								$user = $row['to_user_id'];
								
							} else {
								$user = $row['from_user_id'];
							}
							
							$profile = getProfile($user);
							$detail[$TAG_PROFILE] 			= $profile;
							
							array_push($response[$TAG_DETAILS], $detail);
							$success = true;
						}

						$response["q"] = $query;
						$response[$TAG_SUCCESS] = true;
						$response[$TAG_MESSAGE] = "Chat room created successfully";
						echoRespnse(200, $response);

					}  else {
						$response[$TAG_SUCCESS] = false;
						$response[$TAG_MESSAGE] = "You donot have any Chatroom yet";
						echoRespnse(200, $response);
					}

				} else {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured";
					echoRespnse(200, $response);
				}
			}
        });



/** Post chat
 * url - /chat
 * method - POST
 */
$app->post('/chat', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$from_user_id = $app->request->post('from_user_id');
				$to_user_id = $app->request->post('to_user_id');
				$chat_id = $app->request->post('chat_id');
				$message = $app->request->post('message');
				$created_date = $app->request->post('created_date');
				
				$created_date = date("Y-m-d H:i:s");
				
				
				// Escape
				$message = escape($message);
			
				$response = array();
				
				$isUserInactive = isUserInactive($from_user_id);
				if($isUserInactive[$TAG_USER_INACTIVE]) {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = $isUserInactive[$TAG_MESSAGE];
					echoRespnse(200, $response);
					return;
				}
				

				$query_user_add	= "INSERT INTO $TABLE_CONVERSATION (chat_id, message, from_user_id, created_date) 
									VALUES ('$chat_id', '$message', '$from_user_id', '$created_date')";
				$result_user_add = mysql_query($query_user_add);

				if($result_user_add) {

					$id = mysql_insert_id();
					
					$data = array();
					$data['id'] = $id;
			        $data['chat_id'] = $chat_id;
			        $data['message'] = $message;
			        $data['from_user_id'] = $from_user_id;
			        $data['created_date'] = $created_date;
					
					// Update date
					$query3	= "update $TABLE_CHAT_ROOM set updated_date = '$created_date' where id='$chat_id' ";
					$result3 = mysql_query($query3);

        			$query2 = "SELECT * FROM $TABLE_USER u LEFT JOIN lokaso_gcm g ON u.id = g.user_id WHERE u.id = '$to_user_id'";
					$result2 = mysql_query($query2);
								
					if($result2) {
						if (mysql_num_rows($result2) > 0) {
							while ($row2 = mysql_fetch_array($result2)) {
								$profile = array();
								$gcm_id = $row2['gcm_token'];
							}
						}
					}

					$query3 = "SELECT * FROM $TABLE_USER u LEFT JOIN lokaso_gcm g ON u.id = g.user_id WHERE u.id = '$from_user_id'";
					$result3 = mysql_query($query3);
								
					if($result3) {
						if (mysql_num_rows($result3) > 0) {
							while ($row3 = mysql_fetch_array($result3)) {
								$profile = array();
								$profile[$TAG_ID] 				= $from_user_id;
								$profile[$TAG_TO_USER_ID] 		= $to_user_id;
								$profile[$TAG_NAME]   			= $row3['name'];
								$profile[$TAG_EMAIL]   			= $row3['email'];
								$profile[$TAG_ABOUT_ME] 		= $row3['about_me'];
								$profile[$TAG_PROFESSION] 		= getProfession($row3['profession_id']);
								$profile[$TAG_LOCATION] 		= $row3['location'];
								$profile[$TAG_CURRENT_LAT] 		= $row3['current_lat'];
								$profile[$TAG_CURRENT_LNG] 	 	= $row3['current_lng'];
								$profile[$TAG_PROVIDER] 		= $row3['provider'];
								if ($row3['provider'] == $TAG_EMAIL) {
									$profile[$TAG_IMAGE_URL]  	= $USER_PIC_PATH . $row3['image'];
								} else {
									$profile[$TAG_IMAGE_URL]  	= $row3['image'];
								}
								$profile[$TAG_FACEBOOK_ID] 		= $row3['facebook_id'];
								$profile[$TAG_CREATED_DATE] 	= $row3['created_date'];
								$profile[$TAG_NOTIFICATION_FLAG]= $row3['notification_flag'];
								$profile[$TAG_CREDITS] 			= $row3['credits'];

								$data[$TAG_PROFILE] 			= $profile;
							}
						}
					}

					array_push($response[$TAG_DETAILS], $data);
					$response[$TAG_SUCCESS] = true;
					$response[$TAG_MESSAGE] = $created_date;
					echoRespnse(200, $response);
					
					// Send notification
					$notification_type = $TAG_NOTIFICATION_CHAT;
					$to_user_id = $to_user_id;
					$from_user_id = $from_user_id;
					$content_id = $chat_id;
					$other_data = $message;
					createNotification($to_user_id, $from_user_id, $content_id, $other_data, $notification_type);

				} else {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured";
					echoRespnse(200, $response);
				}
			}
        });



/** Get Chat
 * NAME
 * url - /getChat
 * method - POST
 */
$app->post('/getChat', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$chat_id = $app->request->post('chat_id');
				$user_id = $app->request->post('user_id');
			
				$response = array();
				$response[$TAG_DETAILS] = array();
				
				//CODE
			 	$query = "SELECT * FROM $TABLE_CONVERSATION WHERE chat_id = '$chat_id' ORDER BY created_date ASC";
				$result = mysql_query($query);
				
				if($result)
				{
					if (mysql_num_rows($result) > 0) {
						    while ($row = mysql_fetch_array($result)) {

						    	$detail = array();

								$detail[$TAG_ID] 				= $row['id'];
								$detail[$TAG_CHAT_ID] 			= $row['chat_id'];
								$detail[$TAG_MESSAGE] 			= $row['message'];
								$detail[$TAG_FROM_USER_ID]   	= $row['from_user_id'];
								$detail[$TAG_TO_USER_SEEN]   	= $row['to_user_seen'];

								if ($user_id != $row['from_user_id']) {
									setChatSeen($chat_id, $row['id']);
								}

								$detail[$TAG_CREATED_DATE] 		= $row['created_date'];

								$from_user_id = $row['from_user_id'];

						    	$query2 = "SELECT * FROM $TABLE_USER WHERE id = '$from_user_id'";
								$result2 = mysql_query($query2);
								
								if($result2) {
									if (mysql_num_rows($result2) > 0) {
										    while ($row2 = mysql_fetch_array($result2)) {
												$profile = array();

												$profile[$TAG_ID] 				= $row2['id'];
												$profile[$TAG_NAME]   			= $row2['name'];
												$profile[$TAG_EMAIL]   			= $row2['email'];
												$profile[$TAG_ABOUT_ME] 		= $row2['about_me'];
												$profile[$TAG_PROFESSION] 		= getProfession($row2['profession_id']);
												$profile[$TAG_REPORT] 			= isUserReported($row2['id'], $user_id);
												$profile[$TAG_LOCATION] 		= $row2['location'];
												$profile[$TAG_CURRENT_LAT] 		= $row2['current_lat'];
												$profile[$TAG_CURRENT_LNG] 	 	= $row2['current_lng'];
												$profile[$TAG_PROVIDER] 		= $row2['provider'];
												if ($row2['provider'] == $TAG_EMAIL) {
													$profile[$TAG_IMAGE_URL]  	= $USER_PIC_PATH . $row2['image'];
												} else {
													$profile[$TAG_IMAGE_URL]  	= $row2['image'];
												}
												$profile[$TAG_FACEBOOK_ID] 		= $row2['facebook_id'];
												$profile[$TAG_CREATED_DATE] 	= $row2['created_date'];
												$profile[$TAG_NOTIFICATION_FLAG]= $row2['notification_flag'];
												$profile[$TAG_CREDITS] 			= $row2['credits'];

												$detail[$TAG_PROFILE] 			= $profile;
											}

									}

								} else {
									$response[$TAG_SUCCESS] = false;
									$response[$TAG_MESSAGE] = "Some error occured.";
									echoRespnse(200, $response);
								}

								array_push($response[$TAG_DETAILS], $detail);
							}

							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "Successful";		
							echoRespnse(200, $response);

					} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Currently you donot have chat";
							echoRespnse(200, $response);
					}
				}
				else
				{
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured.";
					echoRespnse(200, $response);
				}
			}
        });



/** Get Users Notificatio list
 * NAME
 * url - /users/getNotifications
 * method - POST
 */
$app->post('/users/getNotifications', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$user_id = $app->request->post('user_id');
			
				$response = array();
				$response[$TAG_DETAILS] = array();
				
				//CODE
			 	$query = "SELECT * FROM $TABLE_NOTIFICATION WHERE user_id = '$user_id' ORDER BY created_date DESC";
				$result = mysql_query($query);
				
				if($result) {
					if (mysql_num_rows($result) > 0) {
						    while ($row = mysql_fetch_array($result)) {
							
								$detail = array();
								$detail[$TAG_ID] 		 		= $row['id'];
								$detail[$TAG_USER_ID] 		 	= $row['user_id'];
								$detail[$TAG_MESSAGE]   		= $row['message'];
								$user 		 					= $row['from_user_id'];
								$detail[$TAG_CREATED_DATE] 		= $row['created_date'];

								$query2 = "SELECT * FROM $TABLE_USER WHERE id = '$user'";
								$result2 = mysql_query($query2);
											
								if($result2) {
									if (mysql_num_rows($result2) > 0) {
										while ($row2 = mysql_fetch_array($result2)) {
											$profile = array();
											//$gcm_id 						= $row2['gcm_token'];
											$profile[$TAG_ID] 				= $row2['id'];
											$profile[$TAG_NAME]   			= $row2['name'];
											$profile[$TAG_EMAIL]   			= $row2['email'];
											$profile[$TAG_ABOUT_ME] 		= $row2['about_me'];
											$profile[$TAG_PROFESSION] 		= getProfession($row2['profession_id']);
											$profile[$TAG_LOCATION] 		= $row2['location'];
											$profile[$TAG_CURRENT_LAT] 		= $row2['current_lat'];
											$profile[$TAG_CURRENT_LNG] 	 	= $row2['current_lng'];
											$profile[$TAG_PROVIDER] 		= $row2['provider'];
											if ($row2['provider'] == $TAG_EMAIL) {
												$profile[$TAG_IMAGE_URL]  	= $USER_PIC_PATH . $row2['image'];
											} else {
												$profile[$TAG_IMAGE_URL]  	= $row2['image'];
											}
											$profile[$TAG_FACEBOOK_ID] 		= $row2['facebook_id'];
											$profile[$TAG_CREATED_DATE] 	= $row2['created_date'];
											$profile[$TAG_NOTIFICATION_FLAG]= $row2['notification_flag'];
											$profile[$TAG_CREDITS] 			= $row2['credits'];

											$detail[$TAG_PROFILE] 			= $profile;
										}

									}

								} else {
									$response[$TAG_SUCCESS] = false;
									$response[$TAG_MESSAGE] = "Some error occured.";
									echoRespnse(200, $response);
								}
							
								array_push($response[$TAG_DETAILS], $detail);
								$success = true;
							}
							
							// Update seen status for user
							$query1 = "update $TABLE_NOTIFICATION set seen_status=1 WHERE user_id = '$user_id' and seen_status=0";
							$result1 = mysql_query($query1);
							

							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "Successful";		
							echoRespnse(200, $response);

					} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Notifications not found";
							echoRespnse(200, $response);
					}

				} else {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured";
					echoRespnse(200, $response);
				}
			}
        });

/** Forgot Password
 * NAME
 * url - /forgot_password
 * method - POST
 */
$app->post('/forgot_password', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$email = $app->request->post('email');
				$newpassword = md5($app->request->post("password"));
				
				
				// Escape
				$newpassword = escape($newpassword);
			
				$response = array();
				$response[$TAG_DETAILS] = array();
					
				$query= "UPDATE $TABLE_USER SET password = '$newpassword' WHERE email = '$email'";
				$result = mysql_query($query);
						
				if($result) {
					$query2 = "SELECT * FROM $TABLE_USER WHERE email = '$email'";
					$result2 = mysql_query($query2);
											
					if($result2) {
						if (mysql_num_rows($result2) > 0) {
							while ($row2 = mysql_fetch_array($result2)) {
								$profile = array();
								//$gcm_id 						= $row2['gcm_token'];
								$profile[$TAG_ID] 				= $row2['id'];
								$profile[$TAG_NAME]   			= $row2['name'];
								$profile[$TAG_EMAIL]   			= $row2['email'];
								$profile[$TAG_ABOUT_ME] 		= $row2['about_me'];
								$profile[$TAG_PROFESSION] 		= getProfession($row2['profession_id']);
								$profile[$TAG_LOCATION] 		= $row2['location'];
								$profile[$TAG_CURRENT_LAT] 		= $row2['current_lat'];
								$profile[$TAG_CURRENT_LNG] 	 	= $row2['current_lng'];
								$profile[$TAG_PROVIDER] 		= $row2['provider'];
								if ($row2['provider'] == $TAG_EMAIL) {
									$profile[$TAG_IMAGE_URL]  	= $USER_PIC_PATH . $row2['image'];
								} else {
									$profile[$TAG_IMAGE_URL]  	= $row2['image'];
								}
								$profile[$TAG_FACEBOOK_ID] 		= $row2['facebook_id'];
								$profile[$TAG_CREATED_DATE] 	= $row2['created_date'];
								$profile[$TAG_NOTIFICATION_FLAG]= $row2['notification_flag'];
								$profile[$TAG_CREDITS] 			= $row2['credits'];

								$detail[$TAG_PROFILE] 			= $profile;

								array_push($response[$TAG_DETAILS], $detail);

								$response[$TAG_SUCCESS] = true;
								$response[$TAG_MESSAGE] = "Password changed successfully";		
								echoRespnse(200, $response);
							}

						} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "User does not exist";
							echoRespnse(200, $response);
						}

					} else {
						$response[$TAG_SUCCESS] = false;
						$response[$TAG_MESSAGE] = "Some error occured.";
						echoRespnse(200, $response);
					}

				} else {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured";
					echoRespnse(200, $response);
				}
			}
			
	});




/** Forgot Password
 * NAME
 * url - /forgot_password
 * method - POST
 */
$app->post('/forgot_password', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$email = $app->request->post('email');
				$newpassword = md5($app->request->post("password"));
				
				
				// Escape
				$newpassword = escape($newpassword);
			
				$response = array();
				$response[$TAG_DETAILS] = array();
					
				$query= "UPDATE $TABLE_USER SET password = '$newpassword' WHERE email = '$email'";
				$result = mysql_query($query);
						
				if($result) {
					$query2 = "SELECT * FROM $TABLE_USER WHERE email = '$email'";
					$result2 = mysql_query($query2);
											
					if($result2) {
						if (mysql_num_rows($result2) > 0) {
							while ($row2 = mysql_fetch_array($result2)) {
								$profile = array();
								//$gcm_id 						= $row2['gcm_token'];
								$profile[$TAG_ID] 				= $row2['id'];
								$profile[$TAG_NAME]   			= $row2['name'];
								$profile[$TAG_EMAIL]   			= $row2['email'];
								$profile[$TAG_ABOUT_ME] 		= $row2['about_me'];
								$profile[$TAG_PROFESSION] 		= getProfession($row2['profession_id']);
								$profile[$TAG_LOCATION] 		= $row2['location'];
								$profile[$TAG_CURRENT_LAT] 		= $row2['current_lat'];
								$profile[$TAG_CURRENT_LNG] 	 	= $row2['current_lng'];
								$profile[$TAG_PROVIDER] 		= $row2['provider'];
								if ($row2['provider'] == $TAG_EMAIL) {
									$profile[$TAG_IMAGE_URL]  	= $USER_PIC_PATH . $row2['image'];
								} else {
									$profile[$TAG_IMAGE_URL]  	= $row2['image'];
								}
								$profile[$TAG_FACEBOOK_ID] 		= $row2['facebook_id'];
								$profile[$TAG_CREATED_DATE] 	= $row2['created_date'];
								$profile[$TAG_NOTIFICATION_FLAG]= $row2['notification_flag'];
								$profile[$TAG_CREDITS] 			= $row2['credits'];

								$detail[$TAG_PROFILE] 			= $profile;

								array_push($response[$TAG_DETAILS], $detail);

								$response[$TAG_SUCCESS] = true;
								$response[$TAG_MESSAGE] = "Password changed successfully";		
								echoRespnse(200, $response);
							}

						} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "User does not exist";
							echoRespnse(200, $response);
						}

					} else {
						$response[$TAG_SUCCESS] = false;
						$response[$TAG_MESSAGE] = "Some error occured.";
						echoRespnse(200, $response);
					}

				} else {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured";
					echoRespnse(200, $response);
				}
			}
			
	});



/** Change Password
 * NAME
 * url - /change_password
 * method - POST
 */
$app->post('/change_password', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$email = $app->request->post('email');
				$current_password = md5($app->request->post("current_password"));
				$new_password = md5($app->request->post("new_password"));
				
				// Escape
				$new_password = escape($new_password);
			
				$response = array();
				$response[$TAG_DETAILS] = array();
					
				$query= "UPDATE $TABLE_USER SET password = '$new_password' WHERE email = '$email' AND password = '$current_password'";
				$result = mysql_query($query);
						
				if($result) {
					$query2 = "SELECT * FROM $TABLE_USER WHERE email = '$email'";
					$result2 = mysql_query($query2);
											
					if($result2) {
						if (mysql_num_rows($result2) > 0) {
							while ($row2 = mysql_fetch_array($result2)) {
								$profile = array();
								//$gcm_id 						= $row2['gcm_token'];
								$profile[$TAG_ID] 				= $row2['id'];
								$profile[$TAG_NAME]   				= $row2['name'];
								$profile[$TAG_EMAIL]   			= $row2['email'];
								$profile[$TAG_ABOUT_ME] 			= $row2['about_me'];
								$profile[$TAG_PROFESSION] 			= getProfession($row2['profession_id']);
								$profile[$TAG_LOCATION] 			= $row2['location'];
								$profile[$TAG_CURRENT_LAT] 		= $row2['current_lat'];
								$profile[$TAG_CURRENT_LNG] 	 	= $row2['current_lng'];
								$profile[$TAG_PROVIDER] 			= $row2['provider'];
								if ($row2['provider'] == $TAG_EMAIL) {
									$profile[$TAG_IMAGE_URL]  	= $USER_PIC_PATH . $row2['image'];
								} else {
									$profile[$TAG_IMAGE_URL]  	= $row2['image'];
								}
								$profile[$TAG_FACEBOOK_ID] 		= $row2['facebook_id'];
								$profile[$TAG_CREATED_DATE] 	 	= $row2['created_date'];
								$profile[$TAG_NOTIFICATION_FLAG] 	= $row2['notification_flag'];
								$profile[$TAG_CREDITS] 			= $row2['credits'];

								$detail[$TAG_PROFILE] 				= $profile;
							}

						}

					} else {
						$response[$TAG_SUCCESS] = false;
						$response[$TAG_MESSAGE] = "Some error occured.";
						echoRespnse(200, $response);
					}

					array_push($response[$TAG_DETAILS], $detail);

					$response[$TAG_SUCCESS] = true;
					$response[$TAG_MESSAGE] = "Password changed successfully";		
					echoRespnse(200, $response);

				} else {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured";
					echoRespnse(200, $response);
				}
			}			
	});



/** Get Users Credit list
 * NAME
 * url - /users/getCredits
 * method - POST
 */
$app->post('/users/getCredits', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);	
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$user_id = $app->request->post('user_id');
			
				$response = array();
				$response[$TAG_DETAILS] = array();
				
				//CODE
			 	$query = "SELECT * FROM $TABLE_USER_CREDITS WHERE user_id = '$user_id' ORDER BY created_date DESC";
				$result = mysql_query($query);
				
				if($result) {
					if (mysql_num_rows($result) > 0) {
						    while ($row = mysql_fetch_array($result)) {
							
								$detail = array();
								$detail[$TAG_ID] 		 		= $row['id'];
								$detail[$TAG_USER_ID] 		 	= $row['user_id'];
								$detail[$TAG_CREDIT_NAME]   	= $row['credit_name'];
								$detail[$TAG_POINTS] 		 		= $row['points'];
								$detail[$TAG_CREATED_DATE] 		= $row['created_date'];
							
								array_push($response[$TAG_DETAILS], $detail);
								$success = true;
							}

							$response[$TAG_SUCCESS] = true;
							$response[$TAG_MESSAGE] = "Successful";		
							echoRespnse(200, $response);

					} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Credits not found";
							echoRespnse(200, $response);
					}

				} else {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured";
					echoRespnse(200, $response);
				}
			}
        });



/** get Chat Unread Count
 * url - /users/getChatUnreadCount
 * method - POST
 */
$app->post('/users/getChatUnreadCount', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$user_id = $app->request->post('user_id');
				$to_user_id = $app->request->post('to_user_id');
			
				$response = array();

				$unread_count = getChatUnreadCount($user_id, $to_user_id);
				
				if($unread_count != null) {
					$response[$TAG_DETAILS] = $unread_count;
					$response[$TAG_SUCCESS] = true;
					$response[$TAG_MESSAGE] = "Successful";
					echoRespnse(200, $response);

				} else {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = "Some error occured";
					echoRespnse(200, $response);
				}
			}
        });




/** extend Validity
 * url - /query/extendValidity
 * method - POST
 */
$app->post('/query/extendValidity', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();

			$token = $app->request()->headers->get($TAG_TOKEN);
			if(authenticate($token)){
							
				mysql_set_charset('utf8');
				// reading post params
				$user_id = $app->request->post('user_id');
				$query_id = $app->request->post('query_id');
				$current_validity = $app->request->post('current_validity');
				$extend_time = $app->request->post('extend_time');
				$extend_time = "+" . $extend_time;
				$current_date = date("Y-m-d H:i:s");

				if($current_validity >= $current_date){
					$valid_until = date("Y-m-d H:i:s", strtotime($current_validity . $extend_time));
				} else {
					$valid_until = date("Y-m-d H:i:s", strtotime($current_date . $extend_time));
				}
			
				$response = array();
				$response[$TAG_DETAILS] = array();
				
				$isUserInactive = isUserInactive($user_id);
				if($isUserInactive[$TAG_USER_INACTIVE]) {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = $isUserInactive[$TAG_MESSAGE];
					echoRespnse(200, $response);
					return;
				}
				
				$isQueryInactive = isQueryInactive($query_id);
				if($isSuggestionInactive[$TAG_INACTIVE]) {
					$response[$TAG_SUCCESS] = false;
					$response[$TAG_MESSAGE] = $isQueryInactive[$TAG_MESSAGE];
					echoRespnse(200, $response);
					return;
				}

				$query = "UPDATE $TABLE_ASK SET valid_until = '$valid_until' WHERE id = '$query_id' AND user_id = '$user_id'";
				$result = mysql_query($query);

					if ($result) {
						$query1 = "SELECT * FROM $TABLE_ASK WHERE user_id = '$user_id' AND id = '$query_id'";
						$result1 = mysql_query($query1);
						
						if($result1) {
							if (mysql_num_rows($result1) > 0) {
						    	while ($row = mysql_fetch_array($result1)) {
						    		$detail = array();
									$detail[$TAG_VALID_DATE] 	= $row['valid_until'];
									$detail[$TAG_VALID_UNTIL] 	= findRemainingDate($row['valid_until']);
									array_push($response[$TAG_DETAILS], $detail);
								}

								$response[$TAG_SUCCESS] = true;
								$response[$TAG_MESSAGE] = $date_valid_until;
								echoRespnse(200, $response);
							}

						} else {
							$response[$TAG_SUCCESS] = false;
							$response[$TAG_MESSAGE] = "Some error occured";
							echoRespnse(200, $response);
						}

					} else {
						$response[$TAG_SUCCESS] = false;
						$response[$TAG_MESSAGE] = "Some error occured";
						echoRespnse(200, $response);
					}
			}
        });



















function getUserInterest($tag, $user_id) {
	
	include("../include/constant.php");
	$resp = array();
	$resp[$tag] = array();
	
	$query = "SELECT * FROM $TABLE_USER_INTEREST WHERE user_id = '$user_id'";
	$result = mysql_query($query);
	
	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result)) {
			$detail = array();
			$detail[$TAG_ID]			= $row["id"];
			$detail[$TAG_INTEREST_ID]	= $row["interest_id"];
			$detail[$TAG_USER_ID]		= $row["user_id"];
			
			
			array_push($resp[$tag], $detail);
		}
		
		return $resp[$tag];
	}
	else {
		return $resp[$tag];
	}		
}









function getQueriesAttended($user_id) {
	include("../include/constant.php");
	$query = "SELECT COUNT(DISTINCT query_id) AS query_attended_count FROM $TABLE_ASK_RESPONSE WHERE user_id = '$user_id'";
	$result = mysql_query($query);
				
	if($result) {
		if (mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_array($result)) {
				$query_attended_count = $row['query_attended_count'];
			}

		} else {
			$query_attended_count = 0;
		}

	} else {
		$query_attended_count = 0;
	}

	return $query_attended_count;
}





function getResponseCommentCount($response_id) {
	include("../include/constant.php");
	$query2 = "SELECT COUNT(response_id) AS comment_count FROM $TABLE_ASK_RESPONSE_COMMENT WHERE response_id = '$response_id'";
	$result2 = mysql_query($query2);
											
	if($result2) {
		if (mysql_num_rows($result2) > 0) {
			while ($row2 = mysql_fetch_array($result2)) {
				$comment_count = $row2['comment_count'];
			}
		} else {
			$comment_count = 0;
		}

	} else {
		$comment_count = 0;
	}

	return $comment_count;
}


function getUpvotes($response_id) {
	include("../include/constant.php");
	$query2 = "SELECT COUNT(response_id) AS vote_count FROM $TABLE_ASK_RESPONSE_ACTION WHERE response_id = '$response_id' AND action = '1'";
	$result2 = mysql_query($query2);
											
	if($result2) {
		if (mysql_num_rows($result2) > 0) {
			while ($row2 = mysql_fetch_array($result2)) {
				$vote_count = $row2['vote_count'];
			}
		} else {
			$vote_count = 0;
		}

	} else {
		$vote_count = 0;
	}

	return $vote_count;
}

function getDownvotes($response_id) {
	include("../include/constant.php");
	$query2 = "SELECT COUNT(response_id) AS vote_count FROM $TABLE_ASK_RESPONSE_ACTION WHERE response_id = '$response_id' AND action = '0'";
	$result2 = mysql_query($query2);
											
	if($result2) {
		if (mysql_num_rows($result2) > 0) {
			while ($row2 = mysql_fetch_array($result2)) {
				$vote_count = $row2['vote_count'];
			}
		} else {
			$vote_count = 0;
		}

	} else {
		$vote_count = 0;
	}

	return $vote_count;
}




function getResponseCount($query_id) {
	include("../include/constant.php");
	$query_response = "SELECT COUNT(query_id) AS response_count FROM $TABLE_ASK_RESPONSE WHERE query_id = '$query_id'  and status=1";
	$result_response = mysql_query($query_response);

	if($result_response) {
		if(mysql_num_rows($result_response) > 0){
			while ($row_response = mysql_fetch_array($result_response)) {
				$response_count = $row_response[$TAG_RESPONSE_COUNT];
			}

		} else {
			$response_count = 0;
		}

	} else {
		$response_count = 0;
	}

	return $response_count;
}




function findRemainingDate($valid_until) {
	include("../include/constant.php");
	$date 	= strtotime($valid_until);
	$diff 	= $date - time();
	$days 	= floor($diff / (60 * 60 * 24));
	$hours 	= round(($diff - $days * 60 * 60 * 24) / (60 * 60));
	$minutes= round($diff / 60);

	if ($days > 0) {
		$date_valid_until = "$days d $hours hrs";

	} else if ($days == 0) {
		if($hours > 0){
			$date_valid_until = "$hours hrs";

		} else{
			$date_valid_until = "$minutes mins";
		}
	} else {
		$date_valid_until = "0";
	}
	
	return $date_valid_until ;
}


function getQueryResponse($reponse_id) {
	include("../include/constant.php");
	$query_response = "SELECT * FROM $TABLE_ASK_RESPONSE WHERE id = '$reponse_id'";
	$result = mysql_query($query_response);

	$detail = null;
	
	if(result) {
		if(mysql_num_rows($result) > 0){
				
				$row = mysql_fetch_array($result);
			
				$detail = array();
				$detail[$TAG_ID] 		 		= $row['id'];
				$detail[$TAG_QUERY_ID]   		= $row['query_id'];
				$user 		 					= $row['user_id'];
				$detail[$TAG_RESPONSE] 			= $row['response'];
				$detail[$TAG_USER_FAV]   		= isResponseUserFav($row['id'], $user_id);
				$detail[$TAG_SPAM]   			= isResponseUserSpammed($row['id'], $user_id);
				$detail[$TAG_MARK_INAPPROPRIATE]= $row['mark_inappropriate'];
				$detail[$TAG_COMMENT_COUNT]		= getResponseCommentCount($row['id']);
				$detail[$TAG_UPVOTES] 			= getUpvotes($row['id']);
				$detail[$TAG_DOWNVOTES] 		= getDownvotes($row['id']);
				$detail[$TAG_CREATED_DATE] 		= $row['created_date'];		

		}
	}

	return $detail;
}


function getProfileLocation($user_id) {
	
	include("../include/constant.php");
	$query = "SELECT id,name,email,image,current_lat,current_lng,location FROM $TABLE_USER WHERE id = '$user_id'";
	$result = mysql_query($query);

	if (mysql_num_rows($result) > 0) {
							
		$row = mysql_fetch_array($result);
		
		$detail = array();
		$detail[$TAG_ID] 				= $row['id'];
		$detail[$TAG_NAME]   			= $row['name'];
		$detail[$TAG_EMAIL]   			= $row['email'];
		if ($row['provider'] == $TAG_EMAIL) {
			$detail[$TAG_IMAGE_URL]  	= $USER_PIC_PATH . $row['image'];
		} else {
			$detail[$TAG_IMAGE_URL]  	= $row['image'];
		}		
		$detail[$TAG_LOCATION] 			= $row['location'];
		$detail[$TAG_CURRENT_LAT] 		= $row['current_lat'];
		$detail[$TAG_CURRENT_LNG] 	 	= $row['current_lng'];
		
		return $detail;
	}
	else {
		return null;
	}
}

function getProfile($user_id) {
	return getProfileAll($user_id, null);
}
function getProfileView($user_id, $current_user_id) {
	return getProfileAll($user_id, $current_user_id);
}
function getProfileAll($user_id, $current_user_id) {
	
	include("../include/constant.php");
	$query = "SELECT * FROM $TABLE_USER WHERE id = '$user_id'";
	$result = mysql_query($query);
	
	if (mysql_num_rows($result) > 0) {
		$row = mysql_fetch_array($result);		
		$detail = getProfileItem($row, $user_id, $current_user_id);			
		return $detail;		
	}
	else {
		return null;
	}
}
function getProfileItem($row, $user_id, $current_user_id=null) {
			
	include("../include/constant.php");
		$detail = array();
		$detail[$TAG_ID] 				= $row['id'];
		$name   						= $row['name'];
		$fname   						= $row['fname'];
		$lname   						= $row['lname'];
		
		if(strlen($fname)==0 && strlen($lname)==0) {				
			$nameArr = explode(" ", $name);
			$fname = $nameArr[0];
			$lname = $nameArr[1];		
		}
		$detail[$TAG_NAME]   			= $name;
		$detail[$TAG_FNAME]   			= $fname;
		$detail[$TAG_LNAME]   			= $lname;
		
		$email   						= $row['email'];		
		if(!strpos($email, "@")) {
			$email = "";
		}
		
		/*
		if(strpos($email, "@")!==false) {}
		else {
			$email = "";
		}
			*/
		
		$detail[$TAG_EMAIL]   			= $email;
		$detail[$TAG_ABOUT_ME] 			= $row['about_me'];
		$detail[$TAG_PROVIDER] 			= $row['provider'];
		$detail[$TAG_LOCATION] 			= $row['location'];
		$detail[$TAG_CURRENT_LAT] 		= $row['current_lat'];
		$detail[$TAG_CURRENT_LNG] 	 	= $row['current_lng'];
		if ($row['provider'] == $TAG_EMAIL) {
			$imageName = $row['image'];
			if(strlen($imageName)==0) {
				$imageUrl = $USER_PIC_DEFAULT;				
			}
			else {
				$imageUrl = $USER_PIC_PATH . $imageName;
			}
			$detail[$TAG_IMAGE_URL]  	= $imageUrl;
		} else {
			$detail[$TAG_IMAGE_URL]  	= $row['image'];
		}
		$detail[$TAG_FACEBOOK_ID] 		= $row['facebook_id'];
		
		$profession_id 					= $row['profession_id'];
		$detail[$TAG_PROFESSION_ID] 	= $profession_id;
		$detail[$TAG_PROFESSION] 		= getProfession($profession_id);
		
		$detail[$TAG_CREATED_DATE] 	 	= $row['created_date'];
		$detail[$TAG_NOTIFICATION_FLAG] = $row['notification_flag'];
		$credits 						= $row['credits'];
		$detail[$TAG_CREDITS] 			= $credits;
		$detail[$TAG_CREDITS_DISPLAY]   = getUserCreditDisplay($credits);
		$detail[$TAG_REFER_CODE] 		= $row['refer_code'];
		
		$detail[$TAG_NUM_ASKS]   		= $row['num_asks'];
		$detail[$TAG_NUM_RESPONSES]   	= $row['num_responses'];
		$detail[$TAG_DISCOVERY_COUNT]   = getDiscoveryCount($user_id);
		$detail[$TAG_QUERY_COUNT]   	= getQueryCount($user_id);
		$detail[$TAG_FOLLOWING_COUNT]   = getFollowingCount($user_id);
		
		$detail[$TAG_CHAT_UNREAD_COUNT] = getChatUnreadCountAll($user_id);
		$detail[$TAG_NOTIFICATION_UNREAD_COUNT] = getNotificationUnreadCountAll($user_id);
		
		$detail[$TAG_INTEREST_LIST] = getUserInterest($TAG_INTEREST_LIST, $user_id);
		
		if($current_user_id!=null && $current_user_id!=$user_id){
			$detail[$TAG_USER_FOLLOWED] = isUserFollowed($user_id, $current_user_id);
			$detail[$TAG_REPORT] 		= isUserReported($user_id, $current_user_id);
		}
		$distance = distance($lat, $lng, $row['current_lat'], $row['current_lng']);
		$detail[$TAG_DISTANCE]			= $distance;
		$detail[$TAG_DISTANCE_DISPLAY]	= $distance."km";
	
		return $detail;		
}

function getQuery($query_id) {
	
	include("../include/constant.php");
	$query = "SELECT * FROM $TABLE_ASK WHERE id = '$query_id'";
	$result = mysql_query($query);

	if (mysql_num_rows($result) > 0) {
		
		$row = mysql_fetch_array($result);
		
		
		
		$detail = array();
		$query_id = $row['id'];
		$detail[$TAG_ID] 				= $query_id;
		//$detail["user_id"]   			= $row['user_id'];
		//$detail["user_name"]   		= getUserName($row['user_id']);
		//$detail["user_image"]   		= getUserImage($row['user_id']);

		$user = $row['user_id'];
		
		$detail[$TAG_PROFILE] 			= getProfile($user);
		
		
		$detail[$TAG_DESCRIPTION]   	= $row['description'];
		$detail[$TAG_VALID_DATE] 	 	= $row['valid_until'];

		$valid_until 					= $row['valid_until'];
		// if(strtotime($valid_until) >= strtotime($current_date) {
		$date_valid_until 				= findRemainingDate($valid_until);
		$detail[$TAG_VALID_UNTIL] 		= $date_valid_until;


		$detail[$TAG_RESPONSE_COUNT] 	= getQueryResponseCount($query_id); //$row['response_count'];
		$detail[$TAG_CREATED_DATE] 		= $row['created_date'];
		$detail[$TAG_LOCATION] 			= $row['location'];
		$detail[$TAG_INTEREST_ID] 		= $row['interest_id'];
		$detail[$TAG_FLAG_SOLVED] 	 	= $row['flag_solved'];
		$detail[$TAG_MARKED_INAPPROPRIATE] = $row['marked_inappropriate'];
		$detail[$TAG_FLAG] 				= isQueryFlagged($row['id'], $user_id);
		$distance						= distance($lat, $lng, $row['lat'], $row['lng']);
		$detail[$TAG_DISTANCE]			= $distance;
		$detail[$TAG_DISTANCE_DISPLAY]	= $distance."km";
		
	
		return $detail;
	}
	else {
		return null;
	}		
}


function getChatUnreadCount($user_id, $to_user_id){
	include("../include/constant.php");
	$query_user = "SELECT DISTINCT id FROM $TABLE_CHAT_ROOM WHERE from_user_id = '$user_id' AND to_user_id = '$to_user_id' OR from_user_id = '$to_user_id' AND to_user_id = '$user_id'";
	$result_user = mysql_query($query_user);

	$unread_count = null;

	if($result_user) {
		if (mysql_num_rows($result_user) > 0) {
			while ($row_user = mysql_fetch_array($result_user)) {
				$chat_id	= $row_user["id"];

				$query = "SELECT COUNT(to_user_seen) AS unread_count FROM $TABLE_CONVERSATION WHERE chat_id = '$chat_id' AND from_user_id != '$user_id' AND to_user_seen = '0'";
				$result = mysql_query($query);

				if($result) {
					if (mysql_num_rows($result) > 0) {
						while ($row = mysql_fetch_array($result)) {
							$unread_count = $row[$TAG_UNREAD_COUNT];
						}

					} else {
						$unread_count = null;
					}

				} else {
					$unread_count = null;
				}
			}

		} else {
			$unread_count = null;
		}

	} else {
		$unread_count = null;
	}

	return $unread_count;
}

/*
* Get nofication unread Count
*/
function getNotificationUnreadCountAll($user_id) {
	include("../include/constant.php");
	
	$query = "SELECT count(*) as unread_count FROM $TABLE_NOTIFICATION where user_id = '$user_id' and seen_status=0";
	$result = mysql_query($query);
	
	$unread_count	= 0;

	if($result) {
		if (mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_array($result)) {
				$unread_count = $row[$TAG_UNREAD_COUNT];
			}

		} else {
			$unread_count = null;
		}

	} else {
		$unread_count = null;
	}
	
	return $unread_count;
}


/*
* Get ChatUnreadCountAll Count
*/
function getChatUnreadCountAll($user_id) {
	include("../include/constant.php");
	
	$query = "SELECT count(*) as unread_count FROM $TABLE_CONVERSATION where chat_id in 
	(SELECT id FROM $TABLE_CHAT_ROOM where from_user_id = '$user_id' or to_user_id = '$user_id') 
	and to_user_seen = 0 and not from_user_id = '$user_id'";
	$result = mysql_query($query);
	
	$unread_count	= 0;

	if($result) {
		if (mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_array($result)) {
				$unread_count = $row[$TAG_UNREAD_COUNT];
			}

		} else {
			$unread_count = null;
		}

	} else {
		$unread_count = null;
	}
	
	return $unread_count;
}


function setChatSeen($chat_id, $conversation_id){
	include("../include/constant.php");
	$query_user_add	= "UPDATE $TABLE_CONVERSATION SET to_user_seen = '1' WHERE chat_id = '$chat_id' AND to_user_seen != '1'";
	$result_user_add = mysql_query($query_user_add);

	if ($result_user_add) {
		$user_add = true;

	} else {
		$user_add = false;
	}
}


function updateDeviceDetails($app, $user_id){
	include("../include/constant.php");

	$device_id 					= $app->request->post('device_id');
	$device_os 					= $app->request->post('device_os');
	$device_name 				= $app->request->post('device_name');
	$device_os_version 			= $app->request->post('device_os_version');
	$app_version_code 			= $app->request->post('app_version_code');
	$app_version_name 			= $app->request->post('app_version_name');
	
	

	if($device_id!=null && strlen($device_id)>0) {
		$query1	= "UPDATE $TABLE_USER SET `device_id` = '$device_id' WHERE id = '$user_id'";
		$result1 = mysql_query($query1);
	}
	
	if($device_os!=null && strlen($device_os)>0) {
		$query2	= "UPDATE $TABLE_USER SET `device_os` = '$device_os' WHERE id = '$user_id'";
		$result2 = mysql_query($query2);
	}
	
	if($device_name!=null && strlen($device_name)>0) {
		$query3	= "UPDATE $TABLE_USER SET `device_name` = '$device_name' WHERE id = '$user_id'";
		$result3 = mysql_query($query3);
	}
	
	if($device_os_version!=null && strlen($device_os_version)>0) {
		$query4	= "UPDATE $TABLE_USER SET `device_os_version` = '$device_os_version' WHERE id = '$user_id'";
		$result4 = mysql_query($query4);
	}
	
	if($app_version_code!=null && strlen($app_version_code)>0) {
		$query5	= "UPDATE $TABLE_USER SET `app_version_code` = '$app_version_code' WHERE id = '$user_id'";
		$result5 = mysql_query($query5);
	}
	
	if($app_version_name!=null && strlen($app_version_name)>0) {
		$query6	= "UPDATE $TABLE_USER SET `app_version_name` = '$app_version_name' WHERE id = '$user_id'";
		$result6 = mysql_query($query6);
	}
	
	$current_date = date("Y-m-d H:i:s");	
	if($current_date!=null && strlen($current_date)>0) {
		$query7	= "UPDATE $TABLE_USER SET `last_seen` = '$current_date' WHERE id = '$user_id'";
		$result7 = mysql_query($query7);
	}
}


// NOTIFICATION ---- START

/*
* Add Notifications for user
*/
function addUserContentNotification($message, $from_user_id, $content_id, $type_id) {
	include("../include/constant.php");
	switch ($type_id) {
		case 1:
				addDiscoveryNotification($message, $from_user_id, $content_id, $type_id);
			break;

		case 2:
				addDiscoveryNotification($message, $from_user_id, $content_id, $type_id);
			break;

		case 4:
				addQueryNotification($message, $from_user_id, $content_id, $type_id);
			break;

		case 5:
				addResponseNotification($message, $from_user_id, $content_id, $type_id);
			break;

		case 6:
				addResponseNotification($message, $from_user_id, $content_id, $type_id);
			break;
		
		default:
			break;
	}
}

function addDiscoveryNotification($message, $from_user_id, $discovery_id, $type_id){
	include("../include/constant.php");
	$query = "SELECT DISTINCT ud.user_id, g.gcm_token FROM $TABLE_USER_SUGGESTION ud LEFT JOIN $TABLE_GCM g ON ud.user_id = g.user_id WHERE ud.discovery_id = '$discovery_id' AND ud.user_id != '$from_user_id'";
	$result = mysql_query($query);

	if($result) {
		if (mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_array($result)) {
				$to_user_id			= $row['user_id'];
				$registration_id	= $row['gcm_token'];
				$data = array();
				$data[$TAG_MESSAGE] = $message;
				notifyUser($data, $registration_id);
				addUserNotification($message, $to_user_id, $from_user_id, $discovery_id, $type_id);
			}

		} else {
			$user_id = null;
		}

	} else {
		$user_id = null;
	}
}

function addQueryNotification($message, $from_user_id, $query_id, $type_id){
	include("../include/constant.php");
	$query = "SELECT DISTINCT uq.user_id, g.gcm_token FROM $TABLE_USER_QUERY uq LEFT JOIN $TABLE_GCM g ON uq.user_id = g.user_id WHERE uq.query_id = '$query_id' AND uq.user_id != '$from_user_id'";
	$result = mysql_query($query);

	if($result) {
		if (mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_array($result)) {
				$to_user_id	= $row["user_id"];
				$registration_id	= $row["gcm_token"];
				$data = array();
				$data[$TAG_MESSAGE] = $message;
				notifyUser($data, $registration_id);
				addUserNotification($message, $to_user_id, $from_user_id, $discovery_id, $type_id);
			}

		} else {
			$user_id = null;
		}

	} else {
		$user_id = null;
	}
}

function addResponseNotification($message, $from_user_id, $response_id, $type_id){
	include("../include/constant.php");
	$query = "SELECT DISTINCT ar.user_id, g.gcm_token FROM $TABLE_ASK_RESPONSE ar LEFT JOIN $TABLE_GCM g ON ar.user_id = g.user_id WHERE ar.id = '$response_id' AND ar.user_id != '$from_user_id'";
	$result = mysql_query($query);

	if($result) {
		if (mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_array($result)) {
				$to_user_id	= $row["user_id"];
				$registration_id	= $row["gcm_token"];
				$data = array();
				$data[$TAG_MESSAGE] = $message;
				notifyUser($data, $registration_id);
				addUserNotification($message, $to_user_id, $from_user_id, $discovery_id, $type_id);
			}

		} else {
			$user_id = null;
		}

	} else {
		$user_id = null;
	}
}



/*
* Add Notifications for user
*/
function addUserNotification($message, $to_user_id, $from_user_id, $content_id, $type_id) {
	include("../include/constant.php");
	$user_add = false;
	switch ($type_id) {
		case $TAG_NOTIFICATION_MY_QUERY_RESPONSE:
			$message = $TAG_NOTIFICATION_MY_QUERY_RESPONSE_TEXT . " : " . $message;
			//$user_add = addUserNotifications($message, $to_user_id, $from_user_id, $content_id, $type_id);
			break;

		case $TAG_NOTIFICATION_FOLLOWED_QUERY_RESPONSE:
			$message = $TAG_NOTIFICATION_FOLLOWED_QUERY_RESPONSE_TEXT . " : " . $message;
			//$user_add = addUserNotifications($message, $to_user_id, $from_user_id, $content_id, $type_id);
			break;
		
		case $TAG_NOTIFICATION_USER_FOLLOW:
			$message = $TAG_NOTIFICATION_USER_FOLLOW_TEXT . " : " . $message;
			//$user_add = addUserNotifications($message, $to_user_id, $from_user_id, $content_id, $type_id);
			break;
		
		case $TAG_NOTIFICATION_FRIEND_JOINS:
			$message = $TAG_NOTIFICATION_FRIEND_JOINS_TEXT . " : " . $message;
			//$user_add = addUserNotifications($message, $to_user_id, $from_user_id, $content_id, $type_id);
			break;
		
		case $TAG_NOTIFICATION_LOCAL_SUGGESTERS:
			$message = $TAG_NOTIFICATION_LOCAL_SUGGESTERS_TEXT . " : " . $message;
			$user_add = addUserNotifications($message, $to_user_id, $from_user_id, $content_id, $type_id);
			break;
					
		default:
			//$user_add = addUserNotifications($message, $to_user_id, $from_user_id, $content_id, $type_id);
			break;
	}

	return $user_add;
}


function addUserNotifications($message, $to_user_id, $from_user_id, $content_id, $type_id) {
	include("../include/constant.php");
	$query_user_add	= "INSERT INTO $TABLE_NOTIFICATION (user_id, message, from_user_id, content_id, type_id) VALUES ('$to_user_id', '$message', '$from_user_id', '$content_id', '$type_id')";
	$result_user_add = mysql_query($query_user_add);

	if ($result_user_add) {
		$user_add = true;

		$query2 = "SELECT * FROM $TABLE_USER u LEFT JOIN lokaso_gcm g ON u.id = g.user_id WHERE u.id = '$to_user_id' AND u.notification_flag = '1'";
		$result2 = mysql_query($query2);

		if($result2) {
			if (mysql_num_rows($result2) > 0) {
				while ($row2 = mysql_fetch_array($result2)) {
					$profile = array();
					$gcm_id 						= $row2['gcm_token'];
					$profile[$TAG_ID] 				= $row2['id'];
					$profile[$TAG_NAME]   			= $row2['name'];
					$profile[$TAG_EMAIL]   			= $row2['email'];
					$profile[$TAG_ABOUT_ME] 		= $row2['about_me'];
					$profile[$TAG_PROFESSION] 		= getProfession($row2['profession_id']);
					$profile[$TAG_LOCATION] 		= $row2['location'];
					$profile[$TAG_CURRENT_LAT] 		= $row2['current_lat'];
					$profile[$TAG_CURRENT_LNG] 	 	= $row2['current_lng'];
					$profile[$TAG_PROVIDER] 			= $row2['provider'];
					if ($row2['provider'] == $TAG_EMAIL) {
						$profile[$TAG_IMAGE_URL]  	= $USER_PIC_PATH . $row2['image'];
					} else {
						$profile[$TAG_IMAGE_URL]  	= $row2['image'];
					}
					$profile[$TAG_FACEBOOK_ID] 		= $row2['facebook_id'];
					$profile[$TAG_CREATED_DATE] 	= $row2['created_date'];
					$profile[$TAG_NOTIFICATION_FLAG]= $row2['notification_flag'];
					$profile[$TAG_CREDITS] 			= $row2['credits'];

					$data[$TAG_PROFILE] 			= $profile;
				}

				$data[$TAG_MESSAGE] = $message;
			}
		}

	} else {
		$user_add = false;
	}

	return $user_add;
}


/*
* notifyUser
*/
function notifyUser($message, $registration_id) {
	include("../include/constant.php");
	require_once '../include/GCM.php';
	$db = new DB_CONNECT();
	$gcm = new GCM();

 	$gcm_id= array($registration_id);
	/*echo */json_encode($message);
	//$message = json_encode($message);
	//$notification_type = 1;
	//$jsonObj = array("message"=>$message, "type"=>$notification_type);
	//
	
	$message = array("message"=>$message, "type"=>3);
	
	
	$result = $gcm->send_notification($gcm_id, $message);
	//echo $result;
}


function addUserNotificationx($message, $to_user_id_csv, $from_user_id, $content_id, $other_data, $type_id) {
	include("../include/constant.php");
	
	$current_date = date("Y-m-d H:i:s");
	
	$values = "";
	
	$to_user_id_list = explode(",", $to_user_id_csv);
	foreach($to_user_id_list as $to_user_id) {
		$to_user_id = trim($to_user_id);
		$values = $values."('$to_user_id', '$message', '$from_user_id', '$content_id', '$type_id', '$current_date')".",";
	}
	
	$values = rtrim($values, ",");
	
	// ('$to_user_id', '$message', '$from_user_id', '$content_id', '$type_id', '$current_date')
	
	$query_user_add	= "INSERT INTO $TABLE_NOTIFICATION (user_id, message, from_user_id, content_id, type_id, created_date)  
	                   VALUES  
					   $values
					   ";
	$result_user_add = mysql_query($query_user_add);

	if ($result_user_add) {
		$user_add = true;

	} else {
		$user_add = false;
	}

	return $query_user_add;
}




/*
 * Create Notifications for user
 */
function createNotification($to_user_id, $from_user_id, $content_id, $other_data, $notification_type) {
	include("../include/constant.php");
	$user_add = false;
	$send_push_notification = false;
	
	// GCM DETAILS
	$profile = null;
	$query_detail = null;
	$query_response = null;
	$suggestion = null;
	$chat_id = 0;
	$chat_message = null;
	
	switch ($notification_type) {
		case $TAG_NOTIFICATION_CHAT:
		
			$profile = getProfile($from_user_id);
								
			$chat_id = $content_id;		
			//$user_name = "".$profile[$TAG_NAME];
			
			$chat_message = "".$other_data;
			
			$message = $chat_message; //." has sent you a chat message.";		
			//$user_add = addUserNotificationx($message, $to_user_id, $from_user_id, $content_id, $other_data, $notification_type);	
			$send_push_notification = true;
			break;
			
		case $TAG_NOTIFICATION_USER_FOLLOW:
		
			$profile = getProfile($from_user_id);
			$user_name = "".$profile[$TAG_NAME];
			
			$message = $user_name." started following you.";		
			$user_add = addUserNotificationx($message, $to_user_id, $from_user_id, $content_id, $other_data, $notification_type);	
			$send_push_notification = true;
			
			break;
		
		case $TAG_NOTIFICATION_FRIEND_JOINS:
			
			$profile = getProfile($from_user_id);
			$user_name = "".$profile[$TAG_NAME];
			
			$message = "Your invited friend ".$user_name." has joined lokaso.";		
			$user_add = addUserNotificationx($message, $to_user_id, $from_user_id, $content_id, $other_data, $notification_type);	
			$send_push_notification = true;
			break;

			
		case $TAG_NOTIFICATION_FOLLOW_QUERY:
			
			$profile = getProfile($from_user_id);
			$query_detail = getQuery($content_id);		
			$user_name = "".$profile[$TAG_NAME];
			
			$message = $user_name." followed your query.";		
			$user_add = addUserNotificationx($message, $to_user_id, $from_user_id, $content_id, $other_data, $notification_type);			
			$send_push_notification = true;
			break;

		case $TAG_NOTIFICATION_MY_QUERY_RESPONSE:
			
			$profile = getProfile($from_user_id);
			$query_detail = getQuery($content_id);		
			$user_name = "".$profile[$TAG_NAME];
			$message = $user_name." has responded on a query that you have asked.";		
			$user_add = addUserNotificationx($message, $to_user_id, $from_user_id, $content_id, $other_data, $notification_type);			
			$send_push_notification = true;
			break;
			
		case $TAG_NOTIFICATION_FOLLOWED_QUERY_RESPONSE:
			
			$profile = getProfile($from_user_id);
			$query_detail = getQuery($content_id);		
			$user_name = "".$profile[$TAG_NAME];
			$message = $user_name." has responded on a query that you have follwed.";		
			$user_add = addUserNotificationx($message, $to_user_id, $from_user_id, $content_id, $other_data, $notification_type);			
			$send_push_notification = true;
			break;

		case $TAG_NOTIFICATION_QUERY_RESPONSE_COMMENT:
			
			$profile = getProfile($from_user_id);
			$user_name = "".$profile[$TAG_NAME];
			
			$query_response = getQueryResponse($content_id);
			$query_id = "".$query_response[$TAG_ID];
			$query_detail = getQuery($query_id);

			$chat_message = "".$other_data;
			$message = $user_name." has commented on one of your response for a query.";		
			$user_add = addUserNotificationx($message, $to_user_id, $from_user_id, $content_id, $other_data, $notification_type);			
			$send_push_notification = true;
			break;
			
		case $TAG_NOTIFICATION_QUERY_RESPONSE_LIKE:
			
			$profile = getProfile($from_user_id);
			$user_name = "".$profile[$TAG_NAME];
			
			$query_response = getQueryResponse($content_id);
			$query_id = "".$query_response[$TAG_ID];
			$query_detail = getQuery($query_id);

			$chat_message = "".$other_data;
			$message = $user_name." has liked one of your response for a query.";		
			$user_add = addUserNotificationx($message, $to_user_id, $from_user_id, $content_id, $other_data, $notification_type);			
			$send_push_notification = true;
			break;

		case $TAG_NOTIFICATION_QUERY_RESPONSE_DISLIKE:
			
			$profile = getProfile($from_user_id);
			$user_name = "".$profile[$TAG_NAME];
			
			$query_response = getQueryResponse($content_id);
			$query_id = "".$query_response[$TAG_ID];
			$query_detail = getQuery($query_id);

			$chat_message = "".$other_data;
			$message = $user_name." has disliked one of your response for a query.";		
			$user_add = addUserNotificationx($message, $to_user_id, $from_user_id, $content_id, $other_data, $notification_type);			
			$send_push_notification = true;
			break;
			
			
		case $TAG_NOTIFICATION_QUERY_NEARBY:
			
			$profile = getProfile($from_user_id);
			$query_detail = getQuery($content_id);		
			$user_name = "".$profile[$TAG_NAME];
			
			$profile = null;
			
			$message = $user_name." has posted a query near you.";		
			$user_add = addUserNotificationx($message, $to_user_id, $from_user_id, $content_id, $other_data, $notification_type);			
			$send_push_notification = true;
			break;

		case $TAG_NOTIFICATION_LOCAL_SUGGESTERS:
			$message = $TAG_NOTIFICATION_LOCAL_SUGGESTERS_TEXT . " : " . $message;
			$user_add = addUserNotificationx($message, $to_user_id, $from_user_id, $content_id, $other_data, $notification_type);	
			break;
			
		default:
			break;
	}
	
	if($send_push_notification) 
	{		
		$details = array();
		$details["profile"] = $profile;
		$details["query"] = $query_detail;
		$details["query_response"] = $query_response;
		$details["suggestion"] = $suggestion;
		$details["chat_id"] = $chat_id;
		$details["chat_message"] = $chat_message;
		
		$result = sendNotification($to_user_id, $details, $message, $notification_type);
		//$user_add = $result;
	}	
	
	return $user_add;
}

/*
 * Create Notifications for user
 */
function createNotificationMultiple($to_user_id, $from_user_id, $content_id, $other_data, $notification_type) {
	include("../include/constant.php");
	$user_add = false;
	$send_push_notification = false;
	
	// GCM DETAILS
	$profile = null;
	$query_detail = null;
	$query_response = null;
	$suggestion = null;
	$chat_id = 0;
	$chat_message = null;
	
	switch ($notification_type) {
		
		case $TAG_NOTIFICATION_QUERY_NEARBY:
			
			$profile = getProfile($from_user_id);
			$query_detail = getQuery($content_id);
			$user_name = "".$profile[$TAG_NAME];
			
			$message = $user_name." has posted a query near you.";
			//$user_add = addUserNotificationx($message, $to_user_id, $from_user_id, $content_id, $other_data, $notification_type);			
			$send_push_notification = true;
			break;

		case $TAG_NOTIFICATION_LOCAL_SUGGESTERS:
			$message = $TAG_NOTIFICATION_LOCAL_SUGGESTERS_TEXT . " : " . $message;
			$user_add = addUserNotificationx($message, $to_user_id, $from_user_id, $content_id, $other_data, $notification_type);	
			break;
			
		default:
			break;
	}
	
	if($send_push_notification) 
	{		
		$details = array();
		$details["profile"] = $profile;
		$details["query"] = $query_detail;
		$details["query_response"] = $query_response;
		$details["suggestion"] = $suggestion;
		$details["chat_id"] = $chat_id;
		$details["chat_message"] = $chat_message;
		
		sendNotification($to_user_id, $details, $message, $notification_type);
		
	}	
	
	return $user_add;
}





/*
 * Method : sendNotification (NOTE : This structure is common for any project)
 *
 * @params
 * details - Its an json object of all the data required 
 */
function sendNotification($to_user_id, $details, $message, $notification_type) {
	include("../include/constant.php");
	$db = new DB_CONNECT();
	
	
	$current_date = date("Y-m-d H:i:s");
	
	$gcm_ids= array();
	
	// Get from user
	
	// Find reg_ids based on to_user_id	
	$gcm_token = "";
	$query = "SELECT gcm_token FROM $TABLE_GCM WHERE user_id in ($to_user_id) ";
	$result = mysql_query($query);
	
	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result)) {
			
			$gcm_token				= $row["gcm_token"];
			$user_id				= $row["user_id"];
			$device_id				= $row["device_id"];
			
			array_push($gcm_ids, $gcm_token);
		}
	}
	
	if(count($gcm_ids)>1) {
		$to_user_id = 0;
	}
	
	$gcm_data = array();
	$gcm_data["to_user_id"] = (int)$to_user_id;
	$gcm_data["details"] = $details;
	$gcm_data["message"] = $message;
	$gcm_data["notification_type"] = (int)$notification_type;
	$gcm_data["created_date"] = $current_date;
	//$gcm_data["gcm_ids"] = $gcm_ids;
	//$gcm_data["query"] = $query;

	$message1 =  json_encode($gcm_data);
	
	// Send	
	require_once '../include/GCM.php';
	$gcm = new GCM();
	
 	$gcm_id= array($gcm_token);
	/*echo */json_encode($message);
	//$message = json_encode($message);
	//$notification_type = 1;
	//$jsonObj = array("message"=>$message, "type"=>$notification_type);
	//
	
	$message = array("message"=>$message1);
	
	//DEVIDE ARRAY INTO SUB ARRAY OF 500
	$arrays = array_chunk($gcm_ids, 500);

	//SEND NOTIFICATION TO EACH SUB ARRAY SET
	foreach ($arrays as $array_num => $array) {
		$result = $gcm->send_notification($array, $message);	  
	}
		
	//$result = $gcm->send_notification($gcm_ids, $message);
	
	return $result." -- ".$message1;
}


/** Send Test notification
 * url - /query/extendValidity
 * method - POST
 */
$app->post('/user/notify_test', function() use ($app) {
	// check for required params
	include("../include/constant.php");
	$db = new DB_CONNECT();

	$token = $app->request()->headers->get($TAG_TOKEN);
	if(authenticate($token)){
					
		mysql_set_charset('utf8');
		// reading post params
		$user_id = $app->request->post('user_id');
		$message = $app->request->post('message');

		$user_id = 100;
				

				
		$details = array();
		//$details["profile"] = $profile;
		//$details["query"] = $query;
		//$details["query_response"] = $query_response;
		//$details["suggestion"] = $suggestion;
		//$details["chat_id"] = $chat_id;
		

		// Send notification
		$notification_type = $TAG_NOTIFICATION_CHAT;
		//$content_id = $chat_id;
		//createNotification($to_user_id, $from_user_id, $content_id, $notification_type);

		echo "Success : message : ".$message.", details : ".$details.", user_id : ".$user_id.", notification_type : ".$notification_type;
		sendNotification($user_id, $details, $message, $notification_type);
			
		
	}
});






// NOTIFICATION ---- END




function getAction($user_id, $action_type) {
	include("../include/constant.php");
	
	$action = array();
	$action[$TAG_ACTION_ID] = $user_id;
	$action[$TAG_ACTION_STATUS] = $action_type;

	return $action;
}


function getResponseUserId($response_id) {
	include("../include/constant.php");
	$query_user = "SELECT * FROM $TABLE_ASK_RESPONSE WHERE id = '$response_id'";
	$result_user = mysql_query($query_user);

	if($result_user) {
		if (mysql_num_rows($result_user) > 0) {
			while ($row_user = mysql_fetch_array($result_user)) {
				$user_id	= $row_user["user_id"];
			}

		} else {
			$user_id = 0;
		}

	} else {
		$user_id = 0;
	}

	return $user_id;
}



function getDiscoveryUserId($discovery_id) {
	include("../include/constant.php");
	$query_user = "SELECT * FROM $TABLE_SUGGESTION WHERE id = '$discovery_id'";
	$result_user = mysql_query($query_user);

	if($result_user) {
		if (mysql_num_rows($result_user) > 0) {
			while ($row_user = mysql_fetch_array($result_user)) {
				$user_id	= $row_user["user_id"];
			}

		} else {
			$user_id = 0;
		}

	} else {
		$user_id = 0;
	}

	return $user_id;
}




/*
* Give points to user for referral code
*/
function createFirstChat($to_user_id) {
		
	$success = false;
	include("../include/constant.php");
	
	$from_user_id = 1; // Lokaso Official
	$message = "Welcome to Lokaso. Now you could chat with Lokaso Official to get any kind of help or give any feedback. We are always here for you.";

	$created_date 	= date("Y-m-d H:i:s");
	
	$query	= "SELECT * FROM $TABLE_CHAT_ROOM WHERE from_user_id = '$from_user_id' AND to_user_id = '$to_user_id' OR from_user_id = '$to_user_id' AND to_user_id = '$from_user_id'";
	$result = mysql_query($query);
	
	if($result) {
		if (mysql_num_rows($result) == 0) {
			
			// First create a chat room
			$query2	= "INSERT INTO $TABLE_CHAT_ROOM (from_user_id, to_user_id, created_date, updated_date) VALUES ('$from_user_id', '$to_user_id', '$created_date', '$created_date')";
			$result2 = mysql_query($query2);

			if ($result2) {
				$chat_id = mysql_insert_id();
				
				// Now create first chat
				$query_user_add	= "INSERT INTO $TABLE_CONVERSATION (chat_id, message, from_user_id, created_date) VALUES ('$chat_id', '$message','$from_user_id', '$created_date')";
				$result_user_add = mysql_query($query_user_add);		

				//echo "qq : ".$query_user_add;
				
				$success = true;
			}
		}
	}
		
	return $success;
}


										
/*
* Give points to user for referral code
*/
function giveReferCodePoint($referral_code, $user_id_invite_to) {
	
	$success = false;
	include("../include/constant.php");
	if(strlen($referral_code)>0) {
				
		$query_user = "SELECT * FROM $TABLE_USER WHERE refer_code = '$referral_code'";
		
		$result_user = mysql_query($query_user);
		if (mysql_num_rows($result_user) > 0) {
		
			while ($row_user = mysql_fetch_array($result_user)) { // This means a user is found whose referral code matches
				$refer_code_user	= $row_user[$TAG_REFER_CODE];
				$user_id_invite_from = $row_user[$TAG_ID];
						
				// Assign point to this user
				getCreditValue($TAG_CREDIT_SIGNUP_INVITE_FROM, $user_id_invite_from);
						
				// Send notification to user who sent invite saying that your invited friend has joined lokaso
				$notification_type = $TAG_NOTIFICATION_FRIEND_JOINS;
				$to_user_id = $user_id_invite_from;
				$from_user_id = $user_id_invite_to;
				$content_id = $user_id_invite_to;
				$other_data = "Your invited friend joins lokaso.";
				createNotification($to_user_id, $from_user_id, $content_id, $other_data, $notification_type);
			}
			
							
			
			// Assign point to this user
			getCreditValue($TAG_CREDIT_SIGNUP_INVITE_TO, $user_id_invite_to);
		}
	}
	else {		
		//echo "fail here";
	}
	
	return $success;
}


/*
* Get user name
*/
function getCreditValue($tag, $user_id) {
	include("../include/constant.php");
	$success = false;
	$query_user = "SELECT * FROM $TABLE_CREDIT WHERE name = '$tag'";
	$result_user = mysql_query($query_user);
	$total_user_points = 0;
	$tag_points	= 0;
	if($result_user) {
		if (mysql_num_rows($result_user) > 0) {
			while ($row_user = mysql_fetch_array($result_user)) {
				$points	= $row_user[$TAG_POINTS];
				$credit_name = $row_user[$TAG_NAME];
				
				$tag_points	= $points; 

				$query_credit = "INSERT INTO $TABLE_USER_CREDITS (user_id, credit_name, points) VALUES ('$user_id', '$credit_name', '$points')";
				$result_credit = mysql_query($query_credit);

				if($result_credit) {
					$query_credit_sum = "SELECT SUM(points) AS points FROM $TABLE_USER_CREDITS WHERE user_id = '$user_id'";
					$result_credit_sum = mysql_query($query_credit_sum);

					if($result_credit_sum) {
						if (mysql_num_rows($result_credit_sum) > 0) {
							while ($row_credit_sum = mysql_fetch_array($result_credit_sum)) {
								$total_user_points	= $row_credit_sum[$TAG_POINTS];
							}

						} else {
							$total_user_points = 0;
						}

					} else {
						$total_user_points = 0;
					}

				} else {
					$total_user_points = 0;
				}

				$success = setUserCredits($user_id, $total_user_points);
			}

		} else {
			$points = null;
			$credit_name = null;
		}

	} else {
		$points = null;
		$credit_name = null;
	}

	return $tag_points;
}

function isQueryInactive($query_id) {
	include("../include/constant.php");
	$query_user = "SELECT status FROM $TABLE_ASK WHERE id = '$query_id'";
	$result_user = mysql_query($query_user);
	$statusObj	= array();
	$statusObj[$TAG_INACTIVE] = false;
	if($result_user) {
		if (mysql_num_rows($result_user) > 0) {
			$row_user = mysql_fetch_array($result_user);
			$status	= $row_user[$TAG_STATUS];
			$statusObj[$TAG_STATUS] = $status;
			if($status==0) {
				$statusObj[$TAG_INACTIVE] = true;
				$statusObj[$TAG_MESSAGE] = "You cannot perform this action because this query has been blocked by the admin.";
			}				
		}
	}

	return $statusObj;
}

function isSuggestionInactive($suggestion_id) {
	include("../include/constant.php");
	$query_user = "SELECT status FROM $TABLE_SUGGESTION WHERE id = '$suggestion_id'";
	$result_user = mysql_query($query_user);
	$statusObj	= array();
	$statusObj[$TAG_INACTIVE] = false;
	if($result_user) {
		if (mysql_num_rows($result_user) > 0) {
			$row_user = mysql_fetch_array($result_user);
			$status	= $row_user[$TAG_STATUS];
			$statusObj[$TAG_STATUS] = $status;
			if($status==0) {
				$statusObj[$TAG_INACTIVE] = true;
				$statusObj[$TAG_MESSAGE] = "You cannot perform this action because this tip has been blocked by the admin.";
			}				
		}
	}

	return $statusObj;
}

function isUserInactive($user_id) {
	include("../include/constant.php");
	$query_user = "SELECT status FROM $TABLE_USER WHERE id = '$user_id'";
	$result_user = mysql_query($query_user);
	$statusObj	= array();
	$statusObj[$TAG_USER_INACTIVE] = false;
	if($result_user) {
		if (mysql_num_rows($result_user) > 0) {
			$row_user = mysql_fetch_array($result_user);
			$status	= $row_user[$TAG_STATUS];
			$statusObj[$TAG_STATUS] = $status;
			if($status==0) {
				$statusObj[$TAG_USER_INACTIVE] = true;
				$statusObj[$TAG_MESSAGE] = "You cannot perform this action because you have been blocked by the admin.";
			}				
		}
	}

	return $statusObj;
}

function getUserCredits($user_id) {
	include("../include/constant.php");
	$query_user = "SELECT credits FROM $TABLE_USER WHERE id = '$user_id'";
	$result_user = mysql_query($query_user);

	if($result_user) {
		if (mysql_num_rows($result_user) > 0) {
			while ($row_user = mysql_fetch_array($result_user)) {
				$credit	= $row_user[$TAG_CREDITS];
			}

		} else {
			$credit = null;
		}

	} else {
		$credit = null;
	}

	return $credit;
}



function setUserCredits($user_id, $total_points) {
	include("../include/constant.php");
	$query_user = "UPDATE $TABLE_USER SET credits = '$total_points' WHERE id = $user_id";
	$result_user = mysql_query($query_user);

	if($result_user) {
		$status = true;

	} else {
		$status = false;
	}

	return $status;
}



/*
* Get Discovery Count
*/
function getDiscoveryCount($user_id) {
	include("../include/constant.php");
	$query_follow = "SELECT COUNT(user_id) AS discovery_count FROM $TABLE_SUGGESTION WHERE user_id = '$user_id'";
	$result_follow = mysql_query($query_follow);

	if($result_follow) {
		if (mysql_num_rows($result_follow) > 0) {
			while ($row_follow = mysql_fetch_array($result_follow)) {
				$discovery_count	= $row_follow[$TAG_DISCOVERY_COUNT];
			}

		} else {
			$discovery_count = 0;
		}

	} else {
		$discovery_count = 0;
	}

	return $discovery_count;
}



/*
* Get Query Count
*/
function getQueryCount($user_id) {
	include("../include/constant.php");
	$query_follow = "SELECT COUNT(user_id) AS query_count FROM $TABLE_ASK WHERE user_id = '$user_id'";
	$result_follow = mysql_query($query_follow);

	if($result_follow) {
		if (mysql_num_rows($result_follow) > 0) {
			while ($row_follow = mysql_fetch_array($result_follow)) {
				$query_count	= $row_follow[$TAG_QUERY_COUNT];
			}

		} else {
			$query_count = 0;
		}
	} else {
		$query_count = 0;
	}

	return $query_count;
}


/*
* Get Following Count
*/
function getFollowingCount($user_id) {
	include("../include/constant.php");
	// Follower count
	$query_follow = "SELECT COUNT(leader) AS follow_count FROM $TABLE_USER_FOLLOW WHERE leader = '$user_id' and status=1";
	$result_follow = mysql_query($query_follow);

	if($result_follow) {
		if (mysql_num_rows($result_follow) > 0) {
			while ($row_follow = mysql_fetch_array($result_follow)) {
				$follow	= $row_follow["follow_count"];
			}

		} else {
			$follow = 0;
		}

	} else {
		$follow = 0;
	}

	return $follow;
}




/*
* Get user name
*/
function getUserName($user_id) {
	include("../include/constant.php");
	$query_user = "SELECT name FROM $TABLE_USER WHERE id = '$user_id'";
	$result_user = mysql_query($query_user);

	if($result_user) {
		if (mysql_num_rows($result_user) > 0) {
			while ($row_user = mysql_fetch_array($result_user)) {
				$user	= $row_user[$TAG_NAME];
			}

		} else {
			$user = null;
		}

	} else {
		$user = null;
	}

	return $user;
}

/*
* Get user image
*/
function getUserImage($user_id) {
	include("../include/constant.php");
	$query_user1 = "SELECT provider, image FROM $TABLE_USER WHERE id = '$user_id'";
	$result_user1 = mysql_query($query_user1);

	if($result_user1) {
		if (mysql_num_rows($result_user1) > 0) {
			while ($row_user1 = mysql_fetch_array($result_user1)) {
				if ($row_user1['provider'] == $TAG_EMAIL) {
					$image = $USER_PIC_PATH . $row_user1['image'];
				} else {
					$image  = $row_user1['image'];
				}
			}

		} else {
			$image = null;
		}

	} else {
		$image = null;
	}

	return $image;
}


/*
* Get favourite count of discovery
*/
function getDiscoveryFavCount($discovery_id) {
	include("../include/constant.php");
	$query_fav_count = "SELECT COUNT(discovery_id) AS fav_count FROM $TABLE_SUGGESTION_FAV WHERE discovery_id = '$discovery_id' and status=1";
	$result_fav_count = mysql_query($query_fav_count);

	if($result_fav_count) {
		if (mysql_num_rows($result_fav_count) > 0) {
			while ($row_fav_count = mysql_fetch_array($result_fav_count)) {
				$fav_count	= $row_fav_count["fav_count"];
			}

		} else {
			$fav_count = 0;
		}

	} else {
		$fav_count = 0;
	}

	return $fav_count;
}


/*
* Get favourite count of discovery
*/
function getQueryResponseCount($query_id) {
	include("../include/constant.php");
	$query = "SELECT COUNT(query_id) AS total FROM $TABLE_ASK_RESPONSE WHERE query_id = '$query_id'  and status=1";
	$result = mysql_query($query);
	
	$total = 0;
	if($result) {
		if (mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_array($result)) {
				$total	= $row["total"];
			}

		} else {
			$total = 0;
		}

	} else {
		$total = 0;
	}

	return $total;
}


function isQueryFav($query_id, $user_id){
	include("../include/constant.php");
$query_query_fav	= "SELECT * FROM $TABLE_USER_QUERY WHERE query_id = '$query_id' AND user_id = '$user_id'";
	$result_query_fav = mysql_query($query_query_fav);

	if($result_query_fav) {
		if (mysql_num_rows($result_query_fav) > 0) {
				$user_fav = true;

		} else {
			$user_fav = false;
		}

	} else {
		$user_fav = false;
	}

	return $user_fav;
}


/*
* Check if user has favourited the discovery
*/
function isDiscoveryUserFav($discovery_id, $user_id) {
	include("../include/constant.php");
	$query_user_fav	= "SELECT * FROM $TABLE_SUGGESTION_FAV WHERE discovery_id = '$discovery_id' AND user_id = '$user_id' and status = 1";
	$result_user_fav = mysql_query($query_user_fav);

	if($result_user_fav) {
		if (mysql_num_rows($result_user_fav) > 0) {
				$user_fav = true;

		} else {
			$user_fav = false;
		}

	} else {
		$user_fav = false;
	}

	return $user_fav;
}

/*
* Check if user has spammed the suggestion
*/
function isSuggestionUserSpam($suggestion_id, $user_id) {
	include("../include/constant.php");
	$query_user_spam	= "SELECT * FROM $TABLE_SUGGESTION_SPAM WHERE suggestion_id = '$suggestion_id' AND user_id = '$user_id'";
	$result_user_spam = mysql_query($query_user_spam);

	if($result_user_spam) {
		if (mysql_num_rows($result_user_spam) > 0) {
				$user_spam = true;

		} else {
			$user_spam = false;
		}

	} else {
		$user_spam = false;
	}

	return $user_spam;
}


/*
* Check if user has spammed the response
*/
function isResponseUserSpammed($response_id, $user_id) {
	include("../include/constant.php");
	$query_user_spam	= "SELECT * FROM $TABLE_ASK_RESPONSE_SPAM WHERE response_id = '$response_id' AND user_id = '$user_id'";
	$result_user_spam = mysql_query($query_user_spam);

	if($result_user_spam) {
		if (mysql_num_rows($result_user_spam) > 0) {
			$user_spam = true;

		} else {
			$user_spam = false;
		}

	} else {
		$user_spam = false;
	}

	return $user_spam;
}


/*
* Check if user has favourited the response
*/
function isResponseUserFav($response_id, $user_id) {
	include("../include/constant.php");
	$query_user_fav	= "SELECT * FROM $TABLE_ASK_RESPONSE_ACTION WHERE response_id = '$response_id' AND user_id = '$user_id'";
	$result_user_fav = mysql_query($query_user_fav);

	if($result_user_fav) {
		if (mysql_num_rows($result_user_fav) > 0) {
			while ($row_user_fav = mysql_fetch_array($result_user_fav)) {
				$user_fav = $row_user_fav["action"];
			}

		} else {
			$user_fav = -1;
		}

	} else {
		$user_fav = -1;
	}

	return $user_fav;
}


/*
* Add Discovery to users list
*/
function addUserDiscovery($discovery_id, $user_id) {
	include("../include/constant.php");
	$query_user_add	= "INSERT INTO $TABLE_USER_SUGGESTION (discovery_id, user_id) VALUES ('$discovery_id', '$user_id')";
	$result_user_add = mysql_query($query_user_add);

	if ($result_user_add) {
		$user_add = true;

	} else {
		$user_add = false;
	}

	return $user_add;
}


/*
* Get Comment count of discovery
*/
function getDiscoveryCommentCount($discovery_id) {
	include("../include/constant.php");
	$query_comment_count = "SELECT COUNT(discovery_id) AS comment_count FROM $TABLE_SUGGESTION_COMMENT WHERE discovery_id = '$discovery_id'";
	$result_comment_count = mysql_query($query_comment_count);

	if($result_comment_count) {
		if (mysql_num_rows($result_comment_count) > 0) {
			while ($row_comment_count = mysql_fetch_array($result_comment_count)) {
				$comment_count	= $row_comment_count["comment_count"];
			}

		} else {
			$comment_count = 0;
		}

	} else {
		$comment_count = 0;
	}

	return $comment_count;
}


/*
* Check if user has favourited the discovery
*/
function isUserDiscovery($discovery_id, $user_id) {
	include("../include/constant.php");
	$query_user_disc  = "SELECT * FROM $TABLE_USER_SUGGESTION WHERE discovery_id = '$discovery_id' AND user_id = '$user_id'";
	$result_user_disc = mysql_query($query_user_disc);

	if($result_user_disc) {
		if (mysql_num_rows($result_user_disc) > 0) {
				$user_disc = true;

		} else {
			$user_disc = false;
		}

	} else {
		$user_disc = false;
	}

	return $user_disc;
}

/*
* Get Credit display
*/
function getCredits($credits) {
	
	return $credits;
}

/*
* Get Profession using profession_id
*/
function getProfession($profession_id) {
	include("../include/constant.php");
	$query_profession = "SELECT name FROM $TABLE_PROFESSION WHERE id = '$profession_id'";
	$result_profession = mysql_query($query_profession);

	if($result_profession) {
		if (mysql_num_rows($result_profession) > 0) {
			while ($row_profession = mysql_fetch_array($result_profession)) {
				$profession	= $row_profession[$TAG_NAME];
			}

		} else {
			$profession = null;
		}

	} else {
		$profession = null;
	}

	return $profession;
}


/*
* Check if user has followed a user
*/
function isUserFollowed($leader, $follower) {
	include("../include/constant.php");
	$query_user_follow	= "SELECT * FROM $TABLE_USER_FOLLOW WHERE leader = '$leader' AND follower = '$follower'";
	$result_user_follow = mysql_query($query_user_follow);

	if($result_user_follow) {
		if (mysql_num_rows($result_user_follow) > 0) {
			while ($row_user_follow = mysql_fetch_array($result_user_follow)) {
				if($row_user_follow['status'] == 1) {
					$user_follow = true;

				} else {
					$user_follow = false;
				}
			}

		} else {
			$user_follow = false;
		}

	} else {
		$user_follow = false;
	}

	return $user_follow;
}


/*
* Check if user has followed a user
*/
function isUserReported($spam_user_id, $user_id) {
	include("../include/constant.php");
	$query_user_spam = "SELECT * FROM $TABLE_ASK_USER_SPAM WHERE spam_user_id = '$spam_user_id' AND user_id = '$user_id'";
	$result_user_spam = mysql_query($query_user_spam);

	if($result_user_spam) {
		if (mysql_num_rows($result_user_spam) > 0) {
			$user_spam = true;

		} else {
			$user_spam = false;
		}

	} else {
		$user_spam = false;
	}

	return $user_spam;
}


/*
* Check if user has followed a user
*/
function isQueryFlagged($query_id, $user_id) {
	include("../include/constant.php");
	$query_ask_spam	= "SELECT * FROM $TABLE_ASK_SPAM WHERE query_id = '$query_id' AND user_id = '$user_id'";
	$result_ask_spam = mysql_query($query_ask_spam);

	if($result_ask_spam) {
		if (mysql_num_rows($result_ask_spam) > 0) {
			$ask_spam = true;

		} else {
			$ask_spam = false;
		}

	} else {
		$ask_spam = false;
	}

	return $ask_spam;
}


function getDistanceDisplay($distance) {
		
	$dist = $distance;
	$km_tag = "kms";
	if($dist>1) {
		$dist = round($dist, 1);
	}
	else {		
		$dist = round($dist, 2);
		$km_tag = "km";
	}
	
	$distance_text = $dist." ".$km_tag." away";
		
	return $distance_text;
}
function getUserCreditDisplay($credit) {
		
	$credit_display = $credit;
	if($credit>1000) {
		$credit_pt = $credit/1000;
		$credit_pt = round($credit_pt, 2);
		$credit_display = $credit_pt."K";
	}
		
	return $credit_display."";
}


/*
* Calculate Distance using Latitude and Longitude
* Unit : Kilometers
*/
function distance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo) {
	include("../include/constant.php");
	$earthRadius = 6371000;
	$latFrom = deg2rad($latitudeFrom);
	$lonFrom = deg2rad($longitudeFrom);
	$latTo = deg2rad($latitudeTo);
	$lonTo = deg2rad($longitudeTo);

	$latDelta = $latTo - $latFrom;
	$lonDelta = $lonTo - $lonFrom;

	$angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
	$total = $angle * $earthRadius;
	
	$dist = $total/1000; // in km
	/*
	if($dist>1) {
		$dist = round($dist, 1);
	}
	else {		
		$dist = round($dist, 2);
	}
	*/
	$dist = round($dist);
	
	return $dist;
}



/**
* Uploading File
*/
function _upload_file($file_data, $upload_path) {
	include("../include/constant.php");
	try {
		$file_data 	= str_replace('data:image/jpeg;base64,', '', $file_data);
		$file_data 	= str_replace(' ', '+', $file_data);
		$file_data 	= base64_decode($file_data);
		$file_name 	= uniqid() . '.jpg';
		$target_dir = $IMAGE_UPLOAD_PATH . $upload_path;
		$file 		= $target_dir . $file_name ;
		$success 	= file_put_contents($file, $file_data);

	} catch(Exception $e) {
		$file_name = '';
		$app->response->setStatus(500);
		send_response('{"error":{"text":'. $e->getMessage() .'}}');
		die;
	}
	return $file_name;
}


/**
* Uploading Image
*/
function uploadImage($user_id, $uploaddir, $file, $tmp_filename){
	include("../include/constant.php");
		
		
	$target_dir = $IMAGE_UPLOAD_PATH . $uploaddir;
	
	$name_type = "suggestion";
	
	$date 						= date("d-m-Y_H-i-s");
	$temp_file					= explode(".", $file);
	$new_filename 				= $user_id.'-'.$name_type.'-'.$date.'.'.end($temp_file);
	$uploadfile 				= $target_dir . $new_filename;

	$response = !move_uploaded_file($tmp_filename, $uploadfile);
	
	if (!$response) {
		$filename = "Error";
		
	}
	
	//$new_filename = $response;

	return $filename = $new_filename;
}


/**
 * Validating email address
 */
function escape($str) {
	
	//$str = str_replace("'","\'",$str);
	
	$str = mysql_real_escape_string($str);
	
	return $str;
}
		

/**
 * Validating email address
 */
function getRandomString($length) {
	include("../include/constant.php");
	$characters = '23456789abcdefghkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}
		
/**
 * Verifying required params posted or not
 */
function verifyRequiredParams($required_fields) {
	include("../include/constant.php");
    $error = false;
    $error_fields = "";
    $request_params = array();
    $request_params = $_REQUEST;
    // Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }
    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["error"] = true;
        $response[$TAG_MESSAGE] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoRespnse(400, $response);
        $app->stop();
    }
}




/**
 * Validating email address
 */
function validateEmail($email) {
	include("../include/constant.php");
    $app = \Slim\Slim::getInstance();
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["error"] = true;
        $response[$TAG_MESSAGE] = 'Email address is not valid';
        echoRespnse(400, $response);
        $app->stop();
    }
}

/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function echoRespnse($status_code, $response) {
	include("../include/constant.php");
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

    echo json_encode($response);
}

$app->run();
?>