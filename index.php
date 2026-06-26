<?php
ob_start();
session_start();
error_reporting(0);
date_Default_timezone_set('Asia/Tashkent');

require_once __DIR__ . '/config.php';
$admin = 7990053633;
$admin_user = "viltrumlik";

function areBusinessUpdatesAllowed($token) {
    $response = file_get_contents("https://api.telegram.org/bot$token/getWebhookInfo");
    if (!$response) {
        return false;
    }

    $data = json_decode($response, true);

    if (!isset($data['result']['allowed_updates']) || !is_array($data['result']['allowed_updates'])) {
        return false;
    }

    $allowedUpdates = $data['result']['allowed_updates'];

    $requiredUpdates = [
        "business_connection",
        "business_message",
        "edited_business_message",
        "deleted_business_messages"
    ];

    foreach ($requiredUpdates as $update) {
        if (!in_array($update, $allowedUpdates)) {
            return false;
        }
    }

    return true;
}

function deleteFolder($path)
{
    if (is_dir($path) === true) {
        $files = array_diff(scandir($path), array('.', '..'));
        foreach ($files as $file)
            deleteFolder(realpath($path) . '/' . $file);
        return rmdir($path);
    } else if (is_file($path) === true)
        return unlink($path);
    return false;
}

function getButtonText($reply_markup, $callback_data)
{
    $inline_keyboard = json_decode($reply_markup, true)['inline_keyboard'];
    foreach ($inline_keyboard as $row) {
        foreach ($row as $button) {
            if ($button['callback_data'] === $callback_data) {
                return $button['text'];
                break 2;
            }
        }
    }
}

function ping($token, $chat_id, $text)
{
    $startTime = microtime(true);

    $url = "https://api.telegram.org/bot$botToken/sendMessage";
    $postData = [
        'chat_id' => $chat_id,
        'text' => $text
    ];

    $options = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-type: application/x-www-form-urlencoded',
            'content' => http_build_query($postData),
        ],
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    $endTime = microtime(true);
    $responseTime = ($endTime - $startTime) * 1000;

    return number_format($responseTime, 2, '.', '');
    ;
}

function htmlCheck($text)
{
    if (substr_count($text, '<tg-emoji') !== substr_count($text, '</tg-emoji>')) {
        return false;
    }

    preg_match_all('/<tg-emoji([^>]*)>/', $text, $matches);
    foreach ($matches[1] as $attrString) {
        if (!preg_match('/emoji-id\s*=\s*[\'"][0-9]+[\'"]/', $attrString)) {
            return false;
        }
    }

    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    $converted = mb_convert_encoding($text, 'HTML-ENTITIES', 'UTF-8');

    $converted = preg_replace('/<tg-emoji([^>]*)>/', '<span tg-emoji$1>', $converted);
    $converted = preg_replace('/<\/tg-emoji>/', '</span>', $converted);

    @$doc->loadHTML('<!DOCTYPE html><html><body>' . $converted . '</body></html>');
    $errors = libxml_get_errors();
    libxml_clear_errors();

    foreach ($errors as $error) {
        $msg = strtolower($error->message);
        if (strpos($msg, 'tag tg-emoji') !== false)
            continue;
        if (strpos($msg, 'attribute') !== false && strpos($msg, 'tg-emoji') !== false)
            continue;
        return false;
    }

    return true;
}



function download($file_id, $ext)
{
    $get = bot('getFile', ['file_id' => $file_id]);
    $path = $get->result->file_path;
    $local = 'data/media/' . time() . $ext;
    $ch = curl_init("https://api.telegram.org/file/bot" . BOT_TOKEN . "/$path");
    $fp = fopen($local, 'wb');
    curl_setopt_array($ch, [CURLOPT_FILE => $fp, CURLOPT_FOLLOWLOCATION => true]);
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
    return $local;
}

function clear_media_folder($path = 'data/media')
{
    if (is_dir($path)) {
        $files = glob($path . '/*');
        foreach ($files as $file) {
            if (is_file($file))
                unlink($file);
        }
    }
}

function bot($method, $data = [])
{
    $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/" . $method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $res = curl_exec($ch);
    if (curl_error($ch)) {
        var_dump(curl_error($ch));
    } else {
        return json_decode($res);
    }
}

$alijonov = json_decode(file_get_contents('php://input'));

// Acknowledge Telegram immediately so it does not retry the update
// (prevents duplicate processing / repeated "ulandi" messages).
ignore_user_abort(true);
if (function_exists('fastcgi_finish_request')) {
    http_response_code(200);
    fastcgi_finish_request();
}

$message = $alijonov->message;
$cid = $message->chat->id;
$name = $message->chat->first_name;
$mid = $message->message_id;
$type = $message->chat->type;
$text = $message->text;
$fid = $message->from->id;
$name = $message->from->first_name;
$familya = $message->from->last_name;
$premium = $message->from->is_premium;
$bio = $message->from->about;
$businessname = $message->from->username;
$chat_id = $message->chat->id;
$message_id = $message->message_id;
$reply = $message->reply_to_message->text;
$nameru = "<a href='tg://user?id=$fid'>$name $familya</a>";

$data = $alijonov->callback_query->data;
$qid = $alijonov->callback_query->id;
$id = $alijonov->inline_query->id;
$query = $alijonov->inline_query->query;
$query_id = $alijonov->inline_query->from->id;
$business_id2 = $alijonov->callback_query->message->business_connection_id;
$cid2 = $alijonov->callback_query->message->chat->id;
$mid2 = $alijonov->callback_query->message->message_id;
$callfrid = $alijonov->callback_query->from->id;
$callname = $alijonov->callback_query->from->first_name;
$calluser = $alijonov->callback_query->from->username;
$surname = $alijonov->callback_query->from->last_name;
$about = $alijonov->callback_query->from->about;
$nameuz = "<a href='tg://user?id=$callfrid'>$callname $surname</a>";

$edit = $alijonov->edited_message;
$e_text = $edit->text;
$e_cid = $edit->chat->id;
$e_mid = $edit->message_id;

$connection = $alijonov->business_connection;
$business_connection = $connection->is_enabled;
$business_connection_id = $connection->user->id;

$edited_message = $alijonov->edited_business_message;
$edited_id = $edited_message->business_connection_id;
$edited_message_id = $edited_message->message_id;
$edited_message_cid = $edited_message->chat->id;
$edited_message_fid = $edited_message->from->id;
$edited_message_name = $edited_message->chat->first_name;
$edited_message_lastname = $edited_message->chat->last_name;
$edited_message_username = $edited_message->chat->username;

$delete_message = $alijonov->deleted_business_messages;
$delete_id = $delete_message->business_connection_id;
$delete_message_cid = $delete_message->chat->id;
$delete_message_fid = $delete_message->from->id;
$delete_message_mids = $delete_message->message_ids;
$delete_message_name = $delete_message->chat->first_name;
$delete_message_lastname = $delete_message->chat->last_name;
$delete_message_username = $delete_message->chat->username;

$business = $alijonov->business_message;
$business_text = $business->text;
$business_id = $business->business_connection_id;

$business_chat_id = $business->chat->id;
$business_from_id = $business->from->id;
$business_message_id = $business->message_id;
$business_name = $business->from->first_name;
$business_lastname = $business->from->last_name;
$business_username = $business->from->username;
$business_reply_to_message_id = $business->reply_to_message->message_id;
$business_date = $business->date;

$business_sticker = $business->sticker;
$business_animation = $business->animation;
$business_photo = $business->photo;
$business_video = $business->video;
$business_video_note = $business->video_note;
$business_audio = $business->audio;
$business_voice = $business->voice;
$business_document = $business->document;
$business_caption = $business->caption;

