<?php
require_once 'loader.php';
Loader::register('../lib','RobThree\\Auth');

use \RobThree\Auth\TwoFactorAuth;
$tfa = new TwoFactorAuth('MyApp');

$data = $tfa->getQRCodeHTML('My label', $secret);

$parts = explode(';', $data);

$type = substr($parts[0], 5);
//echo $type, "<br>\n";
header('Content-Type: ' . $type);

list($type, $base64data) = explode(',', $parts[1]);
$data = base64_decode($base64data);

echo $data;
?>
