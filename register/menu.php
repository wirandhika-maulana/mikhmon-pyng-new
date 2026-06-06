<?php
// hide all error
//error_reporting(0);

	$btnmenuactive = "font-weight: bold;background-color: #f9f9f9; color: #000000";
	if ($id == "register" ) {
		$shome = "active";
		$mpage = "Registrasi";
		$ssk = "menu-open";


if($idleto != "disable"){
	$idleto = 'display:block;';
}else{
	$idleto = 'display:none;';
}
?>
<span style="display:none;" id="idto"><?= $idleto ;?></span>
<div id="navbar" class="navbar">
  <div class="navbar-left">
    <a id="brand" class="text-center" href="javascript:void(0)">MIKHMON</a>

<a id="openNav" class="navbar-hover" href="javascript:void(0)"><i class="fa fa-bars"></i></a>
<a id="closeNav" class="navbar-hover" href="javascript:void(0)"><i class="fa fa-bars"></i></a>
<a id="cpage" class="navbar-left" href="javascript:void(0)"><?= $mpage; ?></a>
</div>
</div>

<div id="notify"><div class="message"></div></div>
<div id="temp"></div>


<div id="notify"><div class="message"></div></div>
<div id="temp"></div>

<div id="main">  
<div id="loading" class="lds-dual-ring"></div>
<?php 
 echo '<div class="main-container" style="display:none">';
?>