$SERVER_NAME = ($_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://" . $_SERVER['SERVER_NAME'] . dirname($_SERVER['SCRIPT_NAME']) . '/';

if (isset($business)) {
    $fileName = "data/history/{$business_chat_id}.json";

    $media = [];

    if (isset($business_photo)) {
        $media[] = [
            'type' => 'photo',
            'file_id' => end($business_photo)->file_id,
            'caption' => $business_caption
        ];
    }

    if (isset($business_video)) {
        $media[] = [
            'type' => 'video',
            'file_id' => $business_video->file_id,
            'caption' => $business_caption
        ];
    }

    if (isset($business_video_note)) {
        $media[] = [
            'type' => 'video_note',
            'file_id' => $business_video_note->file_id,
            'caption' => null,
        ];
    }

    if (isset($business_audio)) {
        $media[] = [
            'type' => 'audio',
            'file_id' => $business_audio->file_id,
            'caption' => $business_caption
        ];
    }

    if (isset($business_voice)) {
        $media[] = [
            'type' => 'voice',
            'file_id' => $business_voice->file_id,
            'caption' => $business_caption
        ];
    }

    if (isset($business_document)) {
        $media[] = [
            'type' => 'document',
            'file_id' => $business_document->file_id,
            'caption' => $business_caption
        ];
    }

    if (isset($business_sticker)) {
        $media[] = [
            'type' => 'sticker',
            'file_id' => $business_sticker->file_id,
            'caption' => null,
        ];
    }

    $newMessage = [
        'user_id' => $business_from_id,
        'message_id' => $business_message_id,
        'reply_to_message_id' => $business_reply_to_message_id,
        'username' => $business_username,
        'first_name' => $business_name,
        'last_name' => $business_lastname,
        'text' => $business_text,
        'date' => date("Y-m-d H:i:s", $business_date),
        'edit_date' => null,
        'edit_message' => [],
        'media' => $media
    ];

    $allMessages = [];
    if (file_exists($fileName)) {
        $allMessages = json_decode(file_get_contents($fileName), true);
        $allMessages[] = $newMessage;
    } else {
        $allMessages = [$newMessage];
    }

    if (!file_exists('data/history')) {
        mkdir('data/history', 0777, true);
    }

    file_put_contents($fileName, json_encode($allMessages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$files = [
    'calculator.txt' => 'off',
    'translator.txt' => 'off',
    'language.txt' => 'Mavjud emas!',
    'animation.txt' => 'off',
    'quiz.txt' => 'off',
    'auto-answer.txt' => 'off',
    'currency.txt' => 'off',
    'weather.txt' => 'off',
    'timed-media.txt' => 'off',
    'edited-message.txt' => 'off',
    'deleted-message.txt' => 'off',
];

foreach ($files as $filename => $default_content) {
    $filepath = "data/$filename";
    if (!file_exists($filepath)) {
        file_put_contents($filepath, $default_content);
    }
}


$takror = file_get_contents("data/cache/$business_chat_id.txt");
$animatsiya = file_get_contents("data/animation.txt");
$avto = file_get_contents("data/auto-answer.txt");
$sozlar = file_get_contents("data/words.json");
$quiz = file_get_contents("data/quiz.txt");
$savol = file_get_contents("data/question.json");
$kalkulyator = file_get_contents("data/calculator.txt");
$valyuta = file_get_contents("data/currency.txt");
$obhavo = file_get_contents("data/weather.txt");
$tarjimon = file_get_contents("data/translator.txt");
$til = file_get_contents("data/language.txt");
$vaqtli_media = file_get_contents("data/timed-media.txt");
$tahrirlangan = file_get_contents("data/edited-message.txt");
$ochirilgan = file_get_contents("data/deleted-message.txt");

$active_id = !empty($callfrid) ? $callfrid : $business_from_id;
$step = file_get_contents("step/$business_chat_id.txt");
$bot = bot('getme', ['bot'])->result->username;
$botname = bot('getme', ['bot'])->result->first_name;
$soat = date('H:i');
$sana = date("d.m.Y");

mkdir("data");
mkdir("data/cache");
mkdir("step");

$settings = json_encode([
    'inline_keyboard' => [
        [['text' => "📝 Tahrirlangan xabarlar", 'callback_data' => "tahrirlangan"], ['text' => "🗑️ O'chirilgan xabarlar", 'callback_data' => "ochirilgan"]],
        [['text' => "⏳ Vaqtli media xabarlar", 'callback_data' => "vaqtli-media"]],
        [['text' => "📊 So'rovnoma", 'callback_data' => "sorovnoma"], ['text' => "🧮 Kalkulyator", 'callback_data' => "kalkulyator"]],
        [['text' => "📚 Tarjimon", 'callback_data' => "tarjimon"], ['text' => "⭐ Animatsiyalar", 'callback_data' => "animatsiya"]],
        [['text' => "📝 Avto javob", 'callback_data' => "avto"]],
        [['text' => "💱 Valyuta", 'callback_data' => "valyuta"], ['text' => "⛅ Ob-havo", 'callback_data' => "ob-havo"]],
        [['text' => "Yopish", 'callback_data' => "yopish"]],
    ]
]);

$tillar = json_encode([
    'inline_keyboard' => [
        [['text' => "🇺🇿 Uzbekistan", 'callback_data' => "from_uz"]],
        [['text' => "🇮🇹 Italian", 'callback_data' => "from_it"], ['text' => "🇸🇦 Saudi Arabia", 'callback_data' => "from_ar-SA"]],
        [['text' => "🇷🇺 Russia", 'callback_data' => "from_ru"], ['text' => "🇰🇷 South Korea", 'callback_data' => "from_ko"], ['text' => "🇹🇷 Turkey", 'callback_data' => "from_tr"]],
        [['text' => "🇪🇸 Spain", 'callback_data' => "from_es"], ['text' => "🇯🇵 Japan", 'callback_data' => "from_ja"], ['text' => "🇺🇦 Ukraine", 'callback_data' => "from_uk"]],
        [['text' => "🇮🇳 India", 'callback_data' => "from_hi"], ['text' => "🇹🇹 Tatar", 'callback_data' => "from_tt"]],
        [['text' => "🇩🇪 German", 'callback_data' => "from_de"], ['text' => "🇵🇹 Portugal", 'callback_data' => "from_pt"], ['text' => "🇮🇩 Indonesian", 'callback_data' => "from_id"]],
        [['text' => "🇰🇿 Kazakhstan", 'callback_data' => "to_kk"], ['text' => "🇹🇲 Turkmenistan", 'callback_data' => "from_tk"], ['text' => "🇦🇿 Azerbaijan", 'callback_data' => "from_az"]],
        [['text' => "🇺🇸 United States", 'callback_data' => "from_en-US"], ['text' => "🇫🇷 France", 'callback_data' => "from_fr"]],
        [['text' => "🇰🇬 Kyrgyzstan", 'callback_data' => "from_ky"]],
        [['text' => "◀️ Orqaga", 'callback_data' => "tsozlash"]]
    ]
]);

$back = json_encode([
    'inline_keyboard' => [
        [['text' => "◀️ Orqaga", 'callback_data' => "settings"]]
    ]
]);

/*
if(isset($alijonov)){
    $json = json_encode($alijonov, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    bot('sendmessage',[
        'chat_id'=>$admin,
        'text'=>"```\n$json\n```",
        'parse_mode'=>'MarkDown',
]);
}
*/

if (isset($connection)) {

    $tx = $business_connection ? "<tg-emoji emoji-id='5206607081334906820'>✔️</tg-emoji> <b>Hisobingizga $botname muvaffaqiyatli ulandi!</b>" : "<tg-emoji emoji-id='5260293700088511294'>⛔️</tg-emoji> <b>Hisobingizdan $botname olib tashlandi!</b>";

    bot('sendMessage', [
        'chat_id' => $business_connection_id,
        'text' => $tx,
        'parse_mode' => 'html',
    ]);
}

if (isset($edited_message)) {
    if ($tahrirlangan == "on") {
        $filePath = "data/history/$edited_message_cid.json";
        if (file_exists($filePath)) {
            $jsonData = file_get_contents($filePath);
            $dataArray = json_decode($jsonData, true);
            $foundMessageIndex = null;
            foreach ($dataArray as $index => &$message) {
                if (isset($message['message_id']) && $message['message_id'] == $edited_message_id) {
                    $foundMessageIndex = $index;
                    break;
                }
            }
            if ($foundMessageIndex !== null) {
                $old_message = $dataArray[$foundMessageIndex]['text'] ?? "Aniqlanmadi!";
                $new_message = $edited_message->text ?? "Aniqlanmadi!";
                $date = $dataArray[$foundMessageIndex]['date'] ?? "Aniqlanmadi!";
                $fullName = $edited_message_username ? "<a href='https://t.me/$edited_message_username'>$edited_message_name $edited_message_lastname</a>" : "<a href='tg://user?id=$edited_message_cid'>$edited_message_name $edited_message_lastname</a>";
                $edit_date = date('Y-m-d H:i:s', $edited_message->edit_date) ?? "Aniqlanmadi!";

                if (!isset($dataArray[$foundMessageIndex]['edit_message'])) {
                    $dataArray[$foundMessageIndex]['edit_message'] = [];
                }

                $dataArray[$foundMessageIndex]['edit_message'][] = [
                    'old_message' => $old_message,
                    'new_message' => $new_message,
                    'edit_date' => $edit_date,
                ];

                $dataArray[$foundMessageIndex]['text'] = $new_message;
                $dataArray[$foundMessageIndex]['edit_date'] = $edit_date;

                if (!empty($edited_message_name)) {
                    $addition = "<b>Ushbu xabar $fullName tomonidan yuborilgan!</b>";
                } else {
                    $addition = "<b>Ushbu xabar kimga tegishli ekanligi aniqlanmadi!</b>";
                }

                if ($edited_message_fid != $admin) {
                    bot('sendMessage', [
                        'chat_id' => $admin,
                        'text' => "<b>Suhbat:</b> $fullName\n\n• <b>Holati:</b> Tahrirlandi!\n• <b>Yuborilgan vaqti:</b> $date\n• <b>Tahrirlangan vaqti:</b> $edit_date\n\n• <b>Eski xabar:</b> $old_message\n• <b>Tahrirlangan xabar:</b> $new_message\n\n$addition",
                        'disable_web_page_preview' => true,
                        'parse_mode' => 'html',
                    ]);
                }

                file_put_contents($filePath, json_encode($dataArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }
        }
    }
}

if (isset($delete_message)) {
    if ($ochirilgan == "on") {
        $filePath = "data/history/$delete_message_cid.json";

        if (file_exists($filePath)) {
            $jsonData = file_get_contents($filePath);
            $dataArray = json_decode($jsonData, true);

            foreach ($delete_message_mids as $delete_message_mid) {
                $foundMessage = array_filter($dataArray, function ($message) use ($delete_message_mid) {
                    return isset($message['message_id']) && $message['message_id'] == $delete_message_mid;
                });

                if (!empty($foundMessage)) {
                    $foundMessage = reset($foundMessage);
                    $first = $foundMessage['first_name'] ?? null;
                    $last = $foundMessage['last_name'] ?? null;
                    $username = $foundMessage['username'] ?? null;
                    $user_id = $foundMessage['user_id'] ?? null;
                    $text = $foundMessage['text'] ?? "Aniqlanmadi!";
                    $date = $foundMessage['date'] ?? "Aniqlanmadi!";
                    $edit_date = $foundMessage['edit_date'] ?? "Tahrirlanmagan!";
                    $media = $foundMessage['media'] ?? null;
                    $fullName = $delete_message_username ? "<a href='https://t.me/$delete_message_username'>$delete_message_name $delete_message_lastname</a>" : "<a href='tg://user?id=$delete_message_cid'>$delete_message_name $delete_message_lastname</a>";
                    $fullname = $username ? "<a href='https://t.me/$username'>$first $last</a>" : "<a href='tg://user?id=$user_id'>$first $last</a>";

                    if (!empty($first)) {
                        $addition = "<b>Ushbu xabar $fullname tomonidan yuborilgan!</b>";
                    } else {
                        $addition = "<b>Ushbu xabar kimga tegishli ekanligi aniqlanmadi!</b>";
                    }

                    if (empty($media)) {
                        bot('sendMessage', [
                            'chat_id' => $admin,
                            'text' => "<b>Suhbat:</b> $fullName\n\n• <b>Holati:</b> O'chirildi!\n• <b>Yuborilgan vaqti:</b> $date\n• <b>Tahrirlangan vaqti:</b> $edit_date\n\n• <b>O'chirilgan xabar:</b> $text\n\n$addition",
                            'disable_web_page_preview' => true,
                            'parse_mode' => 'html',
                        ]);
                    }

                    if ($media) {
                        foreach ($media as $file) {
                            $sarlavha = $file['caption'] ?? '';
                            if (!empty($sarlavha)) {
                                $caption = "• <b>Sarlavhasi:</b> $sarlavha\n\n$addition";
                            } else {
                                $caption = "\n" . $addition;
                            }
                            if ($file['type'] == 'voice') {
                                bot('sendVoice', [
                                    'chat_id' => $admin,
                                    'voice' => $file['file_id'],
                                    'caption' => "<b>Suhbat:</b> $fullName\n\n• <b>Holati:</b> O'chirildi!\n• <b>Yuborilgan vaqti:</b> $date\n\n$addition",
                                    'disable_web_page_preview' => true,
                                    'parse_mode' => 'html'
                                ]);
                            } elseif ($file['type'] == 'photo') {
                                bot('sendPhoto', [
                                    'chat_id' => $admin,
                                    'photo' => $file['file_id'],
                                    'caption' => "<b>Suhbat:</b> $fullName\n\n• <b>Holati:</b> O'chirildi!\n• <b>Yuborilgan vaqti:</b> $date\n$caption",
                                    'disable_web_page_preview' => true,
                                    'parse_mode' => 'html'
                                ]);
                            } elseif ($file['type'] == 'video') {
                                bot('sendVideo', [
                                    'chat_id' => $admin,
                                    'video' => $file['file_id'],
                                    'caption' => "<b>Suhbat:</b> $fullName\n\n• <b>Holati:</b> O'chirildi!\n• <b>Yuborilgan vaqti:</b> $date\n$caption",
                                    'disable_web_page_preview' => true,
                                    'parse_mode' => 'html'
                                ]);
                            } elseif ($file['type'] == 'video_note') {
                                $msg = bot('sendVideoNote', [
                                    'chat_id' => $admin,
                                    'video_note' => $file['file_id'],
                                ])->result->message_id;
                                bot('sendMessage', [
                                    'chat_id' => $admin,
                                    'text' => "<b>Suhbat:</b> $fullName\n\n• <b>Holati:</b> O'chirildi!\n• <b>Yuborilgan vaqti:</b> $date\n\n$addition",
                                    'disable_web_page_preview' => true,
                                    'reply_to_message_id' => $msg,
                                    'parse_mode' => 'html',
                                ]);
                            } elseif ($file['type'] == 'audio') {
                                bot('sendAudio', [
                                    'chat_id' => $admin,
                                    'audio' => $file['file_id'],
                                    'caption' => "<b>Suhbat:</b> $fullName\n\n• <b>Holati:</b> O'chirildi!\n• <b>Yuborilgan vaqti:</b> $date\n$caption",
                                    'disable_web_page_preview' => true,
                                    'parse_mode' => 'html'
                                ]);
                            } elseif ($file['type'] == 'document') {
                                bot('sendDocument', [
                                    'chat_id' => $admin,
                                    'document' => $file['file_id'],
                                    'caption' => "<b>Suhbat:</b> $fullName\n\n• <b>Holati:</b> O'chirildi!\n• <b>Yuborilgan vaqti:</b> $date\n$caption",
                                    'disable_web_page_preview' => true,
                                    'parse_mode' => 'html'
                                ]);
                            } elseif ($file['type'] == 'sticker') {
                                $msg = bot('sendSticker', [
                                    'chat_id' => $admin,
                                    'sticker' => $file['file_id'],
                                ])->result->message_id;
                                bot('sendMessage', [
                                    'chat_id' => $admin,
                                    'text' => "<b>Suhbat:</b> $fullName\n\n• <b>Holati:</b> O'chirildi!\n• <b>Yuborilgan vaqti:</b> $date\n\n$addition",
                                    'disable_web_page_preview' => true,
                                    'reply_to_message_id' => $msg,
                                    'parse_mode' => 'html',
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }
}

if ($text == "/start") {
    $first = bot('getChat', [
        'chat_id' => 7990053633,
    ])->result->first_name;
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => "<tg-emoji emoji-id='5472055112702629499'>👋</tg-emoji> <b>Assalomu alaykum $nameru!</b>
    
<i>Botdan foydalanish huquqiga ega bo'lishni istasangiz, pastdagi admin bilan bog'laning!</i>",
        'parse_mode' => 'html',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => $first, 'url' => "https://t.me/$admin_user"]]
            ]
        ])
    ]);
}

if ($business_text == ".ping" and $business_from_id == $admin) {
    $ping = ping(BOT_TOKEN, $cid, $business_text);
    bot('editMessageText', [
        'business_connection_id' => $business_id,
        'chat_id' => $business_chat_id,
        'message_id' => $business_message_id,
        'text' => "<tg-emoji emoji-id='5256263173928926820'>🚀</tg-emoji> <b>O'rtacha yuklanish:</b> $ping",
        'parse_mode' => 'html',
    ]);
}

if ($business_text == ".memory" and $business_from_id == $admin) {
    $memory = round(memory_get_peak_usage(true) / 1024 / 1024, 2);
    bot('sendChatAction', [
        'business_connection_id' => $business_id,
        'chat_id' => $business_chat_id,
        'action' => "typing"
    ]);
    bot('editMessageText', [
        'business_connection_id' => $business_id,
        'chat_id' => $business_chat_id,
        'message_id' => $business_message_id,
        'text' => "<tg-emoji emoji-id='5257969839313526622'>📂</tg-emoji> <b>Xotira iste'moli:</b> $memory MB\n\n<i>Bot hozirda $memory megabayt xotiradagi joydan foydalanmoqda!</i>",
        'parse_mode' => 'html'
    ]);
}

if ($business_text == ".settings") {
    if ($business_from_id == $admin) {
        bot('sendChatAction', [
            'business_connection_id' => $business_id,
            'chat_id' => $business_chat_id,
            'action' => "typing"
        ]);
        bot('editMessageText', [
            'business_connection_id' => $business_id,
            'chat_id' => $business_chat_id,
            'message_id' => $business_message_id,
            'text' => "<tg-emoji emoji-id='5341715473882955310'>⚙️</tg-emoji> <b>Sozlamalar bo'limidasiz!</b>
	
<i>Quyidagi bo'limlardan birini tanlang:</i>",
            'parse_mode' => 'html',
            'reply_markup' => $settings
        ]);
    }
}

if ($data == "settings") {
    if ($callfrid == $admin) {
        bot('editMessageText', [
            'business_connection_id' => $business_id2,
            'chat_id' => $cid2,
            'message_id' => $mid2,
            'text' => "<tg-emoji emoji-id='5341715473882955310'>⚙️</tg-emoji> <b>Sozlamalar bo'limidasiz!</b>
	
<i>Quyidagi bo'limlardan birini tanlang:</i>",
            'parse_mode' => 'html',
            'reply_markup' => $settings
        ]);
        unlink("step/$fid2.txt");
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ Bu tugma siz uchun mo'ljallanmagan!",
            'show_alert' => false
        ]);
    }
}

if ($data == "yopish") {
    if ($callfrid == $admin) {
        bot('editMessageText', [
            'business_connection_id' => $business_id2,
            'chat_id' => $cid2,
            'message_id' => $mid2,
            'text' => "<tg-emoji emoji-id='5260293700088511294'>⛔️</tg-emoji> <b>Bo'lim yopildi!</b>",
            'parse_mode' => 'html'
        ]);
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ Bu tugma siz uchun mo'ljallanmagan!",
            'show_alert' => false
        ]);
    }
}

//Kalkulyator 

if (strpos($data, "kalkulyator") !== false) {

    if ($callfrid != $admin) {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ Bu tugma siz uchun mo'ljallanmagan!",
            'show_alert' => false
        ]);
        return;
    }

    $ex = strpos($data, "=") !== false ? explode("=", $data)[1] : $kalkulyator;
    $on = $ex === "on" ? "« ✅ »" : "✅";
    $off = $ex === "off" ? "« ☑ »" : "☑";
    $res = $ex === "on" ? "Faollashtirilgan!" : "Faolsizlantirilgan!";

    $buttons = [
        ['text' => $on, 'callback_data' => "kalkulyator=on"],
        ['text' => $off, 'callback_data' => "kalkulyator=off"]
    ];

    $inline_keyboard = [
        $buttons,
        [['text' => "◀️ Orqaga", 'callback_data' => "settings"]]
    ];

    if ($data === "kalkulyator" || $kalkulyator != $ex) {
        if ($kalkulyator != $ex) {
            file_put_contents("data/calculator.txt", $ex);
        }
        bot('editMessageText', [
            'business_connection_id' => $business_id2,
            'chat_id' => $cid2,
            'message_id' => $mid2,
            'text' => "<tg-emoji emoji-id='5341715473882955310'>⚙️</tg-emoji> <b>Kalkulyator sozlamalaridasiz!</b>

<i>— Hozirgi holat: $res</i>

<tg-emoji emoji-id='5406745015365943482'>⬇️</tg-emoji> <b>Chatlarda foydalanish:</b>

<code>.math</code> – <i>Ifodani yozing (masalan: <b>.math 25*4+10</b>) va bot natijani hisoblab beradi.</i>",
            'parse_mode' => 'html',
            'reply_markup' => json_encode(['inline_keyboard' => $inline_keyboard])
        ]);
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ $res",
            'show_alert' => true
        ]);
    }
}


