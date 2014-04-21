<?php

include_once("database.php");

class PollModel{
	//return poll id's sorted by popularity or something
	public function getTopPolls($user_id, $sortby, $page, $asc, $filters){
		$onlyGroup = 'false';
		$onlyVoted = 'false';
		for ($i = 0; $i < count($filters); $i++)
			switch ($filters[$i]){
				case 'closed':
					$where .= ' AND p.end_date <= NOW() '; break;
				case 'voted':
					$onlyVoted = 'true'; break;
				case 'group':
					$onlyGroup = 'true'; break;
			}
		switch ($sortby){
			case 'start':
				$orderby = " ORDER BY start_date "; break;
			case 'end':
				$orderby = " ORDER BY end_date "; break;
			case 'created':
				$orderby = " ORDER BY created_date "; break;
			case 'pop':
				$orderby = " ORDER BY votes "; break;
			default:
				$orderby = " ORDER BY (CASE WHEN date(end_date) <= NOW() THEN 1 ELSE 0 END) DESC, end_date "; break;
		}
		if ($asc == 'true')
			$orderby .= ' ASC';
		else
			$orderby .= ' DESC';

		//$query = "SELECT p.poll_id, COUNT(DISTINCT(v.user_id)) FROM extreme_voting.poll_info p LEFT OUTER JOIN extreme_voting.poll_vote v ON v.poll_id = p.poll_id WHERE (p.public = 1 OR p.creator_id = $user_id) GROUP BY p.poll_id ";
		//echo $query;

		$limit = ($page - 1) * 10;
		$query = "SELECT p.*, COUNT(DISTINCT(v.user_id)) as votes ".
					"FROM extreme_voting.poll_info p ".
					"LEFT OUTER JOIN extreme_voting.poll_vote v ON v.poll_id = p.poll_id ".
					"LEFT OUTER JOIN extreme_voting.group_polls gp ON gp.poll_id = p.poll_id ".
					"LEFT OUTER JOIN extreme_voting.group_members gm ON gm.group_id = gp.group_id ".
					"WHERE (p.public = 1 OR p.creator_id = $user_id) ";
		$query .= $where;
		if ($onlyGroup == 'false')
			$query .= " AND gp.poll_id IS NULL";
		$query .= " GROUP BY p.poll_id ";
		if ($onlyGroup == 'true'){
			if($onlyVoted == 'true'){
				$query .= " HAVING (COUNT(gm.member_id = $user_id) > 0) AND (COUNT(v.user_id = $user_id) > 0)";
			}
			else{
				$query .= " HAVING (COUNT(gm.member_id = $user_id) > 0)";
			}
		}
		$query .= $orderby;
		$query .= " LIMIT $limit, 10";
		//echo $query;

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
	
	//insert or change a user's poll vote
	public function votePoll($poll_id, $user_id, $answer)
	{
		$query = "DELETE FROM poll_vote WHERE poll_id = $poll_id AND user_id = $user_id";
		queryMysql($query);
		
		$query = "INSERT into poll_vote VALUES($poll_id, $user_id, '$answer')";
		queryMysql($query);
	}
	
	//return group name that poll is in
	public function pollGroup($poll_id)
	{
		$query = "SELECT groups.name FROM groups, group_polls " .  
		"WHERE group_polls.poll_id = $poll_id AND group_polls.group_id = groups.group_id";
		$groupname = mysql_fetch_array(queryMysql($query));
		return $groupname['name'];
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
	
	//shows if poll has already been voted on by anyone
	public function noVotesYet($poll_id)
	{
		$query = "SELECT * FROM poll_vote WHERE poll_id=$poll_id";
		$numVotes = mysql_num_rows(queryMysql($query));
		if($numVotes == 0)
			return true;
		else
			return false;
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
	
	//edit a poll
	public function editPoll($poll_id, $poll_title, $description, $is_public, $comments_disabled, $anonymous, $end_date)
	{
		$query = "UPDATE poll_info SET title='$poll_title', description='$description', public=$is_public, comments=$comments_disabled, " .
				"anonymous=$anonymous, end_date='$end_date' WHERE poll_id=$poll_id";
		queryMysql($query);
	}
	
	//insert multiple choices after poll creation to the database
	public function insertChoices($choices, $poll_id)
	{
		if($poll_id == 0)
			$poll_id = mysql_insert_id();
		
		$query = "DELETE FROM poll_choices WHERE poll_id = $poll_id";
		queryMysql($query);
		
		for($i = 0; $i < sizeof($choices); $i++)
		{
			$query = "INSERT INTO poll_choices VALUES($poll_id, '$choices[$i]')";
			queryMysql($query);
		}
		return $poll_id;
	}
	
	//insert poll into group_poll
	public function insertGroupPoll($poll_id, $group_chosen)
	{
		$query = "DELETE FROM group_polls WHERE poll_id = $poll_id";
		queryMysql($query);
		
		$query = "INSERT INTO group_polls (group_id, poll_id) select group_id, $poll_id from groups where name = '$group_chosen'";
		queryMysql($query);
	}
	
	//remove group from group_polls if edited to be public
	public function makePublic($poll_id)
	{
		$query = "DELETE FROM group_polls WHERE poll_id = $poll_id";
		queryMysql($query);
	}
	
	//find out if poll date has ended yet
	public function validPollDate($poll_id)
	{
		$query = "SELECT start_date, end_date FROM poll_info WHERE poll_id = $poll_id";
		$poll = mysql_fetch_array(queryMysql($query));
		$pollEndDate = substr($poll['end_date'], 0, -9);
		if(new DateTime() > new DateTime($pollEndDate))
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
	
	//add comment to database
	public function insertComment($poll_id, $user_id, $comment)
	{
		$query = "INSERT INTO poll_comments VALUES (NULL, $user_id, $poll_id, '$comment', 0, 0)";
		queryMysql($query);
	}
	
	//return comments for a poll
	public function allComments($poll_id)
	{
		$query = "SELECT poll_comments.comment, users.firstname, users.lastname, users.username" .
				 " FROM poll_comments, users WHERE poll_comments.poll_id = $poll_id AND poll_comments.user_id = users.userid";
		return queryMysql($query);
	}
	
	//get poll type
	public function getPollType($type_id)
	{
		$query = "SELECT type FROM extreme_voting.poll_type WHERE type_id = $type_id";
		return queryMysql($query);
	}
}

?>