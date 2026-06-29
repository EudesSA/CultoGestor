<?php

namespace App\Http\Controllers;

use App\Models\Cantor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Armazena/remove a inscrição de push do cantor (PWA). A identidade vem do
 * token do portal — a inscrição fica vinculada ao usuário do cantor.
 */
class PushSubscriptionController extends Controller
{
    public function subscribe(Request $request, string $token): JsonResponse
    {
        $cantor = Cantor::where('token_portal', $token)->firstOrFail();
        abort_unless($cantor->user, 404);

        $data = $request->validate([
            'endpoint'     => 'required|string',
            'keys.p256dh'  => 'required|string',
            'keys.auth'    => 'required|string',
        ]);

        $cantor->user->updatePushSubscription(
            $data['endpoint'],
            $data['keys']['p256dh'],
            $data['keys']['auth'],
        );

        return response()->json(['ok' => true]);
    }

    public function unsubscribe(Request $request, string $token): JsonResponse
    {
        $cantor = Cantor::where('token_portal', $token)->firstOrFail();

        if ($cantor->user && $endpoint = $request->input('endpoint')) {
            $cantor->user->deletePushSubscription($endpoint);
        }

        return response()->json(['ok' => true]);
    }
}
