<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrackingController extends Controller
{
    public function show(Request $request): View
    {
        $request->validate(['q' => ['nullable', 'string', 'max:50']]);

        $term = trim((string) $request->query('q', ''));
        $user = $request->user();
        $results = collect();

        if ($term !== '') {
            // Clients can only ever search their OWN shipments, so a tracking ID that
            // belongs to somebody else simply returns "not found". Admins search everything.
            $base = $user->isAdmin() ? Shipment::query() : $user->shipments();

            $results = $base->matchingReference($term)
                ->with(['events', 'user'])
                ->latest()
                ->limit(10)
                ->get();
        }

        return view('track', [
            'term' => $term,
            'results' => $results,
            'searched' => $term !== '',
        ]);
    }
}
