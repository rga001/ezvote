<?php
	
	include_once 'header.php';
	$userModel = new UserModel();
	
	$error = $email = $firstname = $lastname = $username = $password = $password2 = "";
	$has_error = FALSE;

	//if($_POST['password'] == "")
		//die('<meta http-equiv="REFRESH" content="0; url=index.php">');
	

	
	$user_id = $_SESSION['userid'];
	
	//retrieve userinfo
	$username = $userModel->getUserInfo($user_id);//queryMysql("SELECT * FROM users WHERE firstname='$name[0]' AND lastname='$name[1]'");
		
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
		$password2 = $_POST['password2'];
		
		//check if new password does not match the confirmation password
		if($password != $password2)
		{
			$error .= "ERROR: Passwords do not match.<br>";
			$has_error = TRUE;
		}
		elseif(($password == "") || ($password2 == ""))
		{
			$error .= "Please enter new password<br>";
			$has_error = TRUE;
		}
		//update database w/ new password
		elseif($has_error == FALSE)
		{
			queryMysql("UPDATE users SET password='$password' WHERE username='$un'");
		}
		
		die('<meta http-equiv="REFRESH" content="0; url=index.php">');
	}
	
	
	//account info and change password form
	
	echo <<<_END
	<h1>Acount Information</h1>$error

	<head>
	<script>
	function ValidateForm()
	{
		var pass = document.getElementById("pw").value;
		var pass2 = document.getElementById("pw2").value;
		
		if(pass === ""){
			document.getElementById("p1").innerHTML = 'please enter a password';
			return false;
		}
		else if(pass2 === ""){
			document.getElementById("p1").innerHTML = 'please enter a password';
			return false;
		}
		else if(pass !=  pass2){
			document.getElementById("p1").innerHTML = 'passwords do not match';
			return false;
		}
		else
			return true;
		
	}	
	</script>
	</head>
	
	<p id='p1'></p>
	
	<form method='post' action='account_info.php' onsubmit= 'return ValidateForm();'>
	<table>
		<tbody>
			<tr><td>Email:</td><td>$e</td></tr>
			<tr><td>First Name:</td><td>$fn</td></tr>
			<tr><td>Last Name:</td><td>$ln</td></tr>
			<tr><td>Username:</td><td>$un</td></tr>
			<tr></tr>
			<tr><td>Change password:<td><input type='password' maxlength='16' name='password' id='pw' required></td></tr>
			<tr><td>Confirm password change: </td><td><input type='password' maxlength='16' name='password2' id='pw2' required></td></tr>
		</tbody>
		<tfoot>
			<tr><td><input type='submit' value='Change Password'></td></tr>
		</tfoot>
	</table>
	</form>

	</body>
	</html>
_END;
?>