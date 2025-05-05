<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Payment\Services\PaymentService;

class StripeWebhookController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function handleWebhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        // Queue webhook processing
        \App\Jobs\ProcessStripeWebhook::dispatch($payload, $signature);

        return response()->json(['status' => 'success'], 200);
    }
}
