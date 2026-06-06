<?php
date_default_timezone_set('Asia/Jakarta');

include 'system.config.php';

/*
$token	= '7570046381:AAHHagnrH6s2m7GjedqbD3JQO_wu02kBMBg';
$pesan	= date('d-M-Y H:i:s')."\n.";
$option = [	'text' => $pesan,'chat_id' => '1341792914','parse_mode' => 'html',];
$respone=file_get_contents("https://api.telegram.org/bot$token/sendMessage?" . http_build_query($option) );
*/


function deleteuser90($id) {
	$hasil="";

	global $mikbotamdata;

	$datareseller = $mikbotamdata->delete('re_settings', [

		'id_user' => $id

	]);
	$deletoperating = $mikbotamdata->delete('re_operating', [

		'id_user' => $id

	]);
	$deletlaporan = $mikbotamdata->delete('st_reportdata', [

		'id_user' => $id

	]);

	return $hasil;
}

