<?php //header.php
//every file should call this file to start off (fill this with useful things!)

session_start();

include_once 'database.php';
include_once 'UserModel.php';

echo <<<_END
<!DOCTYPE html>
<html>
<head>
<title>$appname</title>
		
	<div id="image" style="background: url(Banner.jpg) no-repeat center center fixed;height:196px;background-size:cover" />
	<!-- include jquery and css stylesheet-->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<link rel='stylesheet' href='mystyle.css' type='text/css' />

</head>
<body>
_END;

//set user name for  greeting
$user = 'Guest';
//$model = new UserModel();
//$row = $model->getUserInfo(2);
//$tstuser = mysql_fetch_row($row);


if(isset($_SESSION['user']))
{
	$user = $_SESSION['user'];
	$loggedin = TRUE;
}
else
	$loggedin = FALSE;

echo "<div id ='guest'>
		Welcome, $user &nbsp
	  </div>";

if($loggedin)
{
	echo "<div id='loggdin'>
			<a href='index.php'>Home </a>
			<a href='create.php'>Create Poll</a>
			<a href='logout.php'>Log Out</a>
		  </div>";
}

else
{
	echo "
		<div id='links'>
		
		&nbsp<a href='index.php'>Home</a>&nbsp
		&nbsp<a href='login.php'>Log In</a>&nbsp
		&nbsp<a href='registration.php'>Register</a>&nbsp

		</div>
	";
}
echo "<div id='banner'><br>";

include_once 'login.php';
?>

