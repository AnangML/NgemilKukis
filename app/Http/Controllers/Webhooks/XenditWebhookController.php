<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\XenditPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class XenditWebhookController extends Controller
{
    public function __construct(
        protected XenditPaymentService $xenditService,
    ) {}

    /**
     * Handle incoming Xendit invoice webhook notifications.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $callbackToken = $request->header('x-callback-token');

        if (! $this->xenditService->verifyWebhookToken($callbackToken)) {
            Log::warning('Xendit webhook rejected: Invalid callback token.', [
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized callback token.',
            ], 401);
        }

        $data = $request->all();
        $externalId = $data['external_id'] ?? null;
        $status = strtoupper((string) ($data['status'] ?? ''));

        if ($externalId && $status === 'PAID') {
            $order = Order::query()->where('order_number', $externalId)->first();
            if ($order && $order->status !== 'paid') {
                $order->update([
                    'status' => 'paid',
                ]);

                Log::info("Xendit payment confirmed automatically for order {$order->order_number}");
            }
        } elseif ($externalId && $status === 'EXPIRED') {
            $order = Order::query()->where('order_number', $externalId)->first();
            if ($order && $order->status === 'pending') {
                $order->update([
                    'status' => 'cancelled',
                ]);
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
