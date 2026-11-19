<?php

namespace Database\Seeders;

use App\Models\{Faq, Page, Partner, Post, Service, TeamMember, Testimonial};
use Illuminate\Database\Seeder;

/**
 * Starter website content. SAFE TO RE-RUN: it only creates what is missing and never
 * overwrites anything the admin has edited. It does not touch users or shipments.
 *
 * Live site:  php artisan db:seed --class=ContentSeeder --force
 *
 * The wording is generic freight-forwarding text. Replace it with Global Shipping Alliance's own
 * details in the admin (Website > Pages / Services / FAQs). Sample testimonials, team and
 * partners are created on a local machine only, never on the live server.
 */
class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->pages();
        $this->services();
        $this->faqs();

        if (app()->environment('local')) {
            $this->localSamples();
        }
    }

    private function pages(): void
    {
        // [slug, title, subtitle, kind, menu, footer, order, body]
        $rows = [
            ['about', 'About us', 'A freight partner you can follow every step of the way.', 'system', true, true, 10,
"Global Shipping Alliance is a freight forwarding and cargo tracking company. We help importers and exporters move goods by sea, air and road, and we keep every client informed from the port of origin to the final delivery address.

## What we believe

Moving cargo should not feel like a black box. Our clients get a personal account where they can see where each shipment is, what happens next and when it is expected. When something changes, we email you straight away.

## How we work

- **One point of contact.** We coordinate carriers, ports, customs and local delivery so you do not have to.
- **Clear communication.** Status and location updates are logged against every shipment and visible in your dashboard.
- **Honest advice.** We recommend the mode and route that fit your cargo, budget and deadline.

## Get started

Request a quote, or register for a client account to follow your shipments online."],
            ['company-profile', 'Company profile', 'Our mission, vision and values.', 'content', false, true, 20,
"## Mission

To make international freight simple, transparent and dependable for businesses of every size.

## Vision

To be the forwarder clients trust first, because they always know where their cargo is.

## Our values

- **Transparency:** you see what we see.
- **Reliability:** we do what we say, when we say it.
- **Care:** every container carries someone's business.
- **Compliance:** we respect customs, safety and trade regulations on every route.

## Company details

Registered name, registration number, VAT number and bank details can be added here by the administrator."],
            ['how-it-works', 'How it works', 'From first enquiry to delivery in four steps.', 'content', false, true, 30,
"## 1. Tell us what you are shipping

Send us the cargo description, origin, destination and your target dates through the quote form.

## 2. Receive your quote

We reply with the recommended service, transit time and pricing, and explain what is included.

## 3. We move your cargo

We book space, prepare the documents, clear customs and coordinate transport. Each step is recorded on your shipment timeline.

## 4. Track and receive

Sign in to your dashboard or use the tracking box on the home page with your tracking ID or container number. You get an email each time your shipment moves."],
            ['shipping-guide', 'Shipping guide', 'Documents, terms and tips every shipper should know.', 'content', false, true, 40,
"## Documents you will usually need

- Commercial invoice
- Packing list
- Bill of lading (sea) or air waybill (air)
- Import or export permits where applicable
- Certificate of origin when claiming preferential duty

## Common shipping terms (Incoterms)

| Term | Who arranges main carriage | Typical use |
| --- | --- | --- |
| EXW | Buyer | Buyer collects at the seller's premises |
| FOB | Buyer | Seller loads onto the vessel at the origin port |
| CIF | Seller | Seller pays freight and insurance to the destination port |
| DAP | Seller | Seller delivers to the named place, buyer clears import |

Always confirm the exact term and the Incoterms edition in your contract.

## Packing tips

1. Use strong, clean packaging suited to the journey.
2. Label every package clearly on more than one side.
3. Do not exceed the weight limits of the container or pallet.
4. Insure goods of value."],
            ['terms-and-conditions', 'Terms and conditions', 'The terms that apply when you use our services and this website.', 'content', false, true, 90,
"*Starter text. Have it reviewed by a legal adviser and replace company details before relying on it.*

## 1. About these terms

These terms apply to all quotes, services and use of the Global Shipping Alliance website. By requesting a quote, creating an account or using our services you agree to them.

## 2. Quotes and bookings

Quotes are estimates based on the information you give us and are valid for the period stated on the quote. Rates can change if the cargo, route, carrier charges, exchange rates or fuel surcharges change.

## 3. Your responsibilities

You must give accurate and complete information about the cargo, including its nature, weight, value and any dangerous goods. You are responsible for the legality of the goods and for the correctness of customs documents you supply.

## 4. Transit times

Estimated delivery dates are not guaranteed. Delays can result from weather, port congestion, customs inspections, strikes and other events outside our control.

## 5. Liability and insurance

Our liability is limited as set out in our written trading conditions and applicable law. We recommend that you insure your cargo. Claims must be notified in writing within the period stated in those conditions.

## 6. Payment

Invoices are payable by the due date on the invoice. We may hold cargo until amounts due are paid, as the law allows.

## 7. Client accounts

Accounts are activated after approval. You are responsible for keeping your password secret and for activity under your account. We may suspend accounts that are misused.

## 8. Website use

The tracking information shown on this website is provided for convenience and may be delayed. Do not rely on it as a legal record.

## 9. Changes

We may update these terms. The version on this page is the current one.

## 10. Contact

Questions about these terms: use the contact page."],
            ['privacy-policy', 'Privacy policy', 'How we collect, use and protect your personal information.', 'content', false, true, 91,
"*Starter text. Have it reviewed by a legal adviser (for example against POPIA in South Africa) before relying on it.*

## What we collect

- **Account details:** name, username, email, phone, company and address.
- **Shipment details:** cargo, routes and status updates linked to your account.
- **Messages and quote requests** you send through our forms.
- **Newsletter email address,** if you subscribe.

## How we use it

To provide and track shipments, to communicate with you about your cargo, to respond to enquiries and, if you subscribed, to send news. We do not sell your personal information.

## Who sees it

Our staff, and carriers, agents and authorities where needed to move or clear your cargo, and service providers who host the website or send email for us.

## Your choices

You can ask to see, correct or delete your information, and you can unsubscribe from the newsletter at any time using the link in our emails.

## Cookies

The site uses essential cookies to keep you signed in and to protect forms.

## Contact

Use the contact page for any privacy request."],
            // Built-in pages: their title, intro and header image can be edited. They have their own layout.
            ['services', 'Our services', 'Sea, air and road freight, customs and warehousing under one roof.', 'system', true, false, 20, null],
            ['faqs', 'Frequently asked questions', 'Quick answers about shipping, documents, tracking and your account.', 'system', false, false, 50, null],
            ['testimonials', 'Client testimonials', 'What importers and exporters say about working with us.', 'system', false, false, 51, null],
            ['team', 'Meet the team', 'The people who look after your cargo.', 'system', false, false, 52, null],
            ['clients', 'Clients & partners', 'The businesses and carriers we work with.', 'system', false, false, 53, null],
            ['news', 'News & updates', 'Port updates, industry news and shipping tips.', 'system', true, false, 60, null],
            ['contact', 'Contact us', 'Questions about a shipment or a quote? We reply fast.', 'system', true, false, 70, null],
            ['quote', 'Request a quote', 'Tell us about your cargo and route.', 'system', false, false, 71, null],
        ];

        foreach ($rows as [$slug, $title, $subtitle, $kind, $menu, $footer, $order, $body]) {
            Page::firstOrCreate(['slug' => $slug], [
                'title' => $title, 'subtitle' => $subtitle, 'kind' => $kind, 'body' => $body,
                'show_in_menu' => $menu, 'show_in_footer' => $footer, 'sort_order' => $order, 'is_published' => true,
            ]);
        }
    }

    private function services(): void
    {
        $rows = [
            ['sea-freight', 'Sea freight', 'ship', 'Full container (FCL) and shared container (LCL) shipping on major trade lanes.',
"## Full and shared containers

Move large volumes in a dedicated 20ft or 40ft container (FCL), or share container space and pay only for the volume you use (LCL).

## What is included

- Booking with the shipping line
- Bill of lading and documentation
- Port handling and customs coordination
- Delivery to your door, or collection from the port

## Good to know

Sea freight is the most economical option for heavy or bulky cargo when time is not critical."],
            ['air-freight', 'Air freight', 'plane', 'Fast, reliable air cargo for urgent and high-value shipments.',
"## When speed matters

Air freight gets time-sensitive, perishable or high-value goods across the world in days instead of weeks.

## What is included

- Airline space booking and air waybill
- Airport handling and security screening coordination
- Customs clearance support
- Door-to-door options

## Good to know

Pricing is based on the greater of actual or volumetric weight."],
            ['road-freight', 'Road freight', 'truck', 'Local, cross-border and regional trucking with clear tracking.',
"## Reliable overland transport

From port to warehouse to your customer's door, and across borders in the region.

## What is included

- Full and part truck loads
- Port collections and deliveries
- Cross-border documentation support
- Delivery updates logged on your shipment timeline"],
            ['customs-clearance', 'Customs clearance', 'doc', 'Import and export clearance handled correctly and on time.',
"## Avoid delays at the border

Correct classification and paperwork keep your cargo moving. We help you prepare and submit the documents customs requires.

## What is included

- Tariff classification guidance
- Duty and VAT calculations
- Import and export declarations
- Permits and certificates coordination"],
            ['warehousing', 'Warehousing & distribution', 'warehouse', 'Secure storage, pick and pack, and onward delivery.',
"## Store, consolidate and distribute

Hold stock close to the port, consolidate smaller consignments, and release goods to your customers as needed.

## What is included

- Secure, monitored storage
- Receiving, put-away and stock checks
- Pick, pack and dispatch
- Cross-docking for fast turnaround"],
            ['project-cargo', 'Project & heavy cargo', 'anchor', 'Planning and moving oversized, heavy or out-of-gauge equipment.',
"## Specialist moves

Machinery, plant and project equipment need careful route planning, special equipment and permits.

## What is included

- Route and equipment planning
- Breakbulk and out-of-gauge shipping
- Lifting, lashing and securing guidance
- Escort and permit coordination"],
            ['cargo-insurance', 'Cargo insurance', 'shield', 'Protect the value of your goods while they are in transit.',
"## Peace of mind

Carrier liability is usually limited. Cargo insurance covers the value of your goods against loss or damage during transit.

## What to tell us

Cargo value, packaging, route and dates so we can arrange suitable cover."],
            ['door-to-door', 'Door-to-door delivery', 'globe', 'One partner for the whole journey, from supplier to final address.',
"## One contract, one contact

We combine origin collection, main carriage, clearance and final delivery into a single service with one point of contact and one tracking ID.

## Why clients choose it

- Fewer hand-offs and fewer surprises
- Clear status at every stage
- One invoice"],
        ];

        foreach ($rows as $i => [$slug, $title, $icon, $summary, $body]) {
            Service::firstOrCreate(['slug' => $slug], ['title' => $title, 'icon' => $icon, 'summary' => $summary, 'body' => $body, 'sort_order' => ($i + 1) * 10, 'is_active' => true]);
        }
    }

    private function faqs(): void
    {
        if (Faq::query()->exists()) {
            return; // never re-add questions the admin deleted
        }

        $rows = [
            ['Tracking', 'How do I track my shipment?', 'Sign in to your account and enter your tracking ID or container number in the tracking box. You will see the current status, location and the full timeline.'],
            ['Tracking', 'Where do I find my tracking ID?', 'We email it to you when your shipment is added to your account. It is also listed on your dashboard.'],
            ['Tracking', 'Why has my shipment status not changed?', 'Ocean shipments can spend several days between ports. If you are worried, contact us and we will check with the carrier.'],
            ['Account', 'Why can I not sign in after registering?', 'New accounts are reviewed by our team and activated after approval. You will get an email as soon as yours is approved.'],
            ['Account', 'Can I see shipments from other people?', 'No. You can only see shipments that are assigned to your own account.'],
            ['Shipping', 'How long does shipping take?', 'It depends on the route and service. Air freight usually takes days, sea freight takes weeks. Your quote shows the expected transit time.'],
            ['Shipping', 'What documents do I need?', 'Usually a commercial invoice, packing list and bill of lading or air waybill. Some goods need permits or certificates. See our shipping guide.'],
            ['Shipping', 'Can you ship dangerous or restricted goods?', 'Some can be shipped with special handling and paperwork. Tell us about the goods when you request a quote.'],
            ['Pricing', 'How do I get a quote?', 'Use the quote form with your cargo, origin and destination. We will reply with pricing and transit time.'],
            ['Pricing', 'Is cargo insurance included?', 'Not by default. We recommend insuring your goods and can arrange cover for you.'],
        ];
        foreach ($rows as $i => [$cat, $q, $a]) {
            Faq::create(['category' => $cat, 'question' => $q, 'answer' => $a, 'sort_order' => ($i + 1) * 10]);
        }
    }

    /** Local only: clearly labelled sample data so the layout can be previewed. */
    private function localSamples(): void
    {
        if (! Testimonial::query()->exists()) {
            foreach ([['Sample Client One', 'Operations Manager', 'Sample Imports'], ['Sample Client Two', 'Director', 'Sample Trading'], ['Sample Client Three', 'Buyer', 'Sample Retail']] as $i => [$n, $r, $c]) {
                Testimonial::create(['name' => $n, 'role' => $r, 'company' => $c, 'quote' => 'Sample testimonial text for previewing the layout. Replace with a real client review.', 'sort_order' => $i]);
            }
        }
        if (! TeamMember::query()->exists()) {
            foreach (['Sample Person A' => 'Managing Director', 'Sample Person B' => 'Operations Lead', 'Sample Person C' => 'Customer Care', 'Sample Person D' => 'Customs Specialist'] as $n => $r) {
                TeamMember::create(['name' => $n, 'role' => $r, 'bio' => 'Sample biography for previewing the layout.']);
            }
        }
        if (! Partner::query()->exists()) {
            foreach (['Sample Line', 'Sample Air', 'Sample Port', 'Sample Rail', 'Sample Bank'] as $i => $n) {
                Partner::create(['name' => $n, 'sort_order' => $i]);
            }
        }
        if (! Post::query()->exists()) {
            foreach (['Welcome to our new website', 'Five tips for smoother customs clearance', 'Understanding container sizes'] as $i => $t) {
                Post::create(['slug' => \Illuminate\Support\Str::slug($t), 'title' => $t, 'excerpt' => 'Sample article for previewing the layout.', 'body' => "Sample article body.\n\n## A heading\n\nMore sample text.", 'published_at' => now()->subDays($i * 7)]);
            }
        }
    }
}
