<!DOCTYPE HTML>
<html>
<?php 

echo <<<_END
<script>

//keep track of number of rows of choices
function numRows(num)
{
	num_row = num;
}
		
//insert row to table
function insertRow()
{	
	var table = document.getElementById('choices');
	var row = table.insertRow(num_row);
	var cell1 = row.insertCell(0);
	var cell2 = row.insertCell(1);
	cell1.innerHTML = "<input type='text' maxlength='30' name='choices[]' />";
	cell2.innerHTML = "<input type='button' value='Delete' onclick='deleteRow(this);'>";
	num_row++;
}
		
//delete row from table
function deleteRow(row)
{
	while(row.parentNode && row.tagName.toLowerCase() != 'tr')
		row = row.parentNode;

	if(row.parentNode)
	{
		row.parentNode.removeChild(row);
		num_row--;
	}
}
		
</script>
_END;

//set variables to empty
$error = $poll_title = $is_public = $anonymous = $comments_disabled = "";
$choices = array();

echo <<<_END
<body onload="numRows('3')">
<h1>Create Poll</h1>$error

<!-- Start Poll Creation Form -->
<form method='post' action='create.php'>

<!-- Public/Private radio buttons -->
<input type='radio' name='is_public' value='yes' required>Public
<input type='radio' name='is_public' value='no'>Private<br>

<!-- Checkboxes for anonymous and comments -->
<input type='checkbox' name='anonymous' value='yes'> Anonymous<br>
<input type='checkbox' name='comments_disabled' value='yes'> Comments disabled<br><br>

<!-- Title -->
Title <input type='text' maxlength='30' name='poll_title' required>

<!-- Choices -->
<table id='choices'>
<tr><th>Choices</th></tr>
<tr><td><input type='text' maxlenght='30' name='choices[]'></td>
	<td><input type='button' value='Delete' onclick='deleteRow(this)'></td></tr>
<tr><td><input type='text' maxlenght='30' name='choices[]'></td>
	<td><input type='button' value='Delete' onclick='deleteRow(this)'></td></tr>
<tr><td><input type='button' value='Add Choice' onclick='insertRow()'></td></tr>
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