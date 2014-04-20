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
				$("#poll"+id).slideUp();
			}else{
				$("#poll"+id).slideDown();
			}
		});
		$('.sortBtn').click(function(){
			$('.sortBtn').each(function(){
				$(this).attr('id', '');
				$(this).css({'background-color':'#2D3232'});
				$(this).css({'color':'#0099CC'});
			});
			$(this).attr('id', 'selected');
			$(this).css({'background-color':'#0099CC'});
			$(this).css({'color':'white'});
		});
		$('#sortSubmit').click(function(){
			var url = '';
			$('.sortBtn').each(function(){
				if($(this).prop('id').indexOf('selected') > -1){
					url = $(this).attr('name');
				}
			});
			$('.checkFilters').each(function(){
				if ($(this).is(':checked')){
					url+= "&filters[]=" + $(this).val();
				}
			});
			if(url==''){
				url = "index.php";
			}
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
	$count = 0;
	
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
	<div style="width:80%; background: white;margin-left: auto; margin-right: auto;">
		<div style="padding-bottom:1em;width:50%;margin: 0 auto;">
			<form method="post" action="index.php?sortby=end&asc=true" id="filterForm" name="sortForm">
			<button class="sortBtn css" type="button" name="index.php?sortby=start&asc=<?=ascCheck('start')?>" style="padding-left:1em;color:#0099CC;background:#2D3232;border:none;border-radius:5px;" id="startLink <?=selected('start')?>">Poll Open</button><?=sortPic('start')?>
			<button class="sortBtn" type="button" name="index.php?sortby=end&asc=<?=ascCheck('end')?>" style="padding-left:1em;color:#0099CC;background:#2D3232;border:none;border-radius:5px;" id="endLink <?=selected('end')?>">Poll Close</button><?=sortPic('end')?>
			<button class="sortBtn" type="button" name="index.php?sortby=create&asc=<?=ascCheck('create')?>" style="padding-left:1em;color:#0099CC;background:#2D3232;border:none;border-radius:5px;" id="createLink <?=selected('create')?>">Created Date</button><?=sortPic('create')?>
			<button class="sortBtn" type="button" name="index.php?sortby=pop&asc=<?=ascCheck('pop')?>" style="padding-left:1em;color:#0099CC;background:#2D3232;border:none;border-radius:5px;" id="popLink <?=selected('pop')?>">Popular Polls</button><?=sortPic('pop')?>
			<br />
				<input type="checkbox" name="filters[]" value="closed" class="checkFilters" <?=filterCheck('closed')?> />Closed Polls
<?php if (!($user_id == -1)) {?>
				<input type="checkbox" name="filters[]" value="voted" class="checkFilters" <?=filterCheck('voted')?> />My Votes
				<input type="checkbox" name="filters[]" value="group" class="checkFilters" <?=filterCheck('group')?> />Group Polls
<?php	}	?>	
			<input type="submit" id="sortSubmit" value="Sort Polls" style="float:right"/>
			</form>
		</div>
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
<?php	}	
	//if page > 1 show prev
	//show next
	//need to change this to get a pollcount or something so you cant click next forever
	if ($page > 1){
?>
		<div>
			<a href="/index.php?sortby=<?=$sortby?>&asc=<?=$asc?>&page=<?=$page-1?>">Prev</a>
		</div>
<?php } ?>
		<div>
			<a href="/index.php?sortby=<?=$sortby?>&asc=<?=$asc?>&page=<?=$page+1?>">Next</a>
		</div>
	</div>
	
	
<?php	
	echo <<<_END
		</body></html>
_END;

?>



