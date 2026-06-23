<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{User, Subscription, DeliveryDay, MealSelection, MenuItem, WeeklyMenu, PackageWeeklyMenu, Package, AdminUser};
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller {

    private $allDays = [
        ['short'=>'Mon','full'=>'Monday'],['short'=>'Tue','full'=>'Tuesday'],
        ['short'=>'Wed','full'=>'Wednesday'],['short'=>'Thu','full'=>'Thursday'],
        ['short'=>'Fri','full'=>'Friday'],['short'=>'Sat','full'=>'Saturday'],
        ['short'=>'Sun','full'=>'Sunday'],
    ];

    // ── Stats ────────────────────────────────────────────────────────────────
    public function stats() {
        $pkgMap = Package::all()->keyBy('id');
        $activeSubs = Subscription::where('status','active')->get();
        $revenue = $activeSubs->sum(fn($s) => $pkgMap[$s->package_id]?->price ?? 0);
        return response()->json([
            'totalUsers'    => User::count(),
            'totalSubs'     => Subscription::count(),
            'activeSubs'    => Subscription::where('status','active')->count(),
            'pausedSubs'    => Subscription::where('status','paused')->count(),
            'cancelledSubs' => Subscription::where('status','cancelled')->count(),
            'totalMeals'    => MenuItem::count(),
            'totalPackages' => Package::count(),
            'totalRevenue'  => $revenue,
        ]);
    }

    // ── Users ────────────────────────────────────────────────────────────────
    public function users() {
        $users = User::orderBy('created_at','desc')->get();
        $counts = Subscription::selectRaw('user_id, count(*) as count')->groupBy('user_id')->pluck('count','user_id');
        return response()->json($users->map(fn($u) => array_merge($u->toArray(), ['subscriptionCount' => $counts[$u->id] ?? 0])));
    }

    // ── Menu ─────────────────────────────────────────────────────────────────
    public function menuIndex() { return response()->json(MenuItem::orderBy('category')->orderBy('name')->get()); }

    public function menuStore(Request $request) {
        $item = MenuItem::create(array_merge($request->all(), ['id' => $request->input('id', Str::uuid())]));
        return response()->json($item, 201);
    }

    public function menuUpdate(Request $request, $id) {
        $item = MenuItem::findOrFail($id);
        $item->update($request->all());
        return response()->json($item->fresh());
    }

    public function menuDestroy($id) {
        MenuItem::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }

    // ── Weekly menu (legacy) ──────────────────────────────────────────────────
    public function weeklyIndex() { return response()->json(WeeklyMenu::all()); }

    public function weeklyUpdate(Request $request, $id) {
        $meals = MenuItem::whereIn('id', $request->input('mealIds', []))->get()->map(fn($m) => ['id'=>$m->id,'name'=>$m->name,'image'=>$m->image,'calories'=>$m->calories,'tags'=>$m->tags]);
        WeeklyMenu::where('id',$id)->update(['meals' => $meals]);
        return response()->json(WeeklyMenu::find($id));
    }

    // ── Package weekly menus ──────────────────────────────────────────────────
    public function packageMenus(Request $request) {
        $weekStart = $request->query('weekStart');
        $rows = PackageWeeklyMenu::where('week_start', $weekStart)->get()->keyBy(fn($r) => $r->package_id.'_'.$r->day_short);
        $pkgs = Package::all();
        $result = $pkgs->map(function($pkg) use ($rows) {
            $days = [];
            foreach ($this->allDays as $d) {
                $key = $pkg->id.'_'.$d['short'];
                $days[$d['short']] = isset($rows[$key])
                    ? ['id'=>$rows[$key]->id,'dayShort'=>$d['short'],'dayFull'=>$d['full'],'meals'=>$rows[$key]->meals ?? []]
                    : ['dayShort'=>$d['short'],'dayFull'=>$d['full'],'meals'=>[]];
            }
            return ['packageId'=>$pkg->id,'packageName'=>$pkg->name,'mealsPerDay'=>$pkg->meals_per_day,'days'=>$days];
        });
        return response()->json($result);
    }

    public function packageMenuSet(Request $request) {
        $pkgId     = $request->input('packageId');
        $weekStart = $request->input('weekStart');
        $dayShort  = $request->input('dayShort');
        $dayFull   = collect($this->allDays)->firstWhere('short',$dayShort)['full'] ?? $dayShort;
        $meals     = MenuItem::whereIn('id', $request->input('mealIds',[]))->get()->map(fn($m) => ['id'=>$m->id,'name'=>$m->name,'image'=>$m->image,'calories'=>$m->calories,'tags'=>$m->tags]);
        $row = PackageWeeklyMenu::updateOrCreate(
            ['package_id'=>$pkgId,'week_start'=>$weekStart,'day_short'=>$dayShort],
            ['day_full'=>$dayFull,'meals'=>$meals]
        );
        return response()->json($row);
    }

    // ── Packages ─────────────────────────────────────────────────────────────
    public function packagesIndex() { return response()->json(Package::all()); }

    public function packagesStore(Request $request) {
        $pkg = Package::create(array_merge($request->all(), ['id' => $request->input('id', 'pkg-'.Str::slug($request->input('name','new')))]));
        return response()->json($pkg, 201);
    }

    public function packagesUpdate(Request $request, $id) {
        $pkg = Package::findOrFail($id);
        $pkg->update($request->all());
        return response()->json($pkg->fresh());
    }

    public function packagesDestroy($id) {
        Package::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }

    // ── Subscriptions ─────────────────────────────────────────────────────────
    public function subscriptions(Request $request) {
        $query = Subscription::query();
        if ($request->query('status')) $query->where('status', $request->query('status'));
        $subs = $query->orderBy('created_at','desc')->get();
        $users = User::whereIn('id', $subs->pluck('user_id')->unique())->get()->keyBy('id');
        $pkgs  = Package::all()->keyBy('id');
        return response()->json($subs->map(fn($s) => array_merge($s->toArray(), [
            'user'        => $users[$s->user_id] ?? null,
            'packageName' => $pkgs[$s->package_id]?->name ?? $s->package_id,
            'price'       => $pkgs[$s->package_id]?->price ?? 0,
        ])));
    }

    public function subscriptionStatus(Request $request, $id) {
        $sub = Subscription::findOrFail($id);
        $sub->update(['status' => $request->input('status')]);
        return response()->json($sub->fresh());
    }

    public function subscriptionDeliveryDays(Request $request, $id) {
        Subscription::findOrFail($id);
        DeliveryDay::updateOrCreate(['subscription_id' => $id], ['days' => $request->input('days', [])]);
        return response()->json(['days' => $request->input('days', [])]);
    }

    public function subscriptionWeek(Request $request, $id) {
        $sub  = Subscription::findOrFail($id);
        $pkg  = Package::find($sub->package_id);
        $user = User::find($sub->user_id);
        $weekStart = $request->query('weekStart');
        $dayRow  = DeliveryDay::where('subscription_id', $id)->first();
        $selRows = MealSelection::where('subscription_id', $id)->where('week_start', $weekStart)->get()->keyBy('day_short');
        $pkgMenuRows = PackageWeeklyMenu::where('package_id', $sub->package_id)->where('week_start', $weekStart)->get()->keyBy('day_short');
        $deliveryDays = $dayRow?->days ?? [];
        $days = collect($this->allDays)->map(function($d) use ($deliveryDays, $pkgMenuRows, $selRows, $pkg) {
            $isDelivery   = in_array($d['short'], $deliveryDays);
            $pkgMenu      = $pkgMenuRows[$d['short']] ?? null;
            $sel          = $selRows[$d['short']] ?? null;
            $pkgMeals     = $pkgMenu?->meals ?? [];
            $selMealIds   = $sel ? array_keys(array_filter($sel->selections, fn($v) => $v > 0)) : [];
            $effective    = $sel ? array_values(array_filter($pkgMeals, fn($m) => in_array($m['id'], $selMealIds))) : array_slice($pkgMeals, 0, $pkg?->meals_per_day ?? 1);
            return ['dayShort'=>$d['short'],'dayFull'=>$d['full'],'isDelivery'=>$isDelivery,'packageMeals'=>$pkgMeals,'customSelection'=>$sel ? ['id'=>$sel->id,'selections'=>$sel->selections] : null,'effectiveMeals'=>$effective];
        });
        return response()->json(array_merge($sub->toArray(), [
            'user'=>$user,'packageName'=>$pkg?->name??$sub->package_id,'price'=>$pkg?->price??0,
            'mealsPerDay'=>$pkg?->meals_per_day??1,'totalMeals'=>$pkg?->total_meals??0,
            'weekStart'=>$weekStart,'deliveryDays'=>$deliveryDays,'days'=>$days,
        ]));
    }

    public function subscriptionWeekSelection(Request $request, $id) {
        Subscription::findOrFail($id);
        $row = MealSelection::updateOrCreate(
            ['subscription_id'=>$id,'week_start'=>$request->input('weekStart'),'day_short'=>$request->input('dayShort')],
            ['selections'=>$request->input('selections',[])]
        );
        return response()->json($row);
    }
}
