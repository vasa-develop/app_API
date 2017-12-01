<?php

date_default_timezone_set("Asia/Kolkata");

//Response TAGS
$TAG_DETAILS 			= "details";
$TAG_SUCCESS 			= "success";
$TAG_CODE 				= "code";
$TAG_MESSAGE 			= "message";
$TAG_QUERY 				= "query";

//Collumns
$TAG_ID					= "id";
$TAG_NAME				= "name";
$TAG_FNAME				= "fname";
$TAG_LNAME				= "lname";
$TAG_EMAIL				= "email";
$TAG_ABOUT_ME			= "about_me";
$TAG_PROFESSION			= "profession";
$TAG_PROFESSION_ID		= "profession_id";
$TAG_PROFESSION_NAME	= "profession_name";
$TAG_LOCATION			= "location";
$TAG_CURRENT_LAT		= "current_lat";
$TAG_CURRENT_LNG		= "current_lng";
$TAG_PROVIDER			= "provider";
$TAG_FACEBOOK_ID		= "facebook_id";
$TAG_IMAGE_URL			= "image";
$TAG_NOTIFICATION_FLAG	= "notification_flag";
$TAG_IS_REGISTER		= "is_register";
$TAG_IS_NEW_USER		= "is_new_user";
$TAG_IS_NEW_USER_UPDATE = "is_new_user_update";
$TAG_CREDITS 			= "credits";
$TAG_CREDITS_DISPLAY	= "credits_display";
$TAG_CREATED_DATE		= "created_date";
$TAG_UPDATED_DATE 		= "updated_date";
$TAG_PROFILE			= "profile";
$TAG_FNAME				= "fname";
$TAG_LNAME				= "lname";
$TAG_USER_ID		 	= "user_id";
$TAG_IMAGE 				= "image";
$TAG_STATUS 			= "status";
$TAG_INTEREST_ID		= "interest_id";
$TAG_INTEREST			= "interest";
$TAG_LNG 				= "lng";
$TAG_LAT 				= "lat";
$TAG_DISTANCE			= "distance";
$TAG_DISTANCE_DISPLAY	= "distance_display";
$TAG_SUGGESTION_ID		= "discovery_id";
$TAG_FAV_COUNT			= "fav_count";
$TAG_COMMENT_COUNT		= "comment_count";
$TAG_USER_FAV			= "user_fav";
$TAG_USER_DISC			= "user_discovery";
$TAG_TOKEN 				= "token";
$TAG_SUGGESTION_SPAM 	= "suggestion_spam";
$TAG_QUERY_ID 			= "query_id";
$TAG_RESPONSE 			= "response";
$TAG_SPAM 				= "spam";
$TAG_MARK_INAPPROPRIATE = "mark_inappropriate";
$TAG_RESPONSE_ID		= "response_id";
$TAG_COMMENT 			= "comment";
$TAG_QUESTION			= "question";
$TAG_ANSWER_NAME		= "answer_name";
$TAG_CAPTION			= "caption";
$TAG_SUGGESTION 		= "suggestion";
$TAG_NUM_ASKS			= "num_asks";
$TAG_NUM_RESPONSES		= "num_responses";
$TAG_USER_FOLLOWED 		= "user_followed";
$TAG_RESPONSE_COUNT		= "response_count";
$TAG_QUERY_FAV			= "query_fav";
$TAG_DESCRIPTION 		= "description";
$TAG_VALID_DATE 		= "valid_date";
$TAG_FLAG 				= "flag";
$TAG_REPORT				= "report";
$TAG_VALID_UNTIL		= "valid_until";
$TAG_FLAG_SOLVED 		= "flag_solved";
$TAG_MARKED_INAPPROPRIATE = "marked_inappropriate";
$TAG_DISCOVERY_COUNT 	= "discovery_count";
$TAG_QUERY_COUNT 		= "query_count";
$TAG_CHAT_ID 			= "chat_id";
$TAG_FROM_USER_ID 		= "from_user_id";
$TAG_TO_USER_ID 		= "to_user_id";
$TAG_UNREAD_COUNT 		= "unread_count";
$TAG_CREDIT_NAME 		= "credit_name";
$TAG_POINTS 			= "points";
$TAG_FOLLOWING_COUNT 	= "following_count";
$TAG_UPVOTES 			= "upvotes";
$TAG_DOWNVOTES 			= "downvotes";
$TAG_REFER_CODE 		= "refer_code";
$TAG_CHAT_UNREAD_COUNT 	= "chat_unread_count";
$TAG_NOTIFICATION_UNREAD_COUNT 	= "notification_unread_count";
$TAG_TO_USER_SEEN 		= "to_user_seen";
$TAG_CHAT_FROM_USER_ID 	= "chat_from_user_id";
$TAG_CHAT_CREATED_DATE	= "chat_created_date";
$TAG_DISPLAY_ORDER		= "display_order";

