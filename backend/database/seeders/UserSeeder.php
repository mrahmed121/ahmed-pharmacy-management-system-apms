<?php
namespace Database\Seeders;
use App\Domains\Shared\Models\Company;
use App\Domains\Shared\Models\Role;
use App\Domains\Shared\Models\User;
use Illuminate\Database\Seeder;
class UserSeeder extends Seeder
{
    public const DEMO_PASSWORD = 'password123';
    public function run(): void
    {
        $superRole = Role::where('slug', 'super-admin')->firstOrFail();
        $super = User::firstOrCreate(['email' => 'super@apms.local'], ['name' => 'Super Admin', 'company_id' => null, 'password' => self::DEMO_PASSWORD, 'is_active' => true]);
        $super->roles()->syncWithoutDetaching([$superRole->id]);
        $company = Company::where('code', 'AHMED-PHARMA')->firstOrFail();
        foreach ([['Owner','owner','owner'],['Manager','manager','manager'],['Pharmacist','pharmacist','pharmacist'],['Cashier','cashier','cashier'],['Auditor','auditor','auditor']] as [$name, $local, $roleSlug]) {
            $user = User::firstOrCreate(['email' => "{$local}@ahmedpharma.local"], ['name' => $name, 'company_id' => $company->id, 'password' => self::DEMO_PASSWORD, 'is_active' => true]);
            $user->roles()->syncWithoutDetaching([Role::where('slug', $roleSlug)->firstOrFail()->id]);
        }
    }
}
