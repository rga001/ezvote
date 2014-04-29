<?php //registration.php
//register for the website

include_once 'header.php';

$userModel = new userModel();

$error = $email = $firstname = $lastname = $username = $password = $password2 = "";
$has_error = FALSE;

//generate random salt
function generateSalt(){
	$salt = '';
	$seed = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	for($i = 0; $i < 10; $i++){
		$salt .= $seed[rand(0, strlen($seed) - 1)];
	}
	return $salt;
}
//if logged in, redirect to index.php
if($userModel->userIsLoggedIn())
	die('<meta http-equiv="REFRESH" content="0; url=index.php">');

//form validation
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['registration'])) 
{
	//user input
	$email = strtolower(trim($_POST['email2']));
	$username = ucfirst(strtolower(trim($_POST['username2'])));
	$firstname = trim($_POST['firstname2']);
	$lastname = ucfirst(strtolower(trim($_POST['lastname2'])));
	$salt = generateSalt();
	$password = $_POST['password2'];
	$password2 = $_POST['confirm_password2'];
	$saltedPW = hash('sha256', $password + $salt);
	
	//format first name
	$tmp_firstname = explode(" ", $firstname);
	$firstname = "";
	for($i = 0; $i < sizeof($tmp_firstname); $i++)
	{
		$firstname .= ucfirst(strtolower($tmp_firstname[$i]));
		$firstname .= " ";
	}
	$firstname = trim($firstname);

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
		//register user
		$userModel->registerUser($email, $firstname, $lastname, $username, $saltedPW, $salt);
		
		//login user
		$userModel->loginUser($username, $password);
		
		//continue to home page
		die('<meta http-equiv="REFRESH" content="0; url=index.php">');
	}
}

//sign up form
echo <<<_END
<div class="formstyle">
<h1>Registration</h1><span class='error'>$error</span>

<form method='post' action='registration.php'>
<table>
	<tbody>
		<tr><td>Email: </td><td><input type='email' maxlength='254' name='email2' value='$email' required></td></tr>
		<tr><td>Username: </td><td><input type='text' maxlength='20' name='username2' value='$username' required></td></tr>
		<tr><td>First Name: </td><td><input type='text' maxlength='20' name='firstname2' value='$firstname' required></td></tr>
		<tr><td>Last Name: </td><td><input type='text' maxlength='20' name='lastname2' value='$lastname' required></td></tr>
		<tr><td>Password: </td><td><input type='password' maxlength='16' name='password2' required></td></tr>
		<tr><td>Confirm Password: </td><td><input type='password' maxlength='16' name='confirm_password2' required></td></tr>
	</tbody>
	<tfoot>
		<tr><td><input type='submit' name='registration' value='Create Account'></td></tr>
	</tfoot>
</table>
</form>

</body>
</html>
<br>
<br>
</div>
_END;

?> 
