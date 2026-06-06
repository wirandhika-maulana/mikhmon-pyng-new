<?php

// Include file koneksi database
require('db_connection.php');
include('routeros_api.class.php');
include('./beli/function.php');

$API = new routeros_api();
$API->debug = false;
if($API->connect($ip_mikrotik, $user_mikrotik, $password_mikrotik, $mikrotik_port)){    
$json = file_get_contents('php://input');

// Ambil callback signature
$callbackSignature = isset($_SERVER['HTTP_X_CALLBACK_SIGNATURE']) ? $_SERVER['HTTP_X_CALLBACK_SIGNATURE']: '';

// Generate signature untuk dicocokkan dengan X-Callback-Signature
$signature = hash_hmac('sha256', $json, $privateKey);

// Validasi signature
if ($callbackSignature !== $signature) {
    exit(json_encode([
        'success' => false,
        'message' => 'Invalid signaturer'.$signature,
    ]));
}

$data = json_decode($json);

if (JSON_ERROR_NONE !== json_last_error()) {
    exit(json_encode([
        'success' => false,
        'message' => 'Invalid data sent by payment gateway',
    ]));
}

// Hentikan proses jika callback event-nya bukan payment_status
if ('payment_status' !== $_SERVER['HTTP_X_CALLBACK_EVENT']) {
    exit(json_encode([
        'success' => false,
        'message' => 'Unrecognized callback event: ' . $_SERVER['HTTP_X_CALLBACK_EVENT'],
    ]));
}
$invoiceId = $db->real_escape_string($data->merchant_ref);
$tripayReference = $db->real_escape_string($data->reference);
$status = strtoupper((string) $data->status);

$query = "SELECT * FROM transaction WHERE merchant_ref='{$invoiceId}'";
$q = $db->query($query);
if ($q === false) {
    // Handle query error
    die("Database query failed: " . $db->error);
}
$row = $q->fetch_assoc();
$profile = isset($row['profile']) ? $row['profile'] : null;
$phone  = isset($row['customer_phone']) ? $row['customer_phone'] : null;
$pesan  = "Terima kasih sudah memilih voucher hotspot kami. \n\n Kode voucher anda *$invoiceId* telah aktif. \n\n Silahkan Login di : \n http://$dns/login?dst=&username=$merchantRef&password=$merchantRef \n\n Jika anda ingin mengganti voucher, silahkan hubungi hotspot kami. \n\n Terima kasih atas kepercayaan anda terhadap kami! \n\nSalam,";
$pesan1 = "Mohon maaf, \n\n pembayaran Kode voucher anda *$invoiceId* gagal. \n\n Karana sudah melewati batas waktu dan transaksinya otomatis dibatalkan. \n\n Silahkan lakukan pembelian ulang. \n\n Salam,";
$pesan2 = "";
if ($data->is_closed_payment === 1) {
    $result = $db->query("SELECT * FROM transaction WHERE merchant_ref = '{$invoiceId}' AND reference = '{$tripayReference}' AND status = 'UNPAID' LIMIT 1");
    if (! $result) {
        exit(json_encode([
            'success' => false,
            'message' => 'Invoice not found or already paid:',
        ]));
    }

while ($invoice = $result->fetch_object()) {
        switch ($status) {
            // handle status PAID
            case 'PAID':
            sendMSG($phone, $pesan);
            $API->comm("/ip/hotspot/user/add", array(
			"server"		=> "all",
			"profile"		=> "$profile",
			"name"     		=> "$invoiceId",
			"password"		=> "$invoiceId",
			"comment"       => "vc-online",
			));
                if (! $db->query("UPDATE transaction SET status = 'PAID' WHERE  merchant_ref = {$invoice->merchant_ref}")) {
            
                    exit(json_encode([
                        'success' => false,
                        'message' => $db->error,
                    ]));
                }
                break;

            // handle status EXPIRED
            case 'EXPIRED':
                sendMSG($phone, $pesan1);
                if (! $db->query("UPDATE transaction SET status = 'EXPIRED' WHERE merchant_ref = {$invoice->merchant_ref}")) {
                    exit(json_encode([
                        'success' => false,
                        'message' => $db->error,
                    ]));
                }
                break;

            // handle status FAILED
            case 'FAILED':
                if (! $db->query("UPDATE transaction SET status = 'UNPAID' WHERE merchant_ref = {$invoice->merchant_ref}")) {
                    exit(json_encode([
                        'success' => false,
                        'message' => $db->error,
                    ]));
                }
                break;

            default:
                exit(json_encode([
                    'success' => false,
                    'message' => 'Unrecognized payment status',
                ]));
        }

        exit(json_encode(['success' => true]));
    }
}
print_r($data);

} else {
exit(json_encode([
                        'success' => false,
                        'message' => "Mikrotik Offline",
                    ]));    
}
?>