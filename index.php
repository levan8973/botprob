<?php

$TelegramBotApi = '6871543572:AAEQ-Lj2aMdQaQ3ASL2Ke26aOU-Ukl0PBms';
$OpenAiToken = 'pat_niufoCJuzl1IIkzKnMuh5RpKtPrVayyTKOe5HiMPKCPlQ67lIFGrCYPklzRv1w3f';

$update = file_get_contents('php://input');
$update = json_decode($update, true);

if(isset($update['message'])){
    $message = $update['message'];
    $chatId = $message['chat']['id'];
    $text = $message['text'];

    $response = genResponse($text, $OpenAiToken);
    sendMessage($chatId, $response, $TelegramBotApi);
}

function genResponse($txt, $openaiapi)
{
    $data = [
        'prompt' => $txt,
        'max_tokens' => 1024
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-type: application/json',
        'Authorization: Bearer '.$openaiapi
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $response = json_decode($response, true);
    return $response['choices'][0]['text'];
}

function sendMessage($chatid, $message, $BotToken)
{
    $url = "https://api.telegram.org/bot{$BotToken}/sendMessage";
    $data = [
        'chat_id' => $chatid,
        'text' => $message
    ];

    $options = [
        'http' => [
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];

    $context = stream_context_create($options);
    file_get_contents($url, false, $context);
}
