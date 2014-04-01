<?php //functions.php
//useful functions!

//sanitizes string for security purposes on inputs
function sanitizeString($var)
{
	$var = trim($var);
	$var = stripslashes($var);
	$var = htmlspecialchars($var);

	return $var;
}
?>