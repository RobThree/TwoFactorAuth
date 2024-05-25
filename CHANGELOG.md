# RobThree\TwoFactorAuth changelog

# Version 3.x

## Breaking changes

### PHP Version

Version 3.x requires at least PHP 8.2.

### Constructor signature change

In order to ensure users of this library make a conscious choice of QR Code Provider, the QR Code Provider is now a mandatory argument, in first place.

If you didn't provide one explicitly before, you can get the old behavior with:

~~~php
use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\QRServerProvider;
$tfa = new TwoFactorAuth(new QRServerProvider());
~~~

If you provided one before, the order of the parameters have been changed, so simply move the QRCodeProvider argument to the first place or use named arguments.

Documentation on selecting a QR Code Provider is available here: [QR Code Provider documentation](https://robthree.github.io/TwoFactorAuth/qr-codes.html).

### Default secret length

The default secret length has been increased from 80 bits to 160 bits (RFC4226) PR [#117](https://github.com/RobThree/TwoFactorAuth/pull/117). This might cause an issue in your application if you were previously storing secrets in a column with restricted size. This change doesn't impact existing secrets, only new ones will get longer.

Previously a secret was 16 characters, now it needs to be stored in a 32 characters width column.

You can keep the old behavior by setting `80` as argument to `createSecret()` (not recommended, see [#117](https://github.com/RobThree/TwoFactorAuth/pull/117) for further discussion).

## Other changes

* The new PHP attribute [SensitiveParameter](https://www.php.net/manual/en/class.sensitiveparameter.php) was added to the code, to prevent accidental leak of secrets in stack traces.
* Likely not breaking anything, but now all external QR Code providers use HTTPS with a verified certificate. PR [#126](https://github.com/RobThree/TwoFactorAuth/pull/126).
* The CSPRNG is now exclusively using `random_bytes()` PHP function. Previously a fallback to `openssl` or non cryptographically secure PRNG existed, they have been removed. PR [#122](https://github.com/RobThree/TwoFactorAuth/pull/122).
* If an external QR code provider is used and the HTTP request results in an error, it will throw a `QRException`. Previously the error was ignored. PR [#130](https://github.com/RobThree/TwoFactorAuth/pull/130), fixes [#129](https://github.com/RobThree/TwoFactorAuth/issues/129).

# Version 2.x

## Breaking changes

### PHP Version

Version 2.x requires at least PHP 8.1.

### Constructor signature

With version 2.x, the `algorithm` parameter of `RobThree\Auth\TwoFactorAuth` constructor is now an `enum`.

On version 1.x:

~~~php
use RobThree\Auth\TwoFactorAuth;

$lib = new TwoFactorAuth('issuer-name', 6, 30, 'sha1');
~~~

On version 2.x, simple change the algorithm from a `string` to the correct `enum`:

~~~php
use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Algorithm;

$lib = new TwoFactorAuth('issuer-name', 6, 30, Algorithm::Sha1);
~~~

See the [Algorithm.php](./lib/Algorithm.php) file to see available algorithms.
