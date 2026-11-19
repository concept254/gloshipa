<?php

use App\Models;

/*
|--------------------------------------------------------------------------
| Website content registry
|--------------------------------------------------------------------------
| Everything the admin can edit under "Website" is described here. To add a
| field to a content type, add one line to its 'fields' list and a column in a
| new migration. To add a whole new content type, add a block below, a model
| and a table: the admin screens appear automatically.
|
| Field types: text, textarea, markdown, url, number, date, select, checkbox, image
*/

return [

    // Uploaded pictures are stored here. If your host serves the site from a folder other
    // than <project>/public (for example you copy public/ into public_html), set
    // CMS_UPLOAD_PATH to the absolute folder and CMS_UPLOAD_URL to its public URL.
    'upload_path' => env('CMS_UPLOAD_PATH'),
    'upload_url' => env('CMS_UPLOAD_URL'),

    // Pages that have their own built-in layout. Admin edits their title, intro and header image.
    'system_routes' => [
        'about' => 'site.about',
        'services' => 'site.services',
        'faqs' => 'site.faqs',
        'testimonials' => 'site.testimonials',
        'team' => 'site.team',
        'clients' => 'site.clients',
        'news' => 'site.news',
        'contact' => 'site.contact',
        'quote' => 'site.quote',
    ],

    // Slugs a custom page may never use (they would clash with real routes).
    'reserved_slugs' => [
        'admin', 'login', 'logout', 'register', 'forgot-password', 'reset-password', 'dashboard', 'shipments',
        'track', 'profile', 'newsletter', 'sitemap-xml', 'storage', 'build', 'images', 'uploads', 'api', 'up',
        'about', 'services', 'faqs', 'testimonials', 'team', 'clients', 'news', 'contact', 'quote',
    ],

    // Used when a page has no header image of its own: one of these is picked at random.
    'header_images' => ['header-1.svg', 'header-2.svg', 'header-3.svg', 'header-4.svg', 'header-5.svg', 'header-6.svg'],

    'icons' => ['ship', 'plane', 'truck', 'box', 'warehouse', 'doc', 'shield', 'globe', 'clock', 'anchor'],

    'resources' => [

        'pages' => [
            'model' => Models\Page::class,
            'label' => 'Pages',
            'singular' => 'page',
            'help' => 'Every page of the site. Tick "Show in menu" to put a page in the top menu, "Show in footer" for the footer links.',
            'slug_from' => 'title',
            'order' => ['sort_order', 'asc'],
            'columns' => [['title', 'Title'], ['slug', 'Address'], ['is_published', 'Published', 'bool'], ['show_in_menu', 'Menu', 'bool'], ['show_in_footer', 'Footer', 'bool']],
            'fields' => [
                ['title', 'Title', 'text', 'required|string|max:160'],
                ['slug', 'Web address (slug)', 'text', 'nullable|alpha_dash|max:120', 'hint' => 'Leave empty to create it from the title. It cannot be changed on built-in pages.'],
                ['subtitle', 'Subtitle under the title', 'text', 'nullable|string|max:400'],
                ['body', 'Content', 'markdown', 'nullable|string|max:100000'],
                ['header_image', 'Header image', 'image', 'nullable', 'hint' => 'Wide picture behind the page title. If empty, a stock header is picked at random.'],
                ['meta_description', 'Search engine description', 'text', 'nullable|string|max:300'],
                ['is_published', 'Published', 'checkbox', 'boolean', 'default' => true],
                ['show_in_menu', 'Show in top menu', 'checkbox', 'boolean'],
                ['show_in_footer', 'Show in footer', 'checkbox', 'boolean'],
                ['sort_order', 'Order (lowest first)', 'number', 'required|integer|min:0|max:9999', 'default' => 100],
            ],
        ],

        'services' => [
            'model' => Models\Service::class,
            'label' => 'Services',
            'singular' => 'service',
            'slug_from' => 'title',
            'order' => ['sort_order', 'asc'],
            'columns' => [['title', 'Service'], ['icon', 'Icon'], ['is_active', 'Visible', 'bool'], ['sort_order', 'Order']],
            'fields' => [
                ['title', 'Title', 'text', 'required|string|max:160'],
                ['slug', 'Web address (slug)', 'text', 'nullable|alpha_dash|max:120'],
                ['summary', 'Short summary (shown on cards)', 'text', 'nullable|string|max:400'],
                ['body', 'Full description', 'markdown', 'nullable|string|max:100000'],
                ['icon', 'Icon', 'select', 'required|in:ship,plane,truck,box,warehouse,doc,shield,globe,clock,anchor', 'options' => 'icons'],
                ['image', 'Picture', 'image', 'nullable'],
                ['is_active', 'Visible on the site', 'checkbox', 'boolean', 'default' => true],
                ['sort_order', 'Order (lowest first)', 'number', 'required|integer|min:0|max:9999', 'default' => 100],
            ],
        ],

        'faqs' => [
            'model' => Models\Faq::class,
            'label' => 'FAQs',
            'singular' => 'question',
            'order' => ['sort_order', 'asc'],
            'columns' => [['question', 'Question'], ['category', 'Group'], ['is_active', 'Visible', 'bool'], ['sort_order', 'Order']],
            'fields' => [
                ['category', 'Group', 'text', 'required|string|max:80', 'default' => 'General', 'hint' => 'Questions with the same group are listed together.'],
                ['question', 'Question', 'text', 'required|string|max:255'],
                ['answer', 'Answer', 'textarea', 'required|string|max:5000'],
                ['is_active', 'Visible on the site', 'checkbox', 'boolean', 'default' => true],
                ['sort_order', 'Order (lowest first)', 'number', 'required|integer|min:0|max:9999', 'default' => 100],
            ],
        ],

        'testimonials' => [
            'model' => Models\Testimonial::class,
            'label' => 'Testimonials',
            'singular' => 'testimonial',
            'order' => ['sort_order', 'asc'],
            'columns' => [['name', 'Name'], ['company', 'Company'], ['rating', 'Stars'], ['is_active', 'Visible', 'bool']],
            'fields' => [
                ['name', 'Name', 'text', 'required|string|max:120'],
                ['role', 'Job title', 'text', 'nullable|string|max:120'],
                ['company', 'Company', 'text', 'nullable|string|max:120'],
                ['quote', 'What they said', 'textarea', 'required|string|max:1500'],
                ['rating', 'Stars (1 to 5)', 'number', 'required|integer|min:1|max:5', 'default' => 5],
                ['photo', 'Photo', 'image', 'nullable'],
                ['is_active', 'Visible on the site', 'checkbox', 'boolean', 'default' => true],
                ['sort_order', 'Order (lowest first)', 'number', 'required|integer|min:0|max:9999', 'default' => 100],
            ],
        ],

        'team' => [
            'model' => Models\TeamMember::class,
            'label' => 'Team',
            'singular' => 'team member',
            'order' => ['sort_order', 'asc'],
            'columns' => [['name', 'Name'], ['role', 'Role'], ['is_active', 'Visible', 'bool'], ['sort_order', 'Order']],
            'fields' => [
                ['name', 'Name', 'text', 'required|string|max:120'],
                ['role', 'Role', 'text', 'required|string|max:120'],
                ['bio', 'Short bio', 'textarea', 'nullable|string|max:1500'],
                ['photo', 'Photo', 'image', 'nullable'],
                ['linkedin_url', 'LinkedIn link', 'url', 'nullable|url|max:255'],
                ['is_active', 'Visible on the site', 'checkbox', 'boolean', 'default' => true],
                ['sort_order', 'Order (lowest first)', 'number', 'required|integer|min:0|max:9999', 'default' => 100],
            ],
        ],

        'partners' => [
            'model' => Models\Partner::class,
            'label' => 'Clients & partners',
            'singular' => 'client or partner',
            'order' => ['sort_order', 'asc'],
            'columns' => [['name', 'Name'], ['url', 'Website'], ['is_active', 'Visible', 'bool'], ['sort_order', 'Order']],
            'fields' => [
                ['name', 'Name', 'text', 'required|string|max:120'],
                ['logo', 'Logo', 'image', 'nullable', 'hint' => 'Without a logo the name is shown as text.'],
                ['url', 'Website', 'url', 'nullable|url|max:255'],
                ['is_active', 'Visible on the site', 'checkbox', 'boolean', 'default' => true],
                ['sort_order', 'Order (lowest first)', 'number', 'required|integer|min:0|max:9999', 'default' => 100],
            ],
        ],

        'posts' => [
            'model' => Models\Post::class,
            'label' => 'News',
            'singular' => 'article',
            'slug_from' => 'title',
            'order' => ['published_at', 'desc'],
            'columns' => [['title', 'Title'], ['published_at', 'Date', 'date'], ['is_published', 'Published', 'bool']],
            'fields' => [
                ['title', 'Title', 'text', 'required|string|max:200'],
                ['slug', 'Web address (slug)', 'text', 'nullable|alpha_dash|max:120'],
                ['excerpt', 'Short summary', 'text', 'nullable|string|max:400'],
                ['body', 'Article', 'markdown', 'nullable|string|max:100000'],
                ['image', 'Picture', 'image', 'nullable'],
                ['published_at', 'Publish date', 'date', 'nullable|date'],
                ['is_published', 'Published', 'checkbox', 'boolean', 'default' => true],
            ],
        ],
    ],

    /*
    | Site settings: key => [label, type, hint]. Shown on Website > Settings, grouped.
    | A value left empty falls back to the default noted in code (or to .env for contact details).
    */
    'settings' => [
        'Company' => [
            ['company_tagline', 'Tagline (footer)', 'text', 'Clear, reliable tracking for the cargo you move, from the port of origin to your door.'],
            ['footer_about', 'Short company blurb (footer)', 'textarea', 'We move freight by sea, air and road, and keep you informed at every step.'],
            ['company_phone', 'Phone', 'text', 'Defaults to COMPANY_PHONE in .env'],
            ['company_email', 'Email', 'text', 'Defaults to COMPANY_EMAIL in .env'],
            ['company_address', 'Address', 'textarea', 'Defaults to COMPANY_ADDRESS in .env'],
            ['business_hours', 'Business hours', 'text', 'Mon to Fri, 08:00 to 17:00'],
        ],
        'Contact & chat' => [
            ['whatsapp_number', 'WhatsApp number', 'text', 'International format, digits only, e.g. 27821234567. Leave empty to hide the chat bubble.'],
            ['whatsapp_message', 'WhatsApp opening message', 'text', 'Hello Global Shipping Alliance, I would like some help with a shipment.'],
            ['map_embed_url', 'Google Maps embed link', 'text', 'In Google Maps: Share > Embed a map > copy only the address inside src="..."'],
            ['inquiry_email', 'Send contact and quote messages to', 'text', 'Defaults to ADMIN_EMAIL in .env'],
        ],
        'Social links' => [
            ['social_facebook', 'Facebook', 'text', 'https://facebook.com/...'],
            ['social_instagram', 'Instagram', 'text', 'https://instagram.com/...'],
            ['social_linkedin', 'LinkedIn', 'text', 'https://linkedin.com/company/...'],
            ['social_x', 'X (Twitter)', 'text', 'https://x.com/...'],
            ['social_youtube', 'YouTube', 'text', 'https://youtube.com/...'],
        ],
        'Home page' => [
            ['hero_eyebrow', 'Hero small heading', 'text', 'Freight forwarding & cargo tracking'],
            ['hero_title', 'Hero title', 'text', 'Know exactly where your cargo is.'],
            ['hero_text', 'Hero text', 'textarea', 'Follow every container from port to doorstep. Sign in to see the live status, location and delivery estimate of every shipment Global Shipping Alliance is handling for you.'],
            ['stat_1_value', 'Figure 1 number', 'text', 'e.g. 15+  (figures stay hidden until you fill them in)'],
            ['stat_1_label', 'Figure 1 label', 'text', 'Years in freight'],
            ['stat_2_value', 'Figure 2 number', 'text', 'e.g. 40+'],
            ['stat_2_label', 'Figure 2 label', 'text', 'Countries served'],
            ['stat_3_value', 'Figure 3 number', 'text', 'e.g. 5,000+'],
            ['stat_3_label', 'Figure 3 label', 'text', 'Containers moved'],
            ['stat_4_value', 'Figure 4 number', 'text', 'e.g. 98%'],
            ['stat_4_label', 'Figure 4 label', 'text', 'On-time delivery'],
        ],
    ],
];
