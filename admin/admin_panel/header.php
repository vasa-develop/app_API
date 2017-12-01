<?php 
include 'functions.php'; 
if (false) 
{
	header("Location: index.php");
	exit();
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Lokaso</title>
	<link type="text/css" href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link type="text/css" href="../bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
	<link type="text/css" href="../css/jquery.timepicker.css" rel="stylesheet">
	
	<link type="text/css" href="../css/jquery-ui-1.8.21.custom.css" rel="stylesheet">
	<link type="text/css" href="../css/jquery.dataTables.css" rel="stylesheet">
	<link type="text/css" href="../css/jquery.dataTables.css" rel="stylesheet">
	<link type="text/css" href="../css/chosen.min.css" rel="stylesheet">
	<link type="text/css" href="../css/theme.css" rel="stylesheet">
	<link type="text/css" href="../images/icons/css/font-awesome.css" rel="stylesheet">
	
	<!--link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'-->
	<style>
	.display th{text-align:center}
	.display td{text-align:center}
	</style>
	
</head>
<body>

<?php
$email = "admin";
$admin_id = 2;
$_SESSION['login_username']=$email;
$_SESSION['login_userid']=$admin_id;
?>

	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".navbar-inverse-collapse">
					<i class="icon-reorder shaded"></i>
				</a>

			  	<a class="brand" href="index.php">
			  		Lokaso
			  	</a>

				<div class="nav-collapse collapse navbar-inverse-collapse">
					<ul class="nav pull-right">
						<li class="nav-user dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<!--img src="images/user.png" class="nav-avatar" /-->
								Hello <?php echo $_SESSION['login_username'];?>
								<b class="caret"></b>
							</a>
							<ul class="dropdown-menu">
								<li><a href="change_password.php">Change Password</a></li>
								<li><a href="logout.php">Logout</a></li>
							</ul>
						</li>
					</ul>
				</div><!-- /.nav-collapse -->
			</div>
		</div><!-- /navbar-inner -->
	</div><!-- /navbar -->

	<?php
	$query3= "SELECT count(id) as total_pending_brand FROM flauntq_brands where status='0' ";
	$res3= mysqli_query($query3);
	$row3 = mysqli_fetch_array($res3,MYSQL_ASSOC);
	$total_pending_brand=$row3['total_pending_brand'];
	?>
	
	<div class="wrapper">
		<div class="container">
			<div class="row">
				<div class="span3">
					<div class="topbar">
						<ul class="widget widget-menu unstyled">						
							<li><a href="user_detail.php"><i class="menu-icon icon-signout"></i>App User</a></li>
							<li><a href="request_detail.php"><i class="menu-icon icon-signout"></i>User Queries</a></li>
							<li><a href="credit_point.php"><i class="menu-icon icon-signout"></i>Credit Point</a></li>
							<li><a href="discovery_detail.php"><i class="menu-icon icon-signout"></i>Suggestion</a></li>
							<li><a href="ads_list_form.php"><i class="menu-icon icon-signout"></i>Ads</a></li>
							<li><a href="tips_mapping.php?id=1"><i class="menu-icon icon-signout"></i>Tips Mapping</a></li>
							<!--li><a href="question_detail.php"><i class="menu-icon icon-signout"></i>Discovery Question</a></li-->
						</ul>

					</div><!--/.sidebar-->
				</div><!--/.span3-->