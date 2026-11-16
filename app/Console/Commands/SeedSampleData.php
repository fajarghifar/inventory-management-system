<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\DemoDataSeeder;

class SeedSampleData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed-sample-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed comprehensive retail products with real barcodes, sales, stock adjustments, and expenses for demo and testing.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Seeding rich demo data into inventory database...');
        $this->call('db:seed', [
            '--class' => DemoDataSeeder::class,
            '--force' => true,
        ]);
        $this->info('Sample data successfully seeded!');
        return Command::SUCCESS;
    }
}
