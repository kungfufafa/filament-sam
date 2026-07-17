<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteOneSignalSubscriptionRequest;
use App\Http\Requests\StoreOneSignalSubscriptionRequest;
use App\Models\OneSignalSubscription;
use Illuminate\Http\Response;

class OneSignalSubscriptionController extends Controller
{
    public function store(StoreOneSignalSubscriptionRequest $request): Response
    {
        OneSignalSubscription::query()->updateOrCreate(
            ['subscription_id' => $request->validated('subscription_id')],
            [
                'user_id' => $request->user()->getAuthIdentifier(),
                'platform' => 'web',
            ],
        );

        return response()->noContent();
    }

    public function destroy(DeleteOneSignalSubscriptionRequest $request): Response
    {
        OneSignalSubscription::query()
            ->whereBelongsTo($request->user())
            ->where('subscription_id', $request->validated('subscription_id'))
            ->delete();

        return response()->noContent();
    }
}
