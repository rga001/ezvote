<?php
/*
 * need to do:
 * can't vote after end date
 * see who voted for who unless anonymous
 * implement public/private
 * add comments unless comments disabled
 * change vote after voting
 */
include_once 'header.php';

//grab poll info and choices from poll_id
$poll_id = $_GET["pollid"];
$pollModel = new PollModel();
$poll_info = $pollModel->getPollInfo($poll_id);
$choice_info = $pollModel->getPollChoices($poll_id);
$poll = mysql_fetch_array($poll_info);

$userModel = new UserModel();
$user_id = $_SESSION['userid'];

//set variables
$error = $vote = "";

//form validation
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if(isset($_POST['choice']))
	{
		$vote = $_POST['choice'];
		
		//input vote to database
		if(!($pollModel->votedAlready($poll_id, $user_id)))
			$pollModel->votePoll($poll_id, $user_id, $vote);
	}
	else 
		$error = "ERROR: Please select a choice.<br>";
}

//poll title and description
echo "<h1>".htmlspecialchars($poll['title'])."</h1>";
if($poll['description'] != "")
	echo htmlspecialchars($poll['description'])."<br><br>";
echo "<span class='error'>$error</span>";

//view results if already voted or if not logged in
if(!($userModel->userIsLoggedIn()) || $pollModel->votedAlready($poll_id, $user_id))
{
	echo "<table><tbody>";
	echo "<tr><td>Choices</td><td>Votes</td></tr>";
	while($choice_row = mysql_fetch_array($choice_info))
	{
		$choice = $choice_row['choice'];
		$tally = $pollModel->voteTally($poll_id, $choice);
		$choice = htmlspecialchars($choice);
		echo "<tr><td>$choice</td><td>$tally</td></tr>";
	}
	echo "</tbody>";
	echo "</table>";
}
//poll choices to vote from if you haven't voted yet
else 
{
	echo "<form method='post' action='viewpoll.php?pollid=$poll_id'>";
	echo "<table><tbody>";
	while($choice_row = mysql_fetch_array($choice_info))
	{
		$choice = htmlspecialchars($choice_row['choice']);
		echo "<tr><td><input type='radio' name='choice' value=$choice>$choice</td></tr>";
	}
	echo "</tbody><tfoot>";
	echo "<tr><td><input type='submit' value='Vote'></td></tr>";
	echo "</table></form>";
}