<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Payment\Services\PaymentService;

class ProcessStripeWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $payload;
    protected $signature;

    public function __construct($payload, $signature)
    {
        $this->payload = $payload;
        $this->signature = $signature;
    }

    public function handle(PaymentService $paymentService)
    {
        $paymentService->handleWebhook($this->payload, $this->signature);
    }
}
