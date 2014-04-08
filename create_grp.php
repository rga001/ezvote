<?php

	include_once 'header.php';
	
/**/	$error = $groupname = $password = $password2 = "";
	$has_error = FALSE;
	
	//validate form
	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{	
	    $gname = $_POST['gn'];
		$gpass = $_POST['gpassword'];
		$gpass2 = $_POST['gpassword2'];
		$creation_date = date("n\-j\-Y");
		$user = $_SESSION['username'];
		$id = queryMysql("SELECT userid FROM users WHERE username='$user'");		//firstname='$name[0]' AND lastname='$name[1]'");
		
		while($id_num = mysql_fetch_array($id))
		{
		  $creator = $id_num['userid'];	
		}
		
		if($gpass != $gpass2)
		{
			$error .= "ERROR: Passwords do not match.<br>";
			$has_error = TRUE;
		}
		elseif($has_error == FALSE) //insert values into table
		{
			queryMysql("INSERT INTO groups VALUES('$gname', '$gpass', '$creator', '$creation_date')");
		}
	}




echo <<<_END
<h1>Registration</h1>$error

<form method='post' action='registration.php'>
<table>
	<tbody>
		<tr><td>Group Name</td><td><input type='gn' maxlength='254' name='gn' value='$gn'></td></tr>
		<tr><td>Group password</td><td><input type='gpassword' maxlength='16' name='gpassword'></td></tr>
		<tr><td>Confirm group password</td><td><input type='gpassword' maxlength='16' name='gpassword2'></td></tr>
	</tbody>
	<tfoot>
		<tr><td><input type='submit' value='Create Group'></td></tr>
	</tfoot>
</table>
</form>

</body>
</html>
_END;
?>