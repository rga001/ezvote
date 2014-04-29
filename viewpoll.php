<?php
include_once 'header.php';

//grab poll info and choices from poll_id
$poll_id = $_GET["pollid"];
$pollModel = new PollModel();
$poll_info = $pollModel->getPollInfo($poll_id);
$choice_info = $pollModel->getPollChoices($poll_id);
$poll = mysql_fetch_array($poll_info);

//grab user id
$userModel = new UserModel();
$user_id = $_SESSION['userid'];
if($userModel->userIsLoggedIn())
	$user_id = $_SESSION['userid'];
else
	$user_id = -1;

//redirect to index.php if poll is private and user is not logged in or if user is not in poll group
if(!($userModel->userPermissionPoll($poll_id, $user_id)))
{
	echo "You do not have permission to view this poll.";
	die('<meta http-equiv="REFRESH" content="0; url=index.php">');
}

//set variables
$error = $vote = $comment = $comment_error = "";
$changevote = false;
$viewresults = false;

//post request for changing vote
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['changevote']))
{
	$changevote = true;
}

//post request for viewing the results
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['viewresults']))
{
	$viewresults = true;
}

//form validation for voting
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submitvote']))
{
	if(isset($_POST['choice']))
	{
		$vote = $_POST['choice'];
		
		//input or change vote to database
		$pollModel->votePoll($poll_id, $user_id, $vote);
	}
	else 
		$error = "ERROR: Please select a choice.<br>";
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

//HTML stuff

//poll title and error
echo "<div class='formstyle'>";
echo "<h1 class='heading'>".htmlspecialchars($poll['title'])."</h1>";
echo "<span class='error'>$error</span>";

//creator - username if public, real name if private 
$creator_info = $userModel->getUserInfo($poll['creator_id']);
$creator =  mysql_fetch_array($creator_info);
if($poll['public'] == 1)
	echo "Creator: " . htmlspecialchars($creator['username']) . "<br>";
else 
	echo "Creator: " . htmlspecialchars($creator['firstname']) . " " . htmlspecialchars($creator['lastname']) . "<br>";

//random information for the poll
if($poll['public'] == 0)
	echo "Group: " . htmlspecialchars($pollModel->pollGroup($poll_id)) . "<br>";
echo "Start date: " . substr(htmlspecialchars($poll['create_date']), 0, -9) . "<br>";
echo "End date: " . substr(htmlspecialchars($poll['end_date']), 0, -9) . "<br>";
if($poll['description'] != "")
	echo "Description: " . htmlspecialchars($poll['description']) . "<br><br>";

//view results if already voted or if not logged in
if(!($userModel->userIsLoggedIn()) || ($pollModel->votedAlready($poll_id, $user_id) && $changevote == false)
|| !($pollModel->validPollDate($poll_id)) || $viewresults == true)
{
	//results table
	echo "<table><tbody>";
	echo "<tr><td>Choices</td><td>Votes</td></tr>";
	while($choice_row = mysql_fetch_array($choice_info))
	{
		//query for the choice value and number of votes for the choice
		$choice = $choice_row['choice'];
		$tally = $pollModel->voteTally($poll_id, $choice);
		
		//query for who voted for which choice (on mouseover) 
		if($poll['anonymous'] == 0 && $poll['public'] == 0)	//if not anonymous and private give real name
			$voters = $pollModel->choiceVotersNames($poll_id, $choice);
		else if($poll['anonymous'] == 0 && $poll['public'] == 1)	//if not anonymous and private give username
			$voters = $pollModel->choiceVotersUsernames($poll_id, $choice);
		else //if anonymous
			$voters = "";
		
		//output choice and tally info
		$choice = htmlspecialchars($choice);
		$voters = htmlspecialchars($voters);
		echo "<tr title='$voters'><td>$choice</td><td>$tally</td></tr>";
	}
	echo "</tbody>";
	
	//buttons to change vote or edit poll
	if($userModel->userIsLoggedIn() && $pollModel->validPollDate($poll_id))
	{
		//change vote
		echo "<tfoot>";
		echo "<form method='post' action='viewpoll.php?pollid=$poll_id'>";
		echo "<tr><td colspan='2'><input type='submit' name='changevote' value='Change Vote'></td></tr></form>";
		
		//edit poll if user is creator and if no votes have happened yet
		if($poll['creator_id'] == $user_id && $pollModel->noVotesYet($poll_id))
		{
			echo "<form method='post' action='create.php'>";
			echo "<input type='hidden' name='editpollid' value=$poll_id>";
			echo "<tr><td colspan='2'><input type='hidden' name='editpollid' value=$poll_id>";
			echo "<input type='submit' name='editpoll' value='Edit Poll'></td></tr></form>";
		}
		echo "</tfoot>";
	}
	echo "</table>";
}

//poll choices to vote from if you haven't voted yet
else 
{
	//view choices as radio buttons
	echo "<form method='post' action='viewpoll.php?pollid=$poll_id'>";
	echo "<table><tbody>";
	while($choice_row = mysql_fetch_array($choice_info))
	{
		$choice = htmlspecialchars($choice_row['choice']);
		echo "<tr><td><input type='radio' name='choice' value='$choice' required>$choice</td></tr>";
	}
	echo "</tbody><tfoot>";
	echo "<tr><td colspan='2'><input type='submit' name='submitvote' value='Vote'></form>";
	
	//view results if user does not want to vote
	echo "<form method='post' action='viewpoll.php?pollid=$poll_id'>";
	echo "<input type='submit' name='viewresults' value='View Results'></form></td></tr>";
	
	//edit poll if user is creator and if no votes have happened yet
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

//voting period had ended note
if(!($pollModel->validPollDate($poll_id)))
	echo "Voting on this poll has ended.<br>";

//comments
if($poll['comments'] == 1)
{	
	echo "<br>Comments";
	
	//comment textarea box
	if($userModel->userIsLoggedIn())
	{
		echo "<br>";
		echo "$comment_error";
		echo "<form method='post' action='viewpoll.php?pollid=$poll_id'>";
		echo "<textarea name='comment' rows='3' cols='40' maxlength='300' required></textarea><br>";
		echo "<input type='submit' name='submitcomment' value='Submit Comment'></form>";
	}
	
	//show all comments from users
	echo "<br>";
	$poll_comments = $pollModel->allComments($poll_id);
	$comments_user = "";
	while($row = mysql_fetch_array($poll_comments))
	{
		if($poll['public'] == 1)
			$comments_user = $row['username'];
		else
			$comments_user = $row['firstname'] . " " . $row['lastname'];
		echo htmlspecialchars($comments_user) . ": " . htmlspecialchars($row['comment']) . "<br>";
	}
}
echo "<br><br></div>";