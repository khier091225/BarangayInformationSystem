<?php

namespace App\Http\Controllers;

use App\Http\PayMongo;
use App\Http\PayMongoPayments;
use Illuminate\Http\Request;
use JsonException;
use Symfony\Component\HttpFoundation\Response;

class PayMongoWebhookController extends Controller
{
    public function __invoke(Request $request, PayMongo $gateway, PayMongoPayments $payments): Response
    {
        $body = $request->getContent();
        abort_if(strlen($body) > 1048576, 413);
        abort_unless($gateway->hasValidSignature($body, (string) $request->header('Paymongo-Signature')), 401);
        try {
            $payload = json_decode($body, true, 64, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            abort(400);
        }

        abort_unless(is_array($payload), 400);
        $event = data_get($payload, 'data.attributes');
        if (data_get($payload, 'data.type') !== 'event') {
            $event = data_get($payload, 'data');
        }
        abort_unless(is_array($event), 422);
        if (($event['type'] ?? null) !== 'checkout_session.payment.paid') {
            return response()->json(['received' => true]);
        }

        $session = $event['data'] ?? null;
        $liveMode = $event['livemode'] ?? data_get($session, 'attributes.livemode');
        abort_unless(is_array($session) && is_bool($liveMode) && $liveMode === $gateway->isLiveMode(), 422);
        $payments->applyCheckout($session, $liveMode);

        return response()->json(['received' => true]);
    }
}
