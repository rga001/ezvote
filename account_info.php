<?php
	
	include_once 'header.php';

	$error = $email = $firstname = $lastname = $username = $password = $password2 = "";
	$has_error = FALSE;

	
	
	//split name of user into an array
	$name = explode(" ", $_SESSION['user']);
	
	//retrieve username
	$username = queryMysql("SELECT * FROM users WHERE firstname='$name[0]' AND lastname='$name[1]'");
	
	
	while($row = mysql_fetch_array($username))
	{
	  $e = $row['email'];	
	  $fn = $row['firstname'];
	  $ln = $row['lastname'];
	  $un = $row['username'];
	}

	//update database with new password
	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{	
		$password = $_POST['password'];
		$password2 = $_POST['password'];
		
		//check if new password does not match the confirmation password
		if($password != $password2)
		{
			$error .= "ERROR: Passwords do not match.<br>";
			$has_error = TRUE;
		}
		//update database w/ new password
		elseif($has_error == FALSE)
		{
			//mysql_query($conn, "UPDATE users SET password='$password' WHERE email='$email' username='$username'");
			queryMysql("UPDATE users SET password='$password' WHERE username='$un'");
		}
	}
	
	
	//account info and change password form
	
	echo <<<_END
	<h1>Acount Information</h1>$error

	<form method='post' action='account_info.php'>
	<table>
		<tbody>
			<tr><td>Email:</td><td>$e</td></tr>
			<tr><td>First Name:</td><td>$fn</td></tr>
			<tr><td>Last Name:</td><td>$ln</td></tr>
			<tr><td>Username:</td><td>$un</td></tr>
			<tr></tr>
			<tr><td>Change password:<td><input type='password' maxlength='16' name='password'></td></tr>
			<tr><td>Confirm password change: </td><td><input type='password' maxlength='16' name='password2'></td></tr>
		</tbody>
		<tfoot>
			<tr><td><input type='submit' value='Change Password'></td></tr>
		</tfoot>
	</table>
	</form>

	</body>
	</html>
_END;
/**/
?>