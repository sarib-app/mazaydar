<?php
namespace App\Http\Controllers;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller {
    public function index() {
        return response()->json(Package::all());
    }
    public function show($id) {
        $pkg = Package::find($id);
        if (!$pkg) return response()->json(['message' => 'Package not found'], 404);
        return response()->json($pkg);
    }
}
