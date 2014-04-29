<?php
include_once 'header.php';
$pollModel = new PollModel();
if($userModel->userIsLoggedIn())
	$user_id = $_SESSION['userid'];
else
	$user_id = -1;
	
//javascript for clicking for extra poll info
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

$searchQuery = trim($_GET["search"]);
$count = 0;

//background and header
echo "<div style='width:65%; background: #2D3232; margin-left: 300px; margin-right: auto; margin-top: 200px; padding-top: 20px; border-radius: 10px'>";
echo "<div style='padding-bottom:1em;width:75%;margin: 0 auto;'><h1>Search Results</h1></div>";

//check if any fields are empty
if ($searchQuery == "")
	$error = "ERROR: Search field empty.<br>";

else
{
	//query to find search results
	$searchResults = $pollModel->returnSearch($searchQuery);
	
	//show search results
	while ($row = mysql_fetch_array($searchResults))
	{
		//copied from whatever Gage made
		if(($userModel->userPermissionPoll($row[poll_id], $user_id)))	//make sure user can see poll
		{
			try
			{
				$count++;
				$tmpRow = $userModel->getUserInfo($row['creator_id']);
				$tmpCreator = mysql_fetch_array($tmpRow);
				$tmpTypeRow = $pollModel->getPollType($row['type']);
				$tmpType = mysql_fetch_array($tmpTypeRow);
				
				$end_date = substr($row['end_date'], 0, -9);
				$tmp_end_date = explode('-', $end_date);
				$end_date = $tmp_end_date[1] . "/" . $tmp_end_date[2] . "/" . $tmp_end_date[0];
				
				$start_date = substr($row['create_date'], 0, -9);
				$tmp_start_date = explode('-', $start_date);
				$start_date = $tmp_start_date[1] . "/" . $tmp_start_date[2] . "/" . $tmp_start_date[0];
			}
			catch(Exception $e)
			{
				writeLog($e->getMessage(), $user_id);
			}
			
			if($row['public'] == 1)
				$creator = $tmpCreator['username'];
			else
				$creator = $tmpCreator['firstname'].' '.$tmpCreator['lastname'];
	?>
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
				<? if ($row['public'] == 0){?>
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
			<br><br>
<?}}}?> </div>