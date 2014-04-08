<?php

include_once("database.php");

class UserModel{
	//return user information (username, email, etc)
	public function getUserInfo($userid){
		$query = 'SELECT email, firstname, lastname, username FROM extreme_voting.users WHERE userid = '.$userid;
		return queryMysql($query);
	}
	//return user poll information (past poll info) 
}

?>