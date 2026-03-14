# Universal Media Downloader

Laravel application to download photos and videos from social media posts. Paste any post URL, extract all media, and download without registration or limits.

## Supported platforms

| Platform | Status | Notes |
|----------|--------|-------|
| **X / Twitter** | ✅ Supported | Uses public syndication API (no API key) |
| **TikTok** | ✅ Supported | TikWM API (no watermark) → yt-dlp fallback |
| **Instagram** | ✅ Supported | Posts, Reels, Stories, Highlights (session required for Stories) |
| **Reddit** | 🔜 Coming soon | — |
| **YouTube Shorts** | 🔜 Coming soon | — |

## Requirements

- **PHP** 8.2+
- **Composer** 2.x
- **Node.js** 18+ and **npm**
- **SQLite** (default) or MySQL/PostgreSQL
- **Optional:** [yt-dlp](https://github.com/yt-dlp/yt-dlp) — TikTok fallback when TikWM fails

### PHP extensions

- `curl`, `json`, `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `opcache`

## Installation

### 1. Clone and install dependencies

```bash
git clone https://github.com/your-username/universal-media-downloader.git
cd universal-media-downloader

composer install
npm install
```

### 2. Environment setup

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Database

```bash
php artisan migrate
```

### 4. Build assets

```bash
npm run build
```

### 5. Run the application

```bash
php artisan serve
```

Open [http://localhost:8000](http://localhost:8000).

---

## Configuration

### Basic (.env)

| Variable | Description |
|---------|-------------|
| `APP_NAME` | Application name (e.g. MediaGet) |
| `APP_URL` | Full URL (e.g. `http://localhost:8000`) |
| `APP_LOCALE` | Default locale (`en`, `es`, etc.) |

### Instagram (optional)

For **Stories** and **Highlights**, set `INSTAGRAM_SESSION_ID` in `.env`:

1. Open [instagram.com](https://www.instagram.com) in Chrome and log in.
2. Press **F12** → **Application** → **Cookies** → `instagram.com`.
3. Copy the value of the `sessionid` cookie.
4. Add to `.env`:
   ```
   INSTAGRAM_SESSION_ID=your_session_id_here
   ```

> The cookie expires periodically; update it when Stories stop downloading.

### OPcache Preload (production)

The app uses [Laragear Preload](https://github.com/Laragear/Preload) to generate an OPcache preload script from live usage. This speeds up the first request by keeping frequently-used PHP files in memory.

1. Set `PRELOAD_ENABLE=true` in `.env` (production only).
2. Add to your `php.ini` (Laragon: `bin/php/php-X.X.X/php.ini`):
   ```ini
   opcache.enable=1
   opcache.preload=C:/laragon/www/universal-media-downloader/preload.php
   ```
3. Restart PHP/Apache.

The preload script is generated automatically every 10,000 requests (via a queued job). Ensure the queue worker is running. See `php.ini.preload` for recommended OPcache settings.

### TikTok fallback (optional)

If TikWM fails, the app falls back to **yt-dlp** when installed:

```bash
# macOS (Homebrew)
brew install yt-dlp

# Windows (winget)
winget install yt-dlp
```

### Laravel Octane (Laravel Forge)

The app includes [Laravel Octane](https://laravel.com/docs/octane) with **RoadRunner**. For Forge deployment:

1. In **Forge** → Site → **Application** → enable **Laravel Octane**.
2. Set `OCTANE_SERVER=roadrunner` in your Forge environment variables.
3. Add this to your **Deploy Script** (before the default deploy commands) so the RoadRunner binary is available on first deploy:
   ```bash
   cd /home/forge/your-site-name
   ./vendor/bin/rr get-binary -n --ansi 2>/dev/null || true
   ```
4. Forge will create the Octane daemon and run `php artisan octane:reload` on each deploy.

---

## Development

### Run dev server (Vite + Laravel + queue)

```bash
composer run dev
```

Starts: Laravel server, Vite, and queue worker.

### Run individually

```bash
# Terminal 1
php artisan serve

# Terminal 2
npm run dev

# Terminal 3 (if using queues)
php artisan queue:listen --tries=1
```

### Code style

```bash
composer run lint        # Fix
composer run lint:check  # Check only
```

### Tests

```bash
composer run test
```

---

## Project structure

```
app/
├── Http/Controllers/DownloadController.php   # Proxy downloads (avoids CORS)
├── Livewire/MediaDownloader.php             # Main download form
├── Services/MediaExtractor/
│   ├── MediaExtractorService.php            # Dispatches to extractors
│   └── Extractors/
│       ├── TwitterExtractor.php
│       ├── TiktokExtractor.php
│       ├── InstagramExtractor.php
│       ├── RedditExtractor.php
│       └── YoutubeExtractor.php
resources/views/
├── home.blade.php                            # Public homepage
├── layouts/default/                          # Header, footer
└── livewire/media-downloader.blade.php       # Download UI
```

---

## Tech stack

- **Laravel** 12
- **Livewire** 4 + Flux UI
- **Tailwind CSS** 4 + Vite
- **SQLite** (default DB)
- **Laravel Octane** — RoadRunner for high-performance serving
- **Laragear Preload** — OPcache preloading

---

## License

MIT
