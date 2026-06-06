<?php

function sendMSG($number, $msg){
   $url = "https://apiwa.mimoassist.homes/api/send-message";

    // Data to be sent in the POST request
    $data = [
        "phone" => $number,
        "message" => $msg
    ];

    // Initialize cURL session
    $curl = curl_init();

    // Set cURL options for POST request
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data)); // Send the data in URL-encoded format
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    // Execute the request and get the response
    $response = curl_exec($curl);

    // Check for cURL errors
    if (curl_errno($curl)) {
        echo 'Curl error: ' . curl_error($curl);
        return false;
    }

    // Close the cURL session
    curl_close($curl);

    // Decode the JSON response
    return json_decode($response, true);
}
function phonewa($nomor){
if (substr($nomor, 0, 1) === '0') {
        // Ganti 0 dengan 62
        $nomorBaru = '62' . substr($nomor, 1);
        return $nomorBaru;
    }
    // Jika tidak dimulai dengan 0, kembalikan nomor asli
    return $nomor;
}
?>