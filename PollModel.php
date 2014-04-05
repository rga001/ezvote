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
}

?>