<?php
require_once __DIR__ . '/../config.php';
header("Content-Type: application/json; charset=UTF-8");
$text = $_GET['text'];
$from = $_GET['from'];
$to = $_GET['to'];

$url = 'https://web-api.itranslateapp.com/v3/texts/translate';

$data = array(
    'source' => array(
        'dialect' => $from,
        'text' => $text,
        'with' => array('synonyms')
    ),
    'target' => array(
        'dialect' => $to
    )
);

$headers = array(
    'Accept: application/json',
    'Content-Type: application/json',
    'API-KEY: ' . TRANSLATE_API_KEY
);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$data =json_decode($response);

if ($response === false) {
    echo 'Error: ' . curl_error($ch);
}

$result['result']="Topilmadi!";
$result['dasturchi']="@viltrumlik";

if($text == null or $from == null or $to == null){
echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}else{
echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}

curl_close($ch);
?>
