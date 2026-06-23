<?php
namespace Database\Seeders;
use App\Models\AdminUser;
use Illuminate\Database\Seeder;
class AdminUserSeeder extends Seeder {
    public function run(): void {
        if (AdminUser::where('username','admin')->exists()) return;
        AdminUser::create(['username'=>'admin','password_hash'=>password_hash('admin123',PASSWORD_BCRYPT),'role'=>'superadmin']);
        echo "Default admin created: admin / admin123\n";
    }
}
