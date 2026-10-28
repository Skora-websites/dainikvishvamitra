<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Copies the site's demo content verbatim from the legacy flat-PHP installation
 * (database `vishwamitra`) into this Laravel installation.
 *
 * Column-for-column compatible; preserves ids so admin links/bookmarks survive.
 */
class LegacyImportSeeder extends Seeder
{
    /** Tables with an autoincrement id that must be preserved. */
    private const TABLES = [
        'users', 'categories', 'articles', 'key_points', 'ticker_items', 'bulletins',
        'market_indices', 'horoscope_signs', 'polls', 'poll_options',
        'subscribers', 'epapers', 'pages', 'election_events', 'videos',
        'contact_messages', 'settings',
    ];

    public function run(): void
    {
        $source = config('database.connections.mysql_legacy');
        if (! $source) {
            throw new RuntimeException('Legacy source connection (mysql_legacy) not configured.');
        }

        // Truncate first with FK checks off: children (key_points, poll_options)
        // must go before their parents (articles, polls), and a re-seed on an
        // existing database must not fail on "cannot delete or update a parent row".
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        foreach (self::TABLES as $table) {
            DB::table($table)->delete();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        foreach (self::TABLES as $table) {
            $rows = DB::connection('mysql_legacy')->table($table)->get();

            DB::transaction(function () use ($rows, $table) {
                foreach ($rows as $row) {
                    DB::table($table)->insert((array) $row);
                }
            });

            // Re-align AUTO_INCREMENT so future inserts do not collide.
            // (DDL — kept outside the transaction, since MySQL auto-commits DDL.)
            if ($rows->isNotEmpty() && $table !== 'settings') {
                $next = (int) $rows->last()->id + 1;
                DB::statement("ALTER TABLE `{$table}` AUTO_INCREMENT = {$next}");
            }

            $this->command?->getOutput()->writeln("  <fg=gray>{$table}: {$rows->count()} rows</>");
        }
    }
}
