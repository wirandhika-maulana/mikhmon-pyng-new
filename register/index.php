<?php
error_reporting(0);

ob_start("ob_gzhandler");

include('../lang/isocodelang.php');
include('../include/lang.php');
include('../lang/en.php');


include('../include/theme.php');
include('../settings/settheme.php');
include('../settings/setlang.php');


$theme = $theme;
$themecolor = $themecolor;
$idleto = 'display:block;';
$idleto = 'display:none;';

$id=$_GET['id'];
if ($id=='register') {
	$fdata="trxdump.txt";
	if (file_exists($fdata)) {
		$pesan = file_get_contents($fdata);
		unlink ($fdata);
	}
	$mpage="Mikhmon New Account";
    include_once('register.php');
}elseif ($id=='renew') {
	$fdata="trxdump.txt";
	if (file_exists($fdata)) {
		$pesan = file_get_contents($fdata);
		unlink ($fdata);
	}
	$mpage="Mikhmon Renew";
    include_once('renew.php');
}elseif ($id=='renew') {
	$fdata="trxdump.txt";
	if (file_exists($fdata)) {
		$pesan = file_get_contents($fdata);
		unlink ($fdata);
	}
	$mpage="Mikhmon New Account";
    include_once('register.php');
}elseif ($id=='error') {
	$fdata="trxdump.txt";
	if (file_exists($fdata)) {
		$pesan = file_get_contents($fdata);
		unlink ($fdata);
	}
	$pesan=$_GET['pesan'];
	$mpage="Mikhmon Registrasi, error";
    include_once('error.php');
}else{
	echo "<script>window.location='../admin.php?id='</script>";
}
?>