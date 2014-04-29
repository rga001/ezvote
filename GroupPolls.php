<?php
//user logs in, sees all of his/her groups, clicks on which one they want to see, then go to this page

include_once 'header.php';
$userModel = new UserModel();
$pollModel = new PollModel();

//redirect to index.php if user is not logged in
if(!($userModel->userIsLoggedIn()))
	die('<meta http-equiv="REFRESH" content="0; url=index.php">');

$userID = $_SESSION['userid'];
$members = Array();
$pollClosed = FALSE;

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
//if link was clicked
//display group polls
if(isset($_GET['group']))
{   $grp = $_GET['group'];
	//echo $_GET['group'] . " group page"; 
}

if(($_SERVER['REQUEST_METHOD'] == 'GET'))
{
	//get time and date
	$time = date("Y\-m\-d h:i:s A");
	
	$poll = $_GET['pollid'];
	
	//close poll
	$closePoll = queryMysql("UPDATE poll_info SET end_date='$time' WHERE poll_id='$poll'");
	$pollClosed = TRUE;

}
//$gpwd = queryMysql("SELECT * FROM groups WHERE password='$gpassword'");

/*$group = queryMysql("SELECT * FROM groups WHERE password='$gpassword'");
while($row = mysql_fetch_array($groups)){
	$gname = $row['name'];
}*/


/*
$g_mems = queryMysql("SELECT * FROM groups_members");
$cntr = 0;
while($g_info = mysql_fetch_array($g_mems)){
	if ($g_info['group_id'] == $gID){  		        //if user is in the group
		$members[$cntr] = $g_info['member_id'];		//add them to the members array
		$cntr++;									//increment array index
	}
}	
*/	
	
/*echo <<<_END
<h1> $grp Group Page</h1>$error


<body>
</body>
</html>
_END;*/

echo "<div style='width:65%; background: #2D3232; margin-left: 300px; margin-right: auto; margin-top: 200px; padding-top: 20px; border-radius: 10px'>";
echo "<div style='padding-bottom:1em;width:75%;margin: 0 auto;'><h1>$grp</h1></div>";

//get group id
$getID= queryMysql("SELECT group_id FROM groups WHERE name='$grp'");
while ($r = mysql_fetch_array($getID)){
	$gID = $r['group_id'];
}
	
	//get pollids from group_polls
	$groupPollIDs = queryMysql("SELECT * FROM group_polls WHERE group_id='$gID'");
	while ($gpolls = mysql_fetch_array($groupPollIDs)){ 	
		$p = $gpolls['poll_id'];

		//get group polls from database
		$groupPolls = queryMysql("SELECT * FROM poll_info WHERE poll_id='$p'");
		while ($row = mysql_fetch_array($groupPolls)){   
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
							

					<? if($pollModel->validPollDate($row['poll_id'])){  ?>
						 <label style="cursor:pointer;">Poll closes at: <?= $end_date ?></label>
					<?}
					  else{?>
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
	<?php }

	}  //end outer query loop
	?>		
	</div>
