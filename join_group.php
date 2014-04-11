<?php

include_once 'header.php';
	
	$error = $groupname = $password = $password2 = "";
	$has_error = FALSE;
	
	//validate form
	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		$gname = $_POST['gn'];
		$gpass = $_POST['gpassword'];
		
	    //
		$grp = queryMysql("SELECT group_id AND name AND password FROM groups WHERE name='$gname'");
		while($group_info = mysql_fetch_array($grp))
		{
		  $gid = $group_info['group_id'];
		  $group = $group_info['name'];
		  $password = $group_info['password'];
		}
		
		//find userid
		$id = queryMysql("SELECT userid FROM users WHERE username='$user'");
		
		while($id_num = mysql_fetch_array($id))
		{
		  $mid = $id_num['userid'];	
		}
		echo $mid . "<br>";
		echo $gname . " " . $group . " " . $gpass . " " . $password;
		
		//if name and password match group name and password then allow to join group
		if(($gname != $group) || ($gpass != $password))
		{
			$error .= "ERROR: Incorrect group name and/or password<br>";
			$has_error = TRUE;
		}
		else
		{
			queryMysql("INSERT INTO group_members (group_id, member_id) VALUES('$gid', '$mid')");
		}
	}

echo <<<_END
<h1>Join Group</h1>$error

<form method='post' action='join_group.php'>
<table>
	<tbody> 
		<tr><td>Group Name</td><td><input type='gn' maxlength='254' name='gn' value='$gn'></td></tr>
		<tr><td>Group password</td><td><input type='password' maxlength='16' name='gpassword'></td></tr>
	</tbody>
	<tfoot>
		<tr><td><input type='submit' value='Join Group'></td></tr>
	</tfoot>
</table>
</form>

</body>
</html>
_END;
?>