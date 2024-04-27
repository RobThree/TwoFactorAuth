---
layout: post
title: QR Codes
---

An alternative way of communicating the secret to the user is through the use of [QR Codes](http://en.wikipedia.org/wiki/QR_code) which most if not all authenticator mobile apps can scan.

This can avoid accidental typing errors and also pre-set some text values within the two factor authentication mobile application.

You can display the QR Code as a base64 encoded image using the instance as follows, supplying the users name or other public identifier as the first argument

````php
<p>Scan the following image with your app:</p>
<img src="<?php echo $tfa->getQRCodeImageAsDataUri('Bob Ross', $secret); ?>">
````

You can also specify a size as a third argument which is 200 by default.

## Offline Providers

[EndroidQrCodeProvider](qr-codes/endroid.md) and EndroidQrCodeWithLogoProvider

[BaconQRCodeProvider](qr-codes/bacon.md)

**Note:** offline providers may have additional PHP requirements in order to function, you should study what is required before trying to make use of them.

## Custom Provider

If you wish to make your own QR Code provider to reference another service or library, it must implement the [IQRCodeProvider interface](../lib/Providers/Qr/IQRCodeProvider.php).

It is recommended to use similar constructor arguments as the included providers to avoid big shifts when trying different providers.

Example:

```php
use RobThree\Auth\TwoFactorAuth;
// using a custom object implementing IQRCodeProvider
$tfa = new TwoFactorAuth(new MyQrCodeProvider());
// using named argument and a variable
$tfa = new TwoFactorAuth(qrcodeprovider: $qrGenerator);
```

## Online Providers

**Warning:** Using an external service for generating QR codes encoding authentication secrets is **not** recommended! You should instead make use of the included offline providers listed above.

* Gogr.me: [QRServerProvider](qr-codes/qr-server.md)
* Image Charts: [ImageChartsQRCodeProvider](qr-codes/image-charts.md)
* Qrickit: [QRicketProvider](qr-codes/qrickit.md)
* Google Charts: [GoogleChartsQrCodeProvider](qr-codes/google-charts.md)

Example:

```php
use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\GoogleChartsQrCodeProvider;
$tfa = new TwoFactorAuth(new GoogleChartsQrCodeProvider());
```
