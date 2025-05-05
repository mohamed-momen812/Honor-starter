<?php

namespace Modules\Payment\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Modules\Payment\Models\Payment;
use Modules\Payment\Repositories\PaymentRepositoryInterface;
use Modules\Order\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    protected $paymentRepository;

    public function __construct(PaymentRepositoryInterface $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function getAllPayments(): Collection
    {
        return $this->paymentRepository->getAll();
    }

    public function getPaginatedPayments(int $perPage = 15): LengthAwarePaginator
    {
        return $this->paymentRepository->paginate($perPage);
    }

    public function findPaymentById(int $id): ?Payment
    {
        return $this->paymentRepository->findById($id);
    }

    public function createPaymentIntent(int $orderId): array
    {
        return DB::transaction(function () use ($orderId) {
            $order = Order::findOrFail($orderId);
            if ($order->user_id !== auth()->id() && !auth()->user()->hasPermissionTo('manage-payments')) {
                throw new \Exception('Unauthorized to pay for this order');
            }
            if ($order->payments()->exists()) {
                throw new \Exception('Order already has a payment');
            }

            $paymentIntent = PaymentIntent::create([
                'amount' => $order->total * 100, // Convert to cents
                'currency' => 'usd',
                'metadata' => [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                ],
            ]);

            $payment = $this->paymentRepository->create([
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'stripe_payment_id' => $paymentIntent->id,
                'amount' => $order->total,
                'currency' => 'usd',
                'status' => 'pending',
            ]);

            return [
                'client_secret' => $paymentIntent->client_secret,
                'payment_id' => $payment->id,
            ];
        });
    }

    // public function handleWebhook($payload, $signature)
    // {
    //     $webhookSecret = config('services.stripe.webhook_secret');
    //     try {
    //         $event = \Stripe\Webhook::constructEvent($payload, $signature, $webhookSecret);
    //     } catch (\Exception $e) {
    //         throw new \Exception('Invalid webhook signature');
    //     }

    //     if ($event->type === 'payment_intent.succeeded') {
    //         $paymentIntent = $event->data->object;
    //         $payment = Payment::where('stripe_payment_id', $paymentIntent->id)->first();
    //         if ($payment) {
    //             $payment->update(['status' => 'succeeded']);
    //             $payment->order->update(['status' => 'paid']);
    //             // Trigger notification (to be implemented in Step 10)
    //             $this->triggerPaymentNotification($payment, 'succeeded');
    //         }
    //     } elseif ($event->type === 'payment_intent.payment_failed') {
    //         $paymentIntent = $event->data->object;
    //         $payment = Payment::where('stripe_payment_id', $paymentIntent->id)->first();
    //         if ($payment) {
    //             $payment->update(['status' => 'failed']);
    //             // Trigger notification
    //             $this->triggerPaymentNotification($payment, 'failed');
    //         }
    //     }
    // }

    protected function triggerPaymentNotification(Payment $payment, string $status)
    {
        // Placeholder for NotificationService integration (Step 10)
        // Replace with actual notification creation after Step 10
        \Log::info("Payment {$status} for order #{$payment->order_id}");
    }
}
