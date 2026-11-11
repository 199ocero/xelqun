---
title: Commands
description: The Artisan commands of Xelqun Tables.
---

| Command                 | What it does                                                                              |
| ----------------------- | ----------------------------------------------------------------------------------------- |
| `xelqun-tables:install` | Adds the tables and forms npm packages, runs `xelqun-forms:install`, and adds the styles. |
| `make:xelqun-table`     | Creates a [table class](/tables/your-first-table).                                        |
| `make:xelqun-column`    | Creates a [custom column](/tables/columns/custom): a PHP class and its Vue cell.          |

`make:xelqun-table ProductsTable` writes `app/Tables/ProductsTable.php`. It lists the model named after the table, without "Table" and in the singular, here `Product`. `--model=Tag` picks another model.

`make:xelqun-column Sparkline` writes `app/Tables/Columns/Sparkline.php` and `resources/js/tables/SparklineCell.vue`, and prints the line that registers the cell. `--type=acme/sparkline` sets the schema type, which defaults to `app/sparkline`.

Both `make:` commands stop if their file exists. `--force` overwrites it. `xelqun-tables:install` takes `--without-node` to skip the npm install.
