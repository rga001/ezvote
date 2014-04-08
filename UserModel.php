<?php

include_once("database.php");

class UserModel{
	//return user information (username, email, etc)
	public function getUserInfo($userid){
		$query = 'SELECT email, firstname, lastname, username FROM extreme_voting.users WHERE userid = '.$userid;
		return queryMysql($query);
	}
	//return user poll information (past poll info) 
	
	//register user to database
	public function registerUser($email, $firstname, $lastname, $username, $password)
	{
		$query = "INSERT INTO users VALUES(NULL, '$email', '$firstname', '$lastname', '$username', '$password')";
		queryMysql($query);
	}
}

?>