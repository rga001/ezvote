<?php

include_once("database.php");

class PollModel{
	//return poll id's sorted by popularity or something
	public function getTopPolls($user_id, $sort_by, $page, $asc){
		switch ($sort_by){
			case 'start':
				$orderby = ' ORDER BY start_date ';
			case 'end':
				$orderby = ' ORDER BY end_date ';
			case 'created':
				$orderby = ' ORDER BY created_date ';
			case 'pop':
				$orderby = ' ORDER BY votes ';
			default:
				$orderby = ' ORDER BY (CASE WHEN date(end_date) <= NOW() THEN 1 ELSE 0 END) DESC, end_date ';
		}
		if ($asc == 'true')
			$orderby .= ' ASC';
		else
			$orderby .= ' DESC';
		$query = "SELECT p.poll_id, COUNT(DISTINCT(v.user_id)) FROM extreme_voting.poll_info p LEFT OUTER JOIN extreme_voting.poll_vote v ON v.poll_id = p.poll_id WHERE (p.public = 1 OR p.creator_id = $user_id) GROUP BY p.poll_id ";
		echo $query;
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
		$query = "SELECT * FROM poll_choices WHERE poll_id = $pollid";
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
	
	//find out if poll date has ended yet
	public function validPollDate($poll_id)
	{
		$query = "SELECT start_date, end_date FROM poll_info WHERE poll_id = $poll_id";
		$poll = mysql_fetch_array(queryMysql($query));
		if(new DateTime() > new DateTime($end_date))
			return false;
		else
			return true;
	}
	
	//return everyone's real name that voted a certain choice on a poll
	public function choiceVotersNames($poll_id, $choice)
	{
		$voter_names = "";
		$query = "SELECT users.firstname, users.lastname FROM poll_vote, users ". 
		"WHERE users.userid = poll_vote.user_id AND poll_vote.poll_id = $poll_id AND poll_vote.answer = '$choice'";
		$voter_info = queryMysql($query);
		
		//grab names
		while($voters = mysql_fetch_array($voter_info))
			$voter_names .= $voters['firstname'] . " " . $voters['lastname'] . ", ";
		$voter_names = substr($voter_names, 0, -2);
		
		//if there are no votes for a choice
		if($voter_names == "")
			$voter_names = "No votes yet";
		
		return $voter_names;
	}
	
	//return everyone's username that voted a certain choice on a poll
	public function choiceVotersUsernames($poll_id, $choice)
	{
		$voter_names = "";
		$query = "SELECT users.username FROM poll_vote, users ".
				"WHERE users.userid = poll_vote.user_id AND poll_vote.poll_id = $poll_id AND poll_vote.answer = '$choice'";
		$voter_info = queryMysql($query);

		//grab names
		while($voters = mysql_fetch_array($voter_info))
			$voter_names .= $voters['username'] . ", ";
		$voter_names = substr($voter_names, 0, -2);

		//if there are no votes for a choice
		if($voter_names == "")
			$voter_names = "No votes yet";

		return $voter_names;
	}
}

?>