<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return view('dashboard', [
            'user' => $user,
            'open' => $user->shipments()->open()->orderByRaw('eta is null')->orderBy('eta')->get(),
            'deliveredCount' => $user->shipments()->where('status', 'delivered')->count(),
            'recentDelivered' => $user->shipments()->where('status', 'delivered')->latest('delivered_at')->limit(3)->get(),
        ]);
    }
}
