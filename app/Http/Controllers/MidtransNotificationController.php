<?php

namespace App\Http\Controllers;

use App\Services\Midtrans\MidtransNotificationHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MidtransNotificationController extends Controller
{
    public function store(Request $request, MidtransNotificationHandler $handler): Response
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->json()->all();

        if ($payload === []) {
            return response('Invalid payload.', 400);
        }

        $handler->handle($payload);

        return response('OK', 200);
    }
}
