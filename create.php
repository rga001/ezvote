<?php //create.php
//poll creation page

include_once 'header.php';

echo <<<_END
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
});
		
</script>
_END;

//set variables to empty
$error = $poll_title = $is_public = $anonymous = $comments_disabled = "";
$public_checked = $private_checked = $anon_checked = $cd_checked = "";
$has_error = FALSE;
$choices = array();

//form validation
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	//poll title
	if(isset($_POST['poll_title']))
	{
		$poll_title = sanitizeString($_POST['poll_title']);
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
	
	//public/private radio buttons
	if(isset($_POST['is_public']))
	{
		$is_public = sanitizeString($_POST['is_public']);
		if($is_public == 'yes')
		{
			$is_public = TRUE;
			$public_checked = 'checked';
			$private_checked = "";
		}
		else if($is_public == 'no')
		{
			$is_public = FALSE;
			$public_checked = "";
			$private_checked = 'checked';
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
		$anonymous = TRUE;
		$anon_checked = 'checked';
	}
	else
	{
		$anonymous = FALSE;
		$anon_checked = "";
	}
	
	//comments_disabled
	if(isset($_POST['comments_disabled']))
	{
		$comments_disabled = TRUE;
		$cd_checked = 'checked';
	}
	else
	{
		$comments_disabled = FALSE;
		$cd_checked = "";
	}
	
	//choices
	foreach($_POST['choices'] as $choice)
	{
		if(sanitizeString($choice) != "")
			$choices[] = sanitizeString($choice);
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
	
	//input into database if there are no errors
	if($has_error = FALSE)
	{
		//DATABASE STUFF GOES HERE
	}
}

echo <<<_END
<body onload="numRows('3')">
<h1>Create Poll</h1>$error

<!-- Start Poll Creation Form -->
<form method='post' action='create.php'>

<!-- Public/Private radio buttons -->
<input type='radio' name='is_public' value='yes' $public_checked required>Public
<input type='radio' name='is_public' value='no' $private_checked>Private<br>

<!-- Checkboxes for anonymous and comments -->
<input type='checkbox' name='anonymous' value='yes' $anon_checked> Anonymous voting<br>
<input type='checkbox' name='comments_disabled' value='yes' $cd_checked> Comments disabled<br><br>

<!-- Title -->
Title <input type='text' maxlength='30' name='poll_title' value='$poll_title' required>

<!-- Choices -->
<table id='Choices'>
<thead><tr><th>Choices</th></tr></thead>
<tbody>
<tr><td><input type='text' maxlenght='30' name='choices[]'></td>
	<td><input type='button' value='Delete' class='DeleteChoice'></td></tr>
<tr><td><input type='text' maxlenght='30' name='choices[]'></td>
	<td><input type='button' value='Delete' class='DeleteChoice'></td></tr>
</tbody>
<tfoot><tr><td><input type='button' value='Add Choice' id='AddChoice'></td></tr></tfoot>
</table>

<!-- Submit and end form-->
<input type='submit' value='Submit'>
</form>

<!-- Cancel Button -->
<form action='index.php'>
<input type='submit' value='Cancel'>
</form>

_END;

?>
</html>