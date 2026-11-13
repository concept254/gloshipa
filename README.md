# Glo Shipa

Shipment tracking for gloshipa.com. Built with Laravel 13 and Tailwind CSS 4.

**What it does**

- **Website (Phase 2):** a multi-page site (About, Services, FAQs, Team, Clients, News, Contact, Quote, Terms ...) with an admin content manager. See [UPGRADE-WEBSITE.md](UPGRADE-WEBSITE.md).
- **Home page** with a hero, a tracking form and an illustrated ship image.
- **Clients** register, wait for approval, then sign in with their username or email. They get a dashboard (their details, pending shipments), can edit their profile and password, and can track a shipment by tracking ID or container number. A client can only ever see their own shipments.
- **Super admin** approves or switches off clients, creates shipments, assigns each one to a client, and logs status and location updates on a timeline.
- **Emails** go out when a client registers, when the admin approves them, when a shipment is assigned (or reassigned), and when a shipment moves location or status (the admin decides per update).
- **Custom theme**: Tailwind's default palette is removed completely. Only the Glo Shipa colours (navy, orange, teal, sand) exist. See `resources/css/app.css`.

Requirements: PHP 8.3 or newer, Composer, Node.js 22 or newer.

---

## 1. Run it on your computer with Laravel Herd

[Laravel Herd](https://herd.laravel.com) is a free app that serves PHP sites on your machine. It works on macOS and on Windows. On Windows, use the normal Herd for Windows app. It runs natively, not inside WSL, so keep the project in your Windows user folder as shown below.

### Before you start

1. Install Herd and open it once. It adds `php`, `composer` and the `herd` command to your terminal. **Close and reopen your terminal** after installing.
2. Check the versions:

   ```bash
   php -v        # must say 8.3 or higher
   composer -V
   node -v       # 22 or higher (install from nodejs.org if missing or older)
   ```

   If PHP is older than 8.3, switch it in Herd's settings (PHP section).

### Steps

**macOS (Terminal)**

```bash
cd ~/Herd
unzip ~/Downloads/gloshipa-project.zip
cd gloshipa
composer setup
php artisan db:seed
```

**Windows (PowerShell)**

```powershell
cd $HOME\Herd
Expand-Archive $HOME\Downloads\gloshipa-project.zip -DestinationPath .
cd gloshipa
composer setup
php artisan db:seed
```

What those commands do:

- `~/Herd` (or `C:\Users\you\Herd`) is the folder Herd serves by default. Every folder in it becomes a site at `foldername.test`, so this one becomes **http://gloshipa.test**. If you keep your sites somewhere else, add that folder in Herd under Sites, or run `herd link gloshipa` inside the project folder.
- `composer setup` installs PHP packages, copies `.env.example` to `.env`, creates the app key, creates the database tables (a SQLite file, so no database server is needed) and builds the CSS.
- `php artisan db:seed` creates the admin login and, on your computer only, a demo client with three shipments and one client waiting for approval.

Open **http://gloshipa.test** in your browser.

### Logins created by the seeder

| Who | Username | Password |
| --- | --- | --- |
| Super admin | `admin` | `ChangeMe123!` (or the `ADMIN_PASSWORD` you put in `.env` before seeding) |
| Demo client | `demo` | `password` |
| Waiting for approval | `newclient` | `password` (cannot sign in until the admin approves it) |

Demo tracking IDs to try: `GSDEMO0001`, `GSDEMO0002`, `GSDEMO0003`. Container numbers: `MSKU1234567`, `TCLU7654321`, `HLCU1122334`.

### Try the whole flow

1. Go to the home page and enter `GSDEMO0001` in the hero form. You are sent to the sign-in page first (use `demo` and `password`), then straight to the result.
2. Register a new client at `/register`. You will see the "waiting for approval" message and cannot sign in.
3. Sign in as `admin`. The **Clients** tab shows a badge. Open it and approve the new client.
4. Create a shipment (**Shipments > New shipment**), choose the client, and save. The client is emailed.
5. Open the shipment and use **Add an update**: set a new location, leave "Email the client" ticked, and save. The client gets a "Your shipment has moved" email showing the old and new location.
6. Sign in as that client and see the shipment on the dashboard. Search for it by tracking ID and by container number.

### Where do emails go locally?

By default they are written to **`storage/logs/laravel.log`** (`MAIL_MAILER=log` in `.env`). Open the file and search for `Subject:`. Nothing is sent to real addresses while you test.

If you have Herd Pro, its Mail tool can catch them in a proper inbox: in `.env` set `MAIL_MAILER=smtp`, `MAIL_HOST=127.0.0.1` and the port shown in Herd's Mail settings (2525 by default), then run `php artisan config:clear`.

### Everyday commands

```bash
npm run dev          # rebuild the CSS live while you edit views and styles
npm run build        # one-off production build of the CSS
composer test        # run the automated tests (they use a throwaway in-memory database)
php artisan migrate:fresh --seed    # wipe the local database and start again
```

---

## 2. Make it yours

| What | Where |
| --- | --- |
| Phone, email and address in the footer and in every email | `COMPANY_EMAIL`, `COMPANY_PHONE`, `COMPANY_ADDRESS` in `.env` |
| Where "new registration" alerts are sent | `ADMIN_EMAIL` in `.env` |
| Colours and fonts | `@theme` block in `resources/css/app.css`, then `npm run build` |
| Hero image | Put your photo in `public/images/` and change the `asset('images/hero-ship.svg')` line in `resources/views/home.blade.php` |
| Website text, pages, services, FAQs, contact details | Sign in as admin > **Website** (see UPGRADE-WEBSITE.md) |
| Home page layout | `resources/views/home.blade.php` |
| Logo | `resources/views/components/logo.blade.php` |
| Shipment statuses | `STATUSES` in `app/Models/Shipment.php` (also add a colour in `resources/views/components/status-badge.blade.php`) |
| Timezone | `APP_TIMEZONE` in `.env` (default `Africa/Johannesburg`) |

### Map of the code

```
routes/web.php                              every URL, in one file
app/Http/Controllers/Auth/                  sign in, register, password reset
app/Http/Controllers/Admin/                 shipments, timeline updates, client approval
app/Http/Controllers/DashboardController.php, TrackingController.php, ProfileController.php   client side
app/Http/Middleware/                        EnsureAccountIsActive (approval gate), EnsureUserIsAdmin
app/Mail/ + resources/views/emails/         the five emails
app/Support/Notify.php                      sends mail without ever breaking the page if SMTP is down
resources/views/                            all screens, layouts and components
tests/Feature/                              automated tests
```

How access is enforced: `users.status` is `pending`, `active` or `suspended`, and only `active` accounts can sign in. `is_admin` and `status` cannot be set from any form. Clients query shipments through `$user->shipments()`, so someone else's tracking ID simply returns "not found".

---

## 3. Put it live on cPanel (gloshipa.com)

**Using GitHub?** Read [DEPLOY.md](DEPLOY.md) instead: it pushes the project to GitHub and deploys it to cPanel with one button, with no manual zipping. The steps below are the manual method and are also the fallback.

Laravel 13 needs **PHP 8.3 or higher** on the server, and you need **cPanel Terminal or SSH** for two commands. (No shell at all? See step 7.)

### 1. Prepare cPanel

- **MultiPHP Manager**: set gloshipa.com to PHP 8.3 or newer.
- **Select PHP Version / extensions**: make sure `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `ctype`, `fileinfo` and `bcmath` are ticked.
- **MySQL Database Wizard**: create a database and a user with all privileges. Note both full names (cPanel adds your account name in front).
- **Email Accounts**: create `noreply@gloshipa.com` and note its password.
- **SSL/TLS Status**: confirm AutoSSL has issued a certificate for gloshipa.com.

### 2. Build a production copy on your computer

Work on a **copy** so your development folder keeps its dev tools:

```bash
cp -r gloshipa gloshipa-deploy        # Windows: copy the folder in Explorer
cd gloshipa-deploy
composer config platform.php 8.3.0    # stops Composer choosing packages newer than the server's PHP
composer update --no-dev --optimize-autoloader
npm run build
```

In `gloshipa-deploy`, delete these, then zip the folder: `node_modules`, `.git` (if present), `.env`, `database/database.sqlite`.

### 3. Upload

In cPanel File Manager, upload the zip to your home folder (`/home/CPANELUSER/`) and extract it into a new folder `/home/CPANELUSER/gloshipa`. This folder is **outside** `public_html` on purpose, so your code and `.env` are never web-accessible.

### 4. Point the domain at the `public` folder

Pick one:

- **Best, if your host allows it:** cPanel > Domains, and set the document root for gloshipa.com to `gloshipa/public`.
- **Symlink (Terminal):**

  ```bash
  cd /home/CPANELUSER
  mv public_html public_html_old
  ln -s /home/CPANELUSER/gloshipa/public /home/CPANELUSER/public_html
  ```

- **Copy (File Manager only):** copy everything inside `gloshipa/public` into `public_html`, including the hidden `.htaccess`. Then edit `public_html/index.php`: in the three lines that mention `/../storage/framework/maintenance.php`, `/../vendor/autoload.php` and `/../bootstrap/app.php`, change `/../` to `/../gloshipa/` (for example `__DIR__.'/../gloshipa/vendor/autoload.php'`). You must repeat the copy whenever the CSS changes.

### 5. Create the `.env`

Copy `deploy/env.production.example` to `/home/CPANELUSER/gloshipa/.env` (File Manager: upload it, then rename) and replace every `CHANGE-ME`. Set a long, unique `ADMIN_PASSWORD`.

### 6. Create the tables, the admin and the key (Terminal or SSH)

On many hosts the plain `php` command is an older version than your website uses. Use the PHP 8.3 binary explicitly (list what you have with `ls /opt/cpanel | grep ea-php`):

```bash
cd ~/gloshipa
PHP=/opt/cpanel/ea-php83/root/usr/bin/php
$PHP artisan key:generate --force
$PHP artisan migrate --force
$PHP artisan db:seed --force      # creates only the admin; demo data is local-only
$PHP artisan config:cache
$PHP artisan route:cache
$PHP artisan view:cache
```

Set permissions: folders `755`, files `644`, and `storage` plus `bootstrap/cache` writable (`775`, recursively).

### 7. No shell at all?

Run steps 5 and 6 on your computer against a local **MySQL** database instead (Herd Pro, DBngin or XAMPP all work): point a temporary `.env` at it (with `APP_ENV=production`, so no demo data is created, and your real `ADMIN_PASSWORD`), run `php artisan key:generate` and `php artisan migrate --seed --force`, export it from phpMyAdmin, and import the file into your cPanel database. Then paste the app key from your temporary `.env` into the server `.env`. A SQLite file cannot be imported into MySQL this way.

### 8. Check it, then secure it

- Visit https://gloshipa.com. A blank page or error 500 means: check `storage/logs/laravel.log`, then the PHP version, then folder permissions.
- Sign in as `admin` and **change the password** from the Profile page.
- Register a throwaway client, approve it, assign a shipment and confirm the emails arrive. If the admin screen shows a yellow "email could not be sent" warning, the cause is in `storage/logs/laravel.log`, usually the SMTP host, port or password.
- cPanel > **Email Deliverability**: fix any SPF or DKIM warnings for gloshipa.com, otherwise your notifications are likely to land in spam.
- After editing `.env` on the server, run `php artisan config:cache` again so the change is picked up.

### Updating the live site later

Rebuild on your computer (`npm run build`), upload the changed files, then on the server run `migrate --force` if you added migrations, and `config:cache`, `route:cache`, `view:cache`.

---

## Troubleshooting

| Problem | Fix |
| --- | --- |
| `Vite manifest not found` | Run `npm run build` (or `npm run dev`). |
| gloshipa.test does not open | Restart Herd, confirm the folder is inside the Herd sites folder, and check Herd shows the site. |
| `composer setup` says PHP is too old | Switch Herd to PHP 8.3 or newer and reopen the terminal. |
| A new client cannot sign in | They are `pending`. The admin approves them under **Clients**. |
| Times are two hours off | Check `APP_TIMEZONE` in `.env`, then `php artisan config:clear`. |
| Changed `.env` and nothing happened | `php artisan config:clear` locally, `php artisan config:cache` on the server. |
