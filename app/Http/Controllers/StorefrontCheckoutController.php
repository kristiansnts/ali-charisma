<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorefrontCheckoutRequest;
use App\Models\Account;
use App\Services\Midtrans\MidtransSnapService;
use App\Support\StorefrontOrderPlacer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Throwable;

class StorefrontCheckoutController extends Controller
{
    public function pay(
        StorefrontCheckoutRequest $request,
        StorefrontOrderPlacer $placer,
        MidtransSnapService $snap,
    ): JsonResponse {
        try {
            /** @var Account $account */
            $account = Auth::guard('account')->user();
            $snap->ensureConfigured();
            $checkout = $request->checkoutPayload();
            $order = $placer->place($checkout, $account);
            $snapToken = $snap->getSnapToken($order, $checkout);
        } catch (Throwable $exception) {
            report($exception);

            $message = match (true) {
                $exception instanceof \RuntimeException => $exception->getMessage(),
                str_contains($exception->getMessage(), 'Midtrans API is returning API error') => $this->midtransErrorMessage($exception),
                default => config('app.debug')
                    ? $exception->getMessage()
                    : 'Unable to start payment right now.',
            };

            return response()->json(['message' => $message], 502);
        }

        return response()->json([
            'snap_token' => $snapToken,
            'order_uuid' => $order->uuid,
        ]);
    }

    public function finish(Request $request): View
    {
        return view('malefashion.pages.checkout-payment-result', [
            'status' => 'success',
            'orderUuid' => $request->query('order_id'),
        ]);
    }

    public function unfinish(Request $request): View
    {
        return view('malefashion.pages.checkout-payment-result', [
            'status' => 'pending',
            'orderUuid' => $request->query('order_id'),
        ]);
    }

    private function midtransErrorMessage(Throwable $exception): string
    {
        if (preg_match('/"error_messages":\[(.*?)\]/', $exception->getMessage(), $matches)) {
            return trim(stripslashes($matches[1]), '"');
        }

        return 'Payment could not be started. Please check your details and try again.';
    }
}
