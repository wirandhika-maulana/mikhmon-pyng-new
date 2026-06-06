<?php
$func="./settings/function.php";
if (file_exists($func)) {
	include_once $func;
	if (!empty($deletedownline)!==false) {
		$pro=deleteuser($deletedownline);
	}
}
