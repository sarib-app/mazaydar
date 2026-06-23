<?php
namespace Database\Seeders;
use App\Models\Package;
use Illuminate\Database\Seeder;
class PackageSeeder extends Seeder {
    public function run(): void {
        if (Package::count() > 0) return;
        $packages = [
            ['id'=>'pkg-starter',  'name'=>'Starter',   'tagline'=>'Perfect for beginners', 'price'=>420,  'meals_per_day'=>1,'total_meals'=>24,'cycle_days'=>24,'meal_types'=>['lunch'],'features'=>['1 meal/day','24 meals total','Nutritionist approved'],'is_popular'=>false],
            ['id'=>'pkg-standard', 'name'=>'Standard',  'tagline'=>'Most popular choice',   'price'=>800,  'meals_per_day'=>2,'total_meals'=>48,'cycle_days'=>24,'meal_types'=>['lunch','dinner'],'features'=>['2 meals/day','48 meals total','Priority support','Free delivery'],'is_popular'=>true],
            ['id'=>'pkg-fullboard','name'=>'Full Board', 'tagline'=>'Complete nutrition',    'price'=>1000, 'meals_per_day'=>3,'total_meals'=>72,'cycle_days'=>24,'meal_types'=>['breakfast','lunch','dinner'],'features'=>['3 meals/day','72 meals total','24/7 support','Free delivery','Custom menu'],'is_popular'=>false],
        ];
        foreach ($packages as $p) Package::create($p);
    }
}
