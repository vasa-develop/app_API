<!DOCTYPE html>
<html>
  

<head>
<style>
.dropbtn {
    background-color: #4CAF50;
    color: white;
    padding: 16px;
    font-size: 16px;
    border: none;
    cursor: pointer;
}

.dropbtn:hover, .dropbtn:focus {
    background-color: #3e8e41;
}

.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: #f9f9f9;
    min-width: 160px;
    overflow: auto;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1;
}

.dropdown-content a {
    color: black;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
}

.dropdown a:hover {background-color: #f1f1f1}

.show {display:block;}
</style>



  <?php include 'header.php';
$link = mysqli_connect("localhost","phpmyadmin","password","phpmyadmin");

 ?>
    <title>Place searches</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 100%;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
    </style>
    <script>
      // This example requires the Places library. Include the libraries=places
      // parameter when you first load the API. For example:
      // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">


/* When the user clicks on the button, 
toggle between hiding and showing the dropdown content */
function myFunction() {
    document.getElementById("myDropdown").classList.toggle("show");
}

// Close the dropdown if the user clicks outside of it
window.onclick = function(event) {
  if (!event.target.matches('.dropbtn')) {

    var dropdowns = document.getElementsByClassName("dropdown-content");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
}




      var map;
      var infowindow;

      function initMap() {
        var pyrmont = {lat: lat1, lng: lng1};

        map = new google.maps.Map(document.getElementById('map'), {
          center: pyrmont,
          zoom: 15
        });

        infowindow = new google.maps.InfoWindow();
        var service = new google.maps.places.PlacesService(map);
        service.nearbySearch({
          location: pyrmont,
          radius: 500,
          type: ['store']
        }, callback);
      }

      function callback(results, status) {
        /*var x = document.createElement("form");
        x.setAttribute("name", "tip");
        x.setAttribute("action", "tips_mapping.php");
        x.setAttribute("method", "get");
        document.body.appendChild(x);

        var x = document.createElement("INPUT");
        x.setAttribute("type", "submit");
        x.setAttribute("name","placeId");
        x.setAttribute("value","placeId");
        x.setAttribute("id","final");
        document.body.appendChild(x);*/

        if (status === google.maps.places.PlacesServiceStatus.OK) {
          for (var i = 0; i < results.length; i++) {
            console.log(results[i]);
            

            /*var x = document.createElement("INPUT");
        x.setAttribute("type", "checkbox");
        x.setAttribute("value", results[i].place_id);
        x.setAttribute("name", results[i].name);
        x.setAttribute("onclick","setId(this)");
        x.setAttribute("form","tip");

        document.body.appendChild(x);

        var x = document.createElement("p");
        x.setAttribute("id", results[i].place_id);
        document.body.appendChild(x);
        document.getElementById(results[i].place_id).innerHTML = "<strong>Name:</strong> "+results[i].name+"   <strong>PlaceId:</strong> "+results[i].place_id+"   <strong>Rating:</strong> "+results[i].rating+"   <strong>Vicinity:</strong> "+results[i].vicinity;*/


        



            
            /*console.log(obj.name);
            console.log(obj.place_id);*/
          }
        }
      }

      function processAjaxData(response, urlPath){
     
 }



      function setId(cb) {


        
        document.getElementById(cb.value).style.color = "#00ff00";
        
    window.location = "http://localhost/app_panel/admin/admin_panel/tips_mapping.php?placeId="+cb.value+"&placeName="+cb.name+"&id=<?php echo($_GET['id']); ?>";
    //fire(cb.value);



        

      }
      function createMarker(place) {
        var placeLoc = place.geometry.location;
        var marker = new google.maps.Marker({
          map: map,
          position: place.geometry.location
        });

        google.maps.event.addListener(marker, 'click', function() {
          infowindow.setContent(place.name);
          infowindow.open(map, this);
        });
      }
    </script>
  </head>



  <body>
<div id="map"></div>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBwwg7rqag9qAlzrJoIEgngOVV0d9Zb8Kw&libraries=places&callback=initMap" async defer></script>


    <?php

    if(isset($_GET['placeId'])){
      
        if(!isset($_GET['id'])){
        mysqli_query($link , "update `lokaso_discovery` set `placeId`='".$_GET['placeId']."' where `id`= 1"); 
        ?>
        <script type="text/javascript">
          //alert("The location is now set to placeName: "+ "<?php echo $_GET['placeName']; ?>"+". Press NEXT SUGGESTION button to proceed.");
          document.getElementById("new_location") = <?php echo $_GET['placeName']; ?>;
          document.getElementById("<?php echo $_GET['placeId']; ?>").style.color = "#00ff00";
        </script>
        <?php
      }
      else{
      	$c = $_GET['id']-1;
        mysqli_query($link , "update `lokaso_discovery` set `placeId`='".$_GET['placeId']."' where `id`=".$c." "); 
        ?>
        <script type="text/javascript">
          //alert("The location is now set to placeName: "+ "<?php echo $_GET['placeName']; ?>"+". Press NEXT SUGGESTION button to proceed.");
          document.getElementById("new_location") = "<?php echo $_GET['placeName']; ?>";
          document.getElementById("<?php echo $_GET['placeId']; ?>").style.color = "#00ff00";
        </script>
        <?php
      }
        
    }
          if(!isset($_GET['id'])){
          $query = mysqli_query($link , "SELECT * from `lokaso_discovery` WHERE `id`= 1 ");
          
        }
        else {
          $query = mysqli_query($link , "SELECT * from `lokaso_discovery` WHERE `id`=".$_GET['id']."");
          
        }
          $result = mysqli_fetch_assoc($query);
?>
            <script type="text/javascript">
              var lat1 = <?php echo $result['lat']; ?>;
              var lng1 = <?php echo $result['lng']; ?>;
            </script>
    
            
<?php
    ?>
    



<table cellspacing="10">
  <tr>
    <th><?php echo("<strong>".$result['id']."</strong><br>"); ?>&nbsp;&nbsp;
          <?php echo("<strong>Name: </strong><br>".$result['location']."<br>"); ?></th>
    <th><center>CATEGORIES</center><br>
      <a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=night_club">night_club </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=restaurant">restaurant </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=cafe">cafe </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=bar">bar </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=beach">beach </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=museum">museum </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=airport">airport</a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=amusement_park">amusement_park</a> |  
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=aquarium">aquarium </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=art_gallery">art_gallery </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=bakery">bakery </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=beauty_salon">beauty_salon</a> |  
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=bicycle_store">bicycle_store </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=book_store">book_store </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=bowling_alley">bowling_alley </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=campground">campground </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=cemetery">cemetery </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=church">church </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=city_hall">city_hall </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=clothing_store">clothing_store</a> |  
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=convenience_store">convenience_store </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=courthouse">courthouse </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=department_store">department_store </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=florist">florist </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=funeral_home">funeral_home</a> |  
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=furniture_store">furniture_store </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=gym">gym </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=hair_care">hair_care</a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=hindu_temple">hindu_temple </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=jewelry_store">jewelry_store </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=library">library </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=liquor_store">liquor_store </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=lodging">lodging </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=meal_delivery">meal_delivery </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=meal_takeaway">meal_takeaway </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=mosque">mosque </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=movie_theater">movie_theater </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=painter">painter </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=park">park </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=parking">parking </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=pet_store">pet_store </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=school">school </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=shoe_store">shoe_store </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=shopping_mall">shopping_mall </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=spa">spa </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=stadium">stadium </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=storage">storage </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=store">store </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=synagogue">synagogue </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=taxi_stand">taxi_stand </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=train_station">train_station </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=transit_station">transit_station </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=travel_agency">travel_agency </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=university">university </a> | 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=zoo">zoo </a></th>

  </tr>
  <tr>
    <td><strong>Image: </strong><br>
          <img src="../../upload/suggestion_pics/<?php echo($result['image']); ?> " style='width:200px;height:200px;' alt='image not available'/>
          <?php echo("<br><strong>Suggestion: </strong><br>".$result['suggestion']."<br>");?>
          <br>    </td>
          <td>

            <center>

            <?php
            echo "POSSIBLE LOCATIONS<br>";
            if(isset($_GET['placeId'])){
              echo "<strong><h4>The location was now set to ".$_GET['placeName']."</h4></strong>";
            }

            ?>
            
<?php
if($_GET['type']){
include 'GooglePlaces.php';
include 'GooglePlacesClient.php';
$query = mysqli_query($link , "SELECT * from `lokaso_discovery` WHERE `id`=".$_GET['id']."");
$loc = mysqli_fetch_assoc($query);
//echo $loc['lat']."  ".$loc['lng'];
$google_places = new joshtronic\GooglePlaces('AIzaSyBwwg7rqag9qAlzrJoIEgngOVV0d9Zb8Kw');
$google_places->location = array($loc['lat'],$loc['lng']);
$google_places->radius   = 100;
$google_places->types    = array($_GET['type']);
$res                 = $google_places->nearbySearch();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
/*$name =  json_encode($res["results"][0]["name"]);
echo "<a href=tips_mapping.php?id=".$_GET['id']."&placeId=".$res["results"][0]["place_id"]."&placeName=".$res["results"][0]["name"].">".$name."</a><br>"; 
*/
for( $i=0;$i<count($res);$i++){
  ?>
<!-- <script type="text/javascript">
  var x = document.createElement("INPUT");
        x.setAttribute("type", "checkbox");
        x.setAttribute("value", <?php $res["results"][$i]["place_id"] ?>);
        x.setAttribute("name", <?php $res["results"][$i]["name"] ?>);
        x.setAttribute("onclick","setId(this)");
        x.setAttribute("form","tip");

        document.body.appendChild(x);
</script> -->

  <?php
  $c_id = $_GET['id'] + 1;
    $name =  json_encode($res["results"][$i]["name"]);
echo "<a href=tips_mapping.php?id=".$c_id."&placeId=".$res["results"][$i]["place_id"]."&placeName=".$res["results"][$i]["name"].">".$name."</a><br>"; 
}
/*$i=0;
for( $i=0;$i<count($res);$i++){
    $name =  json_encode($res["results"][$i]["name"]);
echo $name; 
} */
}

else{
    include 'GooglePlaces.php';
include 'GooglePlacesClient.php';
$query = mysqli_query($link , "SELECT * from `lokaso_discovery` WHERE `id`=".$_GET['id']."");
$loc = mysqli_fetch_assoc($query);
//echo $loc['lat']."  ".$loc['lng'];
$google_places = new joshtronic\GooglePlaces('AIzaSyBwwg7rqag9qAlzrJoIEgngOVV0d9Zb8Kw');
$google_places->location = array($loc['lat'],$loc['lng']);
$google_places->radius   = 100;
/*$google_places->types    =  array( "accounting","airport,","beach","amusement_park","aquarium","art_gallery","atm","bakery","bank","bar","beauty_salon","bicycle_store","book_store","bowling_alley","bus_station","cafe","campground","car_dealer","car_rental","car_repair","car_wash","casino","cemetery","church","city_hall","clothing_store","convenience_store","courthouse","dentist","department_store","doctor","electrician","electronics_store","embassy","fire_station","florist","funeral_home","furniture_store","gas_station","gym","hair_care","hardware_store","hindu_temple","home_goods_store","hospital","insurance_agency","jewelry_store","laundry","lawyer","library","liquor_store","local_government_office","locksmith","lodging","meal_delivery","meal_takeaway","mosque","movie_rental","movie_theater","moving_company","museum","night_club","painter","park","parking","pet_store","pharmacy","physiotherapist","plumber","police","post_office","real_estate_agency","restaurant","roofing_contractor","rv_park","school","shoe_store","shopping_mall","spa","stadium","storage","store","subway_station","synagogue","taxi_stand","train_station","transit_station","travel_agency","university","veterinary_care","zoo");*/
$res                 = $google_places->nearbySearch();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
//echo $name; 

$i=0;
?>
<!-- <script type="text/javascript">
  alert("<?php echo(count($res)); ?>");
</script> -->
<?php
//echo("POSSIBLE LOCATIONS:"."<br>");
for( $i=0;$i<count($res);$i++){
  ?>
<!-- <script type="text/javascript">
  var x = document.createElement("INPUT");
        x.setAttribute("type", "checkbox");
        x.setAttribute("value", <?php $res["results"][$i]["place_id"] ?>);
        x.setAttribute("name", <?php $res["results"][$i]["name"] ?>);
        x.setAttribute("onclick","setId(this)");
        x.setAttribute("form","tip");

        document.body.appendChild(x);
</script> -->

  <?php
  $c_id = $_GET['id'] + 1;
    $name =  json_encode($res["results"][$i]["name"]);
echo "<a href=tips_mapping.php?id=".$c_id."&placeId=".$res["results"][$i]["place_id"]."&placeName=".$res["results"][$i]["name"].">".$name."</a><br>"; 
}
   
}

?>
<br><br>
<strong><font color="#00ff00">BEST SUGESSION</font><br>
<?php

if($_GET['id']){
	$google_places = new joshtronic\GooglePlaces('AIzaSyBwwg7rqag9qAlzrJoIEgngOVV0d9Zb8Kw');

$query = mysqli_query($link , "SELECT * from `lokaso_discovery` WHERE `id`=".$_GET['id']."");
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
$google_places->radius   = 500;
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

$c_id = $_GET['id'] + 1;


echo "<a href=tips_mapping.php?id=".$c_id."&placeId=".$placeId."&placeName=".$name.">".$name."</a><br>";

}

?>
</center>
<br><br>
<center>



<?php

          if(!isset($_GET['id'])){
        ?>
        
        <strong><a href="tips_mapping.php?id=2">NEXT SUGGESTION</a></strong>
        <?php
        }
        else{
          $count = $_GET['id']+1; 
        ?>  
        
        

        <strong><a href="tips_mapping.php?id=<?php echo($count); ?>">NEXT SUGGESTION</a></strong>
        <?php
        }
        ?>
</center>


          </td>
  </tr>
</table>

    <?php

          
         
          
          

?>

<p id="new_location"></p>

<!-- DROPDOWN LIST -->
<!-- <div class="dropdown">
<button onclick="myFunction()" class="dropbtn">Dropdown</button>
  <div id="myDropdown" class="dropdown-content">
    <a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=accounting">accounting</a> 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=airport">airport</a> 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=amusement_park">amusement_park</a> 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=aquarium">aquarium </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=art_gallery">art_gallery </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=atm">atm </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=bakery">bakery </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=bank">bank </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=bar">bar </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=beauty_salon">beauty_salon</a> 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=bicycle_store">bicycle_store </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=book_store">book_store </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=bowling_alley">bowling_alley </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=bus_station">bus_station </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=cafe">cafe </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=campground">campground </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=car_dealer">car_dealer </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=car_rental">car_rental </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=car_repair">car_repair </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=car_wash">car_wash </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=casino">casino </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=cemetery">cemetery </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=church">church </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=city_hall">city_hall </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=clothing_store">clothing_store</a> 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=convenience_store">convenience_store </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=courthouse">courthouse </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=dentist">dentist </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=department_store">department_store </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=doctor">doctor </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=electrician">electrician</a> 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=electronics_store">electronics_store</a> 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=embassy">embassy </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=fire_station">fire_station </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=florist">florist </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=funeral_home">funeral_home</a> 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=furniture_store">furniture_store </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=gas_station">gas_station </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=gym">gym </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=hair_care">hair_care</a> 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=hardware_store">hardware_store</a> 
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=hindu_temple">hindu_temple </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=home_goods_store">home_goods_store </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=hospital">hospital </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=insurance_agency">insurance_agency </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=jewelry_store">jewelry_store </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=laundry">laundry </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=lawyer">lawyer </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=library">library </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=liquor_store">liquor_store </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=local_government_office">local_government_office </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=locksmith">locksmith </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=lodging">lodging </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=meal_delivery">meal_delivery </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=meal_takeaway">meal_takeaway </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=mosque">mosque </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=movie_rental">movie_rental </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=movie_theater">movie_theater </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=moving_company">moving_company </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=museum">museum </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=night_club">night_club </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=painter">painter </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=park">park </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=parking">parking </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=pet_store">pet_store </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=pharmacy">pharmacy </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=physiotherapist">physiotherapist </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=plumber">plumber </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=police">police </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=post_office">post_office </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=real_estate_agency">real_estate_agency </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=restaurant">restaurant </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=roofing_contractor">roofing_contractor </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=rv_park">rv_park </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=school">school </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=shoe_store">shoe_store </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=shopping_mall">shopping_mall </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=spa">spa </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=stadium">stadium </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=storage">storage </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=store">store </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=subway_station">subway_station </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=synagogue">synagogue </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=taxi_stand">taxi_stand </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=train_station">train_station </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=transit_station">transit_station </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=travel_agency">travel_agency </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=university">university </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=veterinary_care">veterinary_care </a>
<a href = "tips_mapping.php?id=<?php echo($_GET['id']) ?>&type=zoo">zoo </a>

  </div>
</div> -->
<br>

   


  </body>
</html>