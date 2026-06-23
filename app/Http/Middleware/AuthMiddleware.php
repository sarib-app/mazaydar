<?php
namespace App\Http\Middleware;
use App\Models\UserSession;
use Closure;
use Illuminate\Http\Request;
class AuthMiddleware {
    public function handle(Request $request, Closure $next) {
        $token = str_replace('Bearer ', '', $request->header('Authorization', ''));
        if (!$token) return response()->json(['message' => 'Unauthorized'], 401);
        $session = UserSession::where('token', $token)->first();
        if (!$session) return response()->json(['message' => 'Unauthorized'], 401);
        $request->merge(['auth_user_id' => $session->user_id]);
        return $next($request);
    }
}
