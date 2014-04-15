<?php
/*
 * need to do:
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

//if poll is private and user is not logged in or if user is not in poll group
if(!($userModel->userPermissionPoll($poll_id, $user_id)))
{
	echo "You do not have permission to view this poll.";
	die('<meta http-equiv="REFRESH" content="0; url=index.php">');
}

//set variables
$error = $vote = $comment = $comment_error = "";
$changevote = false;
$viewresults = false;

//form validation
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submitvote']))
{
	if(isset($_POST['choice']))
	{
		$vote = $_POST['choice'];
		
		//input vote to database
		$pollModel->votePoll($poll_id, $user_id, $vote);
	}
	else 
		$error = "ERROR: Please select a choice.<br>";
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['changevote']))
{
	$changevote = true;
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['viewresults']))
{
	$viewresults = true;
}

//comments validation
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submitcomment']))
{
	if(isset($_POST['comment']))
	{
		$comment = $_POST['comment'];
		
		//input comment to database
		$pollModel->insertComment($poll_id, $user_id, $comment);
	}
	else
		$comment_error = "ERROR: No comment written<br>";
}
//poll title and description
echo "<h1>".htmlspecialchars($poll['title'])."</h1>";
echo "<span class='error'>$error</span>";

//creator - username if public, real name if private 
$creator_info = $userModel->getUserInfo($poll['creator_id']);
$creator =  mysql_fetch_array($creator_info);
if($poll['public'] == 1)
	echo "Creator: " . htmlspecialchars($creator['username']) . "<br>";
else 
	echo "Creator: " . htmlspecialchars($creator['firstname']) . " " . htmlspecialchars($creator['lastname']) . "<br>";

//random descriptions of the poll
if($poll['public'] == 0)
	echo "Group: " . $pollModel->pollGroup($poll_id) . "<br>";
echo "Start date: " . substr(htmlspecialchars($poll['create_date']), 0, -9) . "<br>";
echo "End date: " . substr(htmlspecialchars($poll['end_date']), 0, -9) . "<br>";
if($poll['description'] != "")
	echo "Description: " . htmlspecialchars($poll['description']) . "<br><br>";

//view results if already voted or if not logged in
if(!($userModel->userIsLoggedIn()) || ($pollModel->votedAlready($poll_id, $user_id) && $changevote == false)
|| !($pollModel->validPollDate($poll_id)) || $viewresults == true)
{
	echo "<table><tbody>";
	echo "<tr><td>Choices</td><td>Votes</td></tr>";
	while($choice_row = mysql_fetch_array($choice_info))
	{
		$choice = $choice_row['choice'];
		$tally = $pollModel->voteTally($poll_id, $choice);
		if($poll['anonymous'] == 0 && $poll['public'] == 0)	//if not anonymous and private give real name
			$voters = $pollModel->choiceVotersNames($poll_id, $choice);
		else if($poll['anonymous'] == 0 && $poll['public'] == 1)	//if not anonymous and private give username
			$voters = $pollModel->choiceVotersUsernames($poll_id, $choice);
		else //if anonymous
			$voters = "";
		$choice = htmlspecialchars($choice);
		echo "<tr title='$voters'><td>$choice</td><td>$tally</td></tr>";
	}
	echo "</tbody>";
	if($userModel->userIsLoggedIn() && $pollModel->validPollDate($poll_id))
	{
		echo "<tfoot>";
		echo "<form method='post' action='viewpoll.php?pollid=$poll_id'>";
		echo "<tr><td colspan='2'><input type='submit' name='changevote' value='Change Vote'></td></tr></form>";
		echo "</tfoot>";
	}
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
	echo "<tr><td><input type='submit' name='submitvote' value='Vote'></form></td>";
	echo "<td><form method='post' action='viewpoll.php?pollid=$poll_id'>";
	echo "<input type='submit' name='viewresults' value='View Results'></form></td></tr>";
	if($poll['creator_id'] == $user_id && $pollModel->noVotesYet($poll_id))
	{
		echo "<form method='post' action='create.php'>";
		echo "<input type='hidden' name='editpollid' value=$poll_id>";
		echo "<tr><td colspan='2'><input type='hidden' name='editpollid' value=$poll_id>";
		echo "<input type='submit' name='editpoll' value='Edit Poll'></td></tr></form>";
	}
	echo "</tfoot>";
	echo "</table>";
}

//list things
echo "<br>";
$list_things = "";
if($poll['public'] == 1)
	$list_things .= "public poll, ";
else
	$list_things .= "private poll, ";
if($poll['anonymous'] == 1)
	$list_things .= "anonymous, ";
if($poll['comments'] == 1)
	$list_things .= "comments enabled, ";
else
	$list_things .= "comments disabled, ";
$list_things = ucfirst($list_things);
$list_things = substr($list_things, 0, -2);
$list_things .= ".";
echo $list_things . "<br>";
if(!($pollModel->validPollDate($poll_id)))
	echo "Voting on this poll has ended.<br>";
echo "<br>";

if($poll['comments'] == 1)
{	
	echo "$comment_error";
	echo "Comments<br>";
	echo "<form method='post' action='viewpoll.php?pollid=$poll_id'>";
	echo "<textarea name='comment' rows='3' cols='50' maxlength='300' required></textarea><br>";
	echo "<input type='submit' name='submitcomment' value='Submit Comment'></form>";
	
	echo "<br>";
	$poll_comments = $pollModel->allComments($poll_id);
	$comments_user = "";
	echo "<table>";
	while($row = mysql_fetch_array($poll_comments))
	{
		if($poll['anonymous'] == 1)
			$comments_user = $row['username'];
		else
			$comments_user = $row['firstname'] . " " . $row['lastname'];
		echo "<tr><td>" . htmlspecialchars($comments_user) . ": </td><td>" . $row['comment'] . "</td></tr>";
	}
	echo "</table>";
}