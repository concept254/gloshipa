<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class MyShipmentController extends Controller
{
    public function index(Request $request): View
    {
        $filter = in_array($request->query('filter'), ['pending', 'delivered'], true)
            ? $request->query('filter')
            : 'all';

        $shipments = $request->user()->shipments()
            ->when($filter === 'pending', fn ($q) => $q->open())
            ->when($filter === 'delivered', fn ($q) => $q->where('status', 'delivered'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('shipments.index', compact('shipments', 'filter'));
    }
}