$TAG_IS_INVITE_ENABLED		= "is_invite_enabled";
$IS_INVITE_ENABLED			= false;

$TAG_ACTION 				= "action";
$TAG_ACTION_ID 				= "action_id";
$TAG_ACTION_STATUS 			= "action_status";

$TAG_VERSION_CODE			= "version_code";
$TAG_VERSION_NAME			= "version_name";
$TAG_VERSION_UPDATE_TYPE	= "version_update_type";

$TAG_INTEREST_LIST			= "interest_list";

$TAG_USER_INACTIVE			= "user_inactive";
$TAG_INACTIVE				= "inactive";

$TAG_ADS_LIST				= "ads_list";
$TAG_AD_POSITION			= "ad_position";
$TAG_URL					= "url";
$TAG_TITLE					= "title";
$TAG_TYPE					= "type";
$TAG_YOUTUBE_ID				= "youtube_id";
$TAG_YOUTUBE_STATUS			= "youtube_status";
$TAG_YOUTUBE_DESCRIPTION	= "youtube_description";
$TAG_YOUTUBE_IMAGE			= "youtube_image";


/*$TAG_CREDIT_LIKE		= "Like";
$TAG_CREDIT_COMMENT		= "Comment";
$TAG_CREDIT_SIGNUP		= "Signup";
$TAG_CREDIT_FOLLOW		= "Follow";
$TAG_CREDIT_RESPONSE	= "Response";*/

//Credits TAGS
$TAG_CREDIT_SIGNUP						= "Signup";
$TAG_CREDIT_ATTEND_QUERY				= "Attend Query";
$TAG_CREDIT_LIKE_RESPONSE				= "Like Response";
$TAG_CREDIT_SUGGESTION					= "Create Suggestion";
$TAG_CREDIT_SUGGESTION_PICTURE			= "Create Suggestion Picture";
$TAG_CREDIT_LIKE_PICTURE				= "Like Picture";
$TAG_CREDIT_USER_INVITE					= "User Invite";
$TAG_CREDIT_ASK_QUERY					= "Ask Query";
$TAG_CREDIT_FOLLOW						= "Follow";
$TAG_CREDIT_SIGNUP_INVITE_FROM			= "Signup_Invite_from";
$TAG_CREDIT_SIGNUP_INVITE_TO			= "Signup_Invite_to";


/*$TAG_NOTIFICATION_DISCOVERY_FAV			= 1;
$TAG_NOTIFICATION_DISCOVERY_COMMENT			= 2;
$TAG_NOTIFICATION_USER_FOLLOW				= 3;
$TAG_NOTIFICATION_QUERY_FOLLOW				= 4;
$TAG_NOTIFICATION_RESPONSE_COMMENT			= 5;
$TAG_NOTIFICATION_RESPONSE_FAV				= 6;*/

//Notification TAGS
//$TAG_NOTIFICATION_MY_QUERY_RESPONSE			= 1;
//$TAG_NOTIFICATION_FOLLOWED_QUERY_RESPONSE	= 2;
//$TAG_NOTIFICATION_USER_FOLLOW				= 3;
//$TAG_NOTIFICATION_FRIEND_JOINS				= 4;
//$TAG_NOTIFICATION_LOCAL_SUGGESTERS			= 5;

