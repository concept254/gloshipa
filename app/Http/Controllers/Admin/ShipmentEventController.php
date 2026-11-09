<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ShipmentUpdated;
use App\Models\Shipment;
use App\Models\ShipmentEvent;
use App\Support\Notify;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShipmentEventController extends Controller
{
    /** Add a timeline entry: moves the shipment to a new status and/or location. */
    public function store(Request $request, Shipment $shipment): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Shipment::STATUSES))],
            'location' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:1000'],
            'occurred_at' => ['required', 'date'],
            'notify' => ['nullable', 'boolean'],
        ]);

        $previousLocation = $shipment->current_location;
        $previousStatus = $shipment->status;

        $event = $shipment->events()->create([
            'status' => $data['status'],
            'location' => $data['location'] ?? null,
            'note' => $data['note'] ?? null,
            'occurred_at' => $data['occurred_at'],
        ]);

        $shipment->update([
            'status' => $data['status'],
            'current_location' => filled($data['location'] ?? null) ? $data['location'] : $shipment->current_location,
            'delivered_at' => $data['status'] === 'delivered' ? ($shipment->delivered_at ?? $event->occurred_at) : null,
        ]);

        $emailed = false;

        if ($request->boolean('notify') && $shipment->user?->isActive()) {
            $emailed = Notify::send(
                $shipment->user,
                new ShipmentUpdated($shipment->fresh(), $event, $previousLocation, $previousStatus)
            );
        }

        $flash = ['status' => $emailed ? 'Update added and the client has been emailed.' : 'Update added.'];

        if ($request->boolean('notify') && ! $emailed && $shipment->user?->isActive()) {
            $flash['warning'] = 'The email could not be sent. Check the mail settings in .env and storage/logs/laravel.log.';
        }

        return back()->with($flash);
    }

    /** Remove a mistaken entry and re-sync the shipment with whatever is now the latest entry. */
    public function destroy(Shipment $shipment, ShipmentEvent $event): RedirectResponse
    {
        abort_unless((int) $event->shipment_id === (int) $shipment->id, 404);

        $event->delete();

        $latest = $shipment->events()->first(); // newest first

        if ($latest) {
            $shipment->update([
                'status' => $latest->status,
                'current_location' => $latest->location ?? $shipment->current_location,
                'delivered_at' => $latest->status === 'delivered' ? ($shipment->delivered_at ?? $latest->occurred_at) : null,
            ]);
        }

        return back()->with('status', 'Timeline entry removed.');
    }
}
