# Putting Global Shipping Alliance on GitHub and deploying it to cPanel

How it works: you push your code to GitHub. A GitHub Actions workflow then runs the tests, installs the production PHP packages, builds the CSS, and uploads the finished site to your cPanel hosting over FTPS. **Your host does not need Composer or Node.js**, and your `.env` and database are never touched by a deployment.

Two workflows live in `.github/workflows/`:

| File | When it runs | What it does |
| --- | --- | --- |
| `tests.yml` | Every push to `main` and every pull request | Runs the automated tests on PHP 8.3 and 8.4. Never touches the server. |
| `deploy.yml` | Only when you press **Run workflow** | Tests, builds, then uploads to cPanel. A failing test stops the upload. |

---

## Part 1. Put the project on GitHub (once, from Windows)

You need [Git for Windows](https://git-scm.com/download/win). Check with `git --version`. If PowerShell says it is not recognised, install it and reopen PowerShell.

If Git asks who you are, set it once:

```powershell
git config --global user.name "Your Name"
git config --global user.email "you@example.com"
```

Then, in PowerShell:

```powershell
cd C:\Users\User\Herd\gloshipa

# Remove the Laravel-team maintenance workflows that came with the starter project.
# They auto-close pull requests and auto-merge dependency bots, which you do not want.
Remove-Item .github\workflows\issues.yml, .github\workflows\pull-requests.yml, .github\workflows\update-changelog.yml, .github\workflows\dependabot-auto-merge.yml, .github\dependabot.yml -ErrorAction SilentlyContinue

# Re-resolve packages so the lock file is safe for a PHP 8.3 server (this project now pins PHP 8.3 in composer.json).
$env:COMPOSER_PROCESS_TIMEOUT = "0"
composer update
composer test

git init -b main
git add .
git status
```

**Read the `git status` list before you commit.** It must **not** contain `.env`, `vendor/`, `node_modules/`, `public/build/` or `database/database.sqlite`. It **should** contain `composer.lock` and `package-lock.json` (both belong in Git). If you see `.env`, stop and do not continue.

```powershell
git commit -m "Initial commit: Global Shipping Alliance"
git remote add origin https://github.com/concept254/gloshipa.git
git push -u origin main
```

The first push opens a browser window so Git Credential Manager can sign you in to GitHub.

**If the push is rejected** because GitHub already created a README or licence in the new repo, merge it in and push again:

```powershell
git pull origin main --allow-unrelated-histories --no-edit -X ours
git push -u origin main
```

**Make the repository private** (Settings > General > Danger Zone > Change visibility). The code holds no passwords, but it is your client's business application.

Open the **Actions** tab on GitHub. The **Tests** workflow starts by itself. A green tick means every test passed on PHP 8.3 and 8.4. If anything is red, open the failed step and send me the error text.

---

## Part 2. One-time setup on cPanel

1. **PHP version:** cPanel > MultiPHP Manager, set gloshipa.com to **PHP 8.3 or higher**. Under Select PHP Version, make sure `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `ctype`, `fileinfo` and `bcmath` are ticked.
2. **Database:** MySQL Database Wizard. Create a database and a user with all privileges. Write down both full names (cPanel adds your account name in front).
3. **Email:** Email Accounts. Create `noreply@gloshipa.com` and note the password.
4. **FTP account for deployments:** cPanel > FTP Accounts > Add FTP Account.
   - Log In: `deploy` (cPanel completes it as something like `deploy@gloshipa.com`).
   - **Directory: `gloshipa`** (replace the suggested `public_html/deploy`). This makes cPanel create `/home/YOURUSER/gloshipa`, outside the public web folder, and locks this account into it.
   - Then open **Configure FTP Client** for that account and note the **FTP server name** and port 21.
5. **Point the domain at the app's `public` folder** (after the first deployment has uploaded it): cPanel > Domains > set gloshipa.com's document root to `gloshipa/public`. If your host will not let you change it, use the symlink method in `README.md` (section 3, step 4). Do **not** copy `public` into `public_html` by hand when using this pipeline, because deployments would then stop updating your CSS.

---

## Part 3. Give GitHub the keys

GitHub repository > **Settings > Secrets and variables > Actions > New repository secret**. Add these four:

| Secret name | Value |
| --- | --- |
| `FTP_HOST` | The FTP server name from step 4 (for example `ftp.gloshipa.com`, or your server's hostname if the certificate for `ftp.` does not match) |
| `FTP_USERNAME` | The full FTP login, for example `deploy@gloshipa.com` |
| `FTP_PASSWORD` | That account's password |
| `FTP_SERVER_DIR` | `./` (the FTP account is already locked into the `gloshipa` folder) |

If the connection fails with a certificate or TLS error, try the server's own hostname as `FTP_HOST`. Avoid switching to plain FTP, which sends your password unencrypted.

---

## Part 4. First deployment

1. GitHub > **Actions** > **Deploy to cPanel** > **Run workflow**.
2. Tick **Dry run** the first time. It tests, builds and connects, and lists what it would upload without sending anything. If that is green, run it again with Dry run unticked.
3. **The first real upload is slow** (often 20 to 60 minutes) because it sends thousands of small files in `vendor/`. After that, only changed files are sent and a deployment takes a minute or two.

When the upload finishes, finish the server setup in **cPanel > Terminal** (or SSH). The plain `php` command is often older than your website's PHP, so use the PHP 8.3 binary explicitly (list what you have with `ls /opt/cpanel | grep ea-php`):

```bash
cd ~/gloshipa
cp deploy/env.production.example .env
nano .env        # replace every CHANGE-ME: database, mail password, ADMIN_EMAIL, ADMIN_PASSWORD

PHP=/opt/cpanel/ea-php83/root/usr/bin/php
$PHP artisan key:generate --force      # ONCE only: changing the key later signs everyone out
$PHP artisan migrate --force
$PHP artisan db:seed --force           # creates only the admin account; demo data is local-only
$PHP artisan optimize
```

Make sure `storage` and `bootstrap/cache` are writable (permissions `775`, recursively).

Visit https://gloshipa.com. Sign in as `admin` with the `ADMIN_PASSWORD` you set, and **change that password** on the Profile page.

No Terminal or SSH on your hosting? Then the one-off database step must be done by importing a database from your computer. See `README.md`, section 3, step 7.

---

## Part 5. Every day after that

```powershell
# on your computer, after changing code
composer test
git add .
git commit -m "Describe what you changed"
git push
```

Wait for the green tick on **Tests**, then run **Deploy to cPanel**. After a deployment that includes new migrations or configuration changes, run this in cPanel Terminal:

```bash
cd ~/gloshipa
PHP=/opt/cpanel/ea-php83/root/usr/bin/php
$PHP artisan migrate --force
$PHP artisan optimize:clear
$PHP artisan optimize
```

(`optimize:clear` followed by `optimize` is what makes edited settings and routes take effect on the live site.)

---

## Troubleshooting

| Problem | Likely cause and fix |
| --- | --- |
| Deploy step: `Login authentication failed` | Wrong `FTP_USERNAME` or `FTP_PASSWORD`. The username must be the full `deploy@yourdomain` form. |
| Deploy step: certificate or TLS error | Use the server hostname from **Configure FTP Client** as `FTP_HOST`. |
| Deploy step: timeout or `ECONNREFUSED` | Your host may block FTP from GitHub. Ask them to allow it, or upload the zip by hand using `README.md` section 3. |
| Site shows error 500 after a deploy | Read `storage/logs/laravel.log` on the server. Usually a missing `.env` value, a PHP version below 8.3, or unwritable `storage`. |
| Site shows old behaviour after a deploy | Run `optimize:clear` then `optimize` (see Part 5). |
| Changed `.env` on the server and nothing happened | Same fix: `optimize:clear`, then `optimize`. |
| `composer update` fails locally | Set `$env:COMPOSER_PROCESS_TIMEOUT = "0"` and run it again. |
| The Tests workflow is red | Open the failed step and send me the error text. |

## Rolling back

Find the last good commit on GitHub, then on your computer:

```powershell
git revert <bad-commit-id>
git push
```

Run the **Deploy to cPanel** workflow again. If a bad migration changed the database, restore the database from a cPanel backup first.
