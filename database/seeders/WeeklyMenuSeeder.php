<?php
namespace Database\Seeders;
use App\Models\WeeklyMenu;
use Illuminate\Database\Seeder;
class WeeklyMenuSeeder extends Seeder {
    public function run(): void {
        if (WeeklyMenu::count() > 0) return;
        $days = [
            ['id'=>'monday',   'day_short'=>'Mon','day_full'=>'Monday'],
            ['id'=>'tuesday',  'day_short'=>'Tue','day_full'=>'Tuesday'],
            ['id'=>'wednesday','day_short'=>'Wed','day_full'=>'Wednesday'],
            ['id'=>'thursday', 'day_short'=>'Thu','day_full'=>'Thursday'],
            ['id'=>'friday',   'day_short'=>'Fri','day_full'=>'Friday'],
            ['id'=>'saturday', 'day_short'=>'Sat','day_full'=>'Saturday'],
            ['id'=>'sunday',   'day_short'=>'Sun','day_full'=>'Sunday'],
        ];
        foreach ($days as $d) WeeklyMenu::create(array_merge($d, ['meals'=>[]]));
    }
}
