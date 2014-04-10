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
	
	//check to see if user input correct login info
	public function checkUserInfo($username, $password)
	{
		$query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
		if (mysql_num_rows(queryMysql($query)) == 0)
			return false;
		else
			return true;
	}
	
	//log in user with sessions
	public function loginUser($username, $password)
	{
		$query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
		$row = mysql_fetch_array(queryMysql($query));
		
		$_SESSION['userid'] = $row['userid'];
		$_SESSION['username'] = $row['username'];
		$_SESSION['name'] = $row['firstname']." ".$row['lastname'];
	}
}

?>