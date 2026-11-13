<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Mail\InquiryReceived;
use App\Models\{Faq, Inquiry, Page, Partner, Post, Service, TeamMember, Testimonial};
use App\Support\Cms;
use App\Support\Notify;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class SiteController extends Controller
{
    public function home(): View
    {
        return view('home', [
            'services' => Cms::guard(fn () => Service::active()->get(), collect()),
            'faqs' => Cms::guard(fn () => Faq::active()->limit(6)->get(), collect()),
            'testimonials' => Cms::guard(fn () => Testimonial::active()->get(), collect()),
            'partners' => Cms::guard(fn () => Partner::active()->get(), collect()),
            'posts' => Cms::guard(fn () => Post::live()->limit(3)->get(), collect()),
            'team' => Cms::guard(fn () => TeamMember::active()->limit(4)->get(), collect()),
            'about' => Cms::page('about'),
        ]);
    }

    public function about(): View
    {
        return view('site.about', [
            'page' => Cms::page('about'),
            'profile' => Cms::page('company-profile'),
            'team' => Cms::guard(fn () => TeamMember::active()->get(), collect()),
            'testimonials' => Cms::guard(fn () => Testimonial::active()->get(), collect()),
            'partners' => Cms::guard(fn () => Partner::active()->get(), collect()),
            'faqs' => Cms::guard(fn () => Faq::active()->limit(5)->get(), collect()),
        ]);
    }

    public function services(): View
    {
        return view('site.services', ['page' => Cms::page('services'), 'services' => Cms::guard(fn () => Service::active()->get(), collect())]);
    }

    public function service(string $slug): View
    {
        $service = Service::active()->where('slug', $slug)->firstOrFail();
        $others = Service::active()->where('id', '!=', $service->id)->limit(4)->get();

        return view('site.service', compact('service', 'others'));
    }

    public function faqs(): View
    {
        return view('site.faqs', ['page' => Cms::page('faqs'), 'groups' => Cms::guard(fn () => Faq::active()->get()->groupBy('category'), collect())]);
    }

    public function testimonials(): View
    {
        return view('site.testimonials', ['page' => Cms::page('testimonials'), 'items' => Cms::guard(fn () => Testimonial::active()->get(), collect())]);
    }

    public function team(): View
    {
        return view('site.team', ['page' => Cms::page('team'), 'members' => Cms::guard(fn () => TeamMember::active()->get(), collect())]);
    }

    public function clients(): View
    {
        return view('site.clients', [
            'page' => Cms::page('clients'),
            'partners' => Cms::guard(fn () => Partner::active()->get(), collect()),
            'testimonials' => Cms::guard(fn () => Testimonial::active()->limit(6)->get(), collect()),
        ]);
    }

    public function news(): View
    {
        return view('site.news', ['page' => Cms::page('news'), 'posts' => Post::live()->paginate(9)]);
    }

    public function post(string $slug): View
    {
        $post = Post::live()->where('slug', $slug)->firstOrFail();
        $recent = Post::live()->where('id', '!=', $post->id)->limit(3)->get();

        return view('site.post', compact('post', 'recent'));
    }

    public function contact(): View
    {
        return view('site.contact', ['page' => Cms::page('contact')]);
    }

    public function contactStore(Request $request): RedirectResponse
    {
        if ($request->filled('website')) {
            return back()->with('status', 'Thank you. We will be in touch shortly.');
        }
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'phone' => ['nullable', 'string', 'max:60'],
            'subject' => ['nullable', 'string', 'max:160'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        return $this->saveInquiry('contact', $data, [], 'Thank you. We have your message and will reply shortly.');
    }

    public function quote(): View
    {
        return view('site.quote', [
            'page' => Cms::page('quote'),
            'services' => Cms::guard(fn () => Service::active()->get(['title']), collect()),
        ]);
    }

    public function quoteStore(Request $request): RedirectResponse
    {
        if ($request->filled('website')) {
            return back()->with('status', 'Thank you. Our team will send your quote shortly.');
        }
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'phone' => ['nullable', 'string', 'max:60'],
            'company' => ['nullable', 'string', 'max:120'],
            'service' => ['nullable', 'string', 'max:160'],
            'origin' => ['required', 'string', 'max:120'],
            'destination' => ['required', 'string', 'max:120'],
            'cargo' => ['nullable', 'string', 'max:255'],
            'weight' => ['nullable', 'string', 'max:60'],
            'message' => ['nullable', 'string', 'max:5000'],
        ]);
        $meta = [
            'Company' => $data['company'] ?? null, 'Service' => $data['service'] ?? null, 'From' => $data['origin'],
            'To' => $data['destination'], 'Cargo' => $data['cargo'] ?? null, 'Weight / volume' => $data['weight'] ?? null,
        ];
        $data['subject'] = 'Quote: ' . $data['origin'] . ' to ' . $data['destination'];

        return $this->saveInquiry('quote', $data, $meta, 'Thank you. Our team will send your quote shortly.');
    }

    private function saveInquiry(string $type, array $data, array $meta, string $thanks): RedirectResponse
    {
        $inquiry = Inquiry::create([
            'type' => $type, 'name' => $data['name'], 'email' => $data['email'], 'phone' => $data['phone'] ?? null,
            'subject' => $data['subject'] ?? null, 'message' => $data['message'] ?? null, 'meta' => $meta ?: null,
        ]);
        // The enquiry is safely stored even if the email fails; it also shows in the admin inbox.
        Notify::send(Cms::inquiryEmail(), new InquiryReceived($inquiry));

        return back()->with('status', $thanks);
    }

    /** Custom pages created in the admin, e.g. /shipping-guide */
    public function page(string $slug): View
    {
        $page = Page::published()->where('kind', 'content')->where('slug', $slug)->firstOrFail();

        return view('site.page', compact('page'));
    }

    public function sitemap(): Response
    {
        $urls = [route('home'), route('track')];
        foreach (Cms::guard(fn () => Page::published()->get(), collect()) as $p) {
            $urls[] = $p->url();
        }
        foreach (Cms::guard(fn () => Service::active()->get(['slug']), collect()) as $s) {
            $urls[] = route('site.service', $s->slug);
        }
        foreach (Cms::guard(fn () => Post::live()->get(['slug']), collect()) as $p) {
            $urls[] = route('site.post', $p->slug);
        }
        $xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach (array_unique($urls) as $u) {
            $xml .= '<url><loc>' . e($u) . '</loc></url>';
        }

        return response($xml . '</urlset>', 200, ['Content-Type' => 'application/xml']);
    }
}
