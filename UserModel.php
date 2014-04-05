<?php

include_once("database.php");

class UserModel{
	//return user information (username, email, etc)
	public function getUserInfo($userid){
		$query = 'CALL extreme_voting.GetUserInfo(2)';
		return queryMysql($query);
	}
	//return user poll information (past poll info) 
}

?>