<?php
include_once 'header.php';
include_once 'PollModel.php';
include_once 'UserModel.php';

//get key function
function GET($key, $default) {
    return (isset($_GET[$key]) && !empty($_GET[$key])) ? $_GET[$key] : $default;
}
//get user id if logged in
if (isset($_SESSION['userid']))
	$user_id = $_SESSION['userid'];
else
	$user_id = -1;

$page = GET('page', 1);
$sortby = GET('sortby', 'null');
$asc = GET('asc', 'true');
$filters = $_REQUEST['filters'];
//echo $filters[0] . ' filters test';	
//echo $user_id;
//var_dump($_SESSION);
//echo $sortby . $page . $asc;

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
		$('.sortBtn').click(function(){
			$('.sortBtn').each(function(){
				$(this).removeClass("selected");
			});
			$(this).addClass("selected");
		});
		$('#sortSubmit').click(function(){
			var url = '';
			$('.sortBtn').each(function(){
				if($(this).prop('class').indexOf('selected') > -1){
					url = $(this).attr('name');
				}
			});
			if(url==''){
				url = "index.php?";
			}
			$('.checkFilters').each(function(){
				if ($(this).is(':checked')){
					url+= "&filters[]=" + $(this).val();
				}
			});
			$('#filterForm').prop('action', url);
			$('#filterForm').submit();
		});
	});
</script>
<?php
echo <<<_END
<!--div style="background: url(bg.png) no-repeat center center fixed;display:block;position:absolute;width:100%;height:100%;background-size:cover" -->

_END;

	$pollModel = new PollModel();
	$userModel = new UserModel();
	$topPolls = $pollModel->getTopPolls($user_id, $sortby, $page, $asc, $filters);
	$count = 1;
	
	function sortPic($check){
		global $sortby, $asc;
		$link = '';
		if ($sortby == $check)
			if ($asc == 'true')
				$link = '<img src="asce.jpg" alt="asce" id="' . $check. '" class="ascImg">';
			else
				$link = '<img src="desc.jpg" alt="desc" id="' . $check . '" class="ascImg">';
		return $link;
	}
	function ascCheck($check){
		global $sortby, $asc;
		$result = 'true';
		if ($sortby == $check && $asc == 'true')
			$result = 'false';
		return $result;
	}
	function filterCheck($check){
		global $filters;
		$result = '';
		foreach($filters as $checked){
			if ($checked == $check)
				$result = 'checked';
		}
		return $result;
	}
	function selected($check){
		$selected = '';
		if (sortPic($check) != ''){
			$selected = 'selected';
		}
		return $selected;
	}
	
?>
	<div style="width:65%; background: #2D3232; margin-left: 300px; margin-right: auto; margin-top: 200px; padding-top: 20px; border-radius: 10px">
		<div style="padding-bottom:1em;width:75%;margin: 0 auto;">
			<h1>Browse Polls</h1>
			<form method="post" action="index.php?sortby=end&asc=true" id="filterForm" name="sortForm">
			<?/*<button class="sortBtn <?=selected('start')?>" type="button" name="index.php?sortby=start&asc=<?=ascCheck('start')?>"  id="startLink">Poll Open</button><?=sortPic('start')?> */?>
			<button class="sortBtn <?=selected('end')?>" type="button" name="index.php?sortby=end&asc=<?=ascCheck('end')?>"  id="endLink">Poll Close</button><?=sortPic('end')?>
			<button class="sortBtn <?=selected('create')?>" type="button" name="index.php?sortby=create&asc=<?=ascCheck('create')?>" id="createLink">Created Date</button><?=sortPic('create')?>
			<button class="sortBtn <?=selected('pop')?>" type="button" name="index.php?sortby=pop&asc=<?=ascCheck('pop')?>"  id="popLink">Popular Polls</button><?=sortPic('pop')?>
			<br />
				<input type="checkbox" name="filters[]" value="closed" class="checkFilters" <?=filterCheck('closed')?> /><label style="color:#0099CC">Closed Polls</label>
<?php if (!($user_id == -1)) {?>
				<input type="checkbox" name="filters[]" value="voted" class="checkFilters" <?=filterCheck('voted')?> /><label style="color:#0099CC">My Votes</label>
				<input type="checkbox" name="filters[]" value="group" class="checkFilters" <?=filterCheck('group')?> /><label style="color:#0099CC">Group Polls</label>
<?php	}	?>	
			<input type="submit" id="sortSubmit" value="Sort Polls" style="float:right"/>
			</form>
		</div>
<?php

	while ($row = mysql_fetch_array($topPolls)){
	try{
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
	catch(Exception $e){
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
					<label style="cursor:pointer;">Poll closes on: <?= $end_date ?></label>
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
<?php	}	
	//if page > 1 show prev
	//show next
	//need to change this to get a pollcount or something so you cant click next forever
?>
	<div style="padding-bottom:1em">
<?
	if ($page > 1){
?>
		<a class="linky" href="/index.php?sortby=<?=$sortby?>&asc=<?=$asc?>&page=<?=$page-1?>">Prev</a>	
<?php } if($count > 10){ ?>		
		<a class="linky" href="/index.php?sortby=<?=$sortby?>&asc=<?=$asc?>&page=<?=$page+1?>">Next</a>
<? } ?>
	</div>
	
	</div>
	
	
<?php	
	echo <<<_END
		</body></html>
_END;

?>



