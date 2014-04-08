<?php //registration.php
//register for the website

include_once 'header.php';
include_once 'UserModel.php';
$userModel = new userModel();

$error = $email = $firstname = $lastname = $username = $password = $password2 = "";
$has_error = FALSE;

//if logged in, redirect to index.php
if(isset($_SESSION['user']))
	die('<meta http-equiv="REFRESH" content="0; url=index.php">');

//form validation
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	//user input
	$email = strtolower(trim($_POST['email']));
	$firstname = ucfirst(trim($_POST['firstname']));
	$lastname = ucfirst(trim($_POST['lastname']));
	$username = trim($_POST['username']);
	$password = $_POST['password'];
	$password2 = $_POST['password2'];

	//check if some fields are empty
	if($email == "" || $firstname == "" || $lastname == "" ||
	$username == "" || $password == "" || $password2 == "")
	{
		$error .= "ERROR: Not all fields were filled.<br>";
		$has_error = TRUE;
	}
	
	//check that email is valid
	if($email != "" && !filter_var($email, FILTER_VALIDATE_EMAIL))
	{
		$error .= "ERROR: E-mail is not valid.<br>";
		$has_error = TRUE;
	}
	
	//check if email already exists
	else if($email != "" && mysql_num_rows(queryMysql("SELECT * FROM users WHERE email='$email'")))
	{
		$error .= "ERROR: That email has already been registered.<br>";
		$has_error = TRUE;
	}
	
	//check if username already exists
	if($username != "" && mysql_num_rows(queryMysql("SELECT * FROM users WHERE username='$username'")))
	{
		$error .= "ERROR: That username has already been registered.<br>";
		$has_error = TRUE;
	}
	
	//check if passwords do not match
	if($password != $password2)
	{
		$error .= "ERROR: Passwords do not match.<br>";
		$has_error = TRUE;
	}
	
	//register user to database
	if($has_error == FALSE)
	{
		$userModel->registerUser($email, $firstname, $lastname, $username, $password);
		
		//store info to keep user logged in
		$_SESSION['user'] = $firstname." ".$lastname;
		$_SESSION['username'] = $username;
		
		//continue to home page
		die('<meta http-equiv="REFRESH" content="0; url=index.php">');
	}
}

//sign up form
echo <<<_END
<h1>Registration</h1>$error

<form method='post' action='registration.php'>
<table>
	<tbody>
		<tr><td>Email</td><td><input type='email' maxlength='254' name='email' value='$email'></td></tr>
		<tr><td>First Name</td><td><input type='text' maxlength='20' name='firstname' value='$firstname'></td></tr>
		<tr><td>Last Name</td><td><input type='text' maxlength='20' name='lastname' value='$lastname'></td></tr>
		<tr><td>Username</td><td><input type='text' maxlength='20' name='username' value='$username'></td></tr>
		<tr><td>Password</td><td><input type='password' maxlength='16' name='password'></td></tr>
		<tr><td>Confirm Password</td><td><input type='password' maxlength='16' name='password2'></td></tr>
	</tbody>
	<tfoot>
		<tr><td><input type='submit' value='Create Account'></td></tr>
	</tfoot>
</table>
</form>

</body>
</html>
_END;

?> 
