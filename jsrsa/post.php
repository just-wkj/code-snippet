<?php


$config = require_once 'config.php';

$private_key = $config['private_key'];

$cipher_text = trim($_POST['ciphertext']);

$data = pack('H*', $cipher_text);
$result = openssl_private_decrypt($data, $plaintext, openssl_pkey_get_private($private_key));

if ($result) {
    echo '密文:<br>' . preg_replace('/[^a-zA-Z0-9]+/i', '', $cipher_text);
    echo '<hr>明文:<br>' . $plaintext;
} else {
    echo '肯定是你配置有误';
}