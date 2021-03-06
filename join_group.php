<?php

include_once 'header.php';

//redirect to index.php if user is not logged in
if(!($userModel->userIsLoggedIn()))
	die('<meta http-equiv="REFRESH" content="0; url=index.php">');
	
	$error = $groupname = $password = $password2 = "";
	$has_error = FALSE;
	
	
	$user_id = $_SESSION['userid'];

	
	//validate form
	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		$gname = $_POST['gn'];
		$gpass = $_POST['gpassword'];
		$inGroup = FALSE;
	    
		$grp = queryMysql("SELECT * FROM groups WHERE name='$gname'");
		while($group_info = mysql_fetch_array($grp))
		{
		  $gid = $group_info['group_id'];
		  $group = $group_info['name'];
		  $password = $group_info['password'];
		  $creator = $group_info['creator_id'];
		  $date = $group_info['created_date'];
		}
		
		//get group members data and make sure user isn't in the group already
		$gMembers = queryMysql("SELECT * FROM group_members");
		while($row = mysql_fetch_array($gMembers))
		{
			$grpID = $row['group_id'];
			$memID = $row['member_id'];
			
			if(($grpID == $gid) && ($memID == $user_id))
			{
				$inGroup = TRUE;
			}	
		}
		

		
		//if name and password match group name and password then allow to join group
		if(($gname != $group) || ($gpass != $password))
		{
			$error .= "ERROR: Incorrect group name and/or password<br>";
			$has_error = TRUE;
		}
		elseif(($gname == "") || ($gpass == ""))
		{
			$error .= "Please enter group name and password<br>";
			$has_error = TRUE;
		}
		elseif($inGroup == TRUE)
		{
			$error .= "You're already in this group<br>";
			$has_error = TRUE;
		}
		else
		{
			queryMysql("INSERT INTO group_members (group_id, member_id) VALUES('$gid', '$user_id')");
			//redirect to home (until group page is up)
			die('<meta http-equiv="REFRESH" content="0; url=index.php">');
		}
		
		
	}

echo <<<_END
<div class="formstyle">
<h1 class="heading">Join Group</h1><span class='error'>$error</span>

<head>
<script>
	function ValidateForm()
	{
		var name = document.getElementById("gn").value;
		var pass = document.getElementById("pw").value;
		
		if(name === ""){
			document.getElementById("p1").innerHTML = 'please enter a name';
			return false;
		}
		else if(pass === ""){
			document.getElementById("p1").innerHTML = 'please enter a password';
			return false;
		}
		else
			return true;
		
	}
</script>
</head>

<p id='p1'></p>

<form method='post' action='join_group.php' onsubmit= "return ValidateForm();">
<table>
	<tbody> 
		<tr><td>Group Name: </td><td><input type='gn' maxlength='254' name='gn' id='gn' value='$gn' required></td></tr>
		<tr><td>Group Password: </td><td><input type='password' maxlength='16' name='gpassword' id='pw' required></td></tr>
	</tbody>
	<tfoot>
		<tr><td><input type='submit' value='Join Group'></td></tr>
	</tfoot>
</table>
</form><br><br>

</body>
</html>
</div>
_END;
?>