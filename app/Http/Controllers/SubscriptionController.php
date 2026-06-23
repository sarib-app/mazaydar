<?php
namespace App\Http\Controllers;
use App\Models\{Subscription, Package, DeliveryDay, MealSelection};
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionController extends Controller {

    private function enrich($sub) {
        $pkg = Package::find($sub->package_id);
        $dayRow = DeliveryDay::where('subscription_id', $sub->id)->first();
        return array_merge($sub->toArray(), [
            'packageName' => $pkg?->name ?? $sub->package_id,
            'tagline'     => $pkg?->tagline,
            'price'       => $pkg?->price ?? 0,
            'mealsPerDay' => $pkg?->meals_per_day ?? 1,
            'totalMeals'  => $pkg?->total_meals ?? 0,
            'cycleDays'   => $pkg?->cycle_days ?? 24,
            'features'    => $pkg?->features ?? [],
            'isPopular'   => $pkg?->is_popular ?? false,
            'deliveryDays'=> $dayRow?->days ?? [],
        ]);
    }

    public function index(Request $request) {
        $subs = Subscription::where('user_id', $request->auth_user_id)->get();
        return response()->json($subs->map(fn($s) => $this->enrich($s)));
    }

    public function store(Request $request) {
        $pkg = Package::find($request->input('packageId'));
        if (!$pkg) return response()->json(['message' => 'Package not found'], 404);
        $start = now()->toDateString();
        $end   = now()->addDays($pkg->cycle_days)->toDateString();
        $sub = Subscription::create([
            'id' => Str::uuid(), 'user_id' => $request->auth_user_id,
            'package_id' => $pkg->id, 'status' => 'active',
            'start_date' => $start, 'end_date' => $end,
            'payment_method' => $request->input('paymentMethod', 'card'),
            'address_id' => $request->input('addressId'),
        ]);
        return response()->json($this->enrich($sub), 201);
    }

    public function show(Request $request, $id) {
        $sub = Subscription::where('id', $id)->where('user_id', $request->auth_user_id)->firstOrFail();
        return response()->json($this->enrich($sub));
    }

    public function pause(Request $request, $id) {
        $sub = Subscription::where('id', $id)->where('user_id', $request->auth_user_id)->firstOrFail();
        $resumeDate = $request->input('resumeDate', now()->addDays(7)->toDateString());
        $sub->update(['status' => 'paused', 'pause_start' => now()->toDateString(), 'resume_date' => $resumeDate, 'pauses_used' => $sub->pauses_used + 1]);
        return response()->json($this->enrich($sub->fresh()));
    }

    public function resume(Request $request, $id) {
        $sub = Subscription::where('id', $id)->where('user_id', $request->auth_user_id)->firstOrFail();
        $sub->update(['status' => 'active', 'pause_start' => null, 'resume_date' => null]);
        return response()->json($this->enrich($sub->fresh()));
    }

    public function setDeliveryDays(Request $request, $id) {
        $sub = Subscription::where('id', $id)->where('user_id', $request->auth_user_id)->firstOrFail();
        $days = $request->input('days', []);
        DeliveryDay::updateOrCreate(['subscription_id' => $sub->id], ['days' => $days]);
        return response()->json(['days' => $days]);
    }

    public function setSelection(Request $request, $id) {
        $sub = Subscription::where('id', $id)->where('user_id', $request->auth_user_id)->firstOrFail();
        $weekStart  = $request->input('weekStart');
        $dayShort   = $request->input('dayShort');
        $selections = $request->input('selections', []);
        MealSelection::updateOrCreate(
            ['subscription_id' => $sub->id, 'week_start' => $weekStart, 'day_short' => $dayShort],
            ['selections' => $selections]
        );
        return response()->json(['weekStart' => $weekStart, 'dayShort' => $dayShort, 'selections' => $selections]);
    }
}
