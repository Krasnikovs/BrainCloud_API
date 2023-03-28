<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Stripe\StripeClient;

class StripeController extends Controller
{
    public function bill (Request $request, User $user) {
        $validated = $request->validate([
            'subscription_type' => 'required|in:Basic,Extra,Rugged storage'
        ]);
        $plan = Plan::where('type', $validated['subscription_type'])->first();
        $stripe = new StripeClient(config('app.stripe_secret'));

        $session = $stripe->checkout->sessions->create([
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'eur',
                        'unit_amount' => round(100 * $plan['price']),
                        'product_data' => [
                            'name' => $plan['type'],
                            'description' => $plan['description'],
                        ],
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => 'http://127.0.0.1:3000/stripe'.'?session_id={CHECKOUT_SESSION_ID}&user='.$user.'&subscription_type='.$validated['subscription_type'],
            'cancel_url' => 'http://127.0.0.1:3000/profile?success=false',
        ]);
        return response()->json([
            'url' => $session['url'],
        ]);
    }

    public function successPayment (Request $request) {
        $stripe = new StripeClient(config('app.stripe_secret'));
        $stripe->checkout->sessions->retrieve($request->get('session_id'));

        $user = User::find(json_decode($request['user'])->id);
        $user['subscription_type'] = $request['subscription_type'];
        $user->save();

        return response()->json([
            'data' => 'success payment',
        ]);
    }
}
