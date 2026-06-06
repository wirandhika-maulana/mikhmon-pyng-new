<?php
	require_once ('system.database.php');
	$settings=getsettings();
	global $settings;
	$identitiy 			=$settings["Nama_router"];
	$mikrotik_ip 		=$settings["IP_router"];
	$mikrotik_username	=$settings["Username_router"];
	$mikrotik_password	=decrypturl($settings["Pass_router"]);
	$mikrotik_port 		=$settings["Port"];
    $dnsname			=$settings["dnsname"];	
	$Name_router 		=$settings["Nama_router"];
	$owner 				=$settings["Owner"];
	$id_own 			=$settings["Id_owner"];
	$token 				=$settings["Token_bot"];
	$usernamebot 		=$settings["Username_bot"];
	$voucher_1			=$settings["Voucher_1"];
	$Voucher_nonsaldo	=$settings["Voucher_nonsaldo"];
	$lastupdate       	=$settings["Tanggal_diubah"];

	$apikirimwaid		= "Ih9Ta2pyMYVnSP8RqBY/YBEV8PfzEUjZc0gNdLTj64VjdF+KuUputzRe6yWbLGt1-septiyan";
	$device				= "mimoassist";
	$email				= "ferisgaming76@gmail.com";
	
//silahkan diedit	
	$nowa				= "nowaanda ";
	$logo				= "https://mikhmon.mimoassist.homes/webhook/img/logobot1.png";  //wajib https://
	$promo				= "Belum ada promo untuk saat ini,..";
	$rekening			 ="*Nomer Rekening TOP UP.*\n\n";
	$rekening			.="BRI 604.301.011.021.50.4\nA/N Prada Santika Prameswari Wicaksono\n\n";
	$rekening			.="DANA 08966809000\nA/N Irawan Akbar Maulana\n\n";
//sampai sini saja

	$rekening		.="Terimakasih dan Selamat ".sapaan();
	