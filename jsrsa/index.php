<?php

$config = require_once 'config.php';
$private_key = $config['private_key'];

$details = openssl_pkey_get_details(openssl_pkey_get_private($private_key));
$n = strtoupper(bin2hex($details['rsa']['n']));
$e = strtoupper(bin2hex($details['rsa']['e']));

?>
<!DOCTYPE html>
<html>
<head>
    <title>加密传输</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
</head>
<body>
<label>明文：<input type="text" name="plaintext" style="width: 400px" value="username=admin&password=admin999"></label>
<input type="button" value="加密" id="btn">
<hr>
<form action="post.php" name="form1" method="post" target="_blank">
    <label>密文：<textarea name="ciphertext" style="width: 400px;height: 200px"></textarea></label>
    <input type="submit" value="提交给post.php">
</form>
<script language="JavaScript" type="text/javascript" src="rsa_gather.js"></script>
<script>
    var N = '<?php echo $n;?>', E = '<?php echo $e;?>';
    var rsa = new RSAKey();
    rsa.setPublic(N, E);
    //var pwd = rsa.encrypt(plaintext.value);

    var btn = document.getElementById('btn');
    var plaintext = document.getElementsByName('plaintext')[0];
    var ciphertext = document.form1.ciphertext;

    btn.onclick = function () {
        if (plaintext.value !== '') {
            ciphertext.value = rsa.encrypt(plaintext.value);
        } else {
            plaintext.focus();
        }
    };
</script>
</body>
</html>