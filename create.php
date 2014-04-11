<?php //create.php
//poll creation page

include_once 'header.php';

$pollModel = new PollModel();
$userModel = new UserModel();
$user_id = $_SESSION['userid'];

echo <<<_END

<!-- jquery datepicker sources -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.9.1.js"></script>
<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
		
<script>
$(document).ready(function(){
	
	//insert row to table
	$('#AddChoice').click(function(){
		newRow = "<tr><td><input type='text' maxlenght='30' name='choices[]'></td>" +
				 "<td><input type='button' value='Delete' class='DeleteChoice'></td></tr>";
		$('#Choices tbody').append(newRow);
	});
	
	//delete row from table
	$('#Choices').on('click', '.DeleteChoice', function(){
		$(this).closest('tr').remove();
	});
	
	//calendar datepicker
	$( "#datepicker" ).datepicker();
	
	//disable groups menu
	$('#private').click(function(){
		$('#groups').attr("disabled", false);
  	});
	$('#public').click(function(){
		$('#groups').attr("disabled", true);
	});
		
});
</script>
_END;

//set variables to empty
$error = $poll_title = $description = $is_public = $group_chosen = $anonymous = $comments_disabled = $date = $start_date = $end_date = "";
$public_checked = $private_checked = $anon_checked = $cd_checked = "";
$disabled = 'disabled';
$has_error = FALSE;
$choices = array();

//date validation to make sure date is in right format
function validateDate($date, $format = 'm/d/Y')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

//form validation
if($_SERVER['REQUEST_METHOD'] == 'POST')
{	
	//poll title
	if(isset($_POST['poll_title']))
	{
		$poll_title = trim($_POST['poll_title']);
		if($poll_title == "")
		{
			$error .= "ERROR: Please fill out the title.<br>";
			$has_error = TRUE;
		}
	}
	else 
	{
		$error .= "ERROR: Please fill out the title.<br>";
		$has_error = TRUE;
	}
	
	//description
	if(isset($_POST['description']))
		$description = trim($_POST['description']);
	
	//public/private radio buttons
	if(isset($_POST['is_public']))
	{
		$is_public = $_POST['is_public'];
		if($is_public == 'yes')
		{
			$is_public = 1;	//yes to public
			$public_checked = 'checked';
			$private_checked = "";
		}
		else if($is_public == 'no')
		{
			$is_public = 0;	//no to public
			$public_checked = "";
			$private_checked = 'checked';
			
			if(isset($_POST['groups']))
			{
				$group_chosen = $_POST['groups'];
				$disabled = "";
			}
			else 
			{
				$error .= "ERROR: You are not in any groups to make a private poll.<br>";
				$has_error = true;
			}
		}	
	}
	else 
	{
		$error .= "ERROR: Please select public or private.<br>";
		$has_error = TRUE;
	}
	
	//anonymous
	if(isset($_POST['anonymous']))
	{
		$anonymous = 1;	//yes to anonymous
		$anon_checked = 'checked';
	}
	else
	{
		$anonymous = 0;	//no to anonymous
		$anon_checked = "";
	}
	
	//comments_disabled
	if(isset($_POST['comments_disabled']))
	{
		$comments_disabled = 0;	//comments are disabled
		$cd_checked = 'checked';
	}
	else
	{
		$comments_disabled = 1;	//comments are enabled
		$cd_checked = "";
	}
	
	//end date
	if(isset($_POST['date']))
	{
		$date = trim($_POST['date']);
		if(validateDate($date))
		{
			$tmp_date = explode("/", $_POST['date']);

			//yyyy-MM-dd HH-mm-ss
			$end_date = $tmp_date[2].'-'.$tmp_date[0].'-'.$tmp_date[1];
			$end_date .= ' 23:59:59';
			
			if(new DateTime() > new DateTime($end_date))
			{
				$error .= "ERROR: Please select a valid date.<br>";
				$has_error = TRUE;
			}
		}
		else
		{
			$error .= "ERROR: Please select a valid date.<br>";
			$has_error = TRUE;
		}
	}
	else
	{
		$error .= "ERROR: Please select a valid date.<br>";
		$has_error = TRUE;
	}
		
	//choices
	if(isset($_POST['choices']))
	{
		foreach($_POST['choices'] as $choice)
		{
			if(trim($choice) != "")
				$choices[] = trim($choice);
		}

		//check if duplicate choices exist
		if(sizeof($choices) > sizeof(array_unique($choices)))
		{
			$error .= "ERROR: Duplicate choice fields entered.<br>";
			$has_error = TRUE;
		}
		
		//check that there are at least two choices
		else if(sizeof($choices) < 2)
		{
			$error .= "ERROR: Please submit more than one choice.<br>";
			$has_error = TRUE;
		}
	}
	else
	{
		$error .= "ERROR: Please submit more than one choice.<br>";
		$has_error = TRUE;
	}
	
	//input into database if there are no errors
	if($has_error == FALSE)
	{
		echo $error;
		$type = 'multiple choice';
		$start_date = '1900-01-01 00:00:00';
		
		//insert into poll_info table
		$pollModel->createPoll($poll_title, $description, $user_id, $type, $is_public, $comments_disabled, $anonymous, $start_date, $end_date);
		
		//insert choices into poll_choices table
		$poll_id = $pollModel->insertChoices($choices);
		
		//if private insert poll id into group_polls
		if($is_public == 0)
			$pollModel->insertGroupPoll($poll_id, $group_chosen);
		
		die('<meta http-equiv="REFRESH" content="0; url=index.php">');
	}
}
echo <<<_END
<body onload="numRows('3')">
<h1>Create Poll</h1>$error

