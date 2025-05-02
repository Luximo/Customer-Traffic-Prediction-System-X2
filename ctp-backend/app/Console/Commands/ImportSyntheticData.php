<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TrafficData;
use App\Models\SalesData;

class ImportSyntheticData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:synthetic-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import synthetic traffic and sales data CSV files into the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->importTrafficData();
        $this->importSalesData();
        $this->importWeatherData();
        $this->importPromotionData();


        $this->info("🎉 All synthetic data imported successfully.");
    }

    private function importTrafficData()
    {
        $this->info('📥 Importing traffic data...');

        $filePath = storage_path('app/traffic_data.csv');
        if (!file_exists($filePath)) {
            $this->error("❌ File not found: $filePath");
            return;
        }

        $file = fopen($filePath, 'r');
        fgetcsv($file); // Skip header

        $imported = 0;
        while (($row = fgetcsv($file)) !== false) {
            TrafficData::create([
                'date' => $row[0],
                'hour' => $row[1],
                'customer_count' => $row[2],
            ]);
            $imported++;
        }

        fclose($file);
        $this->info("✅ Imported $imported traffic records.");
    }

    private function importSalesData()
    {
        $this->info('📥 Importing sales data...');

        $filePath = storage_path('app/sales_data.csv');
        if (!file_exists($filePath)) {
            $this->error("❌ File not found: $filePath");
            return;
        }

        $file = fopen($filePath, 'r');
        fgetcsv($file); // Skip header

        $imported = 0;
        while (($row = fgetcsv($file)) !== false) {
            SalesData::create([
                'date' => $row[0],
                'hour' => $row[1],
                'total_sales' => $row[2],
            ]);
            $imported++;
        }

        fclose($file);
        $this->info("✅ Imported $imported sales records.");
    }

    private function importWeatherData()
    {
        $this->info('📥 Importing weather data...');

        $filePath = storage_path('app/weather_data.csv');
        if (!file_exists($filePath)) {
            $this->error("❌ File not found: $filePath");
            return;
        }

        $file = fopen($filePath, 'r');
        fgetcsv($file); // Skip header

        $imported = 0;
        while (($row = fgetcsv($file)) !== false) {
            \App\Models\WeatherData::create([
                'date' => $row[0],
                'temperature' => $row[1],
                'condition' => $row[2],
            ]);
            $imported++;
        }

        fclose($file);
        $this->info("✅ Imported $imported weather records.");
    }

    private function importPromotionData()
    {
        $this->info('📥 Importing promotion data...');

        $filePath = storage_path('app/promotion_data.csv');
        if (!file_exists($filePath)) {
            $this->error("❌ File not found: $filePath");
            return;
        }

        $file = fopen($filePath, 'r');
        fgetcsv($file); // Skip header

        $imported = 0;
        while (($row = fgetcsv($file)) !== false) {
            \App\Models\PromotionData::create([
                'start_date' => $row[0],
                'end_date' => $row[1],
                'description' => $row[2],
            ]);
            $imported++;
        }

        fclose($file);
        $this->info("✅ Imported $imported promotions.");
    }
}
