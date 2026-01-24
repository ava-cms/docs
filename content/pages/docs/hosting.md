---
title: Hosting
slug: hosting
status: published
meta_title: Hosting Guide | Flat-file PHP CMS | Ava CMS
meta_description: Deploy Ava CMS anywhere PHP runs. Guide to shared hosting, VPS, local development, and deployment workflows for your flat-file site.
excerpt: Get Ava CMS live on the internet. Whether you're hosting your first website or you're a seasoned developer, this guide covers shared hosting, VPS, and local development options.
---

This guide walks you through getting Ava CMS live on the internet. Whether you're hosting your first website ever or you're a seasoned developer, there's an option that fits your needs.

<div class="callout-info">
<strong>Quick Start:</strong> Upload Ava CMS, point your web root to the <code>public/</code> directory, run <code>composer install</code>, and configure. Done!
</div>

## Before You Start

Ava CMS needs:

- **PHP 8.3 or later**
- **[Composer](https://getcomposer.org/)** (PHP's package manager)

That's it. No database, no special server software, no complex stack to configure.

<details class="beginner-box">
<summary>What's Composer?</summary>
<div class="beginner-box-content">

[Composer](https://getcomposer.org/) manages PHP dependencies (the libraries Ava uses). Most hosts have it pre-installed. You only need to run `composer install` once after uploading Ava — it downloads everything into the `vendor/` folder.

</div>
</details>

### Storage Requirements

Ava CMS itself is small. What consumes significant disk space:

- Uploaded media in `public/media/`
- Cache files in `storage/cache/`
- Logs in `storage/logs/`

Make sure PHP can write to the `storage/` directory. For large sites (10k+ items), the content index cache can grow to 100 MB or more depending on your content size and [index backend](/docs/configuration#content-index).



## Local Development

PHP includes a built-in server perfect for local development:

```bash
php -v                              # Check PHP is installed (need 8.3+)
cd /path/to/your/ava-site
composer install                    # Install dependencies (first time only)
php -S localhost:8000 -t public     # Start the dev server
```

Open `http://localhost:8000` in your browser. No Apache, Nginx, or LAMP stack required.

<details class="beginner-box">
<summary>Installing PHP and Composer</summary>
<div class="beginner-box-content">

**macOS:**
```bash
brew install php composer
```

**Linux (Ubuntu/Debian):**
```bash
sudo apt install php php-mbstring php-xml composer
```

**Windows:**

1. Download PHP from [windows.php.net/download](https://windows.php.net/download) (choose the **VS16 x64 Thread Safe** ZIP)
2. Extract to `C:\php` and add it to your PATH ([guide](https://www.php.net/manual/en/install.windows.manual.php))
3. Copy `php.ini-development` to `php.ini` and enable required extensions (uncomment `extension=mbstring`)
4. Download [Composer-Setup.exe](https://getcomposer.org/Composer-Setup.exe) and run the installer

Or use a package manager like [Scoop](https://scoop.sh/): `scoop install php composer`

**Verify installation:**
```bash
php -v        # Should show PHP 8.3+
composer -V   # Should show Composer version
```

</div>
</details>

<details class="beginner-box">
<summary>Windows: Running Ava CLI Commands</summary>
<div class="beginner-box-content">

On Windows, you can't run `./ava` directly. Use `php ava` instead:

```powershell
php ava status       # Check site status
php ava rebuild      # Rebuild content index
php ava lint         # Validate content files
```

All Ava CLI commands work this way—just prefix with `php`.

</div>
</details>



## Shared Hosting

Shared hosting is the easiest and most affordable option. The hosting company handles server maintenance while you get a control panel, file manager, and one-click SSL.

### What to Look For

- **PHP 8.3+** — Check before signing up
- **SSH access** — Optional but highly recommended for running commands
- **Enough RAM** — Ava works fine with 128M `memory_limit`, but larger sites may need more
- **SSD/NVMe storage** — Faster file reads/writes
- **Write permissions** — PHP must be able to write to `storage/`
- **Free SSL** — Look for Let's Encrypt support
- **Daily backups** — Your content is files, so backups matter

### Recommended Providers

| Provider | Starting Price | Notes |
|----------|----------------|-------|
| [Krystal Hosting](https://krystal.uk/web-hosting) | From £7/month | UK-based, SSH access, renewable energy |
| [Porkbun Easy PHP](https://porkbun.com/products/webhosting/managedPHP) | From $10/month | Simple setup, includes domain management |

Had a good experience elsewhere? Let us know in the [Discord](https://discord.gg/fZwW4jBVh5)!


### File Structure

Ava CMS has a simple flat structure:

```
/home/yourusername/
├── app/                  ← Themes, plugins, config
├── content/              ← Your Markdown content
├── core/                 ← Ava CMS core (don't edit)
├── public/               ← Web root (point your domain here)
│   ├── index.php
│   ├── assets/
│   ├── robots.txt
│   └── media/
├── storage/              ← Cache, logs (PHP needs write access)
├── vendor/               ← Composer dependencies
├── ava                   ← CLI tool
├── bootstrap.php
└── composer.json
```

**Setup steps:**

1. Upload Ava CMS to your home directory (e.g., `/home/yourusername/`).
2. Point your domain's document root to the `public/` folder.
3. Run `composer install --no-dev` and `./ava rebuild`.  
**If SSH isn't available**, generate the `vendor/` folder locally, upload it, then rebuild via the admin dashboard.

<div class="callout-info">
<strong>Can't change the document root?</strong> Some shared hosts lock you to <code>public_html/</code>. In that case, copy the contents of <code>public/</code> into <code>public_html/</code>, then edit <code>index.php</code> to update the <code>AVA_ROOT</code> path to point to where you uploaded the rest of Ava.
</div>

## Connecting to Your Server

You'll need to connect to your server to upload files and run commands. Both SSH and SFTP use the same credentials—look in your hosting control panel under "SSH Access" or "SFTP".

**Your credentials:**
- **Host:** Your domain or server IP
- **Username:** Your hosting account username
- **Password:** Your account password (or SSH key)
- **Port:** 22

### SSH (Running Commands)

SSH lets you run commands on your server. It's optional but highly recommended — it makes `./ava rebuild` and troubleshooting much easier.

<details class="beginner-box">
<summary>Don't Be Scared of the Terminal!</summary>
<div class="beginner-box-content">

SSH looks intimidating at first — a black screen with a blinking cursor. But it's just typing commands instead of clicking buttons. You only need a few commands:

- `cd folder-name` — Go into a folder
- `ls` — See what's in the current folder
- `./ava status` — Check if Ava is happy

That's 90% of what you'll do. See the [CLI Guide](/docs/cli) for all available commands.

</div>
</details>

| Platform | Client |
|----------|--------|
| **macOS/Linux** | Built-in Terminal |
| **Windows** | Windows Terminal, PowerShell, or [PuTTY](https://www.putty.org/) |

```bash
ssh username@your-domain.com
```

Once connected, you can run Ava CMS commands:

```bash
./ava status       # Check site health
./ava rebuild      # Rebuild content index
./ava cache:clear  # Clear webpage cache
```

<div class="callout-info">
<strong>Permission Denied?</strong> If you get a "permission denied" error when running <code>./ava</code>, the file needs execute permissions. Run <code>chmod +x ava</code> once to make it executable, then try again.
</div>

<details class="beginner-box">
<summary>Optional: Using SSH keys instead of passwords</summary>
<div class="beginner-box-content">

### SSH Keys

SSH keys are more secure and convenient than passwords. Generate a key pair once:

```bash
ssh-keygen -t ed25519 -C "your-email@example.com"
ssh-copy-id username@your-domain.com
```

Now you can connect without entering your password each time.

</div>
</details>

### SFTP (Uploading Files)

SFTP lets you upload files via a graphical interface. Use "SFTP" (not plain "FTP") in your client.

| Platform | Client |
|----------|--------|
| **macOS** | [Cyberduck](https://cyberduck.io/) (free), [Transmit](https://panic.com/transmit/) |
| **Windows** | [WinSCP](https://winscp.net/), [FileZilla](https://filezilla-project.org/) |
| **Linux** | [FileZilla](https://filezilla-project.org/), built-in file managers |

**Tip:** [VS Code's Remote - SSH extension](https://code.visualstudio.com/docs/remote/ssh) lets you edit files on your server directly—no separate SFTP client needed.

### Uploading Ava CMS

1. Upload all Ava CMS files to your server
2. Run `composer install --no-dev` (via SSH, or locally then upload the `vendor/` folder)
3. Run `./ava rebuild` (or visit your site — auto mode will rebuild on first load)

**No SSH?** Run `composer install --no-dev` on your computer, upload the `vendor/` folder, then visit your site.

### Troubleshooting: "composer: command not found"

```bash
# Install Composer locally to your project
curl -sS https://getcomposer.org/installer | php
php composer.phar install --no-dev
```



## VPS Hosting

A VPS (Virtual Private Server) gives you dedicated resources and full control. Consider this when shared hosting can't keep up, you need specific PHP extensions, or you want to host multiple sites.

### Recommended VPS Providers

| Provider | Starting Price | Notes |
|----------|----------------|-------|
| [Hetzner Cloud](https://www.hetzner.com/cloud) | From €4/month | Excellent value, EU-based |
| [Krystal Cloud VPS](https://krystal.io/cloud-vps) | From £10/month | UK-based, renewable energy |

**Don't want to manage a server?** Use [Ploi.io](https://ploi.io/)—it connects to your VPS and handles server setup, SSL, and deployments through a friendly dashboard.



## Deployment Workflows

### Manual (SFTP)

Upload changed files via SFTP, then run `./ava rebuild` if you have SSH.

### Git-Based

If you keep your site in a Git repository (your own customised Ava CMS installation—not a clone of the main Ava repo), you can deploy by pulling changes:

```bash
# On your server (pulling your own site repo)
git pull origin main && ./ava rebuild
```

<div class="callout-info">
<strong>Note:</strong> This assumes you started from a <a href="https://github.com/avacms/ava/releases">release</a> and committed your site to your own repository. Never clone the main Ava CMS repo directly for production—it may contain incomplete work.
</div>

### Automated (CI/CD)

Use GitHub Actions, Ploi, or Laravel Forge to deploy automatically when you push:

```yaml
name: Deploy
on: { push: { branches: [main] } }
jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.HOST }}
          username: ${{ secrets.USERNAME }}
          key: ${{ secrets.SSH_KEY }}
          script: git pull && composer install --no-dev && ./ava rebuild
```



## CDN (Optional)

Ava CMS's built-in page cache is already fast. A CDN helps most when you have a global audience or lots of large media files.

[Cloudflare](https://www.cloudflare.com/) (free tier) provides caching, DDoS protection, and free SSL. Just point your domain's nameservers to Cloudflare.

Other options: [BunnyCDN](https://bunny.net/) (pay-as-you-go), [KeyCDN](https://www.keycdn.com/) (simple).



<details>
<summary><strong>Server Configuration (Advanced)</strong></summary>

### PHP Settings

| Setting | Recommended | Purpose |
|---------|-------------|---------|
| `memory_limit` | `128M` | Increase for 10k+ items |
| `max_execution_time` | `30` | Increase if rebuilds timeout |
| `upload_max_filesize` | `10M` | For admin media uploads |

### Nginx

```nginx
server {
    listen 80;
    server_name example.com;
    root /home/yourusername/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\. { deny all; }
}
```

### Apache

Ava CMS includes a `.htaccess` file in `public/`. Just enable `mod_rewrite`:

```bash
sudo a2enmod rewrite && sudo systemctl restart apache2
```

</details>



## Need Help?

- [CLI Reference](/docs/cli) — All available commands
- [Configuration](/docs/configuration) — Site settings
- [Performance](/docs/performance) — Optimisation tips
- [Discord Community](https://discord.gg/fZwW4jBVh5) — Ask questions, get help

<div class="related-docs">
<h2>Related Documentation</h2>
<ul>
<li><a href="/docs/configuration">Configuration</a> — Site settings and paths</li>
<li><a href="/docs/performance">Performance</a> — Caching and optimization</li>
<li><a href="/docs/updating">Updating</a> — Keeping Ava CMS current</li>
<li><a href="/docs/cli">CLI Reference</a> — Deployment commands</li>
</ul>
</div>