$TAG_NOTIFICATION_MY_QUERY_RESPONSE			= 1;
$TAG_NOTIFICATION_FOLLOWED_QUERY_RESPONSE	= 2;
$TAG_NOTIFICATION_FOLLOW_QUERY				= 4;
$TAG_NOTIFICATION_LIKE_QUERY				= 5;
$TAG_NOTIFICATION_DISLIKE_QUERY				= 6;
$TAG_NOTIFICATION_QUERY_RESPONSE_LIKE		= 7;
$TAG_NOTIFICATION_QUERY_RESPONSE_DISLIKE	= 8;
$TAG_NOTIFICATION_QUERY_RESPONSE_COMMENT	= 3;
$TAG_NOTIFICATION_QUERY_NEARBY				= 9;
$TAG_NOTIFICATION_QUERY_VERIFY				= 10;

$TAG_NOTIFICATION_LOCAL_SUGGESTERS			= 11;
$TAG_NOTIFICATION_SUGGESTION_LIKE			= 12;
$TAG_NOTIFICATION_SUGGESTION_COMMENT		= 13;
$TAG_NOTIFICATION_SUGGESTION_VERIFY			= 14;

$TAG_NOTIFICATION_USER_FOLLOW				= 21;
$TAG_NOTIFICATION_CHAT						= 22;
$TAG_NOTIFICATION_FRIEND_JOINS				= 23;

$TAG_NOTIFICATION_ADHOC						= 30;
$TAG_NOTIFICATION_LOGOUT_USER				= 31;


$TAG_NOTIFICATION_MY_QUERY_RESPONSE_TEXT		= "Query Response";
$TAG_NOTIFICATION_FOLLOWED_QUERY_RESPONSE_TEXT	= "Query Response";
$TAG_NOTIFICATION_USER_FOLLOW_TEXT				= "User Followed";
$TAG_NOTIFICATION_FRIEND_JOINS_TEXT				= "Friend Joined";
$TAG_NOTIFICATION_LOCAL_SUGGESTERS_TEXT			= "Suggestions";


$DEV_MODE = true;
$LOCAL_MODE = false;

$APP_TOKEN_PROD 				= "%fj38t27v476vj^&^hb";
$APP_TOKEN_DEV 				= "%fj38t27v476vj^&^h";
$APP_TOKEN_LOCAL            = "%fj38t27v476vj^&^";
$APP_TOKEN 				= $DEV_MODE ? ($LOCAL_MODE ? $APP_TOKEN_LOCAL : $APP_TOKEN_DEV)  : $APP_TOKEN_PROD;

$DB_TRUNCATE_CRED_USERNAME = "admin";
$DB_TRUNCATE_CRED_PASSWORD = "admin123";

// Which position the ad to be shown
$AD_POSITION = 2;

$AD_TYPE_IMAGE = 0;
$AD_TYPE_VIDEO = 1;

$RANGE_VALUE_TIP = 100; //8000;
$RANGE_VALUE_TIP_ALL = 8000;
$RANGE_VALUE_QUERY = 50;
$RANGE_VALUE_FOLK = 8000;
$RANGE_VALUE = 50;

$TYPE_SEARCH_NORMAL = 0;
$TYPE_SEARCH_ALL 	= 1;

// PRODUCTION
$SITE_URL 				= "http://lokaso.in/";
$APP_FOLDER 			= "app_panel/";

// DEVELOPMENT
if($DEV_MODE) {
	$SITE_URL 				= "http://targetprogress.in/";
	$APP_FOLDER 			= "lokaso/";	
			
	// LOCALHOST
	if($LOCAL_MODE) {
		$SITE_URL 				= "http://localhost/";
		//$SITE_URL 				= "http://192.168.2.99/";
		$APP_FOLDER 			= "lokaso_web/";	
	}
}

$V1 = "v1/";

$BASE_URL = $SITE_URL.$APP_FOLDER.$V1;

$API_VERIFY 				= $BASE_URL."verify";
$API_USER_YOUTUBE_UPDATE 	= $BASE_URL."verify";



$MAIN_DOMAIN 			= $SITE_URL.$APP_FOLDER;



$VERSION_V1 = "v1/";

$UPLOAD_FOLDER = "upload/";
$UPLOAD_PATH = $MAIN_DOMAIN.$UPLOAD_FOLDER;

