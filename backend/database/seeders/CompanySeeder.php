<?php
namespace Database\Seeders;
use App\Domains\Shared\Models\Company;
use Illuminate\Database\Seeder;
class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::firstOrCreate(['code' => 'AHMED-PHARMA'], ['name' => 'Ahmed Pharmacy', 'address' => 'Main Boulevard, Karachi', 'phone' => '021-35800000', 'status' => 'active']);
        Company::firstOrCreate(['code' => 'SECOND-MED'], ['name' => 'Second Medical Store', 'address' => 'Model Town, Lahore', 'phone' => '042-35770000', 'status' => 'active']);
    }
}
