<?php //login.php
//login to EzVote here

$userModel = new UserModel();
$error = $username = $password = "";
$loggedIn = false;

//log in user validation
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login']))
{
	$username = $_POST['username1'];
	$password = $_POST['password1'];
	
	//check if any fields are empty
	if ($username == "" || $password == "")
		$error = "ERROR: Username/Password invalid<br>";
	
	else 
	{
		//check to see if login info matches a user
		$loggedIn = $userModel->checkUserInfo($username, $password);

		//username/password invalid
		if($loggedIn == false)
			$error = "ERROR: Username/Password invalid<br>";
		
		//username/password valid so log user in with sessions (userid, username, name)
		else
		{
			$userModel->loginUser($username, $password);
			die('<meta http-equiv="REFRESH" content="0; url=index.php">');
		}			
	}
}

//only show login form when not logged in
if(!($userModel->userIsLoggedIn()))
{
	echo <<<_END
		<form method='post' action="">
		<table id='login'>
			<tbody>
				<tr><td>Username</td><td>Password</td></tr>
				<td><input type='text' maxlength='254' name='username1' value='$username' required></td>
				<td><input type='password' maxlength='16' name='password1' required></td>
				<td><input type='submit' name='login' value='Log In'></td></tr>
			</tbody>
			<tfoot>
				<tr><td colspan=2><span class='error'>$error</span></td></tr>
			</tfoot>
		</table>
		</form>
_END;
}
?>
