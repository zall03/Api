<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\User;
use App\Services\FirebaseTokenVerifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private function generateAndSendOtp(User $user): void
    {
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(new OtpMail($otp, $user->name));
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $this->generateAndSendOtp($user);

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil. Kode OTP telah dikirim ke email Anda.',
            'data' => ['email' => $user->email],
        ], 201);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp_code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Akun tidak ditemukan'], 404);
        }

        if ($user->otp_code !== $request->otp_code) {
            return response()->json(['success' => false, 'message' => 'Kode OTP salah'], 422);
        }

        if (now()->greaterThan($user->otp_expires_at)) {
            return response()->json(['success' => false, 'message' => 'Kode OTP sudah kedaluwarsa'], 422);
        }

        $user->update([
            'email_verified_at' => now(),
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        $token = $user->createToken('mobile_app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Email berhasil diverifikasi',
            'data' => ['user' => $user, 'token' => $token],
        ]);
    }

    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), ['email' => 'required|email']);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Akun tidak ditemukan'], 404);
        }

        if ($user->email_verified_at) {
            return response()->json(['success' => false, 'message' => 'Email sudah terverifikasi'], 422);
        }

        $this->generateAndSendOtp($user);

        return response()->json(['success' => true, 'message' => 'Kode OTP baru telah dikirim']);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Email atau kata sandi salah'], 401);
        }

        if (!$user->email_verified_at) {
            $this->generateAndSendOtp($user);
            return response()->json([
                'success' => false,
                'message' => 'Email belum diverifikasi. Kode OTP baru telah dikirim.',
                'requires_verification' => true,
                'data' => ['email' => $user->email],
            ], 403);
        }

        $token = $user->createToken('mobile_app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => ['user' => $user, 'token' => $token],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['success' => true, 'message' => 'Logout berhasil']);
    }

    public function googleLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $payload = app(FirebaseTokenVerifier::class)->verify($request->id_token);

        if (!$payload) {
            return response()->json(['success' => false, 'message' => 'Token Google tidak valid'], 401);
        }

        $email = $payload['email'] ?? null;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['success' => false, 'message' => 'Akun Google tidak memiliki email'], 422);
        }

        if (($payload['email_verified'] ?? false) !== true) {
            return response()->json(['success' => false, 'message' => 'Email Google belum terverifikasi'], 403);
        }

        $name = $payload['name'] ?? strstr($email, '@', true);

        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make(Str::random(32)),
                'email_verified_at' => now(),
            ]);
        }

        $token = $user->createToken('mobile_app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login dengan Google berhasil',
            'data' => ['user' => $user, 'token' => $token],
        ]);
    }

    public function me(Request $request)
    {
        return response()->json(['success' => true, 'data' => $request->user()]);
    }
}