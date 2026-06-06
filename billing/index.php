<?php
if (isset($_POST['kirim'])) {
include('/lib/routeros_api.class.php');

setcookie("message","delete",time()-1);
$API = new routeros_api();
$API->debug = false;    
if($API->connect($ip_mikrotik, $user_mikrotik, $password_mikrotik, $mikrotik_port)){ 
    $merchantRef = $_POST['username']; 
    $profile = $_POST['paket'];
	try {
	$ceksecret = $API->comm('/ppp/secret/print',array(
			 "?name"     		=> $merchantRef,
			 "?profile"     		=> "ISOLIR",
			));
	$cekprofile = $API->comm('/ppp/profile/print',array(
			 "?name"     		=> $profile,
			));	
	if(count($ceksecret)>0){

$method = "DANA";
$amount = $cekprofile[0]['comment'];
$email = "yayan@labkom.co.id"; //ganti
$nama = "Yayan Sopyan"; //ganti
$phone = "089603586107"; //ganti
$query = "SELECT * FROM transaction WHERE merchant_ref='$merchantRef' AND profile='$profile' AND status='UNPAID'";
$q = $db->query($query);
if ($q === false) {
    // Handle query error
    die("Database query failed: " . $db->error);
    
}

$row = $q->fetch_assoc();
$ceklink = isset($row['checkout_url']) ? $row['checkout_url'] : null;
$cekdata = $q->num_rows;

if($cekdata==null){
$data = [
    'method'         => $method,
    'merchant_ref'   => $merchantRef,
    'amount'         => $amount,
    'customer_name'  => $nama,
    'customer_email' => $email,
    'customer_phone' => $phone,
    'order_items'    => [
        [
            'sku'         => $merchantRef,
            'name'        => "$profile-$merchantRef",
            'price'       => $amount,
            'quantity'    => 1,
        ],

    ],
    'return_url'   => 'http://mikhmon.mimoassist.homes',

    'expired_time' => (time() + (24 * 60 * 60)), // 24 jam
    'signature'    => hash_hmac('sha256', $merchantCode.$merchantRef.$amount, $privateKey)
];

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_FRESH_CONNECT  => true,
    CURLOPT_URL            => "$Endpoint./create",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER         => false,
    CURLOPT_HTTPHEADER     => ['Authorization: Bearer '.$apiKey],
    CURLOPT_FAILONERROR    => false,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => http_build_query($data),
    CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4
]);

$response = curl_exec($curl);
$invoiceData = json_decode($response, true);

curl_close($curl);

if (empty($error)) {
        $json_response = json_decode($response, true);
        // Proses respons dari Tripay API
        $payment_url = $json_response['data']['checkout_url'];
        $reference = $json_response['data']['reference'];
        $status = $json_response['data']['status'];
        $customer_name = $json_response['data']['customer_name'];
        $customer_email = $json_response['data']['customer_email'];
        $customer_phone = $json_response['data']['customer_phone'];
        if ($json_response['success']) {
            // Pembayaran berhasil
                $db->query("INSERT INTO transaction(reference, merchant_ref, profile, payment_method, amount, checkout_url, status, customer_name, customer_email, customer_phone) VALUES('$reference', '$merchantRef', '$profile', '$method', '$amount', '$payment_url', '$status', '$customer_name', '$customer_email', '$customer_phone')");
                header('Location: ' . $payment_url);
                exit;
           echo empty($error) ? $response : $error;
        } else {
            // Pembayaran gagal
            $error_message = $json_response['message'];
            setcookie("message", "Pembayaran gagal. Pesan error: $error_message", time()+5);  
            header('Location: ' . $base_url);
        }
    } else {
        // Terjadi kesalahan saat koneksi
            setcookie("message", "Pembayaran gagal. Pesan error: $error", time()+5);  
            header('Location: ' . $base_url);

    }
} else {
header('Location: ' . $ceklink);    
}	    

} else {
setcookie("message", "ID Pelanggan tidak ditemukan", time()+5);  
header('Location: ' . $base_url);
}
	$API->disconnect();
	} 
	catch (Exception $ex) {
setcookie("message", "Caught exception from router: " . $ex->getMessage() . "\n", time()+5); 
header('Location: ' . $base_url);
	}	
 


} else {
setcookie("message", "VPN Offline atau router tidak konek", time()+5);  
header('Location: ' . $base_url);    
}
}  
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Internet</title>
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    
    <link rel="shortcut icon" href="assets/images/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="assets/css/app.css">
</head>

<body>
    <div id="auth">
        
<div class="container">
    <div class="row">
        <div class="col-md-5 col-sm-12 me-2">
            <div class="card py-4">
                <div class="card-body">
                    <div class="text-center mb-5">
                        <img src="assets/images/favicon.svg" height="48" class='mb-4'>
                        <h3>Billing</h3>
                        <p>Masukkan ID Pelanggan Anda</p>
                    </div>
                    <form action="" method="POST">
                        <div class="form-group">
                        <label for="first-name-vertical">ID Pelanggan</label>    
                            <label for="first-name-column"></label>
                            <input type="text" class="form-control" name="username" placeholder="ID Pelanggan">
                        <div class="form-group">
                        <label for="first-name-vertical">Paket Langganan</label>
                                        <select class="form-select" name="paket">
                                            <option value="3Mbps-100rb">3Mbps-100rb</option>
                                            <option value="5Mbps-150rb">5Mbps-150rb</option>
                                            <option value="10Mbps-200rb">10Mbps-200rb</option>
                                        </select>
                        </div>            
                        </div>
                    <?php 
					// Cek apakah terdapat cookie dengan nama message
					if(isset($_COOKIE["message"])){ // Jika ada
						?>
						 <div class="alert alert-danger">
							<?php
							// Tampilkan pesannya
							echo $_COOKIE["message"];
              
							?>
						</div>
						<?php
					}
					?>

                        <div class="clearfix">
                            <button class="btn btn-primary float-right" name="kirim">Bayar sekarang</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

    </div>
    <!--<script src="assets/js/feather-icons/feather.min.js"></script>
    <script src="assets/js/app.js"></script>
    
    <script src="assets/js/main.js"></script>-->
</body>

</html>
