<?php

	include_once 'header.php';
	
	$error = $groupname = $password = $password2 = "";
	$has_error = FALSE;
	
	//validate form
	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{	
	    $gname = $_POST['gn'];
		$gpass = $_POST['gpassword'];
		$gpass2 = $_POST['gpassword2'];
		$creation_date = date("Y\-m\-d h:i:s A");
		$user = $_SESSION['username'];
		$user_id = $_SESSION['userid'];
		
		$groups = queryMysql("SELECT name FROM groups");
		$names = Array();

		while($grp_names = mysql_fetch_array($groups))
		{
			 $names[] = $grp_names['name'];
		}
		
		//check to see if name is already taken
		$name_taken = FALSE;
		foreach($names as $x)
		{
			if($gname == $x)
			{
			    $name_taken = TRUE;
			}	
		}

		if($gpass != $gpass2)
		{
			$error .= "ERROR: Passwords do not match.<br>";
			$has_error = TRUE;
		}
		elseif(($gpass == "") || ($gpass2 == "") || ($gname == ""))
		{
			$error .= "Please enter group name and password<br>";
			$has_error = TRUE;
		}
		elseif($name_taken == TRUE)
		{
			$error .= "Group name taken<br>";
			$has_error = TRUE;
		}
		else   //insert values into table
			queryMysql("INSERT INTO groups (name, password, creator_id, created_date) VALUES('$gname', '$gpass', '$user_id', '$creation_date')");
		
		//redirect home (until group page goes up)
		die('<meta http-equiv="REFRESH" content="0; url=index.php">');
	}




echo <<<_END
<h1>Create Group</h1>$error

<head>
<script>
	function ValidateForm()
	{
		var name = document.getElementById("gn").value;
		var pass = document.getElementById("pw").value;
		var pass2 = document.getElementById("pw2").value;
		
		if(name === ""){
			//alert("No first name entered");
			document.getElementById("p1").innerHTML = 'please enter a name';
			return false;
		}
		else if(pass === ""){
			//alert("No last name entered");
			document.getElementById("p1").innerHTML = 'please enter a password';
			return false;
		}
		else if(pass2 === ""){
			//alert("No email address entered");
			document.getElementById("p1").innerHTML = 'please enter a password';
			return false;
		}
		else if(pass !=  pass2)
		{
			document.getElementById("p1").innerHTML = 'passwords do not match';
			return false;
		}
		else
			return true;
		
	}
</script>
</head>

<p id='p1'></p>
<form method='post' action='create_grp.php' onsubmit= 'return ValidateForm();'>
<table>
	<tbody> 
		<tr><td>Group Name</td><td><input type='gn' maxlength='254' name='gn' id='gn' value='$gn' required></td></tr>
		<tr><td>Group password</td><td><input type='password' maxlength='16' name='gpassword' id='pw' required></td></tr>
		<tr><td>Confirm group password</td><td><input type='password' maxlength='16' name='gpassword2' id='pw2' required></td></tr>
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