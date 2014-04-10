<?php
include_once 'header.php';
include_once 'PollModel.php';
include_once 'UserModel.php';
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('.extraInfo').css('height', $('.extraInfo').height());
		$('.extraInfo').hide();
		$('.expand').click(function(){
			var id = $(this).attr("id");
			if ($("#poll"+id).is(':visible')){
				$("#poll"+id).slideUp();
			}else{
				$("#poll"+id).slideDown();
			}
		});
	});
</script>
<?php
echo <<<_END
<!--div style="background: url(bg.png) no-repeat center center fixed;display:block;position:absolute;width:100%;height:100%;background-size:cover" -->
<h1 align="center">Team E: EZvote</h1>
<h2 align="center">Gage Alvis, Marty Hamilton, Derek Arnold, Katherine Chen</h2>

_END;
	
	$pollModel = new PollModel();
	$userModel = new UserModel();
	$topPolls = $pollModel->getTopPolls();
	$count = 0;
?>
	<div style="width:80%; background: white;margin-left: auto; margin-right: auto;">
<?php	
	while ($row = mysql_fetch_array($topPolls)){
		$tmpRow = $userModel->getUserInfo($row['creator_id']);
		$tmpCreator = mysql_fetch_array($tmpRow);
?>
		<div style="display:table;width:75%;margin-left:auto; margin-right:auto; border: 2px solid gray; border-radius: 5px;">
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
					<label style="cursor:pointer;">Poll closes at: <?= $row['end_date'] ?></label>
				</div>
			</div>
			<div id="poll<?=$row['poll_id'] ?>" style="display:none;width:200px" class="extraInfo">
				<label>Creator: <?=$tmpCreator['firstname'].' '.$tmpCreator['lastname'] ?></label>
			</div>
		</div>
		<br />
<?php	}	?>
	</div>
	
	
<?php	
	echo <<<_END
		</body></html>
_END;

?>



