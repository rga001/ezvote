<?php //login.php
//login to EzVote here

$userModel = new userModel();
$error = $username = $password = "";
$loggedIn = false;

//log in user validation
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$username = $_POST['username'];
	$password = $_POST['password'];
	
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
			$userModel->loginUser($username, $password);			
	}
}

//only show login form when not logged in
if(!isset($_SESSION['userid']))
{
	echo <<<_END
		<form method='post' action='login.php'>
		<table id='login'>
			<tbody>
				<tr><td>Username</td><td>Password</td></tr>
				<td><input type='text' maxlength='254' name='username' value='$username'></td>
				<td><input type='password' maxlength='16' name='password'></td>
				<td><input type='submit' value='Log In'></td></tr>
			</tbody>
			<tfoot>
				<tr><td colspan=2>$error</td></tr>
			</tfoot>
		</table>
		</form>
		</div>
_END;
}
?>
