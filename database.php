<?php

//database login info (change according to your database)
$dbhost = 'localhost';
$dbname = 'extreme_voting';
$dbuser = 'ezvote';
$dbpass = '3zvot3';
$appname = 'EzVote';

mysql_connect($dbhost, $dbuser, $dbpass) or die(mysql_error());
mysql_select_db($dbname) or die(mysql_error());

//function to query MySQL or show error
function queryMysql($query)
{
	$result = mysql_query($query) or die(mysql_error());
	return $result;
}

/*
//ignore the following (used to set up my own database tables):
//function to create table in database
function createTable($name, $query)
{
	queryMysql("CREATE TABLE IF NOT EXISTS $name($query)");
}

//members (email, firstname, lastname, password)
createTable('users',
			'email VARCHAR(254),
			firstname VARCHAR(20),
			lastname VARCHAR(20),
			username VARCHAR(20),
			password VARCHAR(50),
			PRIMARY KEY(email)');
*/
?>
