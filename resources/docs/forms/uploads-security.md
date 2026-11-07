---
title: Uploads and security
description: How Xelqun Forms protects tokens, endpoints and uploads, and what's left for you to set.
---

## Tokens

A form in the browser is a schema and an encrypted token. Each request rebuilds the form on the server from that token, so the browser can't change which class runs, or which record it edits.

`token_ttl` in `config/xelqun-forms.php` sets how many minutes a token lasts. The default is 720.

## Endpoints

Search, uploads, live updates, step checks and actions each run `authorize()` again. They're throttled, and you can change the limits in the config.

```php
'throttle' => [
    'update' => '240,1',
    'upload' => '30,1',
    'search' => '120,1',
    'validate' => '120,1',
    'action' => '60,1',
],
```

## Uploads

A picked file goes to a temporary disk at once. Saving moves it to the field's disk.

```php
'uploads' => [
    'disk' => 'public',
    'temporary_disk' => 'local',
    'temporary_directory' => 'xelqun-tmp',
    'temporary_ttl' => 1440,
    'max_size' => 10240,
],
```

Sizes are in kilobytes, and the temporary lifetime is in minutes. Schedule the cleanup:

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('xelqun-forms:prune-uploads')->hourly();
```

## What to set yourself

- **Accepted types.** Set `acceptedFileTypes()` or `image()` on each upload.
- **SVG.** It can carry scripts, so it isn't accepted by the editors' attachments by default. Think twice before you add it.
- **Size.** Set `maxSize()` below the config's limit where you can.
- **Hidden fields.** People can edit them in the browser. Set anything that decides access or price on the server.

## Rich text

The server cleans `RichEditor` HTML before saving it, and `MarkdownEditor::render()` returns HTML that's safe to print.
