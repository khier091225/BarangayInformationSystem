<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class CurrentDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /** @var array{tables: array<string, list<array<string, mixed>>>, files: list<array{disk: string, path: string}>} $snapshot */
        $snapshot = json_decode(
            File::get(database_path('seeders/data/current-database.json')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        DB::transaction(function () use ($snapshot): void {
            foreach (array_keys($snapshot['tables']) as $table) {
                if (DB::table($table)->exists()) {
                    throw new RuntimeException(
                        'The database snapshot requires empty application tables. Back up existing data before using php artisan migrate:fresh --seed.',
                    );
                }
            }

            foreach ($snapshot['tables'] as $table => $records) {
                foreach (array_chunk($records, 100) as $chunk) {
                    DB::table($table)->insert($chunk);
                }
            }

            foreach ($snapshot['files'] as $file) {
                $contents = File::get(database_path('seeders/data/files/'.$file['disk'].'/'.$file['path']));

                if (! Storage::disk($file['disk'])->put($file['path'], $contents)) {
                    throw new RuntimeException('Unable to restore snapshot file: '.$file['path']);
                }
            }
        });
    }
}
