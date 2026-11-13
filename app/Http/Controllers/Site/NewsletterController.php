<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    public function subscribe(Request $request): RedirectResponse
    {
        // Honeypot: bots fill the hidden field. Pretend it worked.
        if ($request->filled('website')) {
            return back()->with('status', 'Thank you for subscribing.');
        }

        $data = $request->validate(['email' => ['required', 'email:rfc', 'max:190']]);
        $email = Str::lower($data['email']);

        $sub = Subscriber::firstOrNew(['email' => $email]);
        if (! $sub->exists) {
            $sub->token = Str::random(40);
        }
        $sub->subscribed_at = $sub->subscribed_at ?? now();
        $sub->unsubscribed_at = null;
        $sub->save();

        return back()->with('status', 'Thank you for subscribing. You will hear from us soon.');
    }

    public function unsubscribe(string $token)
    {
        $sub = Subscriber::where('token', $token)->first();
        if ($sub) {
            $sub->update(['unsubscribed_at' => now()]);
        }

        return redirect()->route('home')->with('status', 'You have been unsubscribed.');
    }
}
