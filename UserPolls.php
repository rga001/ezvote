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
/*if(($_SERVER['REQUEST_METHOD'] == 'GET'))
{
	//get time and date
	$time = date("Y\-m\-d h:i:s A");

	//get pollid from query string
	$poll = $_GET['pollid'];

	//close poll
	$closePoll = queryMysql("UPDATE poll_info SET end_date='$time' WHERE poll_id='$poll'");
	$pollClosed = TRUE;

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
	echo "<a href=GroupPolls.php?group={$userGroupNames[$z]}>{$userGroupNames[$z]}</a>" . " ";
} 

 */echo "hi";

		echo "<div style='width:65%; background: #2D3232; margin-left: 300px; margin-right: auto; margin-top: 200px; padding-top: 20px; border-radius: 10px'>";
		$userPolls = queryMysql("SELECT * FROM poll_info WHERE creator_id='$userID'");
		echo "<h1 style='padding-left:80px;'> Polls </h1>";
		while ($row = mysql_fetch_array($userPolls)){  


		?>
		
<!--<div style="background-color: #2D3232; padding-top: 20px; margin-top: 150px; margin-left: 250px; width: 800px; border-radius: 10px">-->
<div style="display:table;width:80%; margin-left: auto; margin-right:auto; border-radius: 5px; background-color: white;">
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

				<? if($pollModel->validPollDate($row['poll_id'])){ ?>
					<label style="cursor:pointer;">Poll closes at: <?= $row['end_date'] ?></label>		
					<form method="get" action="UserPolls.php">
						<input type="submit" value="Close Poll"> 
						<input type="hidden" name="pollid" value="<?echo $row['poll_id']?>">
					</form>	
				<? }	
				   else{	?>
				     <label style="cursor:pointer;">Poll closed: <?= $row['end_date'] ?></label>
				 <?}?>	
				</div>
			
			</div>
			<div id="poll<?=$row['poll_id'] ?>" style="display:none;width:200px" class="extraInfo">
				<label>Creator: <?=$_SESSION['name']?></label>
			</div>
		</div>
		<br />
		
	<? } ?> </div>