# Telegram Spectator

A webhook-based Telegram bot (PHP, no framework) for monitoring Telegram Business
accounts — logging edited/deleted messages, timed media, plus utilities like a
quiz/poll builder, translator, calculator, currency and weather lookups.

## Requirements

- PHP 8.3+ with `curl` and `mbstring` extensions
- A web server (nginx + php-fpm) serving the project over HTTPS
- A Telegram bot token from [@BotFather](https://t.me/BotFather)

## Setup

1. Copy the example config and fill in your secrets:

   ```bash
   cp config.example.php config.php
   ```

   Then edit `config.php`:

   ```php
   const BOT_TOKEN = "your-telegram-bot-token";
   const TRANSLATE_API_KEY = "your-itranslate-api-key";
   ```

   `config.php` is gitignored and must never be committed.

2. Set your admin numeric Telegram ID and username in `index.php`:

   ```php
   $admin = 7990053633;        // your numeric Telegram user id
   $admin_user = "viltrumlik"; // your Telegram @username (without @)
   ```

3. Point Telegram's webhook at the deployed `index.php` over HTTPS, enabling
   business updates:

   ```bash
   curl "https://api.telegram.org/bot<BOT_TOKEN>/setWebhook" \
     --data-urlencode "url=https://your-domain/index.php" \
     --data-urlencode 'allowed_updates=["message","edited_message","callback_query","my_chat_member","business_connection","business_message","edited_business_message","deleted_business_messages"]'
   ```

## Project layout

```
index.php              # main bot logic (webhook handler)
config.php             # secrets (gitignored)
config.example.php     # template for config.php
api/translate.php      # iTranslate API wrapper
api/weather.php        # weather API wrapper
data/                  # runtime state, created automatically (gitignored)
step/                  # per-chat conversation state (gitignored)
```
