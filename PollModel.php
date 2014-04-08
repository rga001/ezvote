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
		$query = "CALL extreme_voting.GetPollInfo('$pollid')";
		return queryMysql($query);
	}
	
	//return poll choices by id
	public function getPollChoices($pollid)
	{
		$query = "SELECT * FROM poll_choices WHERE poll_id = 44";
		return queryMysql($query);
	}
	
	//create poll and insert into the database
	public function createPoll($poll_title, $description, $user_id, $type, $is_public, $comments_disabled, $anonymous, $start_date, $end_date)
	{
		$query = "INSERT into poll_info(title, description, creator_id, type, public, comments, anonymous, start_date, end_date, create_date)".
				" (select '$poll_title', '$description', $user_id, type_id, $is_public, $comments_disabled, $anonymous, '$start_date', '$end_date', CONCAT(curdate(), ' ', curtime())".
				" from poll_type where type = '$type')";

		queryMysql($query);
	}
	
	//insert multiple choices to the database
	public function insertChoices($choices)
	{
		$poll_id = mysql_insert_id();
		for($i = 0; $i < sizeof($choices); $i++)
		{
			$query = "INSERT INTO poll_choices VALUES($poll_id, '$choices[$i]')";
			queryMysql($query);
		}
	}
}

?>