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

<div id="links">
<form method='post' action='login.php'>
	<div id="login">
	<label id="user">Username</label>
	<label id="pass">Password</label><br>
    <input id="userd" type="text" />
    <input id="passd" type="password" />
	<input id="lButton" type="submit" value="Login">
	</div>
</form>
</div>
<div id="banner">
	<div id="mLinks">
	</div>
</div>

</body>
</html>
_END;

?>
