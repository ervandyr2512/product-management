<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:newsletter_subscribers,email',
            'name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        NewsletterSubscriber::create([
            'email' => $request->email,
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('messages.newsletter_subscribed'),
        ]);
    }

    public function unsubscribe(Request $request)
    {
        $subscriber = NewsletterSubscriber::where('email', $request->email)->first();

        if (!$subscriber) {
            return response()->json([
                'success' => false,
                'message' => __('messages.newsletter_not_found'),
            ], 404);
        }

        $subscriber->update([
            'is_active' => false,
            'unsubscribed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('messages.newsletter_unsubscribed'),
        ]);
    }
}
