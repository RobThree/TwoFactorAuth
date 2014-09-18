<?php
error_reporting(-1);
require_once 'src/TwoFactorAuth.php';
 
$tfa = new TwoFactorAuth('MyApp');

$secret = $tfa->createSecret();
echo 'First create a secret and associate it with a user: ' . $secret . ' (keep this code private; do not share it with the user or anyone, just store it in your database or something)<br>';
 
echo 'Next create a QR code and let the user scan it:<br>';
 
$label = 'My label';
echo 'Image:<br><img src="' . $tfa->getQRCodeImageAsDataUri($label, $secret) . '"><br>';
 
$code = $tfa->getCode($secret);
echo 'Next, have the user verify the code; at this time the code displayed by a 2FA-app would be: <span style="color:#00c">' . $code . '</span> (but that changes periodically)<br>';
 
echo 'When the code checks out, 2FA can be / is enabled; store secret with user (encrypted?) and have the user verify a code each time a new session is started.<br>';
echo 'When aforementioned code was entered, the result would be: ' . (($tfa->verifyCode($secret, $code) === true) ? '<span style="color:#0c0">OK</span>' : '<span style="color:#c00">FAIL</span>') . '<br>';
echo 'Make sure server-time is NTP-synced!<br>';