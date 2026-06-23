<?php
namespace App\Http\Middleware;
use App\Models\AdminUser;
use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
class AdminAuthMiddleware {
    public function handle(Request $request, Closure $next) {
        $token = str_replace('Bearer ', '', $request->header('Authorization', ''));
        if (!$token) return response()->json(['message' => 'Unauthorized'], 401);
        try {
            $payload = JWT::decode($token, new Key(env('ADMIN_JWT_SECRET', 'secret'), 'HS256'));
            $request->merge(['admin_user' => (array)$payload]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid token'], 401);
        }
        return $next($request);
    }
}
