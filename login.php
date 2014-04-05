<?php //login.php
//login to EzVote here

include_once 'header.php';

$error = $email = $password = "";

//if logged in, redirect to index.php
if(isset($_SESSION['user']))
	die('<meta http-equiv="REFRESH" content="0; url=index.php">');

//log in user
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$email = $_POST['email'];
	$password = $_POST['password'];
	
	//check if any fields are empty
	if ($email == "" || $password == "")
		$error = "ERROR: Username/Password invalid<br>";
	
	else 
	{
		//check to see if login info matches a user
		//$password = crypt($password, 'h3!1o');
		$query = "SELECT email,password FROM users WHERE email='$email' AND password='$password'";
		
		//username invalid
		if (mysql_num_rows(queryMysql($query)) == 0)
			$error = "ERROR: Username/Password invalid<br>";
		
		//username valid
		else
		{
			//retrive first and last name
			$query_names = "SELECT firstname,lastname,username FROM users WHERE email='$email'";
			$row = mysql_fetch_row(queryMysql($query_names));
			$name = $row[0]." ".$row[1];
			$username = $row[2];
				
			//store info to keep user logged in
			$_SESSION['user'] = $name;
			$_SESSION['username'] = $username;
				
			//continue to home page
			die('<meta http-equiv="REFRESH" content="0; url=index.php">');
		}
	}
}

//log in form
echo <<<_END
<h1>Login</h1> $error

<form method='post' action='login.php'>
<table>
	<tbody>
		<tr><td>Email</td><td><input type='email' maxlength='254' name='email' value='$email'></td></tr>
		<tr><td>Password</td><td><input type='password' maxlength='16' name='password'></td></tr>
	</tbody>
	<tfoot>
		<tr><td><input type='submit' value='Log In'></td></tr>
	</tfoot>
</table>
</form>
			
<form action='registration.php'>
<input type='submit' value='Create New Account' />
</form>

</body>
</html>
_END;

?>
