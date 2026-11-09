<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ShipmentAssigned;
use App\Models\Shipment;
use App\Models\User;
use App\Support\Notify;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ShipmentController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $shipments = Shipment::with('user')
            ->when($request->query('q'), function ($query, $term) {
                $query->where(function ($w) use ($term) {
                    $w->where('tracking_id', 'like', '%' . Shipment::normaliseReference($term) . '%')
                        ->orWhere('container_number', 'like', '%' . Shipment::normaliseReference($term) . '%')
                        ->orWhereHas('user', fn ($u) => $u
                            ->where('name', 'like', "%{$term}%")
                            ->orWhere('username', 'like', "%{$term}%")
                            ->orWhere('company', 'like', "%{$term}%"));
                });
            })
            ->when($status && array_key_exists($status, Shipment::STATUSES), fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.shipments.index', [
            'shipments' => $shipments,
            'statuses' => Shipment::STATUSES,
        ]);
    }

    public function create(): View
    {
        return view('admin.shipments.create', [
            'shipment' => new Shipment(['status' => 'pending']),
            'clients' => User::activeClients()->orderBy('name')->get(),
            'statuses' => Shipment::STATUSES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules() + [
            'status' => ['required', Rule::in(array_keys(Shipment::STATUSES))],
        ]);

        $data['container_number'] = $this->cleanContainer($data['container_number'] ?? null);
        $data['tracking_id'] = Shipment::newTrackingId();
        $data['current_location'] = $data['origin'];
        $data['delivered_at'] = $data['status'] === 'delivered' ? now() : null;

        $shipment = Shipment::create($data);

        $shipment->events()->create([
            'status' => $shipment->status,
            'location' => $shipment->origin,
            'note' => 'Shipment record created.',
            'occurred_at' => now(),
        ]);

        // Email the client that a shipment now belongs to them.
        $shipment->load('user');
        $emailed = Notify::send($shipment->user, new ShipmentAssigned($shipment));

        return redirect()->route('admin.shipments.show', $shipment)->with(
            $emailed
                ? ['status' => "Shipment {$shipment->tracking_id} created and {$shipment->user->name} has been emailed."]
                : [
                    'status' => "Shipment {$shipment->tracking_id} created.",
                    'warning' => "The email to {$shipment->user->name} could not be sent. Check the mail settings in .env and storage/logs/laravel.log.",
                ]
        );
    }

    public function show(Shipment $shipment): View
    {
        $shipment->load('user', 'events');

        return view('admin.shipments.show', [
            'shipment' => $shipment,
            'statuses' => Shipment::STATUSES,
        ]);
    }

    public function edit(Shipment $shipment): View
    {
        return view('admin.shipments.edit', [
            'shipment' => $shipment,
            'clients' => User::activeClients()->orWhere('id', $shipment->user_id)->orderBy('name')->get(),
            'statuses' => Shipment::STATUSES,
        ]);
    }

    public function update(Request $request, Shipment $shipment): RedirectResponse
    {
        $data = $request->validate($this->rules($shipment));
        $data['container_number'] = $this->cleanContainer($data['container_number'] ?? null);

        $previousOwner = $shipment->user_id;

        $shipment->update($data);

        $flash = ['status' => 'Shipment details saved.'];

        if ((int) $previousOwner !== (int) $shipment->user_id) {
            $shipment->load('user');

            if (Notify::send($shipment->user, new ShipmentAssigned($shipment))) {
                $flash['status'] = "Shipment reassigned to {$shipment->user->name}, who has been emailed.";
            } else {
                $flash['status'] = "Shipment reassigned to {$shipment->user->name}.";
                $flash['warning'] = 'The email could not be sent. Check the mail settings in .env and storage/logs/laravel.log.';
            }
        }

        return redirect()->route('admin.shipments.show', $shipment)->with($flash);
    }

    public function destroy(Shipment $shipment): RedirectResponse
    {
        $shipment->delete(); // timeline entries are removed with it (cascade)

        return redirect()->route('admin.shipments.index')->with('status', "Shipment {$shipment->tracking_id} deleted.");
    }

    private function rules(?Shipment $shipment = null): array
    {
        return [
            // Only active, non-admin clients can be assigned a shipment. When editing, the
            // shipment's current owner stays valid even if they have since been suspended.
            'user_id' => ['required', Rule::exists('users', 'id')->where(function ($q) use ($shipment) {
                $q->where('is_admin', false)->where(function ($w) use ($shipment) {
                    $w->where('status', User::STATUS_ACTIVE);
                    if ($shipment) {
                        $w->orWhere('id', $shipment->user_id);
                    }
                });
            })],
            'container_number' => ['nullable', 'string', 'max:20'],
            'origin' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'cargo_description' => ['nullable', 'string', 'max:255'],
            'weight_kg' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'eta' => ['nullable', 'date'],
        ];
    }

    private function cleanContainer(?string $value): ?string
    {
        return filled($value) ? Shipment::normaliseReference($value) : null;
    }
}
