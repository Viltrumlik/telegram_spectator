<?php
header("Content-Type: application/json; charset=UTF-8");
header('origin: https://t.me/viltrumlik');
header('created-by: @viltrumlik');

$city = $_GET['city'];

$url = "https://obhavo.uz/$city";
$html = file_get_contents($url);

$dom = new DOMDocument;
@$dom->loadHTML($html);
$xpath = new DOMXPath($dom);

$city_name = $xpath->query("//div[@class='padd-block']//h2");
$date = $xpath->query("//div[@class='current-day']");
$air = $xpath->query("//div[@class='current-forecast-desc']");
$harorat = $xpath->query("//div[@class='current-forecast']/span");
$details = $xpath->query("//div[@class='current-forecast-details']//p");
$days = $xpath->query("//div[@class='current-forecast-day']//p");

$city_name = $city_name->item(0)->textContent;
$date = $date->item(0)->textContent;
$air = $air->item(0)->textContent;
$current_temp = $harorat->item(1)->textContent;
$min_temp = $harorat->item(2)->textContent;

$humidity = str_replace('Namlik: ', '', $details->item(0)->textContent);
$wind = str_replace('Shamol: ', '', $details->item(1)->textContent);
$pressure = str_replace('Bosim: ', '', $details->item(2)->textContent);
$moon = str_replace('Oy: ', '', $details->item(3)->textContent);
$sunrise = str_replace('Quyosh chiqishi: ', '', $details->item(4)->textContent);
$sunset = str_replace('Quyosh botishi: ', '', $details->item(5)->textContent);

$morning = $days->item(2)->textContent;
$day = $days->item(5)->textContent;
$evening = $days->item(8)->textContent;

$data = array(
    'bugun' => [
        'shahar' => $city_name,
        'sana' => $date,
        'havo' => $air,
        'harorat' => "$current_temp - $min_temp",
        'namlik' => $humidity,
        'shamol' => $wind,
        'bosim' => $pressure,
    ],
    'vaqt' => [
        'oy' => $moon,
        'quyosh_chiqishi' => $sunrise,
        'quyosh_botishi' => $sunset,
    ],
    'harorat' => [
        'tong' => $morning,
        'kun' => $day,
        'oqshom' => $evening,
    ]
);

$hasNull = false;
foreach ($data as $section) {
    foreach ($section as $value) {
        if (empty($value)) {
            $hasNull = true;
            break 2;
        }
    }
}

if ($hasNull) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'error_message' => "Ma'lumotlar to'liq emas yoki mavjud emas!"
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} else {
    http_response_code(200);
    echo json_encode([
        'success' => true, 
        'data' => $data
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}

?>