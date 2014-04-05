<?php
//tmp page for displaying basic poll info on front page
//build pollModel page to get poll data
//query needed for getting *top (?????) polls or sorting polls
////////query could sort polls then pick top (page#-1 * 10 to page# * 10)

/////////////////////////////////////////////////////////////////////////
//poll title (link to page with poll id)
//description
//dates
//options (with current # votes)
//////////////////////////////////////////////////////////////////////////
	include_once('PollModel.php');
	
	$pollModel = new PollModel();
	$topPolls = $pollModel->getTopPolls();
	$count = 0;
	echo <<<_END
		<html><body>
_END;
	while ($row = mysql_fetch_array($topPolls)){
		
?>
		<div style="display:table;width:50%;margin-left:auto; margin-right:auto; border: 2px solid gray; border-radius: 5px;">
			<div style="display:table-row; background-color: lightblue;">
				<div style="display:table-cell; padding-top: 1em; padding-left: 1em; font-size:large; ">
					<a href="/poll/?pollid=<?= $row['poll_id']?>"><?= $row['title']?></a>
				</div>
				<div style="display:table-cell"></div>
			</div>
			<div style="display:table-row">
				<div style="display:table-cell; padding-left: 1em; padding-top: 1em;width:50%">
					<label style="border-left: 2px solid lightgray"><?= $row['description']?></label>
				</div>
				<div style="display:table-cell;width:50%;">
					<label>Poll closes at: <?= $row['end_date'] ?></label>
				</div>
			</div>
		</div>
		<br />
<?php
		
	}
	echo <<<_END
		</body></html>
_END;
?>