$INTEREST_PICS 				= "interest_pics/";
$SUGGESTION_PICS 			= "suggestion_pics/";
$USER_PICS 					= "user_pics/";
$AD_PICS 					= "ad/";
$INTEREST_PIC_PATH 			= $UPLOAD_PATH . $INTEREST_PICS;
$DISCOVERY_PIC_PATH 		= $UPLOAD_PATH . $SUGGESTION_PICS;
$USER_PIC_PATH 				= $UPLOAD_PATH . $USER_PICS;
$AD_PATH 					= $UPLOAD_PATH . $AD_PICS;
$USER_PIC_DEFAULT 			= "1defaultuser.jpg";


$path_to_folder = '/'.$APP_FOLDER.$UPLOAD_FOLDER; // On dev server
$IMAGE_UPLOAD_PATH = $_SERVER['DOCUMENT_ROOT'].$path_to_folder;
	


//TABLES
$TABLE_SUFFIX 				= "lokaso_";

//Query Tables
$TABLE_ASK					= $TABLE_SUFFIX . "ask";
$TABLE_ASK_SPAM 			= $TABLE_SUFFIX . "ask_spam";
$TABLE_ASK_USER_SPAM		= $TABLE_SUFFIX . "ask_user_spam";

//Query Response Tables
$TABLE_ASK_RESPONSE 		= $TABLE_SUFFIX . "ask_response";
$TABLE_ASK_RESPONSE_ACTION 	= $TABLE_SUFFIX . "ask_response_action";
$TABLE_ASK_RESPONSE_COMMENT = $TABLE_SUFFIX . "ask_response_comment";
$TABLE_ASK_RESPONSE_SPAM	= $TABLE_SUFFIX . "ask_response_spam";

//Chat Table
$TABLE_CHAT_ROOM 			= $TABLE_SUFFIX . "chat_room";
$TABLE_CONVERSATION 		= $TABLE_SUFFIX . "conversation";

//Suggestion Tables
$TABLE_SUGGESTION			= $TABLE_SUFFIX . "discovery";
$TABLE_SUGGESTION_COMMENT	= $TABLE_SUFFIX . "discovery_comment";
$TABLE_SUGGESTION_FAV		= $TABLE_SUFFIX . "discovery_fav";
$TABLE_SUGGESTION_SPAM		= $TABLE_SUFFIX . "discovery_spam";
$TABLE_SUGGESTION_SHARE		= $TABLE_SUFFIX . "discovery_share";

//User Tables
$TABLE_USER					= $TABLE_SUFFIX . "user";
$TABLE_USER_ACTION			= $TABLE_SUFFIX . "user_action";
$TABLE_USER_CREDITS			= $TABLE_SUFFIX . "user_credits";
$TABLE_USER_SUGGESTION		= $TABLE_SUFFIX . "user_discovery";
$TABLE_USER_FOLLOW			= $TABLE_SUFFIX . "user_follow";
$TABLE_USER_INTEREST		= $TABLE_SUFFIX . "user_interest";
$TABLE_USER_PIC				= $TABLE_SUFFIX . "user_pic";
$TABLE_USER_QUERY			= $TABLE_SUFFIX . "user_query";
$TABLE_USER_VIDEO			= $TABLE_SUFFIX . "user_video";

// Ads
$TABLE_ADS					= $TABLE_SUFFIX . "ads";
$TABLE_ADS_VIEW				= $TABLE_SUFFIX . "ads_view";

//General Tables
$TABLE_PROFESSION			= $TABLE_SUFFIX . "profession";
$TABLE_QUESTION				= $TABLE_SUFFIX . "question";
$TABLE_ANSWER 				= $TABLE_SUFFIX . "answer";
$TABLE_TYPE					= $TABLE_SUFFIX . "type";
$TABLE_INTEREST				= $TABLE_SUFFIX . "interest";
$TABLE_SETTING				= $TABLE_SUFFIX . "setting";
$TABLE_CREDIT				= $TABLE_SUFFIX . "credit";
$TABLE_GCM					= $TABLE_SUFFIX . "gcm";
$TABLE_NOTIFICATION			= $TABLE_SUFFIX . "notification";
$TABLE_CONTACT_INVITE		= $TABLE_SUFFIX . "contact_invite";
?>