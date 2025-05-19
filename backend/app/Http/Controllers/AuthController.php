<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordReset\ForgotPasswordRequest;
use App\Http\Requests\PasswordReset\ResetPasswordRequest;
use App\Mail\PasswordResetMail;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'refresh', 'forgotPassword', 'resetPassword']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Wrong password or email'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $data = $request->validated();
        $token = Str::random(60);

        PasswordReset::create([
            'email' => $data['email'],
            'token' => $token,
            'created_at' => now()
        ]);

        $httpQuery = http_build_query([
            'token' => $token,
            'email' => $data['email'],
        ]);
        $resetLink = config('app.frontend_url') . "/password-reset/?{$httpQuery}";

//        Mail::to($data['email'])->send(new PasswordResetMail($resetLink));

        return response(['data' => true], 200);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $data = $request->validated();

        $passwordReset = PasswordReset::where([
            'email' => $data['email'],
            'token' => $data['token'],
        ])->first();

        if (! $passwordReset) {
            return response(['errors' => ['update' => 'Неверная связка токена и email, попробуйте ещё раз']], 422);
        }

        if (now()->diffInMinutes($passwordReset->created_at) < -30) {
            return response(['errors' => ['update' => 'Истекло время ожидания, попробуйте ещё раз']], 422);
        }

        $user = User::where('email', $data['email'])->first();
        $user->password = Hash::make($data['password']);
        $user->save();

        $passwordReset->delete();
        return response(['data' => true], 200);
    }
}
