<?php
namespace Database\Seeders;
use App\Domains\Inventory\Models\Batch;
use App\Domains\Inventory\Models\Medicine;
use App\Domains\Purchases\Models\Supplier;
use App\Domains\Shared\Models\Company;
use Illuminate\Database\Seeder;
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::where('code', 'AHMED-PHARMA')->firstOrFail();
        $supplier = Supplier::firstOrCreate(['company_id' => $company->id, 'name' => 'MediDistributors'], ['phone' => '021-34567890']);
        $meds = [
            ['Panadol Extra', 'Paracetamol + Caffeine', 'strip', 'A-01', 50, 45.00],
            ['Augmentin 625mg', 'Amoxicillin + Clavulanate', 'strip', 'A-02', 30, 320.00],
            ['Disprin', 'Aspirin', 'strip', 'B-01', 100, 25.00],
        ];
        foreach ($meds as [$name, $generic, $unit, $rack, $reorder, $price]) {
            $med = Medicine::firstOrCreate(['company_id' => $company->id, 'name' => $name],
                ['generic_name' => $generic, 'unit' => $unit, 'rack_location' => $rack, 'reorder_level' => $reorder, 'sale_price' => $price]);
            // Two batches: one expiring sooner (FEFO should pick this first)
            Batch::firstOrCreate(['company_id' => $company->id, 'medicine_id' => $med->id, 'batch_number' => 'B2024A'],
                ['expiry_date' => now()->addMonths(6)->toDateString(), 'quantity' => 200, 'purchase_price' => $price * 0.7]);
            Batch::firstOrCreate(['company_id' => $company->id, 'medicine_id' => $med->id, 'batch_number' => 'B2025A'],
                ['expiry_date' => now()->addMonths(18)->toDateString(), 'quantity' => 300, 'purchase_price' => $price * 0.7]);
        }
    }
}
