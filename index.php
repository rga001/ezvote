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

</body>
</html>
_END;
?>