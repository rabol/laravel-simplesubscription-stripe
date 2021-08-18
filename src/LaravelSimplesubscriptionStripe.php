<?php

namespace Rabol\LaravelSimplesubscriptionStripe;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class LaravelSimplesubscriptionStripe
{
    public const STRIPE_VERSION = '2020-08-27';

    public static function stripe(array $options = []): StripeClient
    {
        return new StripeClient(array_merge([
            'api_key' => $options['api_key'] ?? config('simplesubscription-stripe.stripe_secret'),
            'stripe_version' => static::STRIPE_VERSION,
        ], $options));
    }

    public static function gotoStripeCustomerPortal(User $user, string $return_url)
    {
        return self::stripe()
            ->billingPortal
            ->sessions
            ->create(
                [
                'customer' => $user->stripe_id,
                'return_url' => $return_url,
            ]
            )->url;
    }

    public static function createAsStripeCustomer(User $user, array $options = [])
    {
        if (! is_null($user->stripe_id)) {
            return;
        }

        if (! array_key_exists('name', $options) && $name = $user->name) {
            $options['name'] = $name;
        }

        if (! array_key_exists('email', $options) && $email = $user->email) {
            $options['email'] = $email;
        }

        try {
            // If the customer is in EU and you are in the EU, the $options can / should contain
            // tax id number and exempt status
            $customer = self::stripe()->customers->create($options);

            $user->stripe_id = $customer->id;
            $user->save();
        } catch (ApiErrorException $e) {
            Log::error($e->getMessage());
        }
    }

    public static function createCheckoutSession(array $options)
    {
        return self::stripe()->checkout->sessions->create($options);
    }

    public static function updateStripeCustomer(User $user, array $options)
    {
        try {
            self::stripe()->customers->update($user->stripe_id, $options);
        } catch (ApiErrorException $e) {
            Log::error($e->getMessage());
        }
    }

    public function updateTaxIdOnCustomer(User $user, string $value, string $type='eu_vat')
    {
        // first get all tax id's
        $taxIds = self::stripe()->customers->allTaxIds($user->stripe_id);
        $addNewTaxId = true;

        if ($taxIds && $taxIds->count() != 0) {
            foreach ($taxIds as $taxId) {
                if ($taxId->type == $type && $taxId->value == $value) {
                    $addNewTaxId = false;
                }
            }
        }

        if ($addNewTaxId) {
            $taxId = self::stripe()->customers->createTaxId($user->stripe_id, [
                'type' => $type,
                'value' => $value,
            ]);

            $user->stripe_tax_id = $taxId->id;
            $user->save();
        }
    }
}
