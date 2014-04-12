<?php
include_once 'header.php';
include_once 'PollModel.php';
include_once 'UserModel.php';

//get key function
function GET($key, $default) {
    return (isset($_GET[$key]) && !empty($_GET[$key])) ? $_GET[$key] : $default;
}
//get user id if logged in
if (isset($_SESSION['user_id']))
	$user_id = $_SESSION['user_id'];
else
	$user_id = -1;

$page = GET('page', 1);
$sortby = GET('sortby', 'null');
$asc = GET('asc', 'true');	
echo $sortby . $page . $asc;

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
	$topPolls = $pollModel->getTopPolls($user_id, $sortby, $page, $asc);
	$count = 0;
	
	function sortPic($check){
		global $sortby, $asc;
		$link = '';
		if ($sortby == $check)
			if ($asc == 'true')
				$link = '<img src="asc.jpg" alt="asc">';
			else
				$link = '<img src="desc.jpg" alt="desc">';
		return $link;
	}
	function ascCheck($check){
		global $sortby, $asc;
		$result = 'true';
		if ($sortby == $check && $asc == 'true')
			$result = 'false';
		return $result;
	}
	
?>
	<div style="width:80%; background: white;margin-left: auto; margin-right: auto;">
		<div style="padding-bottom:1em;">
			<label>Sort by: </label>
			<a href="/index.php?sortby=start&asc=<?=ascCheck('start')?>" style="padding-left:1em;">Poll Open</a><?=sortPic('start')?>
			<a href="/index.php?sortby=end&asc=<?=ascCheck('end')?>" style="padding-left:1em;">Poll Close</a><?=sortPic('end')?>
			<a href="/index.php?sortby=create&asc=<?=ascCheck('create')?>" style="padding-left:1em;">Created Date</a><?=sortPic('create')?>
			<a href="/index.php?sortby=pop&asc=<?=ascCheck('pop')?>" style="padding-left:1em;">Popular Polls</a><?=sortPic('pop')?>
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



