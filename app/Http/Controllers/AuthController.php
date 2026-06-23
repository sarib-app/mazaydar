<?php
namespace App\Http\Controllers;
use App\Models\{Otp, User, UserSession};
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthController extends Controller {

    public function sendOtp(Request $request) {
        $phone = $request->input('phone');
        if (!$phone) return response()->json(['message' => 'Phone required'], 400);
        Otp::where('phone', $phone)->delete();
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        Otp::create(['phone' => $phone, 'code' => $code, 'expires_at' => Carbon::now()->addMinutes(5)]);
        return response()->json(['message' => 'OTP sent', 'dev_code' => $code]);
    }

    public function verifyOtp(Request $request) {
        $phone = $request->input('phone');
        $code  = $request->input('code');
        $otp   = Otp::where('phone', $phone)->where('code', $code)->where('expires_at', '>', now())->first();
        if (!$otp) return response()->json(['message' => 'Invalid or expired OTP'], 400);
        $otp->delete();
        $user = User::firstOrCreate(['phone' => $phone], ['id' => Str::uuid()]);
        $token = bin2hex(random_bytes(32));
        UserSession::create(['user_id' => $user->id, 'token' => $token]);
        return response()->json(['token' => $token, 'user' => $user]);
    }

    public function me(Request $request) {
        return response()->json(User::find($request->auth_user_id));
    }

    public function updateProfile(Request $request) {
        $user = User::find($request->auth_user_id);
        $user->update($request->only(['name','email','age','gender','height','weight','goal','preferences']));
        return response()->json($user->fresh());
    }

    public function logout(Request $request) {
        $token = str_replace('Bearer ', '', $request->header('Authorization', ''));
        UserSession::where('token', $token)->delete();
        return response()->json(['message' => 'Logged out']);
    }
}
