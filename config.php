<?php
session_start();

// 1. KONEKSI DATABASE
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'db_servis_kripto';

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}

// 2. KONFIGURASI KUNCI SIMETRIS DEFAULT (AES-256-CBC)
define('SYSTEM_SYMMETRIC_KEY', 'FIXLAP-SECRET-KEY-2026');
define('AES_METHOD', 'AES-256-CBC');

// 3. GENERATE / LOAD KUNCI ASIMETRIS (RSA PUBLIC & PRIVATE KEY)
$keyFile = __DIR__ . '/rsa_keys.json';
$txtKeyFile = __DIR__ . '/kunci_login.txt';

if (!file_exists($keyFile)) {
    $opensslConfigPath = dirname(PHP_BINARY) . '/extras/ssl/openssl.cnf';
    $configArgs = [
        "private_key_bits" => 2048,
        "private_key_type" => OPENSSL_KEYTYPE_RSA,
    ];
    if (file_exists($opensslConfigPath)) {
        $configArgs['config'] = $opensslConfigPath;
    }

    $res = @openssl_pkey_new($configArgs);

    if ($res) {
        openssl_pkey_export($res, $privateKey, null, $configArgs);
        $pubKeyDetails = openssl_pkey_get_details($res);
        $publicKey = $pubKeyDetails["key"];
        $mode = 'OPENSSL_RSA';
    } else {
        $privateKey = "-----BEGIN RSA PRIVATE KEY-----\n" .
                      chunk_split(base64_encode("RSA_PRIV_KEY_AUTH_2026_FIXLAP_VAULT_SECRET"), 64, "\n") .
                      "-----END RSA PRIVATE KEY-----";
        $publicKey  = "-----BEGIN PUBLIC KEY-----\n" .
                      chunk_split(base64_encode("RSA_PUB_KEY_AUTH_2026_FIXLAP_VAULT_PUBLIC"), 64, "\n") .
                      "-----END PUBLIC KEY-----";
        $mode = 'FALLBACK_ASYM';
    }

    file_put_contents($keyFile, json_encode([
        'public_key'  => trim($publicKey),
        'private_key' => trim($privateKey),
        'mode'        => $mode
    ], JSON_PRETTY_PRINT));

    // Simpan ke file .txt agar mudah di-copy saat login
    $isiTxt = "=== KUNCI SIMETRIS (AES-256) ===\n" . SYSTEM_SYMMETRIC_KEY . "\n\n" .
              "=== KUNCI ASIMETRIS (RSA PRIVATE KEY) ===\n" . trim($privateKey) . "\n";
    file_put_contents($txtKeyFile, $isiTxt);
}

$rsaData = json_decode(file_get_contents($keyFile), true);
define('SYSTEM_PUBLIC_KEY', $rsaData['public_key']);
define('SYSTEM_PRIVATE_KEY', $rsaData['private_key']);
define('ASYM_MODE', $rsaData['mode']);

// FUNGSI KRIPTOGRAFI SIMETRIS (AES-256-CBC)
function encryptSymmetric($plaintext, $secretKey) {
    $key = hash('sha256', $secretKey, true);
    $ivLength = openssl_cipher_iv_length(AES_METHOD);
    $iv = openssl_random_pseudo_bytes($ivLength);
    $encrypted = openssl_encrypt($plaintext, AES_METHOD, $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $encrypted);
}

function decryptSymmetric($ciphertextBase64, $secretKey) {
    $data = base64_decode($ciphertextBase64);
    $key = hash('sha256', $secretKey, true);
    $ivLength = openssl_cipher_iv_length(AES_METHOD);
    if (strlen($data) <= $ivLength) return "[Data Korup]";
    
    $iv = substr($data, 0, $ivLength);
    $encrypted = substr($data, $ivLength);
    $decrypted = openssl_decrypt($encrypted, AES_METHOD, $key, OPENSSL_RAW_DATA, $iv);
    return $decrypted !== false ? $decrypted : "[Gagal Dekripsi Simetris]";
}

// FUNGSI KRIPTOGRAFI ASIMETRIS (RSA)
function encryptAsymmetric($plaintext, $publicKey) {
    if (ASYM_MODE === 'OPENSSL_RSA') {
        $encrypted = '';
        if (openssl_public_encrypt($plaintext, $encrypted, $publicKey)) {
            return base64_encode($encrypted);
        }
    }
    return base64_encode(encryptSymmetric($plaintext, md5(trim($publicKey))));
}

function decryptAsymmetric($ciphertextBase64, $privateKey) {
    if (ASYM_MODE === 'OPENSSL_RSA') {
        $decrypted = '';
        $data = base64_decode($ciphertextBase64);
        if (@openssl_private_decrypt($data, $decrypted, $privateKey)) {
            return $decrypted;
        }
        return "[Gagal Dekripsi Asimetris]";
    }
    if (trim($privateKey) !== trim(SYSTEM_PRIVATE_KEY)) {
        return "[Private Key Tidak Valid]";
    }
    return decryptSymmetric(base64_decode($ciphertextBase64), md5(trim(SYSTEM_PUBLIC_KEY)));
}

// FUNGSI VALIDASI LOGIN KUNCI ADMIN
function verifyAdminKeys($inputSymKey, $inputPrivKey) {
    if (trim($inputSymKey) !== SYSTEM_SYMMETRIC_KEY) {
        return false;
    }
    $challengeToken = "AUTH_VERIFY_" . rand(1000, 9999);
    $encryptedToken = encryptAsymmetric($challengeToken, SYSTEM_PUBLIC_KEY);
    $decryptedToken = decryptAsymmetric($encryptedToken, trim($inputPrivKey));

    return ($decryptedToken === $challengeToken);
}
?>