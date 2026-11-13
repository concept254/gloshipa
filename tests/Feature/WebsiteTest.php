<?php

namespace Tests\Feature;

use App\Mail\InquiryReceived;
use App\Models\{Faq, Inquiry, Page, Post, Service, Setting, Subscriber, User};
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class WebsiteTest extends TestCase
{
    use RefreshDatabase;

    private function seedContent(): void
    {
        $this->seed(ContentSeeder::class);
    }

    public function test_every_public_page_renders_empty_and_seeded(): void
    {
        $urls = ['/', '/about', '/services', '/faqs', '/testimonials', '/team', '/clients', '/news', '/contact', '/quote', '/sitemap.xml'];
        foreach ($urls as $u) {
            $this->get($u)->assertOk();
        }

        $this->seedContent();
        foreach (array_merge($urls, ['/services/sea-freight', '/terms-and-conditions', '/privacy-policy', '/company-profile', '/how-it-works', '/shipping-guide']) as $u) {
            $this->get($u)->assertOk();
        }
        $this->get('/services')->assertSee('Sea freight');
        $this->get('/')->assertSee('Our services')->assertSee('Request a quote');
    }

    public function test_seeder_is_safe_to_rerun_and_keeps_admin_edits(): void
    {
        $this->seedContent();
        Page::where('slug', 'about')->update(['title' => 'Edited title']);
        Faq::query()->delete();
        $this->seedContent();

        $this->assertSame('Edited title', Page::where('slug', 'about')->value('title'));
        $this->assertSame(0, Faq::count());
        $this->assertSame(8, Service::count());
    }

    public function test_unknown_pages_and_unpublished_pages_are_404(): void
    {
        $this->get('/nope')->assertNotFound();
        Page::create(['slug' => 'hidden', 'title' => 'Hidden', 'kind' => 'content', 'is_published' => false]);
        $this->get('/hidden')->assertNotFound();
        $this->get('/services/missing')->assertNotFound();
    }

    public function test_existing_routes_are_not_shadowed_by_the_catch_all(): void
    {
        $this->get('/login')->assertOk()->assertSee('Welcome back');
        $this->get('/register')->assertOk();
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_footer_whatsapp_and_back_to_top_appear_everywhere(): void
    {
        Setting::create(['key' => 'whatsapp_number', 'value' => '+27 82 123 4567']);
        $client = User::factory()->create();
        $admin = User::factory()->admin()->create();

        foreach ([['/', null], ['/login', null], ['/dashboard', $client], ['/admin', $admin]] as [$url, $user]) {
            $res = ($user ? $this->actingAs($user) : $this)->get($url)->assertOk();
            $res->assertSee('back-to-top', false)->assertSee('https://wa.me/27821234567', false)->assertSee('Client area');
        }
    }

    public function test_whatsapp_bubble_is_hidden_without_a_number(): void
    {
        $this->get('/')->assertDontSee('wa.me', false);
    }

    public function test_contact_form_stores_the_message_and_emails_the_team(): void
    {
        Mail::fake();
        $this->post('/contact', ['name' => 'Ann', 'email' => 'ann@example.test', 'message' => 'Hello there'])->assertSessionHas('status');

        $this->assertDatabaseHas('inquiries', ['email' => 'ann@example.test', 'type' => 'contact']);
        Mail::assertSent(InquiryReceived::class);
    }

    public function test_contact_form_validates_and_ignores_bots(): void
    {
        $this->post('/contact', ['name' => '', 'email' => 'bad'])->assertSessionHasErrors(['name', 'email', 'message']);
        $this->post('/contact', ['name' => 'Bot', 'email' => 'b@example.test', 'message' => 'x', 'website' => 'spam.example'])->assertSessionHas('status');
        $this->assertDatabaseCount('inquiries', 0);
    }

    public function test_quote_form_saves_the_details(): void
    {
        Mail::fake();
        $this->post('/quote', ['name' => 'Bo', 'email' => 'bo@example.test', 'origin' => 'Shanghai', 'destination' => 'Durban', 'cargo' => 'Tyres'])->assertSessionHas('status');
        $q = Inquiry::first();
        $this->assertSame('quote', $q->type);
        $this->assertSame('Shanghai', $q->meta['From']);
    }

    public function test_a_failing_mail_server_does_not_lose_the_enquiry(): void
    {
        config(['mail.default' => 'smtp', 'mail.mailers.smtp.host' => '127.0.0.1', 'mail.mailers.smtp.port' => 1]);
        $this->post('/contact', ['name' => 'Ann', 'email' => 'ann@example.test', 'message' => 'Hi'])->assertSessionHas('status');
        $this->assertDatabaseCount('inquiries', 1);
    }

    public function test_newsletter_subscribe_resubscribe_and_unsubscribe(): void
    {
        $this->post('/newsletter', ['email' => 'Reader@Example.test'])->assertSessionHas('status');
        $sub = Subscriber::firstOrFail();
        $this->assertSame('reader@example.test', $sub->email);

        $this->post('/newsletter', ['email' => 'reader@example.test']);
        $this->assertDatabaseCount('subscribers', 1);

        $this->get('/newsletter/unsubscribe/' . $sub->token)->assertRedirect('/');
        $this->assertNotNull($sub->fresh()->unsubscribed_at);

        $this->post('/newsletter', ['email' => 'reader@example.test']);
        $this->assertNull($sub->fresh()->unsubscribed_at);

        $this->post('/newsletter', ['email' => 'not-an-email'])->assertSessionHasErrors('email');
    }

    public function test_markdown_is_rendered_and_raw_html_is_stripped(): void
    {
        Page::create(['slug' => 'demo', 'title' => 'Demo', 'kind' => 'content', 'body' => "## Hello\n\n<script>alert(1)</script>\n\n[x](javascript:alert(1))"]);
        $this->get('/demo')->assertOk()->assertSee('<h2>Hello</h2>', false)->assertDontSee('<script>alert(1)', false)->assertDontSee('href="javascript:', false);
    }

    public function test_header_image_falls_back_to_a_bundled_stock_image(): void
    {
        $this->get('/about')->assertOk()->assertSee('/images/headers/header-', false);
    }

    // ---- admin ----

    public function test_only_the_admin_can_use_the_website_manager(): void
    {
        $client = User::factory()->create();
        $this->get('/admin/website')->assertRedirect('/login');
        $this->actingAs($client)->get('/admin/website')->assertForbidden();
        $this->actingAs($client)->post('/admin/website/pages', ['title' => 'X'])->assertForbidden();
        $this->actingAs($client)->put('/admin/website/settings', [])->assertForbidden();
    }

    public function test_admin_screens_render(): void
    {
        $this->seedContent();
        $admin = User::factory()->admin()->create();
        Inquiry::create(['name' => 'Ann', 'email' => 'a@example.test', 'message' => 'Hi', 'type' => 'contact']);
        $inquiry = Inquiry::first();

        $urls = ['/admin/website', '/admin/website/settings', '/admin/website/inbox', "/admin/website/inbox/{$inquiry->id}", '/admin/website/subscribers', '/admin/website/subscribers/export'];
        foreach (array_keys(config('cms.resources')) as $r) {
            $urls[] = "/admin/website/{$r}";
            $urls[] = "/admin/website/{$r}/create";
        }
        $urls[] = '/admin/website/pages/' . Page::first()->id . '/edit';
        $urls[] = '/admin/website/services/' . Service::first()->id . '/edit';

        foreach ($urls as $u) {
            $this->actingAs($admin)->get($u)->assertOk();
        }
        $this->assertNotNull($inquiry->fresh()->read_at);
    }

    public function test_admin_can_create_edit_and_delete_a_custom_page(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/website/pages', ['title' => 'Our Fleet', 'body' => 'Trucks', 'sort_order' => 5, 'is_published' => 1, 'show_in_footer' => 1])
            ->assertRedirect('/admin/website/pages');
        $page = Page::where('slug', 'our-fleet')->firstOrFail();
        $this->assertSame('content', $page->kind);
        $this->get('/our-fleet')->assertOk()->assertSee('Trucks');
        $this->get('/')->assertSee('Our Fleet'); // footer link

        $this->actingAs($admin)->put("/admin/website/pages/{$page->id}", ['title' => 'Fleet', 'slug' => 'fleet', 'sort_order' => 5])->assertRedirect();
        $this->get('/fleet')->assertOk();
        $this->get('/our-fleet')->assertNotFound();

        $this->actingAs($admin)->delete("/admin/website/pages/{$page->id}");
        $this->assertDatabaseMissing('pages', ['id' => $page->id]);
    }

    public function test_reserved_slugs_are_refused_and_slugs_stay_unique(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->post('/admin/website/pages', ['title' => 'X', 'slug' => 'login', 'sort_order' => 1])->assertSessionHasErrors('slug');
        // A title that slugifies to a reserved word is renamed, never allowed to shadow a route
        $this->actingAs($admin)->post('/admin/website/pages', ['title' => 'Login', 'sort_order' => 1]);
        $this->assertDatabaseHas('pages', ['slug' => 'login-2']);
        $this->get('/login')->assertOk()->assertSee('Welcome back');
    }

    public function test_built_in_pages_cannot_be_deleted_or_moved(): void
    {
        $this->seedContent();
        $admin = User::factory()->admin()->create();
        $about = Page::where('slug', 'about')->first();

        $this->actingAs($admin)->delete("/admin/website/pages/{$about->id}")->assertSessionHas('warning');
        $this->assertDatabaseHas('pages', ['slug' => 'about']);

        $this->actingAs($admin)->put("/admin/website/pages/{$about->id}", ['title' => 'About Glo Shipa', 'slug' => 'hacked', 'sort_order' => 10]);
        $about->refresh();
        $this->assertSame('about', $about->slug);
        $this->assertSame('About Glo Shipa', $about->title);
        $this->get('/about')->assertOk()->assertSee('About Glo Shipa');
    }

    public function test_admin_can_manage_services_faqs_and_posts(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $this->post('/admin/website/services', ['title' => 'Rail freight', 'icon' => 'truck', 'summary' => 'Trains', 'sort_order' => 1, 'is_active' => 1])->assertRedirect();
        $this->get('/services/rail-freight')->assertOk();

        $this->post('/admin/website/faqs', ['category' => 'General', 'question' => 'Why?', 'answer' => 'Because', 'sort_order' => 1, 'is_active' => 1]);
        $this->get('/faqs')->assertSee('Why?')->assertSee('Because');

        $this->post('/admin/website/posts', ['title' => 'Big news', 'excerpt' => 'Read it', 'is_published' => 1]);
        $this->get('/news/big-news')->assertOk();
        $this->assertNotNull(Post::first()->published_at);

        $this->post('/admin/website/services', ['title' => '', 'icon' => 'bogus'])->assertSessionHasErrors(['title', 'icon']);
    }

    public function test_hidden_items_do_not_appear_publicly(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->post('/admin/website/services', ['title' => 'Secret svc', 'icon' => 'box', 'sort_order' => 1]); // is_active unticked
        $this->get('/services')->assertDontSee('Secret svc');
        $this->get('/services/secret-svc')->assertNotFound();
    }

    public function test_image_upload_accepts_pictures_and_rejects_svg_and_scripts(): void
    {
        $dir = sys_get_temp_dir() . '/gs-uploads-' . uniqid();
        config(['cms.upload_path' => $dir, 'cms.upload_url' => 'https://cdn.example.test/up']);
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/website/pages', [
            'title' => 'With picture', 'sort_order' => 1, 'header_image' => UploadedFile::fake()->image('h.jpg', 1600, 600),
        ])->assertRedirect();
        $page = Page::where('slug', 'with-picture')->firstOrFail();
        $this->assertFileExists($dir . '/' . $page->header_image);
        $this->get('/with-picture')->assertSee('https://cdn.example.test/up/' . $page->header_image, false);

        $this->actingAs($admin)->post('/admin/website/pages', ['title' => 'Bad one', 'sort_order' => 1, 'header_image' => UploadedFile::fake()->createWithContent('x.svg', '<svg onload="alert(1)"/>')])
            ->assertSessionHasErrors('header_image');
        $this->actingAs($admin)->post('/admin/website/pages', ['title' => 'Bad two', 'sort_order' => 1, 'header_image' => UploadedFile::fake()->create('x.php', 5, 'application/x-php')])
            ->assertSessionHasErrors('header_image');

        $this->actingAs($admin)->put("/admin/website/pages/{$page->id}", ['title' => 'With picture', 'sort_order' => 1, 'remove_header_image' => 1]);
        $this->assertNull($page->fresh()->header_image);
        @array_map('unlink', glob($dir . '/pages/*'));
    }

    public function test_settings_change_the_footer_and_blank_values_fall_back(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->put('/admin/website/settings', ['company_phone' => '+27 11 555 0000', 'business_hours' => 'Mon-Fri 8-5'])->assertSessionHas('status');
        $this->get('/')->assertSee('+27 11 555 0000')->assertSee('Mon-Fri 8-5');

        $this->actingAs($admin)->put('/admin/website/settings', ['company_phone' => ''])->assertSessionHas('status');
        $this->get('/')->assertDontSee('+27 11 555 0000')->assertSee(config('gloshipa.contact.phone'));
    }

    public function test_map_embed_only_accepts_https_and_stats_are_hidden_until_set(): void
    {
        $this->get('/')->assertDontSee('Years in freight');
        Setting::create(['key' => 'map_embed_url', 'value' => 'javascript:alert(1)']);
        $this->get('/contact')->assertDontSee('javascript:alert', false)->assertSee('Open in Google Maps');
        Setting::where('key', 'map_embed_url')->update(['value' => 'https://www.google.com/maps/embed?pb=abc']);
        \App\Support\Cms::forgetSettings();
        $this->get('/contact')->assertSee('<iframe', false);

        Setting::create(['key' => 'stat_1_value', 'value' => '99%']);
        Setting::create(['key' => 'stat_1_label', 'value' => 'Happy clients']);
        \App\Support\Cms::forgetSettings();
        $this->get('/')->assertSee('Happy clients');
    }

    public function test_subscribers_can_be_exported_and_formula_cells_are_neutralised(): void
    {
        $admin = User::factory()->admin()->create();
        Subscriber::create(['email' => '=cmd@example.test', 'token' => 'abc', 'subscribed_at' => now()]);
        $csv = $this->actingAs($admin)->get('/admin/website/subscribers/export')->assertOk()->streamedContent();
        $this->assertStringContainsString("'=cmd@example.test", $csv);
    }

    public function test_the_site_keeps_working_if_the_new_tables_are_missing(): void
    {
        \Illuminate\Support\Facades\Schema::drop('pages');
        \Illuminate\Support\Facades\Schema::drop('settings');
        \Illuminate\Support\Facades\Schema::drop('services');
        \App\Support\Cms::forgetSettings();

        $this->get('/')->assertOk();
        $this->get('/login')->assertOk();
        $client = User::factory()->create();
        $this->actingAs($client)->get('/dashboard')->assertOk();
    }
}
