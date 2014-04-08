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
		
<link rel="stylesheet" type="text/css" href="mystyle.css">
<div id="image" style="background: url(Banner.jpg) no-repeat center center fixed;height:208px;background-size:cover" />		
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

//Greeting
echo "Welcome, $user ";

//show menu
if($loggedin)
{
	echo "| <a href='index.php'>Home</a> ";
	echo " <a href='create.php'>Create Poll</a>";
	echo " <a href='logout.php'>Log Out</a>";
}
else
{
	echo "| <a href='index.php'>Home</a> ";
	echo " <a href='login.php'>Log In</a> ";
	echo " <a href='registration.php'>Register</a>";
}
?>

