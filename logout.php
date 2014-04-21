<?php //logout.php
include_once 'header.php';

//log user out of session
if (isset($_SESSION['userid']))
{
    $_SESSION=array();

	if (session_id() != "" || isset($_COOKIE[session_name()]))
		setcookie(session_name(), '', time()-2592000, '/');

	session_destroy();
}

//redirect to home page
echo '<meta http-equiv="REFRESH" content="0; url=index.php">';
?>