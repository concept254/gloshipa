# Website upgrade (Phase 2): safe upgrade of the live site

This adds the multi-page website and the admin content manager. **It only adds things.** It creates ten new
database tables and new files. It never changes your users, shipments, tracking, logins or emails.

## What you get

- Public pages: Home (new sections), About us (with company profile, team, testimonials, clients, FAQs), Services (and one page per service), FAQs, Testimonials, Team, Clients & partners, News (and articles), Contact (address, form, map), Request a quote, Terms and conditions, Privacy policy, Company profile, How it works, Shipping guide.
- A footer with columns on every screen: public pages, sign-in screens, client dashboard and admin.
- WhatsApp chat bubble and back-to-top button on every screen.
- Inner pages have a header image with breadcrumbs. With no picture set, one of six bundled images is picked at random (and an online photo is the last fallback).
- Newsletter sign-up (stored in the database, downloadable as CSV).
- **Admin > Website**: edit every page, service, FAQ, testimonial, team member, client logo, news article; settings (phone, address, WhatsApp number, map, social links, home page text and figures); inbox for contact and quote messages; subscribers.
- Sections with no content (testimonials, team, clients, news, figures) simply stay hidden on the home page until you add some.

> The starter wording (services, FAQs, About, Terms, Privacy) is generic freight-forwarding text. The old gloshipa.com archive could not be read from here, so **the client must review and replace the text with their own facts**. Terms and Privacy especially should be checked by a legal adviser. No fake testimonials, team or clients are added to the live site.

---

## Step 1. Back up (2 minutes)

On the host, open phpMyAdmin, select the Global Shipping Alliance database, **Export > Quick > Go**. Keep the .sql file.

## Step 2. Try it on your computer first (Herd)

Copy the new files over your local project (same folder structure), then:

```bash
composer install            # nothing new is required, this is just a safety check
npm install
npm run build
php artisan migrate         # creates the new tables only
php artisan db:seed --class=ContentSeeder
composer test
```

Open http://gloshipa.test and click through: home, About, Services, Contact, then sign in as `admin` and open **Website**.
(Locally the seeder also adds clearly labelled "Sample" testimonials, team, clients and articles so you can see the layout. These are never added on the live server.)

## Step 3. Put it on the live site

```bash
git add .
git commit -m "Website pages and admin content manager"
git push
```

Wait for the green tick on **Tests**, then run **Deploy to cPanel** as before. (Your host appears to be DirectAdmin, which is fine: the same steps apply.)

Then, **straight away**, on the server (Terminal/SSH), from the project folder:

```bash
PHP=/opt/cpanel/ea-php83/root/usr/bin/php      # use the PHP 8.3+ binary your host gives you
$PHP artisan migrate --force
$PHP artisan db:seed --class=ContentSeeder --force
$PHP artisan optimize:clear
$PHP artisan optimize
```

> **Never run plain `php artisan db:seed` on the live site.** It also resets the `admin` password to the one in `.env`. Always use `--class=ContentSeeder`.
> `ContentSeeder` is safe to run again at any time: it only adds what is missing and never overwrites what the client edited.

**No terminal on the host?** Run the two commands on your computer against a copy of the live database, or ask me for a plain SQL file of the new tables and the starter content that you can import in phpMyAdmin.

Between uploading the code and running `migrate`, the existing site, tracking and logins keep working. Only the new pages and the Website admin area need the new tables.

## Step 4. Uploads folder

Pictures the admin uploads are saved in `public/uploads/`. Make sure that folder is writable (permissions 755 or 775).

If you copy the contents of `public/` into the host's web folder (instead of pointing the domain at `public/`), set these in the server `.env` so uploads land in a folder the web server serves:

```
CMS_UPLOAD_PATH=/full/path/to/the/web/folder/uploads
CMS_UPLOAD_URL=https://gloshipa.com/uploads
```

Then run `optimize:clear` and `optimize` again. The deployment never deletes uploaded pictures.

## Step 5. Fill in the details (Admin > Website)

1. **Settings**: phone, email, address, business hours, **WhatsApp number** (digits with country code, for example 27821234567; empty hides the bubble), Google Maps embed link, social links, home page text and the four headline figures.
2. **Pages**: rewrite About us, Company profile, Terms and Privacy with the real text. Tick "Show in menu" or "Show in footer" to control the links. Add as many extra pages as you like with **Add page**.
3. **Services, FAQs, Testimonials, Team, Clients & partners, News**: add the real entries and pictures.
4. Submit the Contact and Quote forms once to confirm the email reaches `ADMIN_EMAIL` (or the address set in Settings). Messages are always saved in **Website > Inbox** even if email fails.

**Map:** Google Maps > find the address > Share > Embed a map > copy only the link inside `src="..."`.

## Everyday edits for a developer

- A field on a content type: add one line in `config/cms.php` and a column in a new migration. The admin form, validation and saving follow automatically.
- A new content type: add a block in `config/cms.php`, a model and a migration.
- Page layout: `resources/views/site/`. Footer: `resources/views/components/site-footer.blade.php`. Home page: `resources/views/home.blade.php`.
- Header images: `public/images/headers/header-1.svg` ... `header-6.svg`. Replace them with photos if the client prefers.
- After changing CSS or JS run `npm run build`.

## Rolling back

- **Code only:** `git revert` the commit and deploy again. The new tables stay in the database but nothing uses them. The old site works as before.
- **Remove the new tables too:** `php artisan migrate:rollback --step=1` (this deletes the website content, enquiries and subscribers, not users or shipments).
- **Worst case:** import the phpMyAdmin backup from Step 1.
