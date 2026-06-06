<?php 
// Include file koneksi database
require('db_connection.php');

if (isset($_POST['kirim'])) {
$profile = $_POST['profile'];
$merchantRef = $_POST['vc'];
$method = $_POST['rek'];
$amount = $_POST['saldo'];
$mac = $_POST['mac'];
$email = $_POST['email'];
$nama = $_POST['nama'];
$phone = $_POST['phone'];
$query = "SELECT * FROM transaction WHERE mac='$mac' AND status='UNPAID'";
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
    'return_url'   => 'http://'.$dns.'/login?dst=&username='.$merchantRef.'&password='.$merchantRef,

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
                $db->query("INSERT INTO transaction(reference, merchant_ref, profile, payment_method, amount, checkout_url, status, mac, customer_name, customer_email, customer_phone) VALUES('$reference', '$merchantRef', '$profile', '$method', '$amount', '$payment_url', '$status', '$mac', '$customer_name', '$customer_email', '$customer_phone')");
                //$db->query("UPDATE transaction SET status = 'PAID' WHERE  merchant_ref = {$invoice->merchant_ref}")) 
                header('Location: ' . $payment_url);
                exit;
           echo empty($error) ? $response : $error;
        } else {
            // Pembayaran gagal
            $error_message = $json_response['message'];
            echo "Pembayaran gagal. Pesan error: $error_message";
        }
    } else {
        // Terjadi kesalahan saat koneksi
        echo "Terjadi kesalahan saat melakukan koneksi: $error";
    }
} else {
header('Location: ' . $ceklink);    
}
}

?>

