<?php
namespace App\Http\Controllers;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AddressController extends Controller {

    public function index(Request $request) {
        return response()->json(Address::where('user_id', $request->auth_user_id)->orderBy('is_default','desc')->get());
    }

    public function store(Request $request) {
        $userId = $request->auth_user_id;
        if ($request->input('is_default')) {
            Address::where('user_id', $userId)->update(['is_default' => false]);
        }
        $address = Address::create(array_merge($request->only(['label','street','building','floor','apartment','instructions','is_default','lat','lng']), ['user_id' => $userId, 'id' => Str::uuid()]));
        return response()->json($address, 201);
    }

    public function update(Request $request, $id) {
        $address = Address::where('id', $id)->where('user_id', $request->auth_user_id)->firstOrFail();
        if ($request->input('is_default')) {
            Address::where('user_id', $request->auth_user_id)->update(['is_default' => false]);
        }
        $address->update($request->only(['label','street','building','floor','apartment','instructions','is_default','lat','lng']));
        return response()->json($address->fresh());
    }

    public function destroy(Request $request, $id) {
        $address = Address::where('id', $id)->where('user_id', $request->auth_user_id)->firstOrFail();
        $address->delete();
        return response()->json(Address::where('user_id', $request->auth_user_id)->orderBy('is_default','desc')->get());
    }
}
