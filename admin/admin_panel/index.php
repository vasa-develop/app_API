<?php
include 'functions.php'; 
if (true) 
{
	header("Location: user_detail.php");
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
	<link type="text/css" href="../css/theme.css" rel="stylesheet">
	<link type="text/css" href="../images/icons/css/font-awesome.css" rel="stylesheet">
	<link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
</head>
<body>

	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".navbar-inverse-collapse">
					<i class="icon-reorder shaded"></i>
				</a>

			  	<a class="brand" href="index.html">
			  		Lokaso
			  	</a>
			</div>
		</div><!-- /navbar-inner -->
	</div><!-- /navbar -->

<?php
if(isset($_GET['failed'])&&$_GET['failed']==1)
{
	$login_error_msg="Your password or username is wrong.";
}
else if(isset($_GET['failed'])&&$_GET['failed']==2)
{
	$login_error_msg="Please enter a username and/or password.";
}
else
{
	$login_error_msg='';
}
?>
<?php
	
	
?>
	<div class="wrapper">
		<div class="container">
			<div class="row">
				<div class="module module-login span4 offset4">
					<form class="form-vertical"  method="post"  action="login.php" >
						<div class="module-head">
							<h3>Log In</h3>
							
						</div>
						<div class="module-body">
							<div class="control-group">
								<div class="controls row-fluid">
									<input class="span12" type="text"  id="email" name="email"  placeholder="Username">
								</div>
							</div>
							<div class="control-group">
								<div class="controls row-fluid">
									<input class="span12" type="password" id="inputPassword" name="password" placeholder="Password">
								</div>
							</div>
							<br/>
							<span ><?php echo $login_error_msg;?></span>
						</div>
						<div class="module-foot">
							<div class="control-group">
								<div class="controls clearfix">
									<button type="submit" class="btn btn-inverse pull-right"  value="login"  name="login">Login</button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div><!--/.wrapper-->

	<div class="footer">
		<div class="container">
			<b class="copyright">&copy; <?php echo date('Y');?> Lokaso </b> All rights reserved.
		</div>
	</div>
</body>