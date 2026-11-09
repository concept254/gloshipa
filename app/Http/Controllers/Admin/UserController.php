<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AccountApproved;
use App\Models\User;
use App\Support\Notify;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $tab = in_array($request->query('status'), ['pending', 'active', 'suspended'], true)
            ? $request->query('status')
            : 'pending';

        $users = User::clients()
            ->where('status', $tab)
            ->when($request->query('q'), function ($query, $term) {
                $query->where(function ($w) use ($term) {
                    $w->where('name', 'like', "%{$term}%")
                        ->orWhere('username', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('company', 'like', "%{$term}%");
                });
            })
            ->withCount('shipments')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $counts = User::clients()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.users.index', compact('users', 'tab', 'counts'));
    }

    public function updateStatus(Request $request, User $user): RedirectResponse
    {
        // Admin accounts are never managed from this screen.
        abort_if($user->isAdmin(), 403);

        $data = $request->validate([
            'status' => ['required', Rule::in([User::STATUS_ACTIVE, User::STATUS_SUSPENDED])],
        ]);

        $wasPending = $user->status === User::STATUS_PENDING;

        $user->forceFill([
            'status' => $data['status'],
            'approved_at' => $data['status'] === User::STATUS_ACTIVE ? ($user->approved_at ?? now()) : $user->approved_at,
        ])->save();

        if ($data['status'] === User::STATUS_ACTIVE) {
            if (! $wasPending) {
                return back()->with('status', "{$user->name} is now active.");
            }

            return Notify::send($user, new AccountApproved($user))
                ? back()->with('status', "{$user->name} is now active and has been emailed.")
                : back()->with([
                    'status' => "{$user->name} is now active.",
                    'warning' => 'The approval email could not be sent. Check the mail settings in .env and storage/logs/laravel.log.',
                ]);
        }

        return back()->with('status', "{$user->name} has been switched off.");
    }
}
