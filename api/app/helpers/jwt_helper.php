<?php

require_once __DIR__ . '/../vendor/autoload.php';

// تحميل متغيرات البيئة
if (!isset($_ENV['JWT_SECRET_KEY'])) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/app/');
    $dotenv->load();
}

$JWT_SECRET = $_ENV['JWT_SECRET_KEY'] ?? 'default_secret'; // fallback في حال لم تُحمّل

// BASE64-URL ENCODING
function base64url_encode(string $data): string {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode(string $data): string {
    $padLen = 4 - (strlen($data) % 4);
    if ($padLen < 4) {
        $data .= str_repeat('=', $padLen);
    }
    return base64_decode(strtr($data, '-_', '+/'));
}

// JWT GENERATOR
function generate_jwt(array $payload, ?string $secret = null, int $exp = null): string {
    global $JWT_SECRET;
    $secret = $secret ?? $JWT_SECRET;
    $exp    = $exp ?? intval($_ENV['JWT_EXPIRATION'] ?? 3600);

    $header = [
        'alg' => 'HS256',
        'typ' => 'JWT'
    ];

    $issuedAt = time();
    $payload['iat'] = $issuedAt;
    $payload['exp'] = $issuedAt + $exp;

    $header_encoded  = base64url_encode(json_encode($header));
    $payload_encoded = base64url_encode(json_encode($payload));

    $signature = hash_hmac('sha256', "$header_encoded.$payload_encoded", $secret, true);
    $signature_encoded = base64url_encode($signature);

    return "$header_encoded.$payload_encoded.$signature_encoded";
}

// JWT VALIDATOR
function is_jwt_valid(string $jwt, ?string $secret = null): false|object {
    global $JWT_SECRET;
    $secret = $secret ?? $JWT_SECRET;

    if (substr_count($jwt, '.') !== 2) return false;

    [$header_encoded, $payload_encoded, $signature_provided] = explode('.', $jwt);

    $payload = json_decode(base64url_decode($payload_encoded));

    if (!$payload || !isset($payload->exp) || time() > $payload->exp) {
        return false; // Expired or invalid payload
    }

    $expected_signature = hash_hmac('sha256', "$header_encoded.$payload_encoded", $secret, true);
    $expected_signature_encoded = base64url_encode($expected_signature);

    if (!hash_equals($expected_signature_encoded, $signature_provided)) {
        return false; // Invalid signature
    }

    return $payload;
}

// GET AUTHORIZATION HEADER
function get_authorization_header(): ?string {
    if (!empty($_SERVER['Authorization'])) {
        return trim($_SERVER['Authorization']);
    } elseif (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
        return trim($_SERVER['HTTP_AUTHORIZATION']);
    } elseif (function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        foreach ($headers as $key => $value) {
            if (strtolower($key) === 'authorization') {
                return trim($value);
            }
        }
    }
    return null;
}

// GET BEARER TOKEN
function get_bearer_token(): ?string {
    $header = get_authorization_header();
    if ($header && preg_match('/Bearer\s(\S+)/', $header, $matches)) {
        return $matches[1];
    }
    return null;
}
