<?php
include_once 'header.php';

echo <<<_END
<div style="background: url(bg.png) no-repeat center center fixed;display:block;position:absolute;width:100%;height:100%;background-size:cover" />
<h1 align="center">Team E: EZvote</h1>
<h2 align="center">Gage Alvis, Marty Hamilton, Derek Arnold, Katherine Chen</h2>
<div style="display:table;padding-left:15%">
	<div style="display:table-row">
	     <form action="desc.php" method="get">
          <input type="submit" value="What is EZvote?" />         </form>
	</div>
	<div style="display:table-row">
		BTW You can't login again if you already logged in or registered, you need to logout first.
		<br><a href="registration.php">Register</a>
	</div>
	<div style="display:table-row">
		<a href="login.php">Login</a>
	</div>
	<div style="display:table-row">
		<a href="logout.php">Logout</a>
	</div>
	<div style="display:table-row">
		<a href="create.php">Create Poll</a> <-- not connected to database yet
	</div>
	<div style="display:table-row">
		<a href="MainPage.html">hahahahahahahah</a>
	</div>
	<div style="display:table-row">
		<a href="UserPage.html">hahahahahahahah part2</a>
	</div>
</div>

_END;
	
	$pollModel = new PollModel();
	$topPolls = $pollModel->getTopPolls();
	$count = 0;

	while ($row = mysql_fetch_array($topPolls)){
		
?>
		<div style="display:table;width:50%;margin-left:auto; margin-right:auto; border: 2px solid gray; border-radius: 5px;">
			<div style="display:table-row; background-color: lightblue;">
				<div style="display:table-cell; padding-top: 1em; padding-left: 1em; font-size:large; ">
<?php
		if(isset($_SESSION['user'])){
?>
					<a href="/viewpoll.php?pollid=<?= $row['poll_id']?>"><?= $row['title']?></a>
<?php
		}
		else{
?>
					<label><?= $row['title']?></label>
<?php
		}
?>
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