<!-- Start Poll Creation Form -->
<form method='post' action='create.php'>

<!-- Public/Private radio buttons -->
<input type='radio' id='public' name='is_public' value='yes' $public_checked required>Public
<input type='radio' id='private' name='is_public' value='no' $private_checked>Private

<!-- Select group if private -->
<select name='groups' id='groups' $disabled>
_END;
$groups = $userModel->userGroups($user_id);
while($groups_row = mysql_fetch_array($groups))
{
	$group = $groups_row['name'];
	echo "<option value='$group'>$group</option>";
}
echo <<<_END
</select>

<!-- Checkboxes for anonymous and comments -->
<br><input type='checkbox' name='anonymous' value='yes' $anon_checked> Anonymous voting<br>
<input type='checkbox' name='comments_disabled' value='yes' $cd_checked> Comments disabled<br>

<!--End date -->
End Date: <input type="text" name='date' id="datepicker" value='$date' required><br><br>

<!-- Title -->
Title <input type='text' maxlength='30' name='poll_title' value='$poll_title' required><br>

<!-- Description -->
Description:<br><textarea name='description' maxlength='300'>$description</textarea>
		
<!-- Choices -->
<table id='Choices'>
<thead><tr><th>Choices</th></tr></thead>
<tbody>
_END;
if(empty($choices) || sizeof($choices) == 1)	//default blank choices field or only one choice
{
	if(sizeof($choices) == 1)
		echo "<tr><td><input type='text' maxlength='30' name='choices[]' value='$choices[0]'></td>";
	else
		echo "<tr><td><input type='text' maxlength='30' name='choices[]'></td>";
	echo     "<td><input type='button' value='Delete' class='DeleteChoice'></td></tr>";
	echo "<tr><td><input type='text' maxlenght='30' name='choices[]'></td>";
	echo     "<td><input type='button' value='Delete' class='DeleteChoice'></td></tr>";
}
else //choices already has more than one input
{
	foreach($choices as $choice)
	{
		echo "<tr><td><input type='text' maxlength='30' name='choices[]' value='$choice'></td>";
		echo     "<td><input type='button' value='Delete' class='DeleteChoice'></td></tr>";
	}
}
echo <<<_END
</tbody>
<tfoot><tr><td><input type='button' value='Add Choice' id='AddChoice'></td></tr></tfoot>
</table>

<!-- Submit button -->
<input type='submit' value='Submit'>

<!-- Reset button -->
<input type='reset' value='Reset'>

</form>
		
<!-- Cancel Button -->
<form action='index.php'>
<input type='submit' value='Cancel'>
</form>

_END;

?>
</html>