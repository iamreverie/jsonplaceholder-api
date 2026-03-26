<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email'       => ['required', 'email'],
            'password'    => ['required'],
            'device_name' => ['sometimes', 'string', 'max:255'],
        ]);

        if (! Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user        = $request->user();
        $deviceName  = $request->input('device_name', $request->userAgent() ?? 'unknown');
        $token       = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'token'      => $token,
            'token_type' => 'Bearer',
        ]);
    }
    
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Token revoked.']);
    }
}