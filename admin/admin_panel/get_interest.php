<?php 
require "functions.php";
?>
<option value="">All</option>
<?php
$query1= "SELECT * FROM lokaso_interest  ";
$res1= mysql_query($query1);
while($row1 = mysql_fetch_array($res1,MYSQL_ASSOC))
{	
	?>
	<option value="<?php echo $row1['id'];?>"><?php echo $row1['name'];?></option>
	<?php
}
?>
