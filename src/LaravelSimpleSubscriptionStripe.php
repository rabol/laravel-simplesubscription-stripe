<?php

namespace Rabol\LaravelSimpleSubcriptionStripe;

use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class LaravelSimpleSubscriptionStripe
{
    public const STRIPE_VERSION = '2020-08-27';

    public static function stripe(array $options = []): StripeClient
    {
        return new StripeClient(array_merge([
            'api_key' => $options['api_key'] ?? config('simplesubscription-stripe.stripe_secret'),
            'stripe_version' => static::STRIPE_VERSION,
        ], $options));
    }

    public static function gotoStripeCustomerPortal(string $customer_id, string $return_url): ?string
    {
        try {
            return self::stripe()
                ->billingPortal
                ->sessions
                ->create(
                    [
                        'customer' => $customer_id,
                        'return_url' => $return_url,
                    ]
                )->url;
        } catch (ApiErrorException $e) {
            Log::error($e->getMessage());
        }

        return null;
    }

    public static function createAsStripeCustomer(array $options = []): ?\Stripe\Customer
    {
        try {
            // If the customer is in EU and you are in the EU, the $options can / should contain
            // tax id number and exempt status
            return self::stripe()->customers->create($options);
        } catch (ApiErrorException $e) {
            Log::error($e->getMessage());
        }

        return null;
    }

    public static function createCheckoutSession(array $options): ?\Stripe\Checkout\Session
    {
        try {
            return self::stripe()->checkout->sessions->create($options);
        } catch (ApiErrorException $e) {
            Log::error($e->getMessage());
        }

        return null;
    }

    public static function updateStripeCustomer(string $customer_id, array $options): bool
    {
        try {
            self::stripe()->customers->update($customer_id, $options);
            return true;
        } catch (ApiErrorException $e) {
            Log::error($e->getMessage());
        }

        return false;
    }

    public static function updateTaxIdOnCustomer(string $customer_id, string $value, string $type='eu_vat'): ?\Stripe\TaxId
    {
        $addNewTaxId = true;

        // first get all tax id's
        try {
            $taxIds = self::stripe()->customers->allTaxIds($customer_id);
            // check if the user have the TaxId
            if ($taxIds->count()) {
                foreach ($taxIds as $taxId) {
                    if ($taxId->type == $type && ($taxId->value == $value || $taxId->value == str_replace('-', '', $value))) {
                        $addNewTaxId = false;
                    }
                }
            }
        } catch (ApiErrorException $e) {
            Log::error($e->getMessage());
            return null;
        }

        // Does not seem like the TaxId is attached to the user, so let's attach it
        if ($addNewTaxId) {
            try {
                return self::stripe()->customers->createTaxId($customer_id, [
                    'type' => $type,
                    'value' => $value,
                ]);
            } catch (ApiErrorException $e) {
                Log::error($e->getMessage());
            }
        }

        return null;
    }

    public static function getUsersSubscriptions(string $customer_id): ?\Stripe\Collection
    {
        $stripe = LaravelSimpleSubscriptionStripe::stripe();
        try {
            $stripeCustomer = $stripe->customers->retrieve(
                $customer_id,
                ['expand' => ['subscriptions']]
            );
            return $stripeCustomer->subscriptions;
        } catch (ApiErrorException $e) {
            Log::error($e->getMessage());
        }

        return null;
    }

    public static function getSubscription(string $subscription_id): ?\Stripe\Subscription
    {
        try {
            return self::stripe()->subscriptions->retrieve($subscription_id);
        } catch (ApiErrorException $e) {
            Log::error($e->getMessage());
        }

        return null;
    }

    public static function cancelSubscription(string $subscription_id): ?\Stripe\Subscription
    {
        try {
            return self::stripe()->subscriptions->cancel($subscription_id);
        } catch (ApiErrorException $e) {
            Log::error($e->getMessage());
        }

        return null;
    }
}
