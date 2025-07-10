<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return response()->json(['message' => 'Login successful'], 200);
        } else {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    }

    public function logout(Request $request) {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('home')->with('message', 'You have been signed out successfully.');
    }

    public function subscribe(Request $request, User $user)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $subscriber = Auth::user();
        
        if ($subscriber->id === $user->id) {
            return response()->json(['message' => 'Cannot subscribe to yourself'], 400);
        }

        // Check if already subscribed
        $existingSubscription = Subscription::where('subscriber_id', $subscriber->id)
            ->where('channel_id', $user->id)
            ->first();

        if ($existingSubscription) {
            return response()->json(['message' => 'Already subscribed'], 400);
        }

        // Create subscription
        Subscription::create([
            'subscriber_id' => $subscriber->id,
            'channel_id' => $user->id,
        ]);

        // Update subscriber count
        $user->updateSubscribersCount();

        return response()->json(['message' => 'Subscribed successfully'], 200);
    }

    public function unsubscribe(Request $request, User $user)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $subscriber = Auth::user();

        // Find and delete subscription
        $subscription = Subscription::where('subscriber_id', $subscriber->id)
            ->where('channel_id', $user->id)
            ->first();

        if (!$subscription) {
            return response()->json(['message' => 'Not subscribed'], 400);
        }

        $subscription->delete();

        // Update subscriber count
        $user->updateSubscribersCount();

        return response()->json(['message' => 'Unsubscribed successfully'], 200);
    }
}
