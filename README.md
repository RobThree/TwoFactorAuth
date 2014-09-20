# ![Logo](https://raw.githubusercontent.com/RobThree/TwoFactorAuth/master/logo.png) TwoFactorAuth class for PHP

PHP class for [two-factor (or multi-factor) authentication](http://en.wikipedia.org/wiki/Multi-factor_authentication) using [TOTP](http://en.wikipedia.org/wiki/Time-based_One-time_Password_Algorithm) and [QR-codes](http://en.wikipedia.org/wiki/QR_code). Inspired by, based on but most importantly an *improvement* on '[PHPGangsta/GoogleAuthenticator](https://github.com/PHPGangsta/GoogleAuthenticator)'.

<p align="center">
<img src="https://raw.githubusercontent.com/RobThree/TwoFactorAuth/master/multifactorauthforeveryone.png">
</p>

## Requirements

* Tested on PHP 5.3 and 5.4
* [cURL](http://php.net/manual/en/book.curl.php) when using the provided `GoogleQRCodeProvider` (default), `QRServerProvider` or `QRicketProvider` but you can also provide your own QR-code provider.


## Usage

Here are some code snippets that should help you get started...

````php
// Start by including the TwoFactorAuth.php file which contains all you need (for now)
require_once 'src/TwoFactorAuth.php';
$tfa = new TwoFactorAuth('My Company');
````

The TwoFactorAuth class constructor accepts 5 parameters:

Parameter         | Default value | Use 
------------------|---------------|--------------------------------------------------
`$issuer`         | `null`        | Will be displayed in the app as issuer name
`$digits`         | `6`           | The number of digits the resulting codes will be
`$period`         | `30`          | The number of seconds a code will be valid
`$algorithm`      | `sha1`        | The algorithm used
`$qrcodeprovider` | `null`        | QR-code provider (more on this later)

These parameters are all '`write once`'; the class will, for it's lifetime, use these values when generating / calculating codes. The number of digits, the period and algorithm are all set to values Google's Authticator app uses (and supports). You may specify `8` digits, a period of `45` seconds and the `sha256` algorithm but the authenticator app (be it Google's implementation, Authy or any other app) may or may not support these values. Your mileage may vary; keep it on the safe side if you don't control which app your audience uses.

Next, when a user wants to setup two-factor auth (or, more correctly, multi-factor auth) you need to create a secret. This will be your **shared** (this will be the `one-time` in [TOTP](http://en.wikipedia.org/wiki/Time-based_One-time_Password_Algorithm)) **secret**. This secret will need to be entered by the user in their app. This can be done manually, in which case you simply display the secret and have the user type it in the app:

````php
$secret = $tfa->createSecret();
````

The `createSecret()` method accepts one argument: `$bits` (default: `80`). This will be the number of bits generated for the shared secret. Make sure this argument is a multiple of 8 and, again, keep in mind that not all combinations may be supported by all apps. Google authenticator seems happy with 80 and 160, the default is set to 80 because that's what most sites (that I know of) currently use.

````php
// Display shared secret
<p>Please enter the following code in your app: '<?php echo $secret ?>'</p>
````

Another, more user-friendly, way to get the shared secret into the app is to generate a [QR-code](http://en.wikipedia.org/wiki/QR_code) which can be scanned by the app. To generate these QR codes you can use any one of the built-in `QRProvider` classes:

1. `GoogleQRCodeProvider` (default)
2. `QRServerProvider`
3. `QRicketProvider`

...or implement your own provider. To implement your own provider all you need to do is implement the `IQRCodeProvider` interface. You can use the built-in providers mentioned before to serve as an example or read the next chapter in this file. The built-in classes all use a 3rd (e.g. external) party (Google, QRServer and QRicket) for the hard work of generating QR-codes (note: each of these services might at some point not be available or impose limitations to the number of codes generated per day, hour etc.). You could, however, easily use a project like [PHP QR Code](http://phpqrcode.sourceforge.net/) to generate your own QR-codes. We'll look into that later on.

The built-in providers all have some provider-specific 'tweaks' you can 'apply'. Some provide support for different colors, others may let you specify the desired image-format etc. What they all have in common is that they return a QR-code as binary blob which, in turn, will be turned into a [data URI](http://en.wikipedia.org/wiki/Data_URI_scheme) by the `TwoFactorAuth` class. This makes it easy for you to display the image without requiring extra 'roundtrips' from browser to server and vice versa.

````php
// Display QR code to user
<p>Scan the following image with your app:</p>
<p><img src="<?php $tfa->getQRCodeImageAsDataUri('Bob Ross', $secret) ?>"></p>
````

When outputting a QR-code you can choose a `$label` for the user (which, when entering a shared secret manually, will have to be chosen by the user). This label may be an empty string or `null`. Also a `$size` may be specified (in pixels, width == height) for which we use a default value of `200`.

When the code is added to the app, the app will be ready to start generating codes which 'expire' each '`$period`' number of seconds. To make sure the code was entered, or scanned, correctly you need to verify this by having the user enter a generated code. To check if the generated code is valid you call the `verifyCode()` method:

````php
// Verify code
$result = $tfa->verifyCode($_SESSION['secret'], $_POST['verification']);
````

`verifyCode()` will return either `true` (the code was valid) or `false` (the code was invalid; no points for you!). You may need to store `$secret` in a `$_SESSION` or other persistent storage between requests. The `verifyCode()` accepts, aside from `$secret` and `$code`, two more parameters. The first being `$discrepancy`. Since TOTP codes are based on time("slices") it is very important that the server (but also client) have a correct date/time. But because the two *may* differ a bit we usually allow a certain amount of leeway. Because generated codes are valid for a specific period (remember the `$period` parameter in the `TwoFactorAuth`'s constructor?) we usually check the period directly before and the period directly after the current time when validating codes. So when the current time is `14:34:21`, which results in a 'current timeslice' of `14:34:00` to `14:34:30` we also calculate/verify the codes for `14:33:30` to `14:34:00` and for `14:34:30` to `14:35:00`. This gives us a 'window' of `14:33:30` to `14:35:00`. The `$discrepancy` parameter specifies how many periods (or: timeslices) we check in either direction of the current time. The default `$discrepancy` of `1` results in (max.) 3 period checks: -1, current and +1 period. A `$discrepancy` of `4` would result in a larger window (or: bigger time difference between client and server) of -4, -3, -2, -1, current, +1, +2, +3 and +4 periods.

The second parameter `$time` allows you to check a code for a specific point in time. This parameter has no real practical use but can be handy for unittesting etc. The default value, `null`, means: use the current time.

Ok, so now the code has been verified and found to be correct. Now we can store the `$secret` with our user in our database (or elsewhere) and whenever the user begins a new session we ask for a code generated by the authentication app of their choice. All we need to to is call `verifyCode()` again with the shared secret and the entered code and we know if the user is legit or not.

Simple as 1-2-3.

All we need to remember is 4 methods and a constructor:

````php
__construct($issuer=null, $digits=6, $period=30, $algorithm='sha1', $qrcodeprovider=null)
createSecret($bits = 80)
getCode($secret, $time = null)
verifyCode($secret, $code, $discrepancy = 1, $time = null)
getQRCodeImageAsDataUri($label, $secret, $size = 200)
````

### QR-code providers

As mentioned before, this class comes with three 'built-in' QR-code providers. This chapter will touch the subject a bit but most of it should be self-explanatory. The `TwoFactorAuth`-class accepts a `$qrcodeprovider` parameter which lets you specify a built-in or custom QR-code provider. All three built-in providers do a simple HTTP request to retrieve an image using cURL and implement the `IQRCodeProvider` interface which is all you need to implement to write your own QR-code provider.

The default provider is the `GoogleQRCodeProvider` which uses the [Google Chart Tools](https://developers.google.com/chart/infographics/docs/qr_codes) to render QR-codes. Then we have the `QRServerProvider` which uses the [goqr.me API](http://goqr.me/api/doc/create-qr-code/) and finally we have the `QRicketProvider` which uses the [QRickit API](http://qrickit.com/qrickit_apps/qrickit_api.php). All three inherit from a common (abstract) base-class named `BaseHTTPQRCodeProvider` because all three share the same functionality: retrieve an image from a 3rd party. All three classes have constructors that allow you to tweak some settings and most, if not all, arguments should speak for themselves. If you're not sure which values are supported, click the links in this paragraph for documentation on the API's that are utilized by these classes.

If you don't like any of the built-in classes because you don't want to rely on external resources for example or because you're paranoid about sending the TOTP data to these 3rd parties (which is useless to them since they miss *at least one* other factor in the [MFA process](http://en.wikipedia.org/wiki/Multi-factor_authentication)), feel tree to implement your own. The `IQRCodeProvider` interface couldn't be any simpler. All you need to do is implement 2 methods:

````php
getMimeType();
getQRCodeImage($qrtext, $size);
````

The `getMimeType()` method should return the [MIME type](http://en.wikipedia.org/wiki/Internet_media_type) of the image that is returned by our implementation of `getQRCodeImage()`. In this example it's simply `image/png`. The `getQRCodeImage()` method is passed two arguments: `$qrtext` and `$size`. The latter, `$size`, is simply the width/height in pixels of the image desired by the caller. The first, `$qrtext` is the text that should be encoded in the QR-code. An example of such a text would be:

`otpauth://totp/LABEL:alice@google.com?secret=JBSWY3DPEHPK3PXP&issuer=ISSUER`

All you need to do is return the QR-code as binary image data and you're done. All parts of the `$qrtext` have been escaped for you (but note: you *may* need to escape the entire `$qrtext` just once more when passing the data to another server as GET-parameter).

Let's see if we can use [PHP QR Code](http://phpqrcode.sourceforge.net/) to implement our own, custom, no-3rd-parties-allowed-here, provider. We start with downloading the [required (single) file](https://github.com/t0k4rt/phpqrcode/blob/master/phpqrcode.php) and putting it in our `src/` directory where `TwoFactorAuth.php` is located as well. Now let's implement the provider: create another file named `myprovider.php` in the `src` directory and paste in this content:

````php
<?php
require_once 'phpqrcode.php';                      // Yeah, we're gonna need that

class MyProvider implements IQRCodeProvider {
  public function getMimeType() {
    return 'image/png';                            // This provider only returns PNG's
  }
  
  public function getQRCodeImage($qrtext, $size) {
    ob_start();                                     // 'Catch' QRCode's output
    QRCode::png($qrtext, null, QR_ECLEVEL_L, 3, 4); // We ignore $size and set it to 3
                                                    // since phpqrcode doesn't support
                                                    // a size in pixels...
    $result = ob_get_contents();                    // 'Catch' QRCode's output
    ob_end_clean();                                 // Cleanup
    return $result;                                 // Return image
  }
}
````

That's it. We're done! We've implemented our own provider (with help of PHP QR Code). No more external dependencies, no more unnecessary latencies. Now Let's *use* our provider:

````php
<?php
require_once 'src/TwoFactorAuth.php';
require_once 'src/myprovider.php';

$mp = new MyProvider();
$tfa = new TwoFactorAuth('My Company', 6, 30, 'sha1', $mp);
$secret = $tfa->createSecret();
?>
<p><img src="<?php $tfa->getQRCodeImageAsDataUri('Bob Ross', $secret) ?>"></p>
````

Voilà. Couldn't make it any simpler.

## License

Licensed under MIT license. See LICENSE file for details.

[Logo / icon](http://www.iconmay.com/Simple/Travel_and_Tourism_Part_2/luggage_lock_safety_baggage_keys_cylinder_lock_hotel_travel_tourism_luggage_lock_icon_465) under  CC0 1.0 Universal (CC0 1.0) Public Domain Dedication
