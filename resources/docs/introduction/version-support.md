---
title: Version support
description: Which Xelqun versions are current, what they run on, and what alpha means for your code.
---

## Alpha

Xelqun is alpha. It works, and this site's demo runs on it, but it isn't stable yet.

Until 1.0, a minor version can break existing code. Each package's changelog lists every break, so read it before you upgrade.

## Current versions

| Package                                                                    | Version | PHP  | Laravel | Inertia | Vue  | Tailwind CSS |
| -------------------------------------------------------------------------- | ------- | ---- | ------- | ------- | ---- | ------------ |
| [Forms](https://github.com/199ocero/xelqun-forms/blob/main/CHANGELOG.md)   | 0.10    | 8.4+ | 13      | 3       | 3.5+ | 4            |
| [Tables](https://github.com/199ocero/xelqun-tables/blob/main/CHANGELOG.md) | 0.8     | 8.4+ | 13      | 3       | 3.5+ | 4            |
| [Panels](https://github.com/199ocero/xelqun-panels/blob/main/CHANGELOG.md) | 0.6     | 8.4+ | 13      | 3       | 3.5+ | 4            |

## Versions that go together

The packages move at their own pace, so their version numbers differ. Each one states what it needs.

- Tables needs Forms 0.10.
- Panels needs Forms 0.10 and Tables 0.8.

Composer and npm work this out for you when you upgrade.

## Getting help

If something's broken, please open an issue on the package's repository: [Forms](https://github.com/199ocero/xelqun-forms/issues), [Tables](https://github.com/199ocero/xelqun-tables/issues) or [Panels](https://github.com/199ocero/xelqun-panels/issues).
