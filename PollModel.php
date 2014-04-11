<?php

include_once("database.php");

class PollModel{
	//return poll id's sorted by popularity or something
	public function getTopPolls(){
		$query = 'SELECT * FROM extreme_voting.poll_info WHERE DATE(end_date) >= CURDATE() ORDER BY end_date ASC LIMIT 10';
		return queryMysql($query);
	}
	
	//return return poll info by id
	public function getPollInfo($pollid){	
		$query = "SELECT * FROM poll_info WHERE poll_id = $pollid"; //"CALL extreme_voting.GetPollInfo('$pollid')";
		return queryMysql($query);
	}
	
	//return poll choices by id
	public function getPollChoices($pollid)
	{
		$query = "SELECT * FROM poll_choices WHERE poll_id = 44";
		return queryMysql($query);
	}
	
	//vote on poll
	public function votePoll($poll_id, $user_id, $answer)
	{
		$query = "INSERT into poll_vote VALUES($poll_id, $user_id, '$answer')";
		queryMysql($query);
	}
	
	//shows if user has already voted on poll
	public function votedAlready($poll_id, $user_id)
	{
		$query = "SELECT * FROM poll_vote WHERE poll_id=$poll_id AND user_id=$user_id";
		if (mysql_num_rows(queryMysql($query)) == 0)
			return false;
		else
			return true;
	}
	
	//retrieves number of votes on a certain choice for a poll
	public function voteTally($poll_id, $choice)
	{
		$query = "SELECT * FROM poll_vote WHERE poll_id=$poll_id AND answer='$choice'";
		return mysql_num_rows(queryMysql($query));
	}
	
	//create poll and insert into the database
	public function createPoll($poll_title, $description, $user_id, $type, $is_public, $comments_disabled, $anonymous, $start_date, $end_date)
	{
		$query = "INSERT into poll_info(title, description, creator_id, type, public, comments, anonymous, start_date, end_date, create_date)".
				" (select '$poll_title', '$description', $user_id, type_id, $is_public, $comments_disabled, $anonymous, '$start_date', '$end_date', CONCAT(curdate(), ' ', curtime())".
				" from poll_type where type = '$type')";

		queryMysql($query);
	}
	
	//insert multiple choices after poll creation to the database
	public function insertChoices($choices)
	{
		$poll_id = mysql_insert_id();
		for($i = 0; $i < sizeof($choices); $i++)
		{
			$query = "INSERT INTO poll_choices VALUES($poll_id, '$choices[$i]')";
			queryMysql($query);
		}
		return $poll_id;
	}
	
	//insert poll_id into group_poll
	public function insertGroupPoll($poll_id, $group_chosen)
	{
		$query = "INSERT INTO group_polls (group_id, poll_id) select group_id, $poll_id from groups where name = '$group_chosen'";
		queryMysql($query);
	}
}

?>