<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AdminAuthController extends Controller {

    private function makeToken(AdminUser $admin): string {
        $payload = ['id' => $admin->id, 'username' => $admin->username, 'role' => $admin->role, 'exp' => time() + 86400 * 30];
        return JWT::encode($payload, env('ADMIN_JWT_SECRET', 'secret'), 'HS256');
    }

    public function login(Request $request) {
        $admin = AdminUser::where('username', $request->input('username'))->first();
        if (!$admin || !password_verify($request->input('password'), $admin->password_hash))
            return response()->json(['message' => 'Invalid credentials'], 401);
        return response()->json(['token' => $this->makeToken($admin), 'username' => $admin->username, 'role' => $admin->role]);
    }

    public function list() {
        return response()->json(AdminUser::select('id','username','role','created_at')->orderBy('created_at')->get());
    }

    public function create(Request $request) {
        if (AdminUser::where('username', $request->input('username'))->exists())
            return response()->json(['message' => 'Username already taken'], 409);
        $admin = AdminUser::create(['username' => $request->input('username'), 'password_hash' => password_hash($request->input('password'), PASSWORD_BCRYPT), 'role' => 'admin']);
        return response()->json($admin->only(['id','username','role','created_at']), 201);
    }

    public function remove(Request $request, $id) {
        if ($id === $request->admin_user['id']) return response()->json(['message' => 'Cannot delete yourself'], 400);
        AdminUser::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function changePassword(Request $request, $id) {
        $admin = AdminUser::findOrFail($id);
        $admin->update(['password_hash' => password_hash($request->input('password'), PASSWORD_BCRYPT)]);
        return response()->json(['message' => 'Password updated']);
    }
}
