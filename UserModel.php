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
		$_SESSION['username'] = htmlspecialchars($row['username']);
		$_SESSION['name'] = htmlspecialchars($row['firstname']." ".$row['lastname']);
	}
	
	//check if user is logged in
	public function userIsLoggedIn()
	{
		if(isset($_SESSION['userid']))
			return true;
		else
			return false;
	}
	
	//return groups that user is in
	public function userGroups($userid)
	{
		$query = "select groups.name from groups, group_members where groups.group_id = group_members.group_id AND group_members.member_id = $userid";
		return queryMysql($query);
	}
	
	//find out if user is allowed to see poll
	public function userPermissionPoll($poll_id, $user_id)
	{
		//check if poll is public poll
		$query = "SELECT public FROM poll_info WHERE poll_id = $poll_id";
		$poll_info = mysql_fetch_array(queryMysql($query));
		if($poll_info['public'] == 1)	//return true if public
			return true;
	
	
		//check if user is a member of the group the poll is in
		$query = "SELECT * FROM group_members, group_polls WHERE group_polls.poll_id = $poll_id " .
		"AND group_members.group_id = group_polls.group_id AND group_members.member_id = $user_id";

		if(mysql_num_rows(queryMysql($query)) == 0)
			return false;
		else
			return true;		
	}
}

?>