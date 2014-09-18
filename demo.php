<!doctype html>
<html>
<head>
    <title>Demo</title>
</head>
<body>
    <ul>
        <?php
        error_reporting(-1);
        require_once 'src/TwoFactorAuth.php';

        $tfa = new TwoFactorAuth('MyApp');

        $secret = $tfa->createSecret();
        echo '<li>First create a secret and associate it with a user: ' . $secret . ' (keep this code private; do not share it with the user or anyone else)';
        echo '<li>Next create a QR code and let the user scan it:<br><img src="' . $tfa->getQRCodeImageAsDataUri('My label', $secret) . '">';
        $code = $tfa->getCode($secret);
        echo '<li>Next, have the user verify the code; at this time the code displayed by a 2FA-app would be: <span style="color:#00c">' . $code . '</span> (but that changes periodically)';
        echo '<li>When the code checks out, 2FA can be / is enabled; store secret with user (encrypted?) and have the user verify a code each time a new session is started.';
        echo '<li>When aforementioned code (' . $code . ') was entered, the result would be: ' . (($tfa->verifyCode($secret, $code) === true) ? '<span style="color:#0c0">OK</span>' : '<span style="color:#c00">FAIL</span>');
        echo '<li>Make sure server-time is NTP-synced!';
        ?>
    </ul>
</body>
</html>
