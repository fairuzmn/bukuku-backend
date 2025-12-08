<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Utils\ResponseUtils;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Mail\OtpMail;
use Exception;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $data = [
                'user' => $user,
                'token' => $user->createToken('auth_token')->plainTextToken
            ];

            return ResponseUtils::baseResponse(200, 'User registered successfully', $data);
        } catch (UniqueConstraintViolationException $e) {
            return ResponseUtils::baseResponse(409, 'Email is already in use. Please login or use a different email.');
        } catch (\Exception $e) {
            return ResponseUtils::errorResponse($e, 500);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            if (!Auth::attempt($request->only('email', 'password'))) {
                return ResponseUtils::errorResponse('Invalid credentials', 401);
            }

            $user = User::where('email', $request->email)->firstOrFail();

            $otp = rand(100000, 999999);
            $user->update([
                'otp_code' => $otp,
                'otp_expires_at' => Carbon::now()->addMinutes(5),
            ]);

            Log::info("OTP for {$user->email}: {$otp}");
            
            Mail::to($user->email)->send(new OtpMail($otp));

            return ResponseUtils::baseResponse(200, 'OTP sent to your email. Please verify.');
        } catch (Exception $e) {
            return ResponseUtils::errorResponse($e, 500);
        }
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();

            if ($user->otp_code !== $request->otp) {
                return ResponseUtils::errorResponse('Invalid OTP', 400);
            }

            if ($user->otp_expires_at->isPast()) {
                return ResponseUtils::errorResponse('OTP has expired', 400);
            }

            $user->update([
                'otp_code' => null,
                'otp_expires_at' => null
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return ResponseUtils::baseResponse(200, 'Login successful', [
                'user' => $user,
                'token' => $token
            ]);
        } catch (Exception $e) {
            return ResponseUtils::errorResponse($e, 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return ResponseUtils::baseResponse(200, 'Logged out successfully');
        } catch (\Exception $e) {
            return ResponseUtils::errorResponse($e, 500);
        }
    }
}