if ((mb_stripos($business_text, ".math ") !== false) && ($business_from_id == $admin)) {
    if ($kalkulyator == "on") {
        $ex = trim(substr($business_text, mb_stripos($business_text, ".math ") + 6));
        $misol = str_replace(" ", "", $ex);

        try {
            $result = @eval ('return ' . $misol . ';');

            if (!preg_match("#^[0-9+\-*/().\s]+$#", $misol)) {
                throw new Exception("Noto'g'ri matematik ifoda kiritildi!");
            }
            if ($result === false || is_infinite($result) || is_nan($result)) {
                throw new Exception("Qabul qilinmadi!");
            }

            bot('editMessageText', [
                'business_connection_id' => $business_id,
                'chat_id' => $business_chat_id,
                'message_id' => $business_message_id,
                'text' => "<tg-emoji emoji-id='5231200819986047254'>📊</tg-emoji> <b>Natija:</b> <code>$misol=$result</code>",
                'parse_mode' => 'html',
            ]);
        } catch (Exception $e) {
            $xato = $e->getMessage();
            bot('editMessageText', [
                'business_connection_id' => $business_id,
                'chat_id' => $business_chat_id,
                'message_id' => $business_message_id,
                'text' => "<tg-emoji emoji-id='5447644880824181073'>⚠️</tg-emoji> <b>$xato</b>",
                'parse_mode' => 'html',
            ]);
        }
    } else {
        bot('sendChatAction', [
            'business_connection_id' => $business_id,
            'chat_id' => $business_chat_id,
            'action' => "typing"
        ]);
        bot('editMessageText', [
            'business_connection_id' => $business_id,
            'chat_id' => $business_chat_id,
            'message_id' => $business_message_id,
            'text' => "<tg-emoji emoji-id='5447644880824181073'>⚠️</tg-emoji> <b>Kalkulyator faolsizlantirilgan!</b>",
            'parse_mode' => 'html',
        ]);
    }
}

//tarjimon

if (strpos($data, "tarjimon") !== false) {

    if ($callfrid != $admin) {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ Bu tugma siz uchun mo'ljallanmagan!",
            'show_alert' => false
        ]);
        return;
    }

    $ex = strpos($data, "=") !== false ? explode("=", $data)[1] : $tarjimon;
    $on = $ex === "on" ? "« ✅ »" : "✅";
    $off = $ex === "off" ? "« ☑ »" : "☑";
    $res = $ex === "on" ? "Faollashtirilgan!" : "Faolsizlantirilgan!";

    $buttons = [
        ['text' => $on, 'callback_data' => "tarjimon=on"],
        ['text' => $off, 'callback_data' => "tarjimon=off"]
    ];

    if ($ex === "on") {
        $inline_keyboard = [
            [['text' => "⚙ Sozlamalar", 'callback_data' => "tsozlash"]],
            $buttons,
            [['text' => "◀️ Orqaga", 'callback_data' => "settings"]]
        ];
    } else {
        $inline_keyboard = [
            $buttons,
            [['text' => "◀️ Orqaga", 'callback_data' => "settings"]]
        ];
    }

    if ($data === "tarjimon" || $tarjimon != $ex) {
        if ($tarjimon != $ex) {
            file_put_contents("data/translator.txt", $ex);
        }
        bot('editMessageText', [
            'business_connection_id' => $business_id2,
            'chat_id' => $cid2,
            'message_id' => $mid2,
            'text' => "<tg-emoji emoji-id='5341715473882955310'>⚙️</tg-emoji> <b>Tarjimon sozlamalaridasiz!</b>

<i>— Hozirgi holat: $res
— Tanlangan tillar: $til</i>

<tg-emoji emoji-id='5406745015365943482'>⬇️</tg-emoji> <b>Chatlarda foydalanish:</b>

<code>.translator</code> – <i><b>Matnni tarjima qilish</b>. Buyruqdan keyin tarjima qilmoqchi bo‘lgan matningizni yozing (masalan: <b>.translator Salom, Dunyo!</b>), bot uni tanlangan tilga tarjima qilib beradi.</i>",
            'parse_mode' => 'html',
            'reply_markup' => json_encode(['inline_keyboard' => $inline_keyboard])
        ]);
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ $res",
            'show_alert' => true
        ]);
    }
}


if ($data == "tsozlash") {
    if ($callfrid == $admin) {
        bot('editMessageText', [
            'business_connection_id' => $business_id2,
            'chat_id' => $cid2,
            'message_id' => $mid2,
            'text' => "<tg-emoji emoji-id='5406745015365943482'>⬇️</tg-emoji> <b>Quyidagilardan birini tanlang!</b>",
            'parse_mode' => 'html',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => "✏ Tilni o'zgartirish", 'callback_data' => "til"]],
                    [['text' => "◀️ Orqaga", 'callback_data' => "tarjimon"]]
                ]
            ])
        ]);
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ Bu tugma siz uchun mo'ljallanmagan!",
            'show_alert' => false
        ]);
    }
}

if ($data == "til") {
    if ($callfrid == $admin) {
        bot('editMessageText', [
            'business_connection_id' => $business_id2,
            'chat_id' => $cid2,
            'message_id' => $mid2,
            'text' => "<tg-emoji emoji-id='5406745015365943482'>⬇️</tg-emoji> <b>Quyidagi tillardan birini tanlang:</b>",
            'parse_mode' => 'html',
            'reply_markup' => $tillar,
        ]);
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ Bu tugma siz uchun mo'ljallanmagan!",
            'show_alert' => false
        ]);
    }
}

if (mb_stripos($data, "from_") !== false) {
    if ($callfrid == $admin) {
        $from = explode("_", $data)[1];
        $button = getButtonText($tillar, $data);
        bot('editMessageText', [
            'business_connection_id' => $business_id2,
            'chat_id' => $cid2,
            'message_id' => $mid2,
            'text' => "<tg-emoji emoji-id='5206607081334906820'>✔️</tg-emoji> $button <b>tanlandi!</b>

<i>Qaysi tilga tarjima qilmoqchisiz?</i>",
            'parse_mode' => 'html',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => "🇺🇿 Uzbekistan", 'callback_data' => "to_uz_$from"]],
                    [['text' => "🇮🇹 Italian", 'callback_data' => "to_it_$from"], ['text' => "🇸🇦 Saudi Arabia", 'callback_data' => "to_ar-SA_$from"]],
                    [['text' => "🇷🇺 Russia", 'callback_data' => "to_ru_$from"], ['text' => "🇰🇷 South Korea", 'callback_data' => "to_ko_$from"], ['text' => "🇹🇷 Turkey", 'callback_data' => "to_tr_$from"]],
                    [['text' => "🇪🇸 Spain", 'callback_data' => "to_es_$from"], ['text' => "🇯🇵 Japan", 'callback_data' => "to_ja_$from"], ['text' => "🇺🇦 Ukraine", 'callback_data' => "to_uk_$from"]],
                    [['text' => "🇮🇳 India", 'callback_data' => "to_hi"], ['text' => "🇹🇹 Tatar", 'callback_data' => "to_tt_$from"]],
                    [['text' => "🇩🇪 German", 'callback_data' => "to_de_$from"], ['text' => "🇵🇹 Portugal", 'callback_data' => "to_pt_$from"], ['text' => "🇮🇩 Indonesian", 'callback_data' => "to_id_$from"]],
                    [['text' => "🇰🇿 Kazakhstan", 'callback_data' => "to_kk_$from"], ['text' => "🇹🇲 Turkmenistan", 'callback_data' => "to_tk_$from"], ['text' => "🇦🇿 Azerbaijan", 'callback_data' => "to_az_$from"]],
                    [['text' => "🇺🇸 United States", 'callback_data' => "to_en-US_$from"], ['text' => "🇫🇷 France", 'callback_data' => "to_fr_$from"]],
                    [['text' => "🇰🇬 Kyrgyzstan", 'callback_data' => "to_ky_$from"]],
                    [['text' => "◀️ Orqaga", 'callback_data' => "til"]]
                ]
            ])
        ]);
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ Bu tugma siz uchun mo'ljallanmagan!",
            'show_alert' => false
        ]);
    }
}

if (mb_stripos($data, "to_") !== false) {
    if ($callfrid == $admin) {
        $to = explode("_", $data)[1];
        $from = explode("_", $data)[2];
        if ($from != $to) {
            bot('editMessageText', [
                'business_connection_id' => $business_id2,
                'chat_id' => $cid2,
                'message_id' => $mid2,
                'text' => "<tg-emoji emoji-id='5341715473882955310'>⚙️</tg-emoji> <i>O'rnatilmoqda...</i>",
                'parse_mode' => 'html',
            ]);
            bot('editMessageText', [
                'business_connection_id' => $business_id2,
                'chat_id' => $cid2,
                'message_id' => $mid2,
                'text' => "<tg-emoji emoji-id='5206607081334906820'>✔️</tg-emoji> <b>$from/$to tanlandi!</b>

<i>Tarjima qilish uchun kerali tillar o'rnatildi!</i>",
                'parse_mode' => 'html',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [['text' => "◀️ Orqaga", 'callback_data' => "tsozlash"]]
                    ]
                ])
            ]);
            file_put_contents("data/language.txt", "$from/$to");
        } else {
            bot('answerCallbackQuery', [
                'callback_query_id' => $qid,
                'text' => "Ushbu til tanlangan. Boshqa tilni tanlang!",
                'show_alert' => true
            ]);
        }
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ Bu tugma siz uchun mo'ljallanmagan!",
            'show_alert' => false
        ]);
    }
}

if (mb_stripos($business_text, ".translator") !== false) {
    if ($business_from_id == $admin) {
        if ($tarjimon == "on") {
            if ($til != "Mavjud emas!") {
                $tx = trim(substr($business_text, mb_stripos($business_text, ".translator ") + 12));
                if ($tx == null) {
                    bot('sendChatAction', [
                        'business_connection_id' => $business_id,
                        'chat_id' => $business_chat_id,
                        'action' => "typing"
                    ]);
                    bot('editMessageText', [
                        'business_connection_id' => $business_id,
                        'chat_id' => $business_chat_id,
                        'message_id' => $business_message_id,
                        'text' => "<tg-emoji emoji-id='5447644880824181073'>⚠️</tg-emoji> <b>Kerakli matn kiritilmadi!</b>",
                        'parse_mode' => 'html',
                    ]);
                } else {
                    $from = explode("/", $til)[0];
                    $to = explode("/", $til)[1];
                    $json = json_decode(file_get_contents($SERVER_NAME . "api/translate.php?text=" . urlencode($tx) . "&from=$from&to=$to"), true);
                    $matn = $json['target']['text'];
                    if ($matn != null) {
                        bot('sendChatAction', [
                            'business_connection_id' => $business_id,
                            'chat_id' => $business_chat_id,
                            'action' => "typing"
                        ]);
                        bot('editMessageText', [
                            'business_connection_id' => $business_id,
                            'chat_id' => $business_chat_id,
                            'message_id' => $business_message_id,
                            'text' => $matn,
                            'parse_mode' => 'html',
                        ]);
                    } else {
                        bot('sendChatAction', [
                            'business_connection_id' => $business_id,
                            'chat_id' => $business_chat_id,
                            'action' => "typing"
                        ]);
                        bot('editMessageText', [
                            'business_connection_id' => $business_id,
                            'chat_id' => $business_chat_id,
                            'message_id' => $business_message_id,
                            'text' => "<tg-emoji emoji-id='5447644880824181073'>⚠️</tg-emoji> <b>API bilan bog'liq muammo yuzaga keldi!</b>",
                            'parse_mode' => 'html',
                        ]);
                    }
                }
            } else {
                bot('sendChatAction', [
                    'business_connection_id' => $business_id,
                    'chat_id' => $business_chat_id,
                    'action' => "typing"
                ]);
                bot('editMessageText', [
                    'business_connection_id' => $business_id,
                    'chat_id' => $business_chat_id,
                    'message_id' => $business_message_id,
                    'text' => "<tg-emoji emoji-id='5447644880824181073'>⚠️</tg-emoji> <b>Tarjima qilish uchun, kerakli tillar tanlanmagan!</b>",
                    'parse_mode' => 'html',
                ]);
            }
        } else {
            bot('sendChatAction', [
                'business_connection_id' => $business_id,
                'chat_id' => $business_chat_id,
                'action' => "typing"
            ]);
            bot('editMessageText', [
                'business_connection_id' => $business_id,
                'chat_id' => $business_chat_id,
                'message_id' => $business_message_id,
                'text' => "<tg-emoji emoji-id='5447644880824181073'>⚠️</tg-emoji> <b>Tarjimon faolsizlantirilgan!</b>",
                'parse_mode' => 'html',
            ]);
        }
    }
}

// Animatsiyalar

if (strpos($data, "animatsiya") !== false) {

    if ($callfrid != $admin) {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ Bu tugma siz uchun mo'ljallanmagan!",
            'show_alert' => false
        ]);
        return;
    }

    $ex = strpos($data, "=") !== false ? explode("=", $data)[1] : $animatsiya;
    $on = $ex === "on" ? "« ✅ »" : "✅";
    $off = $ex === "off" ? "« ☑ »" : "☑";
    $res = $ex === "on" ? "Faollashtirilgan!" : "Faolsizlantirilgan!";

    $holatlar = [
        ['text' => $on, 'callback_data' => "animatsiya=on"],
        ['text' => $off, 'callback_data' => "animatsiya=off"]
    ];
    $orqaga = [['text' => "◀️ Orqaga", 'callback_data' => "settings"]];

    $inline_keyboard = [$holatlar, $orqaga];

    if ($data === "animatsiya" || $animatsiya != $ex) {
        if ($animatsiya != $ex) {
            file_put_contents("data/animation.txt", $ex);
        }
        bot('editMessageText', [
            'business_connection_id' => $business_id2,
            'chat_id' => $cid2,
            'message_id' => $mid2,
            'text' => "<tg-emoji emoji-id='5341715473882955310'>⚙️</tg-emoji> <b>Animatsiyalar sozlamalaridasiz!</b>

<i>— Hozirgi holat: $res</i>

<tg-emoji emoji-id='5406745015365943482'>⬇️</tg-emoji> <b>Chatlarda foydalanish:</b>

<code>.typing</code> – <i>Matnni yozilish animatsiyasida yuborish (masalan: <b>.typing Salom</b>)</i>.
<code>.heart</code> – <i>Chiroyli yuraklar animatsiyasini yuborish</i>.",
            'parse_mode' => 'html',
            'reply_markup' => json_encode(['inline_keyboard' => $inline_keyboard])
        ]);
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ $res",
            'show_alert' => true
        ]);
    }
}


