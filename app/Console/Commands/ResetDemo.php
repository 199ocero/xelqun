<?php

namespace App\Console\Commands;

use Database\Seeders\DemoSeeder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

#[Signature('demo:reset')]
#[Description('Put the public demo back to its seeded state')]
class ResetDemo extends Command
{
    /** Everything else is wiped, so a new table is covered with no change here. */
    private const array KEPT_TABLES = ['migrations', 'sessions', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs'];

    public function handle(): int
    {
        $tables = array_diff(Schema::getTableListing(schemaQualified: false), self::KEPT_TABLES);

        // SQLite can't change foreign key checks inside a transaction, so that wraps it.
        // shortcut: MySQL's TRUNCATE commits by itself, so a visitor could catch empty pages for a moment there. Use DELETE if the demo moves to MySQL and that shows.
        Schema::withoutForeignKeyConstraints(fn () => DB::transaction(function () use ($tables): void {
            foreach ($tables as $table) {
                DB::table($table)->truncate();
            }

            Model::withoutEvents(fn () => app(DemoSeeder::class)->run());
        }));

        Storage::disk('demo')->deleteDirectory('/');
        Storage::disk('public')->deleteDirectory('demo');
        app(DemoSeeder::class)->publishImages();

        $this->components->info('The demo is back to its seeded state.');

        return self::SUCCESS;
    }
}
