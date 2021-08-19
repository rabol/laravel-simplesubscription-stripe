<?php

namespace Rabol\LaravelSimpleSubscriptionStripe\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;
use UnexpectedValueException;

class WebhookController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function handleEvents(Request $request): Response
    {
        $payload = $request->getContent();

        if (is_null($payload)) {
            return new Response('Bad request', 400);
        }

        if (is_string($payload) && strlen($payload) == 0) {
            return new Response('Bad request', 400);
        }

        $sig_header = $request->header('Stripe-Signature');

        if (is_null($sig_header)) { // Not from stripe
            return new Response('Bad request', 400);
        }

        $endpoint_secret = config('simplesubscription-stripe.stripe_webhook_secret');
        $endpoint_tolerance = config('simplesubscription-stripe.stripe_webhook_tolerance');

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret, $endpoint_tolerance);

            $method = 'handle' . Str::studly(str_replace('.', '_', $event->type)) . 'Event';

            if (method_exists($this, $method)) {
                return $this->{$method}($event);
            }
        } catch (UnexpectedValueException $e) {
            // Invalid payload
            Log::error('Invalid pay load:', json_decode($payload, true));
            return new Response('Invalid payload', 400);
        } catch (SignatureVerificationException $e) {
            // Invalid signature
            Log::error('Invalid signature:', $payload != '' ? json_decode($payload, true) : []);
            return new Response('Invalid signature', 400);
        }

        $data = json_decode($payload, true);
        Log::debug('Missing: ' . $data['type'] . ' event handler');
        return new Response('Webhook Handled', 200);
    }
}
