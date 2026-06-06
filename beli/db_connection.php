<?php

// Konfigurasi koneksi database
$servername = "localhost"; // Ganti dengan nama server database Anda
$username = "mimoassi_callback"; // Ganti dengan nama pengguna database Anda
$password = "mimoassi_callback"; // Ganti dengan kata sandi database Anda
$database = "mimoassi_callback"; // Ganti dengan nama database Anda

// Buat koneksi
$db = new mysqli($servername, $username, $password, $database);

// Periksa koneksi
if ($db->connect_error) {
    die("Koneksi database gagal: " . $db->connect_error);
}
//ENDPOINT
//Sandbox URL 	https://tripay.co.id/api-sandbox/transaction/create
//Production URL 	https://tripay.co.id/api/transaction/create

$Endpoint     = 'https://tripay.co.id/api-sandbox/transaction/'; 
$apiKey       = 'DEV-2LVXLmt8UbAMtdGr20b7IUcOwaFB1TPCT7MqM83Y';
$privateKey   = 'NVsmc-tIVrZ-j724L-Pex6A-fBbim';
$merchantCode = 'T19855';

$dns = 'login.mimoassist.my.id';
$user_mikrotik = "@Rasendria001";
$password_mikrotik = "@Rasendria001";
$ip_mikrotik = "id-45.hostddns.us"; //vpn remote member.labkom.co.id
$mikrotik_port = "7522";

date_default_timezone_set('Asia/Jakarta');

?>
