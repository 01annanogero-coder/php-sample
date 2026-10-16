# Aeline: PHP website with an AI chatbot

This is a small demo of how to add an AI chatbot to a website **without putting the API key in the frontend**.

```
index.php            ← the website (HTML/CSS). No secrets in here.
assets/style.css
assets/chat.js       ← chat widget. It only calls backend/chat.php
backend/             ← the backend: the only place that knows the API key
  chat.php           ← receives the chat, calls Claude, returns the reply
  config.php         ← loads the key from .env, plus model and limits
  .env.example       ← copy to .env and add your key
  .htaccess          ← (Apache) blocks browser access to everything except chat.php
  composer.json      ← installs the official Anthropic PHP SDK
```

## How it works

```
Browser (chat.js) ──POST /backend/chat.php──▶ PHP backend ──(API key)──▶ Claude API
        ◀──────────── { "reply": "..." } ◀──────────────┘
```

The browser never sees the key. Anyone who opens DevTools only sees requests to your own `backend/chat.php`.

## Setup

Requirements: PHP 8.1+ (with the `curl` and `mbstring` extensions) and [Composer](https://getcomposer.org/).

1. Install the SDK:
   ```bash
   cd backend
   composer install
   ```
2. Add your API key. Get one at https://console.anthropic.com/:
   ```bash
   cp .env.example .env
   ```
   Then edit `backend/.env` and set `ANTHROPIC_API_KEY=sk-ant-...`.
   (Or set `ANTHROPIC_API_KEY` as a real environment variable on your host.)
3. Run it locally from the project root:
   ```bash
   php -S localhost:8000
   ```
   Open http://localhost:8000 and click the chat bubble in the bottom-right corner.

## Customising

- **What the bot knows and how it talks:** edit `system_prompt` in `backend/config.php`.
- **Model / length:** `model` and `max_tokens` in `backend/config.php`.
- **Abuse limits:** `rate_limit`, `rate_window`, `max_chars`, `max_history`.

## Going to production

- **Better still: keep secrets outside the web root.** Put `.env` (or the whole `backend` folder except `chat.php`) one level above `public_html`. The included `.htaccess` only works on Apache; on **Nginx**, add a rule that denies `/backend/` except `chat.php`.
- Never commit `backend/.env`. It's already in `.gitignore`.
- The session-based rate limit is basic. For real traffic, add a CAPTCHA, a login, or a rate limit backed by Redis or your database, so nobody can spend your API credit.
- Set a monthly spend limit in the Anthropic Console.