if (mb_stripos($business_text, ".typing") !== false) {
    if ($business_from_id == $admin) {
        if ($animatsiya == "on") {
            $text = trim(substr($business_text, mb_stripos($business_text, ".yozish ") + 8));
            $length = mb_strlen($text, 'UTF-8');

            for ($i = 0; $i < $length; $i++) {
                $result = mb_substr($text, 0, $i + 1, 'UTF-8');

                bot('sendChatAction', [
                    'business_connection_id' => $business_id,
                    'chat_id' => $business_chat_id,
                    'action' => "typing"
                ]);

                bot('editMessageText', [
                    'business_connection_id' => $business_id,
                    'chat_id' => $business_chat_id,
                    'message_id' => $business_message_id,
                    'text' => $result,
                    'parse_mode' => 'html'
                ]);
            }
        } else {
            bot('sendChatAction', [
                'business_connection_id' => $business_id,
                'chat_id' => $business_chat_id,
                'action' => "typing"
            ]);
            bot('editMessageText', [
                'business_connection_id' => $business_id,
                'chat_id' => $business_chat_id,
                'message_id' => $business_message_id,
                'text' => "<tg-emoji emoji-id='5447644880824181073'>⚠️</tg-emoji> <b>Animatsiyali yozish faolsizlantirilgan!</b>",
                'parse_mode' => 'html'
            ]);
        }
    }
}

if ($business_text == ".heart") {
    if ($business_from_id == $admin) {
        if ($animatsiya == "on") {
            $hearts = ["❤️", "💚", "💙", "💜", "🧡", "💛"];
            $random_heart = $hearts[array_rand($hearts)];

            bot('sendChatAction', [
                'business_connection_id' => $business_id,
                'chat_id' => $business_chat_id,
                'action' => "typing"
            ]);

            bot('editMessageText', [
                'business_connection_id' => $business_id,
                'chat_id' => $business_chat_id,
                'message_id' => $business_message_id,
                'text' => $random_heart,
                'parse_mode' => 'html'
            ]);

            for ($i = 0; $i < 10; $i++) {
                $random_heart = $hearts[array_rand($hearts)];
                bot('editMessageText', [
                    'business_connection_id' => $business_id,
                    'chat_id' => $business_chat_id,
                    'message_id' => $business_message_id,
                    'text' => $random_heart,
                    'parse_mode' => 'html'
                ]);
            }

            $text = "Men sizni ❤️ sevaman!";
            $length = mb_strlen($text, 'UTF-8');
            for ($i = 0; $i < $length; $i++) {
                $result = mb_substr($text, 0, $i + 1, 'UTF-8');

                bot('sendChatAction', [
                    'business_connection_id' => $business_id,
                    'chat_id' => $business_chat_id,
                    'action' => "typing"
                ]);

                bot('editMessageText', [
                    'business_connection_id' => $business_id,
                    'chat_id' => $business_chat_id,
                    'message_id' => $business_message_id,
                    'text' => "<b>$result</b>",
                    'parse_mode' => 'html'
                ]);
            }
        } else {
            bot('sendChatAction', [
                'business_connection_id' => $business_id,
                'chat_id' => $business_chat_id,
                'action' => "typing"
            ]);
            bot('editMessageText', [
                'business_connection_id' => $business_id,
                'chat_id' => $business_chat_id,
                'message_id' => $business_message_id,
                'text' => "<tg-emoji emoji-id='5447644880824181073'>⚠️</tg-emoji> <b>Animatsiyali yozish faolsizlantirilgan!</b>",
                'parse_mode' => 'html'
            ]);
        }
    }
}

//So'rovnoma

if (strpos($data, "sorovnoma") !== false) {

    if ($callfrid != $admin) {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ Bu tugma siz uchun mo'ljallanmagan!",
            'show_alert' => false
        ]);
        return;
    }

    $ex = strpos($data, "=") !== false ? explode("=", $data)[1] : $quiz;
    $on = $ex === "on" ? "« ✅ »" : "✅";
    $off = $ex === "off" ? "« ☑ »" : "☑";
    $res = $ex === "on" ? "Faollashtirilgan!" : "Faolsizlantirilgan!";

    $savollar = json_decode($savol, true);
    $son = count($savollar);
    $soni = $son == 0 ? "Mavjud emas!" : "$son ta";

    $sozlamalar = [['text' => "⚙ Sozlamalar", 'callback_data' => "qsozlash"]];
    $holatlar = [
        ['text' => $on, 'callback_data' => "sorovnoma=on"],
        ['text' => $off, 'callback_data' => "sorovnoma=off"]
    ];
    $orqaga = [['text' => "◀️ Orqaga", 'callback_data' => "settings"]];

    if ($ex === "on") {
        $inline_keyboard = [$sozlamalar, $holatlar, $orqaga];
    } else {
        $inline_keyboard = [$holatlar, $orqaga];
    }

    if ($data === "sorovnoma" || $quiz != $ex) {
        if ($quiz != $ex) {
            file_put_contents("data/quiz.txt", $ex);
        }
        bot('editMessageText', [
            'business_connection_id' => $business_id2,
            'chat_id' => $cid2,
            'message_id' => $mid2,
            'text' => "<tg-emoji emoji-id='5341715473882955310'>⚙️</tg-emoji> <b>So'rovnoma sozlamalaridasiz!</b>

<i>— Hozirgi holat: $res
— Testlar soni: $soni</i>

<tg-emoji emoji-id='5406745015365943482'>⬇️</tg-emoji> <b>Chatlarda foydalanish:</b>

<code>.quiz</code> - <i>Test jarayonini boshlash uchun buyruq.</i>
<code>.clear</code> - <i>Chatdagi barcha test ma'lumotlarini o‘chirib tozalash uchun buyruq.</i>",
            'parse_mode' => 'html',
            'reply_markup' => json_encode(['inline_keyboard' => $inline_keyboard])
        ]);
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ $res",
            'show_alert' => true,
        ]);
    }
}


if ($data == "qsozlash") {
    if ($callfrid == $admin) {
        bot('editMessageText', [
            'business_connection_id' => $business_id2,
            'chat_id' => $cid2,
            'message_id' => $mid2,
            'text' => "<tg-emoji emoji-id='5406745015365943482'>⬇️</tg-emoji> <b>Quyidagilardan birini tanlang!</b>",
            'parse_mode' => 'html',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => "➕ Qo'shish", 'callback_data' => "savol"], ['text' => "🧹 Tozalash", 'callback_data' => "qutozala"]],
                    [['text' => "◀️ Orqaga", 'callback_data' => "sorovnoma"]]
                ]
            ])
        ]);
        unlink("step/$cid2.txt");
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ Bu tugma siz uchun mo'ljallanmagan!",
            'show_alert' => false
        ]);
    }
}

if ($business_text == ".clear") {
    if ($business_from_id == $admin) {
        $kesh = file_get_contents("data/cache/$business_chat_id.json");
        if ($kesh != null) {
            bot('editMessageText', [
                'business_connection_id' => $business_id,
                'chat_id' => $business_chat_id,
                'message_id' => $business_message_id,
                'text' => "<tg-emoji emoji-id='5445267414562389170'>🗑</tg-emoji> <i>O'chirilmoqda...</i>",
                'parse_mode' => 'html',
            ]);
            bot('editMessageText', [
                'business_connection_id' => $business_id,
                'chat_id' => $business_chat_id,
                'message_id' => $business_message_id,
                'text' => "<tg-emoji emoji-id='5206607081334906820'>✔️</tg-emoji> <b>Chatdagi test ma'lumotlari o'chirib tashlandi!</b>",
                'parse_mode' => 'html',
            ]);
            unlink("data/cache/$business_chat_id.json");
        } else {
            bot('editMessageText', [
                'business_connection_id' => $business_id,
                'chat_id' => $business_chat_id,
                'message_id' => $business_message_id,
                'text' => "<tg-emoji emoji-id='5231012545799666522'>🔍</tg-emoji> <i>Qidirilmoqda...</i>",
                'parse_mode' => 'html',
            ]);
            bot('editMessageText', [
                'business_connection_id' => $business_id,
                'chat_id' => $business_chat_id,
                'message_id' => $business_message_id,
                'text' => "<tg-emoji emoji-id='5260293700088511294'>⛔️</tg-emoji> <b>Ma'lumotlar topilmadi!</b>",
                'parse_mode' => 'html',
            ]);
        }
    }
}

if ($data == "qutozala") {
    if ($callfrid == $admin) {
        if ($savol != null) {
            bot('editMessageText', [
                'business_connection_id' => $business_id2,
                'chat_id' => $cid2,
                'message_id' => $mid2,
                'text' => "<tg-emoji emoji-id='5445267414562389170'>🗑</tg-emoji> <i>O'chirilmoqda...</i>",
                'parse_mode' => 'html',
            ]);
            bot('editMessageText', [
                'business_connection_id' => $business_id2,
                'chat_id' => $cid2,
                'message_id' => $mid2,
                'text' => "<tg-emoji emoji-id='5206607081334906820'>✔️</tg-emoji> <b>Ma'lumotlar muvaffaqiyatli tozalandi!</b>",
                'parse_mode' => 'html',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [['text' => "◀️ Orqaga", 'callback_data' => "qsozlash"]]
                    ]
                ])
            ]);
            unlink("data/question.json");
        } else {
            bot('editMessageText', [
                'business_connection_id' => $business_id2,
                'chat_id' => $cid2,
                'message_id' => $mid2,
                'text' => "<tg-emoji emoji-id='5231012545799666522'>🔍</tg-emoji> <i>Qidirilmoqda...</i>",
                'parse_mode' => 'html',
            ]);
            bot('editMessageText', [
                'business_connection_id' => $business_id2,
                'chat_id' => $cid2,
                'message_id' => $mid2,
                'text' => "<tg-emoji emoji-id='5260293700088511294'>⛔️</tg-emoji> <b>Ma'lumotlar topilmadi!</b>",
                'parse_mode' => 'html',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [['text' => "◀️ Orqaga", 'callback_data' => "qsozlash"]]
                    ]
                ])
            ]);
        }
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ Bu tugma siz uchun mo'ljallanmagan!",
            'show_alert' => false
        ]);
    }
}


if ($data == "savol") {
    if ($callfrid == $admin) {
        bot('editMessageText', [
            'business_connection_id' => $business_id2,
            'chat_id' => $cid2,
            'message_id' => $mid2,
            'text' => "<tg-emoji emoji-id='5443038326535759644'>💬</tg-emoji> <b>Savolingizni yuboring:</b>",
            'parse_mode' => 'html',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => "◀️ Orqaga", 'callback_data' => "qsozlash"]]
                ]
            ])
        ]);
        file_put_contents("step/$cid2.txt", "savol");
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ Bu tugma siz uchun mo'ljallanmagan!",
            'show_alert' => false
        ]);
    }
}

if ($step == "savol" && $business_from_id == $admin && isset($business_text)) {
    bot('editMessageText', [
        'business_connection_id' => $business_id,
        'chat_id' => $business_chat_id,
        'message_id' => $business_message_id,
        'text' => "<tg-emoji emoji-id='5206607081334906820'>✔️</tg-emoji> $business_text <b>qabul qilindi!</b>\n\n<i>Birinchi variantni kiriting:</i>",
        'parse_mode' => 'html',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => "◀️ Orqaga", 'callback_data' => "qsozlash"]]
            ]
        ])
    ]);
    file_put_contents("step/$business_chat_id.txt", "variant&$business_text");
}

if (mb_stripos($step, "variant&") !== false && $business_from_id == $admin && isset($business_text)) {
    $ex = explode("&", $step)[1];
    bot('editMessageText', [
        'business_connection_id' => $business_id,
        'chat_id' => $business_chat_id,
        'message_id' => $business_message_id,
        'text' => "<tg-emoji emoji-id='5206607081334906820'>✔️</tg-emoji> $business_text <b>qabul qilindi!</b>\n\n<i>Ikkinchi variantni kiriting:</i>",
        'parse_mode' => 'html',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => "◀️ Orqaga", 'callback_data' => "qsozlash"]]
            ]
        ])
    ]);
    file_put_contents("step/$business_chat_id.txt", "variant2&$ex&$business_text");
}

if (mb_stripos($step, "variant2&") !== false && $business_from_id == $admin && isset($business_text)) {
    $savol = explode("&", $step)[1];
    $variant = explode("&", $step)[2];
    bot('editMessageText', [
        'business_connection_id' => $business_id,
        'chat_id' => $business_chat_id,
        'message_id' => $business_message_id,
        'text' => "<tg-emoji emoji-id='5206607081334906820'>✔️</tg-emoji> $business_text <b>qabul qilindi!</b>\n\n<i>Uchinchi variantni kiriting:</i>",
        'parse_mode' => 'html',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => "◀️ Orqaga", 'callback_data' => "qsozlash"]]
            ]
        ])
    ]);
    file_put_contents("step/$business_chat_id.txt", "variant3&$savol&$variant&$business_text");
}

if (mb_stripos($step, "variant3&") !== false && $business_from_id == $admin && isset($business_text)) {
    $savol = explode("&", $step)[1];
    $variant_1 = explode("&", $step)[2];
    $variant_2 = explode("&", $step)[3];
    bot('editMessageText', [
        'business_connection_id' => $business_id,
        'chat_id' => $business_chat_id,
        'message_id' => $business_message_id,
        'text' => "<tg-emoji emoji-id='5206607081334906820'>✔️</tg-emoji> $business_text <b>qabul qilindi!</b>\n\n<b>1.</b> $variant_1\n<b>2.</b> $variant_2\n<b>3.</b> $business_text\n\n<i>To'g'ri javob raqamini kiriting:</i>",
        'parse_mode' => 'html',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => "◀️ Orqaga", 'callback_data' => "qsozlash"]]
            ]
        ])
    ]);
    file_put_contents("step/$business_chat_id.txt", "javob&$savol&$variant_1&$variant_2&$business_text");
}

