<?php

include_once 'header.php';
$userModel = new UserModel();
$pollModel = new PollModel();

$userID = $_SESSION['userid'];
$name = $_SESSION['name'];
$userGroupIDs = Array();
$userGroupNames = Array();
$pollClosed = FALSE;


//echo $poll;
if(($_SERVER['REQUEST_METHOD'] == 'GET'))
{
	//get time and date
	$time = date("Y\-m\-d h:i:s A");
	//echo $time;
	
	$poll = $_GET['pollid'];
	
	//close poll
	$closePoll = queryMysql("UPDATE poll_info SET end_date='$time' WHERE poll_id='$poll'");
	$pollClosed = TRUE;
	//$p = $poll;

}

$groups = queryMysql("SELECT group_id FROM group_members WHERE member_id='$userid'");
$cntr = 0;
while($row = mysql_fetch_array($groups)){
	$userGroups[$cntr] = $row['group_id'];
	$cntr++;
}

foreach($userGroups as $y)
{
	echo $userGroups[$x];
	$grp = queryMysql("SELECT name FROM groups WHERE group_id='$userGroups[$y]'");
	while($row = mysql_fetch_array($grp))
	{
		$userGroupNames[$y] = $row['name'];
	}
}	

//display links to group pages
echo "groups: ";
foreach($userGroups as $z) 
{
	//echo $userGroupNames[$z] . " ";
	echo "<a href=GroupPolls.php?group={$userGroupNames[$z]}>{$userGroupNames[$z]}</a>";
} 

 //echo "<h1>$name's Polls</h1>";


		$userPolls = queryMysql("SELECT * FROM poll_info WHERE creator_id='$userID'");
		while ($row = mysql_fetch_array($userPolls)){   ?>
		
<div style="display:table;width:50%;margin-left:auto; margin-right:auto; border-radius: 5px; background-color: white;">
			<div style="display:table-row; background-color: #0099CC;">
				<div style="display:table-cell; padding-top: 1em; padding-left: 1em; font-size:large;">
				    <a href="/viewpoll.php?pollid=<?= $row['poll_id']?>"><?= $row['title']?></a>
				</div>
				<div style="display:table-cell"></div>
			</div>
			<div style="cursor:pointer;height:100%;width:100%;float:left;padding-left:1em;padding-top:1em;" id="<?= $row['poll_id']?>" class="expand">
				<div style="float:left;width:50%;">
					<label style="cursor:pointer;border-left: 2px solid lightgray;"><?= $row['description']?></label>
				</div>
				<div style="float:left;width:50%;">
					<label style="cursor:pointer;">Poll closes at: <?= $row['end_date'] ?></label>		
					<form method="get" action="UserPolls.php">
					<? if(($pollClosed == 1)&& ($poll != $row['poll_id'])) { ?>
						<input type="submit" value="Close Poll"> 
					<? }	?>
					<input type="hidden" name="pollid" value="<?echo $row['poll_id']?>">
					</form>		
				</div>
				<div>
					
				</div>
			</div>
			<div id="poll<?=$row['poll_id'] ?>" style="display:none;width:200px" class="extraInfo">
				<label>Creator: <?=$_SESSION['name']?></label>
			</div>
		</div>
		<br />
	<?php } ?>		