<?php
function base64UrlEncode($data) {
    return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
}

function base64UrlDecode($data) {
    return base64_decode(str_replace(['-', '_'], ['+', '/'], $data));
}

function create_jwt($payload, $secret) {
    $header = base64UrlEncode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
    $payload = base64UrlEncode(json_encode($payload));
    $signature = base64UrlEncode(hash_hmac('sha256', "$header.$payload", $secret, true));
    return "$header.$payload.$signature";
}

function verify_jwt($jwt, $secret) {
    $parts = explode('.', $jwt);
    if (count($parts) === 3) {
        list($header, $payload, $signature) = $parts;
        $valid_signature = base64UrlEncode(hash_hmac('sha256', "$header.$payload", $secret, true));
        if (hash_equals($signature, $valid_signature)) {
            return json_decode(base64UrlDecode($payload), true);
        }
    }
    return false;
}
?>
