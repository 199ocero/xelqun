---
title: Commands
description: The Artisan commands of Xelqun Forms.
---

| Command                      | What it does                                                                                         |
| ---------------------------- | ---------------------------------------------------------------------------------------------------- |
| `xelqun-forms:install`       | Adds the npm package and sets up `app.ts` and `app.css`.                                             |
| `xelqun-forms:plugin`        | Adds an installed [plugin](/forms/plugins) to your frontend, e.g. `xelqun-forms:plugin acme/rating`. |
| `xelqun-forms:prune-uploads` | Deletes temporary uploads that were never saved. [Schedule it](/forms/production) in production.     |
| `make:xelqun-form`           | Creates a [form class](/forms/your-first-form).                                                      |
| `make:xelqun-field`          | Creates a [custom field](/forms/fields/custom-fields): a PHP class and its Vue component.            |

`make:xelqun-form OrderForm` writes `app/Forms/OrderForm.php`. It edits the model named after the form, without "Form" and in the singular, here `Order`. `--model=Customer` picks another model. Its first field is a column the model's table has: `name`, `title`, `label`, `number`, `email`, `slug`, or its first text column. `--force` overwrites the file. Without it, the command stops if the file exists.

`make:xelqun-field Rating` writes `app/Forms/Components/Rating.php` and `resources/js/forms/RatingField.vue`, and prints the line that registers the Vue file. Its options:

- `--type=acme/rating` sets the schema type. It defaults to `app/` and the name in kebab case, here `app/rating`.
- `--search` starts from a field that searches the server as the user types.
- `--uploads` starts from a field that takes file uploads.
- `--force` overwrites the files. Without it, the command stops if they exist.

`xelqun-forms:install` and `xelqun-forms:plugin` take `--without-node` to skip the npm install, when you add the package yourself.
