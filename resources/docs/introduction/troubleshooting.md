---
title: Troubleshooting
description: What to check when you can't sign in, a resource doesn't show up, or the page has no styles.
---

Xelqun refuses by default. Nobody gets in, and nothing shows, until you say so. That's safe, but on a first run it can look broken. These are the usual causes.

## I can't sign in

Your user model needs `PanelUser`, and `canAccessPanel()` has to return `true` for you. Until then, the right password fails just like a wrong one.

With `APP_DEBUG` on, the login form says when the model is missing `PanelUser`. If it has it, check that your email is in the list, or that your rule lets you in. See [Let people in](/panels/installation#let-people-in).

## I signed in, but get a 403

`canAccessPanel()` said no for this panel. If you have [more than one panel](/panels/multiple-panels), it's asked for each one, with that panel passed in.

The panel may also want a [verified email](/panels/auth/email-verification), and your user model needs `MustVerifyEmail` for that.

## A resource isn't in the sidebar

- **The policy says no.** A resource shows nothing until its policy's `viewAny` returns `true`. With no policy, or no such method, the answer is no. See [allow access](/introduction/getting-started#allow-access).
- **The panel doesn't know about it.** A new panel finds resources in `app/Xelqun/{Panel}/Resources`. If the class is somewhere else, or your panel doesn't call `discoverResources()`, add it to `->resources([...])`.
- **It's hidden on purpose.** `shouldRegisterNavigation()` returning `false` leaves it out of the sidebar, though its pages still work.

## A button or action is missing

Actions check the policy too. No create button means `create` said no, and no edit or delete on a row means `update` or `delete` did. Each method has to exist and return `true`.

## The page has no styles, or is blank

Rebuild your assets with `npm run build`, or keep `npm run dev` running. "Unable to locate file in Vite manifest" means the same thing.

If that doesn't help, check that the installer added the styles and `panelsPlugin()` to your Inertia entry file. See [installation](/panels/installation#install-the-package).

## Tables won't install

Xelqun Tables needs PHP's `intl` extension. Turn it on in your `php.ini`, or install it with your PHP.

## Still stuck?

Open an issue on the package's repository: [Forms](https://github.com/199ocero/xelqun-forms/issues), [Tables](https://github.com/199ocero/xelqun-tables/issues) or [Panels](https://github.com/199ocero/xelqun-panels/issues).
