<?php

include_once 'header.php';
include_once 'PollModel.php';

//grab poll info from poll_id
$poll_id = $_GET["pollid"];
$pollModel = new PollModel();
$poll_info = $pollModel->getPollInfo($poll_id);
$poll = mysql_fetch_array($poll_info);

echo "<h1>".$poll['title']."</h1>";
echo $poll['description']."<br>";

//grab choices from poll_id
//$query = "SELECT * FROM poll_info";
//queryMysql($query);
//$choice_info = $pollModel->getPollChoices($poll_id);

/*while($choice = mysql_fetch_array($choice_info))
{
	echo $choice['choice']."<br>";
}*/
