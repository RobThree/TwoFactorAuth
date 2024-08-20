# ![Logo](https://raw.githubusercontent.com/RobThree/TwoFactorAuth/master/logo.png) PHP library for Two Factor Authentication

[![Build status](https://img.shields.io/github/actions/workflow/status/robthree/twofactorauth/test.yml?branch=master)](https://github.com/RobThree/TwoFactorAuth/actions?query=branch%3Amaster) [![Latest Stable Version](https://img.shields.io/packagist/v/robthree/twofactorauth.svg?style=flat-square)](https://packagist.org/packages/robthree/twofactorauth) [![License](https://img.shields.io/packagist/l/robthree/twofactorauth.svg?style=flat-square)](LICENSE) [![Downloads](https://img.shields.io/packagist/dt/robthree/twofactorauth.svg?style=flat-square)](https://packagist.org/packages/robthree/twofactorauth) [![PayPal donate button](http://img.shields.io/badge/paypal-donate-orange.svg?style=flat-square)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6MB5M2SQLP636 "Keep me off the streets")

PHP library for [two-factor (or multi-factor) authentication](http://en.wikipedia.org/wiki/Multi-factor_authentication) using [TOTP](http://en.wikipedia.org/wiki/Time-based_One-time_Password_Algorithm) and [QR-codes](http://en.wikipedia.org/wiki/QR_code). Inspired by, based on but most importantly an *improvement* on '[PHPGangsta/GoogleAuthenticator](https://github.com/PHPGangsta/GoogleAuthenticator)'. There's a [.Net implementation](https://github.com/RobThree/TwoFactorAuth.Net) of this library as well.

<p align="center">
<img src="https://raw.githubusercontent.com/RobThree/TwoFactorAuth/master/multifactorauthforeveryone.png">
</p>

## Requirements

* Requires PHP version >=8.2

Optionally, you may need:

* [sockets](https://www.php.net/manual/en/book.sockets.php) if you are using `NTPTimeProvider`
* [endroid/qr-code](https://github.com/endroid/qr-code) if using `EndroidQrCodeProvider` or `EndroidQrCodeWithLogoProvider`.
* [bacon/bacon-qr-code](https://github.com/Bacon/BaconQrCode) if using `BaconQrCodeProvider`.
* [php-curl library](http://php.net/manual/en/book.curl.php) when using an external QR Code provider such as `QRServerProvider`, `ImageChartsQRCodeProvider`, `QRicketProvider` or any other custom provider connecting to an external service.

## Installation

The best way of installing this library is with composer:

`php composer.phar require robthree/twofactorauth`

## Usage

For a quick start, have a look at the [getting started](https://robthree.github.io/TwoFactorAuth/getting-started.html) page or try out the [demo](demo/demo.php).

If you need more in-depth information about the configuration available then you can read through the rest of [documentation](https://robthree.github.io/TwoFactorAuth).

## Integrations

- [CakePHP 3](https://github.com/andrej-griniuk/cakephp-two-factor-auth)
- [CI4-Auth: a user, group, role and permission management library for Codeigniter 4](https://github.com/glewe/ci4-auth)

## License

Licensed under MIT license. See [LICENSE](./LICENSE) for details.

[Logo / icon](http://www.iconmay.com/Simple/Travel_and_Tourism_Part_2/luggage_lock_safety_baggage_keys_cylinder_lock_hotel_travel_tourism_luggage_lock_icon_465) under  CC0 1.0 Universal (CC0 1.0) Public Domain Dedication  ([Archived page](http://riii.nl/tm7ap))
