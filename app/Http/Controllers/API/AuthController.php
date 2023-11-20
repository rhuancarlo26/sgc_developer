<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function authenticate(Request $request)
    {
        // Valida Form
        try {
            $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required']]);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        // Valida Autenticação
        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Credenciais Inválidas'], 401);
        }

        // Busca Usuário
        $user = User::where('email', $request->email)->first();

        // Remove Tokens gerados anteriormente
        $user->tokens()->delete();

        $expiresAt = Carbon::now()->addMinutes(config('sanctum.expiration') ?? 60 * 24 * 7);

        // Gera novo token
        $tokenObj = $user->createToken(
            name: "API TOKEN",
            expiresAt: $expiresAt
        );

        // Retorna resposta com dados do token
        return response()->json([
            'message' => 'Usuário Autenticado com sucesso',
            'token' => $tokenObj->plainTextToken,
            'expires_at' => $expiresAt->format('Y-m-d H:i')
        ]);
    }
}
