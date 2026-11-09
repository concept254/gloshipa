<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'awaitingApproval' => User::clients()->where('status', User::STATUS_PENDING)->latest()->get(),
            'stats' => [
                'clients' => User::clients()->where('status', User::STATUS_ACTIVE)->count(),
                'open' => Shipment::open()->count(),
                'delayed' => Shipment::where('status', 'delayed')->count(),
                'delivered' => Shipment::where('status', 'delivered')->count(),
            ],
            'recent' => Shipment::with('user')->latest()->limit(6)->get(),
        ]);
    }
}