if (mb_stripos($step, "javob&") !== false && $business_from_id == $admin) {
    if (is_numeric($business_text) == true) {
        if ($business_text >= 1 && $business_text <= 3) {
            $savol = explode("&", $step)[1];
            $variant_1 = explode("&", $step)[2];
            $variant_2 = explode("&", $step)[3];
            $variant_3 = explode("&", $step)[4];
            bot('editMessageText', [
                'business_connection_id' => $business_id,
                'chat_id' => $business_chat_id,
                'message_id' => $business_message_id,
                'text' => "<tg-emoji emoji-id='5206607081334906820'>✔️</tg-emoji> $business_text <b>qabul qilindi!</b>\n\n<b>1.</b> $variant_1\n<b>2.</b> $variant_2\n<b>3.</b> $variant_3\n\n<b>To'g'ri javob:</b> $business_text - variant\n\n<i>Javob uchun qisqacha izoh kiriting:</i>",
                'parse_mode' => 'html',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [['text' => "◀️ Orqaga", 'callback_data' => "qsozlash"]]
                    ]
                ])
            ]);
            file_put_contents("step/$business_chat_id.txt", "izoh&$savol&$variant_1&$variant_2&$variant_3&$business_text");
        } else {
            bot('editMessageText', [
                'business_connection_id' => $business_id,
                'chat_id' => $business_chat_id,
                'message_id' => $business_message_id,
                'text' => "<b>Qiymat noto'g'ri kiritildi!</b>",
                'parse_mode' => 'html',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [['text' => "◀️ Orqaga", 'callback_data' => "qsozlash"]]
                    ]
                ])
            ]);
        }
    } else {
        bot('editMessageText', [
            'business_connection_id' => $business_id,
            'chat_id' => $business_chat_id,
            'message_id' => $business_message_id,
            'text' => "<b>Faqat raqam kiriting!</b>",
            'parse_mode' => 'html',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => "◀️ Orqaga", 'callback_data' => "qsozlash"]]
                ]
            ])
        ]);
    }
}

if (mb_stripos($step, "izoh&") !== false && $business_from_id == $admin && isset($business_text)) {
    if (mb_strlen($business_text, 'UTF-8') <= 200) {
        $savol = explode("&", $step)[1];
        $variant_1 = explode("&", $step)[2];
        $variant_2 = explode("&", $step)[3];
        $variant_3 = explode("&", $step)[4];
        $javob = explode("&", $step)[5];
        $json = json_decode(file_get_contents("data/question.json"), true);
        $new = [
            'savol' => $savol,
            'variant_1' => $variant_1,
            'variant_2' => $variant_2,
            'variant_3' => $variant_3,
            'javob' => $javob,
            'izoh' => $business_text
        ];
        $json[] = $new;
        file_put_contents("data/question.json", json_encode($json, JSON_PRETTY_PRINT));
        bot('editMessageText', [
            'business_connection_id' => $business_id,
            'chat_id' => $business_chat_id,
            'message_id' => $business_message_id,
            'text' => "<tg-emoji emoji-id='5206607081334906820'>✔️</tg-emoji> <b>Savol muvaffaqiyatli qo'shildi!</b>

<tg-emoji emoji-id='5443038326535759644'>💬</tg-emoji> <b>Savol:</b> $savol

<b>1 - variant:</b> $variant_1
<b>2 - variant:</b> $variant_2
<b>3 - variant:</b> $variant_3

<b>To'g'ri javob:</b> $javob - variant

<tg-emoji emoji-id='5282843764451195532'>🖥</tg-emoji> <b>Izoh:</b> $business_text",
            'parse_mode' => 'html',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => "◀️ Orqaga", 'callback_data' => "qsozlash"]]
                ]
            ])
        ]);
        unlink("step/$business_chat_id.txt");
    } else {
        bot('editMessageText', [
            'business_connection_id' => $business_id,
            'chat_id' => $business_chat_id,
            'message_id' => $business_message_id,
            'text' => "<b>Qiymat qabul qilinmadi!</b>
            
Kiritilgan izoh uzunligi 200 ta belgidan oshmasligi kerak!",
            'parse_mode' => 'html',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => "◀️ Orqaga", 'callback_data' => "qsozlash"]]
                ]
            ])
        ]);
    }
}


if ($business_text == ".quiz" && $business_from_id == $admin) {
    if ($quiz == "on") {
        $savol = file_get_contents("data/question.json");

        if ($savol != null) {
            $used_questions_file = 'data/cache/' . $business_chat_id . '.json';
            $used_questions = file_exists($used_questions_file)
                ? json_decode(file_get_contents($used_questions_file), true)
                : [];

            $json = json_decode($savol, true);
            $question_count = count($json);
            $remaining_questions = array_diff(range(0, $question_count - 1), $used_questions);

            if (empty($remaining_questions)) {
                bot('sendChatAction', [
                    'business_connection_id' => $business_id,
                    'chat_id' => $business_chat_id,
                    'action' => "typing"
                ]);
                bot('editMessageText', [
                    'business_connection_id' => $business_id,
                    'chat_id' => $business_chat_id,
                    'message_id' => $business_message_id,
                    'text' => "<tg-emoji emoji-id='5447644880824181073'>⚠️</tg-emoji> <b>Savollar tugadi!</b>",
                    'parse_mode' => 'html',
                ]);
                exit();
            }

            $rand_index = array_rand($remaining_questions);
            $rand = $remaining_questions[$rand_index];

            $used_questions[] = $rand;
            file_put_contents($used_questions_file, json_encode($used_questions, JSON_PRETTY_PRINT));

            $savol = $json[$rand]['savol'];
            $variant_1 = $json[$rand]['variant_1'];
            $variant_2 = $json[$rand]['variant_2'];
            $variant_3 = $json[$rand]['variant_3'];
            $j = $json[$rand]['javob'];
            $javob = $json[$rand]["variant_$j"];
            $izoh = $json[$rand]['izoh'];
            $t = $j - 1;

            bot('SendPoll', [
                'business_connection_id' => $business_id,
                'chat_id' => $business_chat_id,
                'reply_to_message_id' => $business_message_id,
                'question' => $savol,
                'type' => 'quiz',
                'explanation' => $izoh,
                'open_period' => 30,
                'correct_option_id' => $t,
                'is_anonymous' => true,
                'options' => json_encode([$variant_1, $variant_2, $variant_3]),
            ]);
        } else {
            bot('sendChatAction', [
                'business_connection_id' => $business_id,
                'chat_id' => $business_chat_id,
                'action' => "typing"
            ]);
            bot('editMessageText', [
                'business_connection_id' => $business_id,
                'chat_id' => $business_chat_id,
                'message_id' => $business_message_id,
                'text' => "<tg-emoji emoji-id='5447644880824181073'>⚠️</tg-emoji> <b>Savollar mavjud emas!</b>",
                'parse_mode' => 'html',
            ]);
        }
    } else {
        bot('sendChatAction', [
            'business_connection_id' => $business_id,
            'chat_id' => $business_chat_id,
            'action' => "typing"
        ]);
        bot('editMessageText', [
            'business_connection_id' => $business_id,
            'chat_id' => $business_chat_id,
            'message_id' => $business_message_id,
            'text' => "<tg-emoji emoji-id='5447644880824181073'>⚠️</tg-emoji> <b>So'rovnoma faolsizlantirilgan!</b>",
            'parse_mode' => 'html',
        ]);
    }
}


//Avto javob

if (strpos($data, "avto") !== false) {
    if ($callfrid != $admin) {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ Bu tugma siz uchun mo'ljallanmagan!",
            'show_alert' => false
        ]);
        return;
    }

    $ex = strpos($data, "=") !== false ? explode("=", $data)[1] : $avto;
    $on = $ex === "on" ? "« ✅ »" : "✅";
    $off = $ex === "off" ? "« ☑ »" : "☑";
    $res = $ex === "on" ? "Faollashtirilgan!" : "Faolsizlantirilgan!";

    $sozlamalar = [
        ['text' => "⚙ Sozlamalar", 'callback_data' => "asozlash"]
    ];
    $holatlar = [
        ['text' => $on, 'callback_data' => "avto=on"],
        ['text' => $off, 'callback_data' => "avto=off"]
    ];
    $orqaga = [
        ['text' => "◀️ Orqaga", 'callback_data' => "settings"]
    ];

    if ($ex === "on") {
        $inline_keyboard = [$sozlamalar, $holatlar, $orqaga];
    } else {
        $inline_keyboard = [$holatlar, $orqaga];
    }

    if ($data === "avto" || $avto != $ex) {
        if ($avto != $ex) {
            file_put_contents("data/auto-answer.txt", $ex);
        }
        bot('editMessageText', [
            'business_connection_id' => $business_id2,
            'chat_id' => $cid2,
            'message_id' => $mid2,
            'text' => "<tg-emoji emoji-id='5341715473882955310'>⚙️</tg-emoji> <b>Avto javob sozlamalaridasiz!</b>

<i>— Hozirgi holat: $res</i>

<tg-emoji emoji-id='5406745015365943482'>⬇️</tg-emoji> <b>Chatlarda foydalanish:</b>

<i>Barcha chatlarda avtomatik tarzda amalga oshiriladi!</i>",
            'parse_mode' => 'html',
            'reply_markup' => json_encode(['inline_keyboard' => $inline_keyboard])
        ]);
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ $res",
            'show_alert' => true,
        ]);
    }
}


if ($data == "asozlash") {
    if ($callfrid == $admin) {
        bot('editMessageText', [
            'business_connection_id' => $business_id2,
            'chat_id' => $cid2,
            'message_id' => $mid2,
            'text' => "<tg-emoji emoji-id='5406745015365943482'>⬇️</tg-emoji> <b>Quyidagilardan birini tanlang!</b>",
            'parse_mode' => 'html',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => "📝 Ro'yxat", 'callback_data' => "list"]],
                    [['text' => "➕ Qo'shish", 'callback_data' => "add"], ['text' => "🗑 O'chirish", 'callback_data' => "delete"]],
                    [['text' => "✏️ Tahrirlash", 'callback_data' => "rename"]],
                    [['text' => "◀️ Orqaga", 'callback_data' => "avto"]]
                ]
            ])
        ]);
        unlink("step/$cid2.txt");
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ Bu tugma siz uchun mo'ljallanmagan!",
            'show_alert' => false
        ]);
    }
}

if ($data == "list" || strpos($data, "list&page=") === 0) {
    if ($callfrid == $admin) {
        $json = json_decode(file_get_contents('data/words.json'), true);

        $matn = "";
        $i = 1;

        $items_per_page = 5;
        $total_items = count($json);
        $total_pages = ceil($total_items / $items_per_page);

        if (strpos($data, "list&page=") === 0) {
            $page = (int) str_replace("list&page=", "", $data);
        } else {
            $page = 1;
        }

        $page = max(1, min($total_pages, $page));

        if ($data == "list&page=" . ($page - 1) || $data == "list&page=" . ($page + 1)) {
            if ($page == 1 && $data == "list&page=" . ($page - 1)) {
                bot('answerCallbackQuery', [
                    'callback_query_id' => $qid,
                    'text' => "Bosh sahifadasiz!",
                    'show_alert' => true
                ]);
                return;
            } elseif ($page == $total_pages && $data == "list&page=" . ($page + 1)) {
                bot('answerCallbackQuery', [
                    'callback_query_id' => $qid,
                    'text' => "Mavjud emas!",
                    'show_alert' => true
                ]);
                return;
            }
        }

        $current_items = array_slice($json, ($page - 1) * $items_per_page, $items_per_page);

        foreach ($current_items as $soz => $javob) {
            $matn .= "<b>" . ($i + ($page - 1) * $items_per_page) . ".</b> " . $soz . " - " . $javob . "\n";
            $i++;
        }

        if (empty($json)) {
            $matn2 = "<tg-emoji emoji-id='5260293700088511294'>⛔️</tg-emoji> <b>Ma'lumotlar topilmadi!</b>";
            $keys = json_encode([
                'inline_keyboard' => [
                    [['text' => "◀️ Orqaga", 'callback_data' => "asozlash"]]
                ]
            ]);
        } else {
            $matn2 = "<tg-emoji emoji-id='5406745015365943482'>⬇️</tg-emoji> <b>Hozirda mavjud so'zlar!</b>\n\n<i>$matn</i>";

            $keyboard2 = [
                [
                    ['text' => "⬅️", 'callback_data' => "list&page=" . ($page - 1)],
                    ['text' => "$page/$total_pages", 'callback_data' => "none"],
                    ['text' => "➡️️", 'callback_data' => "list&page=" . ($page + 1)],
                ],
                [
                    ['text' => "📝 Formatsiz ko'rish", 'callback_data' => "formatsiz"],
                ],
                [
                    ['text' => "◀️ Orqaga", 'callback_data' => "asozlash"],
                ]
            ];

            $keys = json_encode([
                'inline_keyboard' => $keyboard2,
            ]);

            bot('editMessageText', [
                'business_connection_id' => $business_id2,
                'chat_id' => $cid2,
                'message_id' => $mid2,
                'text' => "<tg-emoji emoji-id='5231012545799666522'>🔍</tg-emoji> <i>Qidirilmoqda...</i>",
                'parse_mode' => 'html',
            ]);
            bot('editMessageText', [
                'business_connection_id' => $business_id2,
                'chat_id' => $cid2,
                'message_id' => $mid2,
                'text' => $matn2,
                'parse_mode' => 'html',
                'reply_markup' => $keys
            ]);
        }
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ Bu tugma siz uchun mo'ljallanmagan!",
            'show_alert' => false
        ]);
    }
}


