# Telegram Spectator

A webhook-based Telegram bot written in plain PHP (no framework) that connects
to a Telegram Business account. It keeps a history of the account's private
chats and reports edited and deleted messages to the owner, can save timed
media, and adds in-chat tools — quiz polls, auto-replies, a translator, a
calculator, currency rates and weather — driven by short dot-commands such as
`.weather Tashkent`. Its interface is in Uzbek.

## Features

- **Business connection** — the account owner is notified when the bot is
  connected to or removed from the account.
- **Chat history** — every incoming business message (text and media) is saved
  to `data/history/<chat_id>.json`.
- **Edited messages** — the owner receives the old and new text of an edited message.
- **Deleted messages** — the owner receives the deleted text or media (photo,
  video, video note, voice, audio, document).
- **Timed media** — reply `😅` to a media message to have the bot download it
  and forward a copy to the owner.
- **Auto-reply** — keyword → answer rules managed from the settings menu
  (add, list, rename, delete); answers support `%first%`, `%last%`, `%id%`,
  `%username%`, `%hour%`, `%date%` placeholders.
- **Quiz polls** — build a question with three options, the correct answer and
  an explanation in settings, then post it with `.quiz`.
- **Translator** — iTranslate API, target language chosen in settings.
- **Calculator**, **typing / heart animations**, **currency rates** (Central
  Bank of Uzbekistan) and **weather** (scraped from obhavo.uz).
- **Settings panel** — every module can be switched on or off from inline
  buttons (`.settings`).

## Commands

Dot-commands are typed by the account owner inside any business chat; the bot
edits the command message in place with the result.

| Command | Description |
|---|---|
| `.settings` | Open the settings panel (toggle modules, manage quiz and auto-replies) |
| `.ping` | Show the bot's response time |
| `.memory` | Show the PHP process's peak memory usage |
| `.math <expression>` | Calculate an arithmetic expression, e.g. `.math 2*(3+4)` |
| `.translator <text>` | Translate text to the selected language |
| `.typing <text>` | Type the text out letter by letter |
| `.heart` | Animate the message through coloured hearts, then type out a short message |
| `.quiz` | Post the next saved quiz question as a quiz poll |
| `.clear` | Reset the list of quiz questions already used in this chat |
| `.currency` | Show official exchange rates |
| `.weather <city>` | Show today's weather for a city (obhavo.uz city slug) |
| `😅` (as a reply) | Save the replied-to media and send it to the owner |

In a private chat with the bot, `/start` shows a greeting with a button to
contact the admin.

## Tech stack

- PHP 8.3+ with the `curl`, `mbstring` and `dom` extensions; `allow_url_fopen` enabled
- Telegram Bot API (Business updates) via webhook
- Flat-file storage in `data/` and `step/` (no database)
- External services: iTranslate API, cbu.uz, obhavo.uz

## Getting started

### Prerequisites

- A web server (e.g. nginx + php-fpm) serving the project over HTTPS, with
  write access for PHP to the project directory (`data/` and `step/` are
  created at runtime)
- A bot token from [@BotFather](https://t.me/BotFather), with **Business Mode**
  enabled for the bot in @BotFather's bot settings
- A Telegram account with Telegram Premium, to connect the bot under
  Settings → Telegram Business → Chatbots
- An iTranslate API key (only for the translator module)

### Install

```bash
git clone https://github.com/Viltrumlik/telegram_spectator.git
cd telegram_spectator
cp config.example.php config.php
```

### Configure

Edit `config.php` (gitignored — never commit it):

```php
const BOT_TOKEN = "YOUR_TELEGRAM_BOT_TOKEN";
const TRANSLATE_API_KEY = "YOUR_ITRANSLATE_API_KEY";
```

Set the owner in `index.php` — commands and settings only work for this account:

```php
$admin = 123456789;          // your numeric Telegram user id
$admin_user = "your_username"; // your Telegram username, without @
```

`index.php` also passes the owner id directly to `getChat` in the `/start`
handler; change that number as well.

### Set the webhook

Point Telegram at the deployed `index.php` and enable business updates:

```bash
curl "https://api.telegram.org/bot<BOT_TOKEN>/setWebhook" \
  --data-urlencode "url=https://your-domain/index.php" \
  --data-urlencode 'allowed_updates=["message","edited_message","callback_query","my_chat_member","business_connection","business_message","edited_business_message","deleted_business_messages"]'
```

Check it with:

```bash
curl "https://api.telegram.org/bot<BOT_TOKEN>/getWebhookInfo"
```

Then connect the bot to your account in Telegram (Settings → Telegram Business
→ Chatbots) and send `.settings` in any chat to switch modules on.

## Project structure

```
index.php            webhook handler: all bot logic
config.example.php   template for config.php
config.php           secrets (gitignored)
api/translate.php    iTranslate API wrapper, called by index.php
api/weather.php      obhavo.uz scraper returning JSON, called by index.php
data/                runtime state: settings, history, quiz, auto-replies (gitignored)
step/                per-chat conversation state (gitignored)
```

## License

Released under the [MIT License](LICENSE).
