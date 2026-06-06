<?php
$dtdemo="/start|/tools|";
for ($x=0;$x<count(explode("|",$dtdemo));$x++) {
	$kirim=kirimtext("6289633033332",explode("|",$dtdemo)[$x]);
	echo $kirim."\n";
	sleep(2);
}

function kirimtext($nomor,$pesan) {
//	$nomor="6289633033332";
	$reqParams = [
		'token' => capi(2),
		'url' => 'https://api.kirimwa.id/v1/messages',
		'method' => 'POST',
		'payload' => json_encode(
			[
				'message' => $pesan,
				'phone_number' => $nomor,
				'message_type' => 'text',
				'device_id' => capi(0)
			]
		)
	];
	try {
		$response = apiKirimWaRequest($reqParams);
		$status=$response['body'];
	} catch (Exception $e) {
		$status = $e;
	}	
	$kk=logcore($status,$nomor,$pesan);
	return (0);
}

function capi($cari) {
	$file 	= 'wahookk.php';
	$hasil	= trim(explode("|",file_get_contents($file))[$cari]);
	return $hasil;
}

function apiKirimWaRequest(array $params) {
  $httpStreamOptions = [
    'method' => $params['method'] ?? 'GET',
    'header' => [
      'Content-Type: application/json',
      'Authorization: Bearer ' . ($params['token'] ?? '')
    ],
    'timeout' => 15,
    'ignore_errors' => true
  ];

  if ($httpStreamOptions['method'] === 'POST') {
    $httpStreamOptions['header'][] = sprintf('Content-Length: %d', strlen($params['payload'] ?? ''));
    $httpStreamOptions['content'] = $params['payload'];
  }

  // Join the headers using CRLF
  $httpStreamOptions['header'] = implode("\r\n", $httpStreamOptions['header']) . "\r\n";

  $stream = stream_context_create(['http' => $httpStreamOptions]);
  $response = file_get_contents($params['url'], false, $stream);

  // Headers response are created magically and injected into
  // variable named $http_response_header
  $httpStatus = $http_response_header[0];

  preg_match('#HTTP/[\d\.]+\s(\d{3})#i', $httpStatus, $matches);

  if (! isset($matches[1])) {
    throw new Exception('Can not fetch HTTP response header.');
  }

  $statusCode = (int)$matches[1];
  if ($statusCode >= 200 && $statusCode < 300) {
    return ['body' => $response, 'statusCode' => $statusCode, 'headers' => $http_response_header];
  }

  throw new Exception($response, $statusCode);
}
function logcore($lig,$nm,$psn) {
	$file="corewamix.log";
	file_put_contents($file, "Send Mpwa : \n".$nm."\n".$psn."\n".ltrim($lig)."#\n".str_repeat("=",100)."\n\n", FILE_APPEND | LOCK_EX);
}

?>