if ($data == "formatsiz" || strpos($data, "formatsiz&page=") === 0) {
    if ($callfrid == $admin) {
        $json = json_decode(file_get_contents('data/words.json'), true);

        $matn = "";
        $i = 1;

        $items_per_page = 5;
        $total_items = count($json);
        $total_pages = ceil($total_items / $items_per_page);

        if (strpos($data, "formatsiz&page=") === 0) {
            $page = (int) str_replace("formatsiz&page=", "", $data);
        } else {
            $page = 1;
        }

        $page = max(1, min($total_pages, $page));

        if ($data == "formatsiz&page=" . ($page - 1) || $data == "formatsiz&page=" . ($page + 1)) {
            if ($page == 1 && $data == "formatsiz&page=" . ($page - 1)) {
                bot('answerCallbackQuery', [
                    'callback_query_id' => $qid,
                    'text' => "Bosh sahifadasiz!",
                    'show_alert' => true
                ]);
                return;
            } elseif ($page == $total_pages && $data == "formatsiz&page=" . ($page + 1)) {
                bot('answerCallbackQuery', [
                    'callback_query_id' => $qid,
                    'text' => "Mavjud emas!",
                    'show_alert' => true
                ]);
                return;
            }
        }

        $current_items = array_slice($json, ($page - 1) * $items_per_page, $items_per_page);

        foreach ($current_items as $soz => $javob) {
            $matn .= "<b>" . ($i + ($page - 1) * $items_per_page) . ".</b> " . $soz . " - " . htmlspecialchars($javob) . "\n";
            $i++;
        }

        if (empty($json)) {
            $matn2 = "<tg-emoji emoji-id='5260293700088511294'>⛔️</tg-emoji> <b>Ma'lumotlar topilmadi!</b>";
            $keys = json_encode([
                'inline_keyboard' => [
                    [['text' => "◀️ Orqaga", 'callback_data' => "asozlash"]]
                ]
            ]);
        } else {
            $matn2 = "<tg-emoji emoji-id='5406745015365943482'>⬇️</tg-emoji> <b>Hozirda mavjud so'zlar!</b>\n\n<i>$matn</i>";

            $keyboard2 = [
                [
                    ['text' => "⬅️", 'callback_data' => "formatsiz&page=" . ($page - 1)],
                    ['text' => "$page/$total_pages", 'callback_data' => "none"],
                    ['text' => "➡️️", 'callback_data' => "formatsiz&page=" . ($page + 1)],
                ],
                [
                    ['text' => "📝 Formatli ko'rish", 'callback_data' => "list"],
                ],
                [
                    ['text' => "◀️ Orqaga", 'callback_data' => "asozlash"],
                ]
            ];

            $keys = json_encode([
                'inline_keyboard' => $keyboard2,
            ]);

            bot('editMessageText', [
                'business_connection_id' => $business_id2,
                'chat_id' => $cid2,
                'message_id' => $mid2,
                'text' => "<tg-emoji emoji-id='5231012545799666522'>🔍</tg-emoji> <i>Qidirilmoqda...</i>",
                'parse_mode' => 'html',
            ]);
            bot('editMessageText', [
                'business_connection_id' => $business_id2,
                'chat_id' => $cid2,
                'message_id' => $mid2,
                'text' => $matn2,
                'parse_mode' => 'html',
                'reply_markup' => $keys
            ]);
        }
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ Bu tugma siz uchun mo'ljallanmagan!",
            'show_alert' => false
        ]);
    }
}

if ($data == "add") {
    if ($callfrid == $admin) {
        bot('editMessageText', [
            'business_connection_id' => $business_id2,
            'chat_id' => $cid2,
            'message_id' => $mid2,
            'text' => "<tg-emoji emoji-id='5443038326535759644'>💬</tg-emoji> <b>Kerakli so'zni yuboring:</b>",
            'parse_mode' => 'html',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => "◀️ Orqaga", 'callback_data' => "asozlash"]]
                ]
            ])
        ]);
        file_put_contents("step/$cid2.txt", "add");
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ Bu tugma siz uchun mo'ljallanmagan!",
            'show_alert' => false
        ]);
    }
}

if ($step == "add" && $business_from_id == $admin) {
    if (isset($business_text)) {
        $sozlar_file = "data/words.json";
        $sozlar = json_decode(file_get_contents($sozlar_file), true);

        function normalizeText($text)
        {
            return preg_replace('/[^\p{L}\p{N}\s]/u', '', mb_strtolower($text));
        }

        $normalized_business_text = normalizeText($business_text);

        $text_found = false;

        foreach ($sozlar as $soz => $javob) {
            $normalized_soz = normalizeText($soz);
            if (trim($normalized_soz) === $normalized_business_text) {
                $text_found = true;
                break;
            }
        }

        if ($text_found) {
            bot('editMessageText', [
                'business_connection_id' => $business_id,
                'chat_id' => $business_chat_id,
                'message_id' => $business_message_id,
                'text' => "<tg-emoji emoji-id='5447644880824181073'>⚠️</tg-emoji> $business_text <b>qabul qilinmadi!</b>
                
<i>Ushbu so'zdan avval foydalanilgan! Boshqa so'z kiriting:</i>",
                'parse_mode' => 'html',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [['text' => "◀️ Orqaga", 'callback_data' => "asozlash"]]
                    ]
                ])
            ]);
        } else {
            bot('editMessageText', [
                'business_connection_id' => $business_id,
                'chat_id' => $business_chat_id,
                'message_id' => $business_message_id,
                'text' => "<tg-emoji emoji-id='5206607081334906820'>✔️</tg-emoji> $business_text <b>qabul qilindi!</b>

<code>%first%</code> - <b>Foydalanuvchi ismi</b>
<code>%last%</code> - <b>Foydalanuvchi familiyasi</b>
<code>%id%</code> - <b>Foydalanuvchi ID sini ko'rsatadi</b>
<code>%username%</code> - <b>Foydalanuvchi useri</b>
<code>%hour%</code> - <b>Soatni ko'rsatadi</b>
<code>%date%</code> - <b>Sanani ko'rsatadi</b>

<tg-emoji emoji-id='5395444784611480792'>✏️</tg-emoji> <b>Tahlil qilish rejimi:</b> <a href='https://help.publer.io/en/article/how-to-style-telegram-text-using-html-tags-xdepnw/'>HTML</a>

<i>Endi esa, ushbu so'z uchun kerakli javobni kiriting:</i>",
                'disable_web_page_preview' => true,
                'parse_mode' => 'html',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [['text' => "◀️ Orqaga", 'callback_data' => "asozlash"]]
                    ]
                ])
            ]);
            file_put_contents("step/$business_chat_id.txt", "sozlar&$business_text");
        }
    }
}


if (mb_stripos($step, "sozlar&") !== false) {
    if (isset($business_text) and ($business_from_id == $admin)) {
        $tx = htmlspecialchars($business_text);
        if (htmlCheck($business_text) == true) {

            $matn = explode("&", $step)[1];
            $json = json_decode(file_get_contents('data/words.json'), true);
            $json[$matn] = "$business_text";
            file_put_contents('data/words.json', json_encode($json));

            bot('editMessageText', [
                'business_connection_id' => $business_id,
                'chat_id' => $business_chat_id,
                'message_id' => $business_message_id,
                'text' => "<tg-emoji emoji-id='5206607081334906820'>✔️</tg-emoji> <code>$tx</code> <b>qabul qilindi!</b>
	
<i>Ma'lumotlar muvaffaqiyatli o'rnatildi!</i>",
                'parse_mode' => 'html',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [['text' => "◀️ Orqaga", 'callback_data' => "asozlash"]]
                    ]
                ])
            ]);
            unlink("step/$business_chat_id.txt");
        } else {
            bot('editMessageText', [
                'business_connection_id' => $business_id,
                'chat_id' => $business_chat_id,
                'message_id' => $business_message_id,
                'text' => "<tg-emoji emoji-id='5447644880824181073'>⚠️</tg-emoji> <code>$tx</code> <b>qabul qilinmadi!</b>
	
<i>Matnda sintaktik xatolar bor!</i>",
                'parse_mode' => 'html',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [['text' => "◀️ Orqaga", 'callback_data' => "asozlash"]]
                    ]
                ])
            ]);
        }
    }
}

if ($data == "delete" || strpos($data, "delete&page=") === 0) {
    if ($callfrid == $admin) {
        $json = json_decode(file_get_contents('data/words.json'), true);

        if (!empty($json)) {
            $keys = array_keys($json);
            $soni = count($keys);

            if (strpos($data, "delete&page=") === 0) {
                $page = (int) str_replace("delete&page=", "", $data);
            } else {
                $page = 1;
            }

            $limit = 5;
            $offset = ($page - 1) * $limit;
            $total_pages = ceil($soni / $limit);
            $page = max(1, min($total_pages, $page));

            if ($data == "delete&page=" . ($page - 1) || $data == "delete&page=" . ($page + 1)) {
                if ($page == 1 && $data == "delete&page=" . ($page - 1)) {
                    bot('answerCallbackQuery', [
                        'callback_query_id' => $qid,
                        'text' => "Bosh sahifadasiz!",
                        'show_alert' => true
                    ]);
                    return;
                } elseif ($page == $total_pages && $data == "delete&page=" . ($page + 1)) {
                    bot('answerCallbackQuery', [
                        'callback_query_id' => $qid,
                        'text' => "Mavjud emas!",
                        'show_alert' => true
                    ]);
                    return;
                }
            }

            $key = [];
            for ($for = $offset; $for < min($offset + $limit, $soni); $for++) {
                $savol = $keys[$for];
                $javob = strip_tags($json[$savol]);
                $key[] = ["text" => "$savol - $javob", "callback_data" => "atanla=$savol"];
            }

            $keyboard2 = [
                [
                    ['text' => "⬅️", 'callback_data' => "delete&page=" . ($page - 1)],
                    ['text' => "$page/$total_pages", 'callback_data' => "none"],
                    ['text' => "➡️️", 'callback_data' => "delete&page=" . ($page + 1)],
                ],
                [
                    ['text' => "◀️ Orqaga", 'callback_data' => "asozlash"],
                ]
            ];

            $keyboard2 = array_merge(array_chunk($key, 1), $keyboard2);
            $keys = json_encode([
                'inline_keyboard' => $keyboard2,
            ]);

            bot('editMessageText', [
                'business_connection_id' => $business_id2,
                'chat_id' => $cid2,
                'message_id' => $mid2,
                'text' => "<tg-emoji emoji-id='5231012545799666522'>🔍</tg-emoji> <i>Qidirilmoqda...</i>",
                'parse_mode' => 'html',
            ]);
            bot('editMessageText', [
                'business_connection_id' => $business_id2,
                'chat_id' => $cid2,
                'message_id' => $mid2,
                'text' => "<tg-emoji emoji-id='5406745015365943482'>⬇️</tg-emoji> <b>Quyidagilardan birini tanlang!</b>",
                'parse_mode' => 'html',
                'reply_markup' => $keys
            ]);
        } else {
            bot('editMessageText', [
                'business_connection_id' => $business_id2,
                'chat_id' => $cid2,
                'message_id' => $mid2,
                'text' => "<tg-emoji emoji-id='5231012545799666522'>🔍</tg-emoji> <i>Qidirilmoqda...</i>",
                'parse_mode' => 'html',
            ]);
            bot('editMessageText', [
                'business_connection_id' => $business_id2,
                'chat_id' => $cid2,
                'message_id' => $mid2,
                'text' => "<tg-emoji emoji-id='5260293700088511294'>⛔️</tg-emoji> <b>Ma'lumotlar topilmadi!</b>",
                'parse_mode' => 'html',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [['text' => "◀️ Orqaga", 'callback_data' => "asozlash"]]
                    ]
                ])
            ]);
        }
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ Bu tugma siz uchun mo'ljallanmagan!",
            'show_alert' => false
        ]);
    }
}


if (mb_stripos($data, "atanla=") !== false) {
    if ($callfrid == $admin) {
        $ex = explode("=", $data)[1];
        $json = json_decode(file_get_contents("data/words.json"), true);
        unset($json[$ex]);
        $json2 = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents("data/words.json", $json2);
        bot('editMessageText', [
            'business_connection_id' => $business_id2,
            'chat_id' => $cid2,
            'message_id' => $mid2,
            'text' => "<tg-emoji emoji-id='5445267414562389170'>🗑</tg-emoji> <i>O'chirilmoqda...</i>",
            'parse_mode' => 'html',
        ]);
        bot('editMessageText', [
            'business_connection_id' => $business_id2,
            'chat_id' => $cid2,
            'message_id' => $mid2,
            'text' => "<tg-emoji emoji-id='5206607081334906820'>✔️</tg-emoji> $ex <b>o'chirib tashlandi!</b>",
            'parse_mode' => 'html',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => "◀️ Orqaga", 'callback_data' => "delete"]]
                ]
            ])
        ]);
        unlink("step/$cid2.txt");
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ Bu tugma siz uchun mo'ljallanmagan!",
            'show_alert' => false
        ]);
    }
}


if ($data == "rename" || strpos($data, "rename&page=") === 0) {
    if ($callfrid == $admin) {
        $json = json_decode(file_get_contents('data/words.json'), true);

        if (!empty($json)) {
            $keys = array_keys($json);
            $soni = count($keys);

            if (strpos($data, "rename&page=") === 0) {
                $page = (int) str_replace("rename&page=", "", $data);
            } else {
                $page = 1;
            }

            $limit = 5;
            $offset = ($page - 1) * $limit;
            $total_pages = ceil($soni / $limit);

            $page = max(1, min($total_pages, $page));

            if ($data == "rename&page=" . ($page - 1) || $data == "rename&page=" . ($page + 1)) {
                if ($page == 1 && $data == "rename&page=" . ($page - 1)) {
                    bot('answerCallbackQuery', [
                        'callback_query_id' => $qid,
                        'text' => "Bosh sahifadasiz!",
                        'show_alert' => true
                    ]);
                    return;
                } elseif ($page == $total_pages && $data == "rename&page=" . ($page + 1)) {
                    bot('answerCallbackQuery', [
                        'callback_query_id' => $qid,
                        'text' => "Mavjud emas!",
                        'show_alert' => true
                    ]);
                    return;
                }
            }

            $key = [];
            for ($for = $offset; $for < min($offset + $limit, $soni); $for++) {
                $savol = $keys[$for];
                $javob = strip_tags($json[$savol]);
                $key[] = ["text" => "$savol - $javob", "callback_data" => "renameAnswer=$savol"];
            }

            $keyboard2 = [
                [
                    ['text' => "⬅️", 'callback_data' => "rename&page=" . ($page - 1)],
                    ['text' => "$page/$total_pages", 'callback_data' => "none"],
                    ['text' => "➡️️", 'callback_data' => "rename&page=" . ($page + 1)],
                ],
                [
                    ['text' => "◀️ Orqaga", 'callback_data' => "asozlash"],
                ]
            ];

            $keyboard2 = array_merge(array_chunk($key, 1), $keyboard2);
            $keys = json_encode([
                'inline_keyboard' => $keyboard2,
            ]);

            bot('editMessageText', [
                'business_connection_id' => $business_id2,
                'chat_id' => $cid2,
                'message_id' => $mid2,
                'text' => "<tg-emoji emoji-id='5231012545799666522'>🔍</tg-emoji> <i>Qidirilmoqda...</i>",
                'parse_mode' => 'html',
            ]);
            bot('editMessageText', [
                'business_connection_id' => $business_id2,
                'chat_id' => $cid2,
                'message_id' => $mid2,
                'text' => "<tg-emoji emoji-id='5406745015365943482'>⬇️</tg-emoji> <b>Quyidagilardan birini tanlang!</b>",
                'parse_mode' => 'html',
                'reply_markup' => $keys
            ]);
        } else {
            bot('editMessageText', [
                'business_connection_id' => $business_id2,
                'chat_id' => $cid2,
                'message_id' => $mid2,
                'text' => "<tg-emoji emoji-id='5231012545799666522'>🔍</tg-emoji> <i>Qidirilmoqda...</i>",
                'parse_mode' => 'html',
            ]);
            bot('editMessageText', [
                'business_connection_id' => $business_id2,
                'chat_id' => $cid2,
                'message_id' => $mid2,
                'text' => "<tg-emoji emoji-id='5260293700088511294'>⛔️</tg-emoji> <b>Ma'lumotlar topilmadi!</b>",
                'parse_mode' => 'html',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [['text' => "◀️ Orqaga", 'callback_data' => "asozlash"]]
                    ]
                ])
            ]);
        }
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ Bu tugma siz uchun mo'ljallanmagan!",
            'show_alert' => false
        ]);
    }
}


