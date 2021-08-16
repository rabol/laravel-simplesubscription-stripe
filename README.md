# Laravel Simple subscription Stripe

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rabol/laravel-simplesubscription-stripe.svg?style=flat-square)](https://packagist.org/packages/rabol/laravel-simplesubscription-stripe)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/rabol/laravel-simplesubscription-stripe/run-tests?label=tests)](https://github.com/rabol/laravel-simplesubscription-stripe/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/rabol/laravel-simplesubscription-stripe/Check%20&%20fix%20styling?label=code%20style)](https://github.com/rabol/laravel-simplesubscription-stripe/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/rabol/laravel-simplesubscription-stripe.svg?style=flat-square)](https://packagist.org/packages/rabol/laravel-simplesubscription-stripe)

---
This package is not Laravel Cashier, Laravel Cashier is much more advanced and have several other features.
If you want to get up and running with subscriptions and Stripe payments for your Laravel app quickly, then this package is for you.

It only contains 1 migration and a helper class.

Setting up Stripe is out of scope for this package

---

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
    'stripe_key' => env('STRIPE_KEY'),
    'stripe_secret' => env('STRIPE_SECRET'),
];

```

## Usage

Here is a simple example of how to use this package.


```php

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Rabol\LaravelSimplesubscriptionStripe\LaravelSimplesubscriptionStripe;
use Auth;

class StripeController extends Controller
{
    public function index()
    {
        return view('stripe.index')
            ->with('stripePrices', LaravelSimplesubscriptionStripe::stripe()->prices->all());
    }

    public function gotoStripeCustomerPortal(Request $request)
    {
        return redirect(LaravelSimplesubscriptionStripe::gotoStripeCustomerPortal(Auth::user(), route('stripe.index')));
    }

    public function checkout(Request $request)
    {
        $user = Auth::user();

        if (!$user->stripe_id) {
            $options = [
                    'address' => [
                    'city' => $user->city ?? '',
                    'line1' => $user->adr_line_1 ?? '',
                    'line2' => $user->adr_line_2 ?? '',
                    'postal_code' => $user->postal_code ?? '',
                    'country' => $user->country ?? '',
                    'state' => $user->state ?? '',
                ]];

            // If the user should be Tax Exempt, and the information to the options array
            LaravelSimplesubscriptionStripe::createAsStripeCustomer($user, $options);
        }

        $priceId = $request->input('priceId');
        $session = LaravelSimplesubscriptionStripe::createCheckoutSession([
            'allow_promotion_codes' => true,
            'success_url' => config('app.url') . '/stripe/success/{CHECKOUT_SESSION_ID}',
            'cancel_url' => config('app.url') . '/stripe/cancled',
            'customer' => $user->stripe_id,
            'customer_update' => [
                'name' => 'auto',
                'address' => 'auto',
            ],
            'payment_method_types' => [
                'card'
            ],
            'mode' => 'subscription',
            'tax_id_collection' => [
                'enabled' => true
            ],
            'line_items' => [[
                'price' => $priceId,
                // For metered billing, do not pass quantity
                'quantity' => 1,
            ]],
            'subscription_data' => [
                'metadata' => ['name' => 'Advanced'],
                //'default_tax_rates' => ['txr_xxxxxxxx'] // get the tax id from the Stripe dashboard
            ]
        ]);

        return redirect()->to($session->url);
    }

    public function cancled(Request $request)
    {
        return view('stripe_cancled')
            ->with('request', $request);
    }

    public function success(Request $request, string $session_id)
    {
        $session = LaravelSimplesubscriptionStripe::stripe()->checkout->sessions->retrieve($session_id);
        $customer = LaravelSimplesubscriptionStripe::stripe()->customers->retrieve($session->customer);

        return view('stripe_success')
            ->with('session', $session)
            ->with('customer', $customer);
    }
}

```

## View files

Index - /resources/views/stripe/index.blade.php
```
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning">
                        <div>
                            <div class="row">
                                <div class="col-4">
                                    {{ __('Simple subsctiption Stripe') }}
                                </div>
                                @if(auth()->user()->stripe_id)
                                <div class="col-8 text-right">
                                    <form method="POST" action="{{ route('stripe.customer_portal') }}">
                                        @csrf
                                        <button class="btn btn-sm btn-primary" type="submit">Billing portal</button>
                                    </form>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <th>Stripe Price Id</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Actions</th>
                            </thead>
                            <tbody>
                            @foreach($stripePrices as $stripePrice)
                                <tr>
                                    <td>{{$stripePrice->id}}</td>
                                    <td>{{Rabol\LaravelSimplesubscriptionStripe\LaravelSimplesubscriptionStripe::stripe()->products->retrieve($stripePrice->product)->name}}</td>
                                    <td>
                                        @php
                                            $nf = new \NumberFormatter('es_ES', \NumberFormatter::CURRENCY);
                                            $value = $nf->formatCurrency(($stripePrice->unit_amount / 100), 'EUR');
                                        @endphp
                                        {{ $value}}
                                    </td>
                                    <td>
                                        <form action="{{ route('stripe.checkout') }}" method="POST">
                                        @csrf
                                            <input type="hidden" name="priceId" value="{{ $stripePrice->id }}" />
                                            <button class="btn btn-sm btn-primary" type="submit">Checkout</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

```
Cacncled - /resources/views/stripe/canceld.blade.php
```
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Simple subsctiption Stripe') }}</div>

                    <div class="card-body">
                        Sorry to see that you canceled the checkout
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

```

Success - /resources/views/stripe/success.blade.php
```
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Simple subsctiption Stripe') }}</div>

                    <div class="card-body">
                        <div class="row">

                        </div>
                        Thanks, {{$customer->name}} enjoy your subscription.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
````

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
