<?php //header.php
//every file should call this file to start off (fill this with useful things!)

session_start();

include_once 'database.php';
include_once 'UserModel.php';
include_once 'PollModel.php';

$userModel = new UserModel();

echo <<<_END
<!DOCTYPE html>
<html>
<head>
<title>$appname</title>

<!--<div id="image" style="background: url(Banner.jpg) no-repeat center center fixed; height:196px; background-size:cover" />-->

<!-- include jquery and css stylesheet-->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<link rel='stylesheet' href='mystyle.css' type='text/css' />

</head>
<body>
_END;

//menu if user is logged in
if($userModel->userIsLoggedIn())
{
	$name = $_SESSION['name'];
	
	echo <<<_END
	<div id='guest'>Welcome, $name</div>
	<div id='links'>
	<a class='linky' href='index.php'>Home </a>
	<a class='linky' href='create.php'>Create Poll</a>
	<a class='linky' href='logout.php'>Log Out</a>
	</div>
_END;
}
//menu if user is not logged in
else
{
	echo <<<_END
	<div id='guest'>Welcome, Guest</div>
	<div id='links'>
	&nbsp<a class='linky' href='index.php'>Home</a>
	<a class='linky' href='registration.php'>Register</a>
	</div>
_END;
}

include_once 'login.php';
echo "<div id='banner'>";
echo "</div>";
?>

