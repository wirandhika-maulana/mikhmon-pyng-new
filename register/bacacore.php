<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

function bacaLogBayar($logFilePath, $outputFilePath) {
    if (!file_exists($logFilePath)) {
        die("File logbayar.log tidak ditemukan.");
    }

    $logData = file($logFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $outputData = "";

    foreach ($logData as $line) {
        echo "Processing line: $line\n"; // Debugging
        $jsonData = json_decode($line, true);
        
        if ($jsonData && isset($jsonData['data'])) {
            $data = $jsonData['data'];
            $formattedLine = sprintf(
                "%s|{\"reference\":\"%s\",\"merchant_ref\":\"%s\",\"payment_method_code\":\"%s\",\"payment_method\":\"%s\",\"total_amount\":%d,\"fee_merchant\":%d,\"fee_customer\":%d,\"amount_received\":%d,\"status\":\"%s\"}#\n",
                date('Y-m-d'),
                $data['reference'],
                $data['merchant_ref'],
                $data['payment_selection_type'],
                $data['payment_method'],
                $data['amount'],
                $data['fee_merchant'],
                $data['fee_customer'],
                $data['amount_received'],
                $data['status']
            );

            $outputData .= $formattedLine;
        } else {
            echo "Invalid JSON or missing 'data': $line\n"; // Debugging
        }
    }

    // Debugging: tampilkan output data sebelum ditulis
    echo "Output data:\n$outputData\n";

    file_put_contents($outputFilePath, $outputData);
    echo "Data berhasil ditulis ke $outputFilePath";
}

$logFilePath = './register/logbayar.log'; // Ganti dengan path absolut
$outputFilePath = '/tripay/dtdump.txt'; // Ganti dengan path absolut

bacaLogBayar($logFilePath, $outputFilePath);
?>