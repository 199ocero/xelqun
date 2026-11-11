---
title: Two-factor
description: Authenticator apps, email codes and recovery codes, with no extra dependency.
---

```php
$panel->multiFactorAuthentication();                    // optional for each user
$panel->multiFactorAuthentication(isRequired: true);    // nobody gets past the profile page without it
```

<!-- screenshot: two-factor | The two-factor setup dialog -->

Add the columns, then put `HasAppAuthentication` and the `InteractsWithAppAuthentication` trait on your user model. People turn it on from the profile page, with their password.

```bash
php artisan vendor:publish --tag=xelqun-panels-mfa
```

```php
use Xelqun\Panels\Auth\Concerns\InteractsWithAppAuthentication;
use Xelqun\Panels\Contracts\HasAppAuthentication;

class User extends Authenticatable implements HasAppAuthentication
{
    use InteractsWithAppAuthentication;
}
```

## How it works

It's the standard 30 second, 6 digit TOTP that any authenticator app reads, with 8 recovery codes. A code works once, and the right password alone never gets anyone in. Five wrong codes end the challenge, so the password has to be entered again, and more guesses then wait 15 minutes.

It's checked on every page and token of the panel, so signing in through your app's own login can't get around it, and a remember-me login is asked for a code again in a new session. Secrets are encrypted, and only hashes of recovery codes are kept.

> [!NOTE]
> Setup gives a link and a key to type, with no QR code, because that would need a new dependency.

## Email codes

`emailAuthentication()` lets people get a 6 digit code by email at sign in. Add `HasEmailAuthentication` to the user model, and the same trait covers it.

```php
$panel->emailAuthentication();
```

The code is kept only as a hash, works once, and ends with the 5 minute challenge. It's weaker than an authenticator, because anyone in the mailbox can read it.

The challenge page says when a code was emailed, and offers "Send a new code". A new code replaces the old one and gives the challenge another 5 minutes. It can be asked for 3 times a minute.

## With a Laravel starter kit

A starter kit has Fortify's own two-factor, on its settings page. That's separate: it uses other columns, and the panel never reads them, so someone set up in one isn't asked for a code by the other. Keep both, or turn Fortify's `twoFactorAuthentication` feature off in `config/fortify.php`.

[Register an account in the demo](demo:/register), then open your profile. The shared demo account can't turn this on.
