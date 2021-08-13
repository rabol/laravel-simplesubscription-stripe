# Laravel Simple subscription Stripe

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rabol/laravel-simplesubscription-stripe.svg?style=flat-square)](https://packagist.org/packages/rabol/laravel-simplesubscription-stripe)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/rabol/laravel-simplesubscription-stripe/run-tests?label=tests)](https://github.com/rabol/laravel-simplesubscription-stripe/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/rabol/laravel-simplesubscription-stripe/Check%20&%20fix%20styling?label=code%20style)](https://github.com/rabol/laravel-simplesubscription-stripe/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/rabol/laravel-simplesubscription-stripe.svg?style=flat-square)](https://packagist.org/packages/rabol/laravel-simplesubscription-stripe)

---
This package is not Laravel Cashier, Laravel Cashier is much more advanced and have several other features.
If you want to get up and running with subscriptions and Stripe payments for your Laravel app quickly, then this package is for you.

It only contains 2 migrations - copied from Laravel Cashier - 4 Jobs, and a helper class.

As this package uses the same DB structure as Laravel Cashier, it's very easy to switch if you need.

So why do you even need this package ?

Well, understanding Laravel Cashier and getting it to work properly might take you 2-3 weeks, while with this package you will be up and running in a few hours.


NOTE: THIS IS STILL JUST A SKELETON, CODE WILL BE AVAILABLE SHORTLY

---


## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-simplesubscription-stripe.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-simplesubscription-stripe)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require rabol/laravel-simplesubscription-stripe
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Rabol\LaravelSimplesubscriptionStripe\LaravelSimplesubscriptionStripeServiceProvider" --tag="laravel-simplesubscription-stripe-migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Rabol\LaravelSimplesubscriptionStripe\LaravelSimplesubscriptionStripeServiceProvider" --tag="laravel-simplesubscription-stripe-config"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$laravel-simplesubscription-stripe = new Rabol\LaravelSimplesubscriptionStripe();
echo $laravel-simplesubscription-stripe->echoPhrase('Hello, Spatie!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Steen Rabol](https://github.com/rabol)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
