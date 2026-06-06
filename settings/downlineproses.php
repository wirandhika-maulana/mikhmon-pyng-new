<?php
$func1="settings/function1.php";
if (file_exists($func1)) {
	include $func1;
	deleteuser90($deletedownline);
	echo "<script>window.location='./admin.php?id=downline'</script>";
}
