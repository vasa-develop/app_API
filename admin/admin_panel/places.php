<?php


//include 'header.php';
$link = mysqli_connect("localhost","phpmyadmin","password","phpmyadmin");


include 'GooglePlaces.php';
include 'GooglePlacesClient.php';






$google_places = new joshtronic\GooglePlaces('AIzaSyBwwg7rqag9qAlzrJoIEgngOVV0d9Zb8Kw');

/*$q = mysqli_query($link , "SELECT * from `lokaso_discovery`");
$r = mysqli_fetch_assoc($q);
$num = mysqli_num_rows($r['id']);
echo $num;*/
for($j=0;$j<10;$j++){

	$query = mysqli_query($link , "SELECT * from `lokaso_discovery` WHERE `id`=".$j."");
$loc = mysqli_fetch_assoc($query);

$string = $loc['location'];
/* Use tab and newline as tokenizing characters as well  */
$tok = strtok($string, " ");
$l="";
while ($tok !== false) {
	
    $l = $l.$tok;
    $tok = strtok(" ");
    
}
//echo $l;

$string1  = $l;


$fstring = strstr($string1, "'", true); // As of PHP 5.3.0
 // prints name


$google_places->location = array($loc['lat'],$loc['lng']);
$google_places->radius   = 200;
if(strlen($fstring)==0){
	$google_places->input    = $l;	
}
else{
	$google_places->input    = $fstring;	
}


$results                 = $google_places->autocomplete();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
$placeId =  json_encode($results["predictions"][0]["place_id"]);
$name =  json_encode($results["predictions"][0]["structured_formatting"]["main_text"]);
echo $name;
if($name!=null){
	mysqli_query($link , "update `lokaso_discovery` set `approved` = 1 , `placeId`= ".$placeId." WHERE `id`=".$j."");
}
}









//echo json_encode($results["predictions"]);

/*$a=array("");



for($i=0;$i<count($results);$i++){
	$name =  json_encode($results["predictions"][$i]["structured_formatting"]["main_text"]);
//echo $placeId;
	array_push($a,$name);
	
}

print_r($a);*/

//echo $loc['location'];


/*$google_places->types    = array( "accounting","airport,","amusement_park","aquarium","art_gallery","atm","bakery","bank","bar","beauty_salon","bicycle_store","book_store","bowling_alley","bus_station","cafe","campground","car_dealer","car_rental","car_repair","car_wash","casino","cemetery","church","city_hall","clothing_store","convenience_store","courthouse","dentist","department_store","doctor","electrician","electronics_store","embassy","fire_station","florist","funeral_home","furniture_store","gas_station","gym","hair_care","hardware_store","hindu_temple","home_goods_store","hospital","insurance_agency","jewelry_store","laundry","lawyer","library","liquor_store","local_government_office","locksmith","lodging","meal_delivery","meal_takeaway","mosque","movie_rental","movie_theater","moving_company","museum","night_club","painter","park","parking","pet_store","pharmacy","physiotherapist","plumber","police","post_office","real_estate_agency","restaurant","roofing_contractor","rv_park","school","shoe_store","shopping_mall","spa","stadium","storage","store","subway_station","synagogue","taxi_stand","train_station","transit_station","travel_agency","university","veterinary_care","zoo");*/

?>

