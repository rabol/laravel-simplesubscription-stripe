<?php

namespace Rabol\LaravelSimplesubscriptionStripe\Http\Controllers;

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
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('simplesubscription-stripe.stripe_webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
            $method = 'handle' . Str::studly(str_replace('.', '_', $event->type)) . 'Event';

            if (method_exists($this, $method)) {
                return $this->{$method}($event);
            }
        } catch(UnexpectedValueException $e) {
            // Invalid payload
            Log::error('Invalid pay load:' , json_decode($payload,true));
            return new Response('Invalid payload', 400);
        } catch(SignatureVerificationException $e) {
            // Invalid signature
            Log::debug($sig_header);
            Log::debug($endpoint_secret);
            Log::debug($e->getMessage());
            //Log::debug(json_decode($request->getContent(false)));
            Log::error('Invalid signature:' , json_decode($payload,true));

            return new Response('Invalid signature', 400);
        }

        $data = json_decode($payload,true);
        Log::debug('Missing: ' . $data['type'] . ' event handler');
        return new Response('Webhook Handled', 200);
    }
}
