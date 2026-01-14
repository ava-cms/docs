---
title: Hosting
slug: hosting
status: published
meta_title: Hosting Guide | Flat-file PHP CMS | Ava CMS
meta_description: Deploy Ava CMS anywhere PHP runs. Guide to local development, shared hosting, VPS, cloud platforms, and server configuration for your flat-file site.
excerpt: Get Ava live on the internet. Whether you're hosting your first website or you're a seasoned developer, this guide covers local dev, shared hosting, VPS, and cloud options.
---

This guide walks you through getting Ava live on the internet. Whether you're hosting your first website ever or you're a seasoned developer, there's an option that fits your needs and budget.

## Before You Start

Ava needs two things:

1. **PHP 8.3 or later**
2. **Composer** (PHP's package manager)

That's it. No database, no special server software, no complex stack to configure.

## Disk Space & Write Permissions

On most hosts, Ava itself is small — the things that usually consume disk space are:

- Your uploaded media in `public/media/`
- Cache files in `storage/cache/` (content index + webpage cache)
- Logs in `storage/logs/`

As a rough reference point from Ava's own benchmarks: with **25,000 posts**, the content index cache was about **96 MB** (Array + igbinary) or **25 MB** (SQLite). Your real numbers will vary depending on how large your Markdown bodies are and how many content types you have.

Also make sure your web server/PHP user can write to `storage/` (especially `storage/cache/`), otherwise Ava can't rebuild indexes or write webpage cache files.

<details class="beginner-box">
<summary>What's Composer?</summary>
<div class="beginner-box-content">

### What's Composer?

[Composer](https://getcomposer.org/) manages PHP dependencies (the libraries Ava uses). Most hosts have it pre-installed. You only need to run `composer install` once after uploading Ava—it downloads everything into the `vendor/` folder.

</div>
</details>



## Local Development

You don't need a full web server to work on your Ava site locally. PHP includes a built-in development server that's perfect for previewing changes on your own computer.

<details class="beginner-box">
<summary>What is "Local Development"?</summary>
<div class="beginner-box-content">

### What is "Local Development"?

It means running your website on your own computer instead of a server on the internet. You can test changes privately before making them public.

**Important:** The built-in PHP server is for development only—it's not designed for public traffic. For a live website, you'll need real hosting (keep reading!).

</div>
</details>

### Running Ava Locally

First, check you have PHP installed:

```bash
php -v
```

If you see a version number (8.3 or higher), you're good. If not:

| Platform | How to Install PHP |
|----------|-------------------|
| **macOS** | `brew install php` (requires [Homebrew](https://brew.sh)) |
| **Windows** | Download from [windows.php.net](https://windows.php.net/download) or use [XAMPP](https://www.apachefriends.org/) |
| **Linux** | `sudo apt install php` (Debian/Ubuntu) or your distro's package manager |

Once PHP is installed, navigate to your Ava folder and start the server:

```bash
cd /path/to/your/ava-site
php -S localhost:8000 -t public
```

Open `http://localhost:8000` in your browser. You're running Ava!

<div class="callout-info">
<strong>Tip:</strong> You don't need Apache, Nginx, or LAMP/MAMP/WAMP for local development. PHP's built-in server handles everything.
</div>



## Shared Hosting

Shared hosting is the easiest and most affordable way to get Ava live. You get your own protected space on a server, but the hosting company handles all the technical stuff. This is a very common way for small to medium-sized websites to get online.

### What You Get

- **PHP pre-installed** — Usually just works
- **Control panel** — Manage files, domains, and settings through a web interface
- **File manager** — Upload files without extra software
- **One-click SSL** — Free HTTPS certificates (Let's Encrypt)

### What You Need

- **PHP 8.3+** — Check before signing up

If you can choose, also look for hosts that offer SSH access — it's optional, but it makes deployments, debugging, and maintenance much easier.

### What to Look For

Hosting companies describe plans in different ways, but for Ava you generally care about:

- **Modern PHP** — PHP 8.3+.
- **Enough RAM for rebuilds** — Many shared hosts enforce a PHP `memory_limit`. Ava often works fine at `128M`, but larger sites (10k+ items) may need more headroom, especially if you use the Array index backend.
- **Fast storage (SSD/NVMe)** — Ava reads and writes real files: Markdown content, cache files, and uploaded media. “SSD” or “NVMe” is a good sign.
- **Disk space that matches your media** — The biggest variable is almost always images/files in `public/media/`. If you plan to upload lots of photos, don’t pick a tiny storage quota.
- **Write permissions** — Your host must allow PHP to write to `storage/` (for index rebuilds, webpage cache, logs).
- **Easy HTTPS** — Look for free Let’s Encrypt support.
- **Backups** — Daily backups are ideal. With a flat-file CMS, backups matter because your content is literally files.

#### What hosts usually “sell” as

- **Shared hosting:** marketed as disk space + bandwidth (sometimes “unlimited”), plus a control panel. CPU/RAM are usually shared and not described clearly.
- **VPS hosting:** marketed as **vCPU**, **RAM**, and **SSD/NVMe storage**. This is where you can pick specific resources.

### Recommended Providers

| Provider | Starting Price | Notes |
|----------|----------------|-------|
| [Krystal Hosting](https://krystal.uk/web-hosting) | From £7/month | Premium UK host, vastly scalable, excellent support, 100% renewable energy |
| [Porkbun Easy PHP](https://porkbun.com/products/webhosting/managedPHP) | From $10/month | Simple, great domain provider, good for getting started in one place |

Both include SSH access and modern PHP versions.

Had a good experience with Ava on another host? Let us know in the [Discord community](https://discord.gg/fZwW4jBVh5) so we can update this list!

### File Structure on Shared Hosting

Shared hosts typically give you a structure like this:

```
/home/yourusername/
├── public_html/          ← Your "web root" (publicly accessible)
│   └── index.php         ← Ava's entry point goes here
│
├── ava/                  ← Put Ava here (ABOVE public_html)
│   ├── app/
│   ├── content/
│   ├── core/
│   ├── storage/
│   └── ...
│
└── logs/                 ← Server logs (usually auto-created)
```

**Key insight:** Only the `public/` folder contents should be web-accessible. Everything else stays above the web root for security—this means your config files, content, and storage are never directly downloadable by visitors.

### Setting Up on Shared Hosting

1. **Upload Ava** to a folder *above* your web root (e.g., `/home/you/ava/`)
2. **Move or symlink** the contents of `public/` into your web root (`public_html/`)
3. **Update paths** in `public/index.php` to point to your Ava installation
4. **Visit your site** — Ava will automatically build its index on first load

Alternatively, if your host lets you change the document root, point it directly at Ava's `public/` folder—cleaner and easier.



## SSH: Running Commands on Your Server

SSH (Secure Shell) lets you run commands on your server as if you were sitting in front of it.

It's **not required** for basic Ava usage (you can manage content via file uploads and the admin panel), but if your host offers it, SSH is strongly recommended — it makes deployments faster, lets you rebuild indexes on demand (`./ava rebuild`), and is invaluable for troubleshooting.

<details class="beginner-box">
<summary>Don't Be Scared of the Terminal!</summary>
<div class="beginner-box-content">

### Don't Be Scared of the Terminal!

SSH looks intimidating at first—a black screen with a blinking cursor. But it's just typing commands instead of clicking buttons. Once you get the hang of it, you'll wonder how you lived without it.

**You only need a few commands:**
- `cd folder-name` — Go into a folder
- `ls` — See what's in the current folder
- `./ava status` — Check if Ava is happy

That's honestly 90% of what you'll do. Even if you've never used a command line before, this is a perfect place to start—Ava's commands are friendly and helpful.

**Want the full reference?** See the [CLI Guide](/docs/cli) for all available commands.

</div>
</details>

### SSH Clients

You'll need an SSH client to connect. Here's what to use on each platform:

| Platform | Options |
|----------|---------|
| **macOS** | Built-in Terminal app (just works) |
| **Linux** | Built-in terminal (just works) |
| **Windows** | Windows Terminal, PowerShell, or [PuTTY](https://www.putty.org/) |

**Pro tip:** Many code editors have built-in SSH terminals. [VS Code's Remote - SSH extension](https://code.visualstudio.com/docs/remote/ssh) is particularly good—you can edit files and run commands in one place.

### Connecting via SSH

Open your terminal and type:

```bash
ssh username@your-domain.com
```

Replace:
- `username` with your hosting account username
- `your-domain.com` with your server address (your host will tell you both)

**First-time connection:** You'll see a message about the server's fingerprint. Type `yes` to continue. Then enter your password.

**Example session:**

```bash
$ ssh john@example.com
The authenticity of host 'example.com' can't be established.
Are you sure you want to continue connecting? yes
john@example.com's password: 
Welcome to Ubuntu 22.04 LTS
john@server:~$ 
```

You're now connected! The prompt shows you're logged into the server.

### Finding Your SSH Details

Your hosting provider will give you:
- **Username** — Often the same as your cPanel/control panel login
- **Server address** — Your domain, an IP address, or something like `ssh.example.com`
- **Port** — Usually 22 (the default), sometimes different for security

Look in your hosting control panel under "SSH Access", "Terminal", or "Shell Access". If you can't find it, ask your host's support.

### Using SSH Keys (More Secure)

Passwords are fine for getting started, but SSH keys are more secure and convenient. With keys, you don't need to type your password each time.

**Generate a key pair (do this once on your computer):**

```bash
ssh-keygen -t ed25519 -C "your-email@example.com"
```

Press Enter to accept the defaults. This creates two files:
- `~/.ssh/id_ed25519` — Your private key (keep this secret!)
- `~/.ssh/id_ed25519.pub` — Your public key (share this with servers)

**Add your public key to your server:**

```bash
ssh-copy-id username@your-domain.com
```

Or manually paste the contents of `~/.ssh/id_ed25519.pub` into `~/.ssh/authorized_keys` on your server.

Now you can connect without a password:

```bash
ssh username@your-domain.com   # No password prompt!
```

### Running Ava Commands via SSH

Once connected, navigate to your Ava installation and run commands:

```bash
cd ~/ava                # Go to your Ava folder
./ava status           # Check site health
./ava rebuild          # Rebuild content index
./ava cache:clear      # Clear webpage cache
```

<pre><samp><span class="t-cyan">   ▄▄▄  ▄▄ ▄▄  ▄▄▄     ▄▄▄▄ ▄▄   ▄▄  ▄▄▄▄
  ██▀██ ██▄██ ██▀██   ██▀▀▀ ██▀▄▀██ ███▄▄
  ██▀██  ▀█▀  ██▀██   ▀████ ██   ██ ▄▄██▀</span>

  <span class="t-dim">───</span> <span class="t-cyan t-bold">Site Status</span> <span class="t-dim">─────────────────────────────────────</span>

  <span class="t-cyan">▸ PHP Version</span>         8.3.14
  <span class="t-cyan">▸ Content Index</span>       Fresh (rebuilt 2 mins ago)
  <span class="t-cyan">▸ Content Items</span>       47 published, 3 drafts
  <span class="t-cyan">▸ Page Cache</span>          Enabled (23 cached pages)

  <span class="t-green">✓ Everything looks good!</span></samp></pre>

### Checking if Your Host Supports SSH

Look in your hosting control panel for:
- "SSH Access" or "Shell Access"
- "Terminal" or "Command Line"
- A section about "SSH Keys"

If you can't find it, ask support: "Do you offer SSH access?" Most quality hosts do.



## SFTP: Uploading Files

SFTP (Secure File Transfer Protocol) is the safe way to upload files to your server. Think of it as the modern, encrypted version of FTP.

### SFTP Clients

| Platform | Recommended Clients |
|----------|---------------------|
| **macOS** | [Cyberduck](https://cyberduck.io/) (free), [Transmit](https://panic.com/transmit/) (paid, excellent) |
| **Windows** | [WinSCP](https://winscp.net/) (free), [FileZilla](https://filezilla-project.org/) (free) |
| **Linux** | [FileZilla](https://filezilla-project.org/) (free), built-in file managers (Nautilus, Dolphin) |
| **Cross-platform** | [FileZilla](https://filezilla-project.org/), [Cyberduck](https://cyberduck.io/) |

**Pro tip:** Many code editors can connect to servers directly. VS Code with Remote - SSH lets you edit files on your server as if they were local.

### Connecting via SFTP

You'll need the same credentials as SSH:
- **Host:** Your domain or server address
- **Username:** Your hosting account username
- **Password:** Your hosting account password (or SSH key)
- **Port:** 22 (same as SSH)

In your SFTP client, choose "SFTP" (not plain "FTP") and enter these details.

### Uploading Ava

When uploading Ava for the first time:

1. **Connect** to your server via SFTP
2. **Navigate** to your home directory (e.g., `/home/yourusername/`)
3. **Create** a folder for Ava (e.g., `ava/`)
4. **Upload** all Ava files except:
   - `vendor/` (run `composer install` on the server instead)
   - `storage/cache/` (will be regenerated)
   - `.git/` (not needed in production)
5. **Install dependencies and build the index**:

  If you have SSH:

  ```bash
  cd ~/ava
  composer install --no-dev
  ./ava rebuild
  ```

  If you *don't* have SSH:
  - Run `composer install --no-dev` on your computer
  - Upload the resulting `vendor/` folder
  - Visit your site (in the default `auto` index mode, Ava will rebuild the content index on first load)

### Syncing Content Updates

For ongoing content updates:

1. **Upload** only the changed files (usually in `content/`)
2. If you have SSH, run `./ava rebuild`

If you don't have SSH, Ava can still pick up content changes automatically in the default `auto` index mode — the next visit will rebuild the index (which may take a few seconds on larger sites).

Or use a deployment tool (see [Deployment Workflows](#deployment-workflows) below).

### Troubleshooting: "composer: command not found"

If you get this error, Composer isn't installed on your server. Most quality shared hosts include Composer, but if yours doesn't:

**Option 1: Install Composer locally** (recommended for shared hosting)

```bash
# Download Composer to your Ava folder
curl -sS https://getcomposer.org/installer | php

# Now use it with php composer.phar instead of composer
php composer.phar install
```

This installs Composer just for your project—no server-wide installation needed.

**Option 2: Ask your host**

Contact support and ask: "Can you enable Composer for my account?" Many hosts can enable it on request.

**Option 3: Install dependencies locally**

Run `composer install` on your local computer, then upload the entire `vendor/` folder along with your Ava files. This works but makes updates slightly more manual.

<details class="beginner-box">
<summary>Stuck?</summary>
<div class="beginner-box-content">

### Stuck?

Join the [Discord community](https://discord.gg/fZwW4jBVh5)—we're happy to help you get set up, even if you're brand new to all this!

</div>
</details>



## VPS Hosting (Level Up)

A VPS (Virtual Private Server) gives you your own slice of a server. More control, more power, but more responsibility.

### When to Consider a VPS

- Your site is getting a lot of traffic that shared hosting can't handle
- You want to host multiple sites with different configurations
- You need specific PHP extensions or configurations for advanced functionality
- You just want to learn more about servers (this is a great way!)

### What You Get

- **Root access** — Full control over the server
- **Dedicated resources** — CPU and RAM just for you
- **Any software** — Install whatever you need

### What You'll Need to Learn

- Basic server administration (or use a management tool—see below)
- How to secure a server
- How to set up a web server (Nginx or Apache)

### Recommended VPS Providers

| Provider | Starting Price | Notes |
|----------|----------------|-------|
| [Hetzner Cloud](https://www.hetzner.com/cloud) | From €4/month | Excellent value, EU-based, great performance |
| [Krystal Cloud VPS](https://krystal.io/cloud-vps) | From £10/month | UK-based, renewable energy, managed options |

### Making VPS Easy: Server Management Panels

If managing a server sounds daunting, use a management tool:

**[Ploi.io](https://ploi.io/)** — Connects to your VPS and handles all the server setup for you. Deploy sites, manage SSL, run commands—all through a friendly dashboard. Perfect for developers who want VPS power without the sysadmin work.

With Ploi, setting up Ava on a VPS is almost as easy as shared hosting.



## Deployment Workflows

How you get files from your computer to your server is up to you. Here are common approaches:

### Manual Upload (SFTP)

Use an SFTP client to drag and drop files.

**Good for:** Quick changes, beginners, occasional updates

**Process:**
1. Connect via SFTP
2. Upload changed files
3. If you have SSH, run `./ava rebuild` (recommended)

If you don't have SSH, the default `auto` index mode can rebuild the content index on the next visit.

### Git-Based Deployment

Push to a Git repository, then pull on your server.

```bash
# On your server
cd ~/ava
git pull origin main
./ava rebuild
```

**Good for:** Version control, team collaboration, rollback capability

**Setup:**
1. Add your server as a Git remote or use GitHub/GitLab
2. Clone or pull on the server
3. Run rebuild after each pull

### Automated Deployment

Services like Ploi, Forge, or GitHub Actions can automatically deploy when you push code.

**Good for:** Frequent updates, CI/CD workflows, hands-off deployment

**Example GitHub Actions workflow:**

```yaml
name: Deploy
on:
  push:
    branches: [main]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - name: Deploy to server
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.HOST }}
          username: ${{ secrets.USERNAME }}
          key: ${{ secrets.SSH_KEY }}
          script: |
            cd ~/ava
            git pull origin main
            composer install --no-dev
            ./ava rebuild
```



## Scaling Up

Ava is already fast—cached pages serve almost instantly. But if you're getting serious traffic or want extra resilience, here are some options.

### Free CDN with Cloudflare

[Cloudflare](https://www.cloudflare.com/) sits between your visitors and your server, caching static files at data centers worldwide.

**Benefits:**
- Faster load times for visitors far from your server
- DDoS protection
- Free SSL certificate
- Analytics

**Setup:** Point your domain's nameservers to Cloudflare, configure caching rules, done. The free tier is plenty for most sites.

### Other CDNs

- **BunnyCDN** — Pay-as-you-go, very affordable
- **KeyCDN** — Simple, developer-friendly
- **Fastly** — More advanced, used by large sites

### Do You Need This?

Ava's built-in page cache is already blazing fast. A CDN helps most when:
- You're serving lots of large images or files
- Your audience is globally distributed
- You want DDoS protection

Start simple. Add complexity only when you need it.



## Server Configuration

### PHP Settings

Ava works with default PHP settings, but you may want to adjust these for large sites:

| Setting | Recommended | Purpose |
|---------|-------------|---------|
| `memory_limit` | `128M` | Sufficient for most sites; increase for 10k+ items |
| `max_execution_time` | `30` | Default is fine; increase if rebuilding times out |
| `upload_max_filesize` | `10M` | For admin media uploads |
| `post_max_size` | `10M` | Should match or exceed upload_max_filesize |

### Nginx Configuration

If you're running Nginx, here's a sample configuration:

```nginx
server {
    listen 80;
    server_name example.com;
    root /home/user/ava/public;
    index index.php;

    # Serve static files directly
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Pass PHP requests to PHP-FPM
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Deny access to sensitive files
    location ~ /\. {
        deny all;
    }
}
```

### Apache Configuration

For Apache, Ava includes a `.htaccess` file in the `public/` folder. Make sure `mod_rewrite` is enabled:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```



## Pre-Launch Checklist

Before going live, make sure:

- [ ] **PHP 8.3+** is running (`php -v`)
- [ ] **Required extensions** are enabled (`./ava status` will check)
- [ ] **Content index** is built (`./ava rebuild`)
- [ ] **Domain** points to the right folder
- [ ] **HTTPS enabled** (free with Let's Encrypt—required for admin access)
- [ ] **Webpage cache enabled** in config (for performance)
- [ ] **Debug mode disabled** (`display_errors => false` in config)
- [ ] **Admin user created** (`./ava user:add`)



## Need Help?

- [CLI Reference](/docs/cli) — All the commands you can run
- [Configuration](/docs/configuration) — Site settings and options
- [Performance](/docs/performance) — Optimisation tips
- [Discord Community](https://discord.gg/fZwW4jBVh5) — Ask questions, get help
