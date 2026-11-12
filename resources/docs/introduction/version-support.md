---
title: Version support
description: Which Xelqun versions are current, what they run on, and what beta means for your code.
---

## Beta

Xelqun is beta. It's ready to build on, and this site's demo runs on it, but it isn't 1.0 yet. Until then:

- **A patch release** (0.7.0 to 0.7.1) never breaks your code, except to fix a security issue, and the changelog says so when it does.
- **A minor release** (0.7 to 0.8) can break code. Every break is listed under "Upgrading" in the package's changelog, with what to change, so read it before you upgrade.
- Anything marked `@internal` can change in any release.
- Fixes go to the latest minor only.

From 1.0, only a major version breaks code.

## Current versions

| Package                                                                    | Version | PHP  | Laravel | Inertia | Vue  | Tailwind CSS |
| -------------------------------------------------------------------------- | ------- | ---- | ------- | ------- | ---- | ------------ |
| [Forms](https://github.com/199ocero/xelqun-forms/blob/main/CHANGELOG.md)   | 0.10    | 8.4+ | 13      | 3       | 3.5+ | 4            |
| [Tables](https://github.com/199ocero/xelqun-tables/blob/main/CHANGELOG.md) | 0.8     | 8.4+ | 13      | 3       | 3.5+ | 4            |
| [Panels](https://github.com/199ocero/xelqun-panels/blob/main/CHANGELOG.md) | 0.7     | 8.4+ | 13      | 3       | 3.5+ | 4            |

## Versions that go together

The packages move at their own pace, so their version numbers differ. Each one states what it needs.

- Tables needs Forms 0.10.
- Panels needs Forms 0.10 and Tables 0.8.

Composer and npm work this out for you when you upgrade.

## Getting help

If something's broken, please open an issue on the package's repository: [Forms](https://github.com/199ocero/xelqun-forms/issues), [Tables](https://github.com/199ocero/xelqun-tables/issues) or [Panels](https://github.com/199ocero/xelqun-panels/issues).
