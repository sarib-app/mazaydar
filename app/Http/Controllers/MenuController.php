<?php
namespace App\Http\Controllers;
use App\Models\{MenuItem, WeeklyMenu};
use Illuminate\Http\Request;

class MenuController extends Controller {
    public function index() {
        return response()->json(MenuItem::all());
    }
    public function weekly() {
        return response()->json(WeeklyMenu::all());
    }
    public function show($id) {
        return response()->json(MenuItem::findOrFail($id));
    }
}
