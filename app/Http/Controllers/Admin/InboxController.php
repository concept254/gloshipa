<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\Subscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InboxController extends Controller
{
    public function index(): View
    {
        return view('admin.inbox.index', ['rows' => Inquiry::latest()->paginate(20)]);
    }

    public function show(Inquiry $inquiry): View
    {
        if (! $inquiry->read_at) {
            $inquiry->forceFill(['read_at' => now()])->save();
        }

        return view('admin.inbox.show', compact('inquiry'));
    }

    public function destroy(Inquiry $inquiry): RedirectResponse
    {
        $inquiry->delete();

        return redirect()->route('admin.website.inbox')->with('status', 'Message deleted.');
    }

    public function subscribers(): View
    {
        return view('admin.inbox.subscribers', [
            'rows' => Subscriber::latest()->paginate(30),
            'active' => Subscriber::whereNull('unsubscribed_at')->count(),
        ]);
    }

    public function destroySubscriber(Subscriber $subscriber): RedirectResponse
    {
        $subscriber->delete();

        return back()->with('status', 'Subscriber removed.');
    }

    public function export(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['email', 'subscribed_at', 'unsubscribed_at']);
            Subscriber::orderBy('id')->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $r) {
                    // Guard against spreadsheet formula injection from user-supplied emails
                    $email = preg_match('/^[=+\-@]/', $r->email) ? "'" . $r->email : $r->email;
                    fputcsv($out, [$email, $r->subscribed_at, $r->unsubscribed_at]);
                }
            });
            fclose($out);
        }, 'subscribers-' . now()->format('Y-m-d') . '.csv', ['Content-Type' => 'text/csv']);
    }
}