if (mb_stripos($data, "renameAnswer=") !== false) {
    if ($callfrid == $admin) {
        $ex = explode("=", $data)[1];
        $json = json_decode(file_get_contents('data/words.json'), true);
        $jsonText = strip_tags($json[$ex]);
        bot('editMessageText', [
            'business_connection_id' => $business_id2,
            'chat_id' => $cid2,
            'message_id' => $mid2,
            'text' => "<tg-emoji emoji-id='5231012545799666522'>🔍</tg-emoji> <i>Qidirilmoqda...</i>",
            'parse_mode' => 'html',
        ]);
        bot('editMessageText', [
            'business_connection_id' => $business_id2,
            'chat_id' => $cid2,
            'message_id' => $mid2,
            'text' => "<tg-emoji emoji-id='5395444784611480792'>✏️</tg-emoji> <b>Nimani tahrirlamoqchisiz?</b>
			
<i>Quyidagilardan birini tanlang:</i>",
            'parse_mode' => 'html',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => $ex, 'callback_data' => "renameText=$ex"]],
                    [['text' => $jsonText, 'callback_data' => "renameJson=$ex"]],
                    [['text' => "◀️ Orqaga", 'callback_data' => "rename"]]
                ]
            ])
        ]);
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ Bu tugma siz uchun mo'ljallanmagan!",
            'show_alert' => false
        ]);
    }
}

if (mb_stripos($data, "renameText=") !== false) {
    if ($callfrid == $admin) {
        $ex = explode("=", $data)[1];
        bot('editMessageText', [
            'business_connection_id' => $business_id2,
            'chat_id' => $cid2,
            'message_id' => $mid2,
            'text' => "<tg-emoji emoji-id='5282843764451195532'>🖥</tg-emoji> <b>Yangi qiymatni yuboring:</b>",
            'parse_mode' => 'html',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => "◀️ Orqaga", 'callback_data' => "rename"]]
                ]
            ])
        ]);
        file_put_contents("step/$cid2.txt", "renameText=$ex");
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ Bu tugma siz uchun mo'ljallanmagan!",
            'show_alert' => false
        ]);
    }
}

if (mb_stripos($step, "renameText=") !== false) {
    if (isset($business_text) and ($business_from_id == $admin)) {
        $ex = explode("=", $step)[1];
        $sozlar_file = "data/words.json";
        $sozlar = json_decode(file_get_contents($sozlar_file), true);

        function normalizeText($text)
        {
            return preg_replace('/[^\p{L}\p{N}\s]/u', '', mb_strtolower($text));
        }

        $normalized_business_text = normalizeText($business_text);

        $text_found = false;

        foreach ($sozlar as $soz => $javob) {
            $normalized_soz = normalizeText($soz);
            if (trim($normalized_soz) === $normalized_business_text) {
                $text_found = true;
                break;
            }
        }

        if ($text_found) {
            bot('editMessageText', [
                'business_connection_id' => $business_id,
                'chat_id' => $business_chat_id,
                'message_id' => $business_message_id,
                'text' => "<tg-emoji emoji-id='5447644880824181073'>⚠️</tg-emoji> $business_text <b>qabul qilinmadi!</b>
			
<i>Ushbu so'zdan avval foydalanilgan. Boshqa so'z kiriting:</i>",
                'parse_mode' => 'html',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [['text' => "◀️ Orqaga", 'callback_data' => "renameAnswer=$ex"]]
                    ]
                ])
            ]);
        } else {
            $matn = explode("=", $step)[1];
            $json = json_decode(file_get_contents("data/words.json"), true);

            if (isset($json[$matn])) {
                $json[$business_text] = $json[$matn];
                unset($json[$matn]);
            }
            file_put_contents("data/words.json", json_encode($json, JSON_PRETTY_PRINT));
            bot('editMessageText', [
                'business_connection_id' => $business_id,
                'chat_id' => $business_chat_id,
                'message_id' => $business_message_id,
                'text' => "<i>$business_text</i>

<tg-emoji emoji-id='5206607081334906820'>✔️</tg-emoji> <b>Ma'lumot muvaffaqiyatli o'zgartirildi!</b>",
                'parse_mode' => 'html',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [['text' => "◀️ Orqaga", 'callback_data' => "renameAnswer=$business_text"]]
                    ]
                ])
            ]);
            unlink("step/$business_chat_id.txt");
        }
    }
}

if (mb_stripos($data, "renameJson=") !== false) {
    if ($callfrid == $admin) {
        $ex = explode("=", $data)[1];
        bot('editMessageText', [
            'business_connection_id' => $business_id2,
            'chat_id' => $cid2,
            'message_id' => $mid2,
            'text' => "<tg-emoji emoji-id='5395444784611480792'>✏️</tg-emoji> <b>Tahlil qilish rejimi:</b> <a href='https://help.publer.io/en/article/how-to-style-telegram-text-using-html-tags-xdepnw/'>HTML</a>

<code>%first%</code> - <b>Foydalanuvchi ismi</b>
<code>%last%</code> - <b>Foydalanuvchi familiyasi</b>
<code>%id%</code> - <b>Foydalanuvchi ID sini ko'rsatadi</b>
<code>%username%</code> - <b>Foydalanuvchi useri</b>
<code>%hour%</code> - <b>Soatni ko'rsatadi</b>
<code>%date%</code> - <b>Sanani ko'rsatadi</b>

<tg-emoji emoji-id='5282843764451195532'>🖥</tg-emoji> <b>Yangi qiymatni yuboring:</b>",
            'disable_web_page_preview' => true,
            'parse_mode' => 'html',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => "◀️ Orqaga", 'callback_data' => "rename"]]
                ]
            ])
        ]);
        file_put_contents("step/$cid2.txt", "renameJson=$ex");
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ Bu tugma siz uchun mo'ljallanmagan!",
            'show_alert' => false
        ]);
    }
}

if (mb_stripos($step, "renameJson=") !== false) {
    if (isset($business_text) and ($business_from_id == $admin)) {
        $matn = explode("=", $step)[1];
        $tx = htmlspecialchars($business_text);
        if (htmlCheck($business_text) == true) {
            $json = json_decode(file_get_contents("data/words.json"), true);

            if (isset($json[$matn])) {
                $json[$matn] = $business_text;
            }
            file_put_contents("data/words.json", json_encode($json, JSON_PRETTY_PRINT));
            bot('editMessageText', [
                'business_connection_id' => $business_id,
                'chat_id' => $business_chat_id,
                'message_id' => $business_message_id,
                'text' => "<code>$tx</code>

<tg-emoji emoji-id='5206607081334906820'>✔️</tg-emoji> <b>Ma'lumot muvaffaqiyatli o'zgartirildi!</b>",
                'parse_mode' => 'html',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [['text' => "◀️ Orqaga", 'callback_data' => "renameAnswer=$matn"]]
                    ]
                ])
            ]);
            unlink("step/$business_chat_id.txt");
        } else {
            bot('editMessageText', [
                'business_connection_id' => $business_id,
                'chat_id' => $business_chat_id,
                'message_id' => $business_message_id,
                'text' => "<tg-emoji emoji-id='5447644880824181073'>⚠️</tg-emoji> <code>$tx</code> <b>qabul qilinmadi!</b>
		
<i>Matnda sintaktik xatolar bor!</i>",
                'parse_mode' => 'html',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [['text' => "◀️ Orqaga", 'callback_data' => "renameAnswer=$matn"]]
                    ]
                ])
            ]);
        }
    }
}

$json = json_decode(file_get_contents("data/words.json"), true);
if (isset($business_text)) {
    if ($business_from_id != $admin) {
        if ($avto == "on") {
            $responses = [];
            foreach ($json as $kalit => $qiymat) {
                $pattern = '/\b' . preg_quote($kalit, '/') . '\b/i';
                if (preg_match($pattern, $business_text)) {
                    $responses[] = $qiymat;
                }
            }

            if (!empty($responses)) {
                $result = str_replace(['%first%', '%last%', '%id%', '%username%', '%hour%', '%date%'], [$business_name, $business_lastname, $business_from_id, $business_username, $soat, $sana], $responses);
                bot('sendChatAction', [
                    'business_connection_id' => $business_id,
                    'chat_id' => $business_chat_id,
                    'action' => "typing"
                ]);

                $message = (strlen(implode(" ", $result)) > 50) ? implode("\n", $result) : implode(" ", $result);

                bot('sendMessage', [
                    'business_connection_id' => $business_id,
                    'chat_id' => $business_chat_id,
                    'text' => $message,
                    'parse_mode' => 'html',
                    'disable_web_page_preview' => true,
                ]);
            }
        }
    }
}


//Valyuta

if (strpos($data, "valyuta") !== false || mb_stripos($data, "vly=") !== false) {
    if ($callfrid != $admin) {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ Bu tugma siz uchun mo'ljallanmagan!",
            'show_alert' => false
        ]);
        return;
    }

    $ex = mb_stripos($data, "=") !== false ? explode("=", $data)[1] : $valyuta;
    $on = $ex === "on" ? "« ✅ »" : "✅";
    $off = $ex === "off" ? "« ☑ »" : "☑";
    $res = $ex === "on" ? "Faollashtirilgan!" : "Faolsizlantirilgan!";

    if ($data === "valyuta" || $valyuta != $ex) {
        if ($valyuta != $ex) {
            file_put_contents("data/currency.txt", $ex);
        }

        bot('editMessageText', [
            'business_connection_id' => $business_id2,
            'chat_id' => $cid2,
            'message_id' => $mid2,
            'text' => "<tg-emoji emoji-id='5341715473882955310'>⚙️</tg-emoji> <b>Valyuta sozlamalaridasiz!</b>
    
<i>— Hozirgi holat: $res</i>
    
<tg-emoji emoji-id='5406745015365943482'>⬇️</tg-emoji> <b>Chatlarda foydalanish:</b>
    
<code>.currency</code> – <i>Hozirgi valyuta kurslarini ko‘rsatadi.</i>",
            'parse_mode' => 'html',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => $on, 'callback_data' => "valyuta=on"], ['text' => $off, 'callback_data' => "valyuta=off"]],
                    [['text' => "◀️ Orqaga", 'callback_data' => "settings"]]
                ]
            ])
        ]);
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ $res",
            'show_alert' => true,
        ]);
    }
}


if ($business_text == ".currency") {
    if ($business_from_id == $admin) {
        if ($valyuta == "on") {
            $json = json_decode(file_get_contents("https://cbu.uz/uz/arkhiv-kursov-valyut/json/"), true);

            foreach ($json as $json2) {
                if ($json2['Ccy'] == "USD") {
                    $usd = $json2['Rate'];
                    break;
                }
            }
            foreach ($json as $json2) {
                if ($json2['Ccy'] == "EUR") {
                    $eur = $json2['Rate'];
                    break;
                }
            }
            foreach ($json as $json2) {
                if ($json2['Ccy'] == "RUB") {
                    $rub = $json2['Rate'];
                    break;
                }
            }

            bot('sendChatAction', [
                'business_connection_id' => $business_id,
                'chat_id' => $business_chat_id,
                'action' => "typing"
            ]);
            bot('editMessageText', [
                'business_connection_id' => $business_id,
                'chat_id' => $business_chat_id,
                'message_id' => $business_message_id,
                'text' => "<tg-emoji emoji-id='5231200819986047254'>📊</tg-emoji> <b>Valyuta kurslari!</b>

<tg-emoji emoji-id='5409048419211682843'>💵</tg-emoji> <i>1 ( USD ) - $usd UZS
<tg-emoji emoji-id='5233326571099534068'>💸</tg-emoji> 1 ( EURO ) - $eur UZS
<tg-emoji emoji-id='5231449120635370684'>💸</tg-emoji> 1 ( RUB ) - $rub UZS</i>",
                'parse_mode' => 'html',
            ]);
        } else {
            bot('sendChatAction', [
                'business_connection_id' => $business_id,
                'chat_id' => $business_chat_id,
                'action' => "typing"
            ]);
            bot('editMessageText', [
                'business_connection_id' => $business_id,
                'chat_id' => $business_chat_id,
                'message_id' => $business_message_id,
                'text' => "<tg-emoji emoji-id='5447644880824181073'>⚠️</tg-emoji> <b>Valyuta faolsizlantirilgan!</b>",
                'parse_mode' => 'html',
            ]);
        }
    }
}

//Ob-havo

if (strpos($data, "ob-havo") !== false) {
    if ($callfrid != $admin) {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ Bu tugma siz uchun mo'ljallanmagan!",
            'show_alert' => false
        ]);
        return;
    }

    $ex = strpos($data, "=") !== false ? explode("=", $data)[1] : $obhavo;
    $on = $ex === "on" ? "« ✅ »" : "✅";
    $off = $ex === "off" ? "« ☑ »" : "☑";
    $res = $ex === "on" ? "Faollashtirilgan!" : "Faolsizlantirilgan!";

    $keyboard = [
        ['text' => "⚙ Sozlamalar", 'callback_data' => "ob-sozlash"]
    ];
    $buttons = [
        ['text' => $on, 'callback_data' => "ob-havo=on"],
        ['text' => $off, 'callback_data' => "ob-havo=off"]
    ];

    if ($ex === "on") {
        $inline_keyboard = [$keyboard, $buttons, [['text' => "◀️ Orqaga", 'callback_data' => "settings"]]];
    } else {
        $inline_keyboard = [$buttons, [['text' => "◀️ Orqaga", 'callback_data' => "settings"]]];
    }

    if ($data === "ob-havo" || $obhavo != $ex) {
        if ($obhavo != $ex) {
            file_put_contents("data/weather.txt", $ex);
        }
        bot('editMessageText', [
            'business_connection_id' => $business_id2,
            'chat_id' => $cid2,
            'message_id' => $mid2,
            'text' => "<tg-emoji emoji-id='5341715473882955310'>⚙️</tg-emoji> <b>Ob-havo sozlamalaridasiz!</b>

<i>— Hozirgi holat: $res</i>

<tg-emoji emoji-id='5406745015365943482'>⬇️</tg-emoji> <b>Chatlarda foydalanish:</b>

<code>.weather</code> – <i>Ko‘rsatilgan shaharning ob-havo ma’lumotlarini taqdim etadi (masalan: <b>.weather ferghana</b>). ⚙ Sozlamalar –> 📝 Ro'yxat bo‘limida berilgan shahar kalitlaridan foydalaning.</i>",
            'parse_mode' => 'html',
            'reply_markup' => json_encode(['inline_keyboard' => $inline_keyboard])
        ]);
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ $res",
            'show_alert' => true,
        ]);
    }
}


