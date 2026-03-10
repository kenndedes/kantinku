<?php

$key = 'OTaX_sSz3QBfc5lK6DL-s4Km47mqEvZmFmmSK-6Jh_0';
$timestamp = (string) intval(microtime(true));
$method = 'POST';
$path = '/transactions';
$body = [
    'merchant_id' => 19,
    'ref_id' => 'test_' . time(),
    'amount' => 10000,
    'channel_code' => 'QRIS',
    'expires_in_minutes' => 15,
    'notify_url' => 'https://sectorial-swishiest-grisel.ngrok-free.dev/webhook/xoftware'
];

$bodyString = json_encode($body, JSON_UNESCAPED_SLASHES);
$message = $timestamp . "\n" . $method . "\n" . $path . "\n" . $bodyString;

echo "Timestamp: $timestamp\n";
echo "Path: $path\n";
echo "Body JSON:\n$bodyString\n\n";
echo "Message to sign:\n$message\n\n";

$signature = hash_hmac('sha256', $message, $key, true);
$signatureHex = bin2hex($signature);

echo "Signature: $signatureHex\n";
