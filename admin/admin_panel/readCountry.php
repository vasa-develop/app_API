<?php 
require "functions.php";
?>
<ul>
<?php
$query1= "SELECT * FROM user where ";
$res1= mysql_query($query1);
while($row1 = mysql_fetch_array($res1,MYSQL_ASSOC))
{	
	?>
	<li><?php echo $row1['name'];?></li>
	<?php
}
?>
</ul>