if ($data == "ob-sozlash") {
    if ($callfrid == $admin) {
        bot('editMessageText', [
            'business_connection_id' => $business_id2,
            'chat_id' => $cid2,
            'message_id' => $mid2,
            'text' => "<tg-emoji emoji-id='5406745015365943482'>⬇️</tg-emoji> <b>Quyidagilardan birini tanlang!</b>",
            'parse_mode' => 'html',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => "📝 Ro'yxat", 'callback_data' => "ob-list"]],
                    [['text' => "◀️ Orqaga", 'callback_data' => "ob-havo"]]
                ]
            ])
        ]);
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ Bu tugma siz uchun mo'ljallanmagan!",
            'show_alert' => false
        ]);
    }
}

if ($data == "ob-list") {
    if ($callfrid == $admin) {
        bot('editMessageText', [
            'business_connection_id' => $business_id2,
            'chat_id' => $cid2,
            'message_id' => $mid2,
            'text' => "<tg-emoji emoji-id='5406745015365943482'>⬇️</tg-emoji> <b>Hozirda mavjud shaharlar!</b>

<b>1.</b> <code>tashkent</code> - <b>Toshkent</b>
<b>2.</b> <code>andijan</code> - <b>Andijon</b>
<b>3. </b> <code>bukhara</code> - <b>Buxoro</b>
<b>4.</b> <code>gulistan</code> - <b>Guliston</b>
<b>5.</b> <code>jizzakh</code> - <b>Jizzax</b>
<b>6.</b> <code>zarafshan</code> - <b>Zarafshon</b>
<b>7. </b> <code>karshi</code> - <b>Qarshi</b>
<b>8.</b> <code>navoi</code> - <b>Navoiy</b>
<b>9.</b> <code>namangan</code> - <b>Namangan</b>
<b>10.</b> <code>nukus</code> - <b>Nukus</b>
<b>11.</b> <code>samarkand</code> - <b>Samarqand</b>
<b>12.</b> <code>termez</code> - <b>Termiz</b>
<b>13.</b> <code>urgench</code> - <b>Urganch</b>
<b>14.</b> <code>ferghana</code> - <b>Farg'ona</b>
<b>15.</b> <code>khiva</code> - <b>Xiva</b>

<tg-emoji emoji-id='5282843764451195532'>🖥</tg-emoji> <b>Namuna:</b> <code>.weather tashkent</code>

<tg-emoji emoji-id='5447644880824181073'>⚠️</tg-emoji> <b>Shahar nomlarini berilgan namunadagi ko'rinishda kiriting!</b>",
            'parse_mode' => 'html',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => "◀️ Orqaga", 'callback_data' => "ob-sozlash"]]
                ]
            ])
        ]);
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ Bu tugma siz uchun mo'ljallanmagan!",
            'show_alert' => false
        ]);
    }
}

if (mb_stripos($business_text, ".weather") !== false) {
    if ($business_from_id == $admin) {
        if ($obhavo == "on") {
            $city = trim(substr($business_text, mb_stripos($business_text, ".weather ") + 9));
            if ($city != null) {
                $json = json_decode(file_get_contents($SERVER_NAME . "api/weather.php?city=$city"), true);

                $success = $json['success'];
                $error_message = $json['error_message'] ?? "Noma'lum xatolik!";

                $sana = $json['data']['bugun']['sana'];
                $harorat = $json['data']['bugun']['harorat'];
                $havo = $json['data']['bugun']['havo'];
                $namlik = $json['data']['bugun']['namlik'];
                $shamol = $json['data']['bugun']['shamol'];
                $bosim = $json['data']['bugun']['bosim'];

                $oy = $json['data']['vaqt']['oy'];
                $chiqish = $json['data']['vaqt']['quyosh_chiqishi'];
                $botish = $json['data']['vaqt']['quyosh_botishi'];

                $tun = $json['data']['harorat']['tong'];
                $kun = $json['data']['harorat']['kun'];
                $oqshom = $json['data']['harorat']['oqshom'];

                if ($success == true) {
                    bot('sendChatAction', [
                        'business_connection_id' => $business_id,
                        'chat_id' => $business_chat_id,
                        'action' => "typing"
                    ]);
                    bot('editMessageText', [
                        'business_connection_id' => $business_id,
                        'chat_id' => $business_chat_id,
                        'message_id' => $business_message_id,
                        'text' => "<tg-emoji emoji-id='5283155153875116393'>🌥</tg-emoji> <b>Ob-havo ma'lumotlari!</b>

<tg-emoji emoji-id='5431897022456145283'>📆</tg-emoji> <b>Bugun:</b>

• <b>Sana:</b> $sana
• <b>Harorat:</b> $harorat
• <b>Havo:</b> $havo
• <b>Namlik:</b> $namlik
• <b>Shamol:</b> $shamol
• <b>Bosim:</b> $bosim

<tg-emoji emoji-id='5451732530048802485'>⏳</tg-emoji> <b>Vaqt:</b>

• <b>Oy:</b> $oy
• <b>Quyosh chiqishi:</b> $chiqish
• <b>Quyosh botishi:</b> $botish

<tg-emoji emoji-id='5470049770997292425'>🌡</tg-emoji> <b>Harorat:</b>

• <b>Tong:</b> $tun
• <b>Kun:</b> $kun
• <b>Oqshom:</b> $oqshom",
                        'parse_mode' => 'html',
                    ]);
                } else {
                    bot('sendChatAction', [
                        'business_connection_id' => $business_id,
                        'chat_id' => $business_chat_id,
                        'action' => "typing"
                    ]);
                    bot('editMessageText', [
                        'business_connection_id' => $business_id,
                        'chat_id' => $business_chat_id,
                        'message_id' => $business_message_id,
                        'text' => "<tg-emoji emoji-id='5447644880824181073'>⚠️</tg-emoji> <b>$error_message</b>",
                        'parse_mode' => 'html',
                    ]);
                }
            } else {
                bot('sendChatAction', [
                    'business_connection_id' => $business_id,
                    'chat_id' => $business_chat_id,
                    'action' => "typing"
                ]);
                bot('editMessageText', [
                    'business_connection_id' => $business_id,
                    'chat_id' => $business_chat_id,
                    'message_id' => $business_message_id,
                    'text' => "<tg-emoji emoji-id='5447644880824181073'>⚠️</tg-emoji> <b>Kerakli shahar kiritilmadi!</b>",
                    'parse_mode' => 'html',
                ]);
            }
        } else {
            bot('sendChatAction', [
                'business_connection_id' => $business_id,
                'chat_id' => $business_chat_id,
                'action' => "typing"
            ]);
            bot('editMessageText', [
                'business_connection_id' => $business_id,
                'chat_id' => $business_chat_id,
                'message_id' => $business_message_id,
                'text' => "<tg-emoji emoji-id='5447644880824181073'>⚠️</tg-emoji> <b>Ob-havo faolsizlantirilgan!</b>",
                'parse_mode' => 'html',
            ]);
        }
    }
}


// Vaqtli media xabar

if (strpos($data, "vaqtli-media") !== false) {
    if ($callfrid != $admin) {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ Bu tugma siz uchun mo'ljallanmagan!",
            'show_alert' => false
        ]);
        return;
    }

    $ex = strpos($data, "=") !== false ? explode("=", $data)[1] : $vaqtli_media;
    $on = $ex === "on" ? "« ✅ »" : "✅";
    $off = $ex === "off" ? "« ☑ »" : "☑";
    $res = $ex === "on" ? "Faollashtirilgan!" : "Faolsizlantirilgan!";

    if ($data === "vaqtli-media" || $vaqtli_media != $ex) {
        if ($vaqtli_media != $ex) {
            file_put_contents("data/timed-media.txt", $ex);
        }
        bot('editMessageText', [
            'business_connection_id' => $business_id2,
            'chat_id' => $cid2,
            'message_id' => $mid2,
            'text' => "<tg-emoji emoji-id='5341715473882955310'>⚙️</tg-emoji> <b>Vaqtli media xabarlar sozlamalaridasiz!</b>

<i>— Hozirgi holat: $res</i>

<tg-emoji emoji-id='5406745015365943482'>⬇️</tg-emoji> <b>Chatlarda foydalanish:</b>

<code>😅</code> – <i><b>Vaqtli media xabarlarni saqlash.</b> Reply qilib yuborilgan vaqtli media xabarni <b>ochmasdan</b> shu buyruqni kiriting va bot uni siz uchun saqlab beradi.</i>",
            'parse_mode' => 'html',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => $on, 'callback_data' => "vaqtli-media=on"], ['text' => $off, 'callback_data' => "vaqtli-media=off"]],
                    [['text' => "◀️ Orqaga", 'callback_data' => "settings"]]
                ]
            ])
        ]);
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ $res",
            'show_alert' => true
        ]);
    }
}


if (isset($business->reply_to_message) && $business_text == "😅" && $business_from_id == $admin && $vaqtli_media == "on") {

    if (!is_dir('data/media'))
        mkdir('data/media', 0777, true);

    $r = $business->reply_to_message;
    if (isset($r->photo)) {
        $f = end($r->photo);
        $local = download($f->file_id, '.jpg');
        bot('sendPhoto', [
            'chat_id' => $admin,
            'photo' => curl_file_create($local),
        ]);
    } elseif (isset($r->document)) {
        $ext = '.' . pathinfo($r->document->file_name, PATHINFO_EXTENSION);
        $local = download($r->document->file_id, $ext);
        bot('sendDocument', [
            'chat_id' => $admin,
            'document' => curl_file_create($local),
        ]);
    } elseif (isset($r->video)) {
        $local = download($r->video->file_id, '.mp4');
        bot('sendVideo', [
            'chat_id' => $admin,
            'video' => curl_file_create($local),
        ]);
    } elseif (isset($r->voice)) {
        $local = download($r->voice->file_id, '.ogg');
        bot('sendVoice', [
            'chat_id' => $admin,
            'voice' => curl_file_create($local),
        ]);
    } elseif (isset($r->audio)) {
        $local = download($r->audio->file_id, '.mp3');
        bot('sendAudio', [
            'chat_id' => $admin,
            'audio' => curl_file_create($local),
        ]);
    } elseif (isset($r->video_note)) {
        $local = download($r->video_note->file_id, '.mp4');
        bot('sendVideoNote', [
            'chat_id' => $admin,
            'video_note' => curl_file_create($local),
        ]);
    }
    clear_media_folder('data/media');
}


// O'chirilgan xabarlar

if (strpos($data, "ochirilgan") !== false) {
    if ($callfrid != $admin) {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ Bu tugma siz uchun mo'ljallanmagan!",
            'show_alert' => false
        ]);
        return;
    }

    $ex = strpos($data, "=") !== false ? explode("=", $data)[1] : $ochirilgan;
    $on = $ex === "on" ? "« ✅ »" : "✅";
    $off = $ex === "off" ? "« ☑ »" : "☑";
    $res = $ex === "on" ? "Faollashtirilgan!" : "Faolsizlantirilgan!";

    if ($data === "ochirilgan" || $ochirilgan != $ex) {
        if ($ochirilgan != $ex) {
            file_put_contents("data/deleted-message.txt", $ex);
        }
        bot('editMessageText', [
            'business_connection_id' => $business_id2,
            'chat_id' => $cid2,
            'message_id' => $mid2,
            'text' => "<tg-emoji emoji-id='5341715473882955310'>⚙️</tg-emoji> <b>O'chirilgan xabarlar sozlamalaridasiz!</b>

<i>— Hozirgi holat: $res</i>

<tg-emoji emoji-id='5406745015365943482'>⬇️</tg-emoji> <b>Chatlarda foydalanish:</b>

<i>Barcha chatlarda avtomatik tarzda amalga oshiriladi va o'chirilgan xabarlar haqida ma'lumotlar bot orqali sizga yuboriladi!</i>",
            'parse_mode' => 'html',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => $on, 'callback_data' => "ochirilgan=on"], ['text' => $off, 'callback_data' => "ochirilgan=off"]],
                    [['text' => "◀️ Orqaga", 'callback_data' => "settings"]]
                ]
            ])
        ]);
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ $res",
            'show_alert' => true
        ]);
    }
}


// Tahrirlangan xabarlar

if (strpos($data, "tahrirlangan") !== false) {
    if ($callfrid != $admin) {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ Bu tugma siz uchun mo'ljallanmagan!",
            'show_alert' => false
        ]);
        return;
    }

    $ex = strpos($data, "=") !== false ? explode("=", $data)[1] : $tahrirlangan;
    $on = $ex === "on" ? "« ✅ »" : "✅";
    $off = $ex === "off" ? "« ☑ »" : "☑";
    $res = $ex === "on" ? "Faollashtirilgan!" : "Faolsizlantirilgan!";

    if ($data === "tahrirlangan" || $tahrirlangan != $ex) {
        if ($tahrirlangan != $ex) {
            file_put_contents("data/edited-message.txt", $ex);
        }
        bot('editMessageText', [
            'business_connection_id' => $business_id2,
            'chat_id' => $cid2,
            'message_id' => $mid2,
            'text' => "<tg-emoji emoji-id='5341715473882955310'>⚙️</tg-emoji> <b>Tahrirlangan xabarlar sozlamalaridasiz!</b>

<i>— Hozirgi holat: $res</i>

<tg-emoji emoji-id='5406745015365943482'>⬇️</tg-emoji> <b>Chatlarda foydalanish:</b>

<i>Barcha chatlarda avtomatik tarzda amalga oshiriladi va tahrirlangan xabarlar haqida ma'lumotlar bot orqali sizga yuboriladi!</i>",
            'parse_mode' => 'html',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => $on, 'callback_data' => "tahrirlangan=on"], ['text' => $off, 'callback_data' => "tahrirlangan=off"]],
                    [['text' => "◀️ Orqaga", 'callback_data' => "settings"]]
                ]
            ])
        ]);
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ $res",
            'show_alert' => true
        ]);
    }
}

?>