<?php

include_once 'header.php';
$userModel = new UserModel();
$pollModel = new PollModel();

//redirect to index.php if user is not logged in
if(!($userModel->userIsLoggedIn()))
	die('<meta http-equiv="REFRESH" content="0; url=index.php">');

$userID = $_SESSION['userid'];
$name = $_SESSION['name'];
$userGroups = Array();
$userGroupNames = Array();
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('.extraInfo').css('height', $('.extraInfo').height());
		$('.extraInfo').hide();
		$('.expand').click(function(){
			var id = $(this).attr("id");
			if ($("#poll"+id).is(':visible')){
				$("#poll"+id).hide();
			}else{
				$("#poll"+id).css("height","");
				$("#poll"+id).show();
			}
		});
	});
</script>

<?php
if(($_SERVER['REQUEST_METHOD'] == 'GET'))
{
	//get time and date
	$time = date("Y\-m\-d h:i:s A");

	//get pollid from query string
	$poll = $_GET['pollid'];

	//close poll
	$closePoll = queryMysql("UPDATE poll_info SET end_date='$time' WHERE poll_id='$poll'");
	$pollClosed = TRUE;

}


		echo "<div style='width:65%; background: #2D3232; margin-left: 300px; margin-right: auto; margin-top: 200px; padding-top: 20px; border-radius: 10px'>";
		echo "<div style='padding-bottom:1em;width:75%;margin: 0 auto;'><h1>My Polls</h1></div>";
		


		$groups = queryMysql("SELECT group_id FROM group_members WHERE member_id='$userID'");
		$cntr = 0;
		while($row = mysql_fetch_array($groups)){
			$userGroups[$cntr] = $row['group_id'];
			//echo $userGroups[$cntr]." ";
			$cntr++;
		}
		$cntr = 0;
		foreach($userGroups as $y)
		{	
			$grp = queryMysql("SELECT name FROM groups WHERE group_id='$userGroups[$cntr]'");
			while($row = mysql_fetch_array($grp))
			{	
				$userGroupNames[$cntr] = $row['name'];
				//echo $userGroupNames[$cntr];

			}
			$cntr++;
		}	

		
		//display links to group pages
		$cntr = 0;
		echo "groups: ";
		foreach($userGroupNames as $z) 
		{
			//echo $userGroupNames[$z];
			echo "<a href=GroupPolls.php?group={$userGroupNames[$cntr]}>{$userGroupNames[$cntr]}</a>" . " ";
			$cntr++;
		} 

		$userPolls = queryMysql("SELECT * FROM poll_info WHERE creator_id='$userID'");
		while ($row = mysql_fetch_array($userPolls)){  

		$tmpRow = $userModel->getUserInfo($row['creator_id']);
		$tmpCreator = mysql_fetch_array($tmpRow);
		
		$end_date = substr($row['end_date'], 0, -9);
		$tmp_end_date = explode('-', $end_date);
		$end_date = $tmp_end_date[1] . "/" . $tmp_end_date[2] . "/" . $tmp_end_date[0];
		
		$start_date = substr($row['create_date'], 0, -9);
		$tmp_start_date = explode('-', $start_date);
		$start_date = $tmp_start_date[1] . "/" . $tmp_start_date[2] . "/" . $tmp_start_date[0];
		
		$creator = $tmpCreator['firstname'].' '.$tmpCreator['lastname'];
		?>
		
<!--<div style="background-color: #2D3232; padding-top: 20px; margin-top: 150px; margin-left: 250px; width: 800px; border-radius: 10px">-->
		<div style="display:table;width:75%;margin-left:auto; margin-right:auto; border: 2px solid gray; border-radius: 5px; background-color:white;">
			<div style="display:table-row; background-color: lightblue;">
				<div style="display:table-cell; padding-top: 1em; padding-left: 1em; font-size:large; ">
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
					<label style="cursor:pointer;">Poll closes at: <?= $end_date ?></label>		
					<form method="get" action="UserPolls.php">
						<input type="submit" value="Close Poll"> 
						<input type="hidden" name="pollid" value="<?echo $row['poll_id']?>">
					</form>	
				<? }	
				   else{	?>
				     <label style="cursor:pointer;">Poll closed on: <?= $end_date ?></label>
				 <?}?>	
				</div>
			
			</div>
			<div id="poll<?=$row['poll_id'] ?>" style="display:none;height:auto;overflow:auto;" class="extraInfo">
				<div style="padding-top:.5em;padding-left:1em;">
					<label>Creator: <?=$creator ?></label>
				</div>
				<div style="padding-left:1em">
					<label>Poll Start: <?=$start_date?></label>
				</div>
				<!-- ><div style="padding-left:1em">
					<label>Voting is set to <?=$tmpType['type']?></label>
				</div>-->
			<?if ($row['public'] == 0){?>
				<div style="padding-left:1em">
					<label>This poll is private</label>
				</div>
			<?}if ($row['comments'] == 0){?>
				<div style="padding-left:1em">
					<label>Comments are disabled.</label>
				</div>
			<?}if ($row['anonymous'] == 1){?>
				<div style="padding-left:1em">
					<label>Voting is anonymous</label>
				</div><?}?>				
			</div>
		</div>
		<br />
		
	<? } ?> </div>