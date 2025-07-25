<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
  /**
   * Handle an incoming authentication request.
   * 
   * @OA\Post(
   *     path="/auth/login",
   *     summary="Fazer login",
   *     description="Autentica um usuário e retorna um token de acesso",
   *     operationId="login",
   *     tags={"Authentication"},
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(
   *             required={"email", "password"},
   *             @OA\Property(property="email", type="string", format="email", example="admin@example.com"),
   *             @OA\Property(property="password", type="string", format="password", example="password")
   *         )
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Login realizado com sucesso",
   *         @OA\JsonContent(
   *             @OA\Property(property="access_token", type="string", example="1|token_example"),
   *             @OA\Property(property="token_type", type="string", example="Bearer"),
   *             @OA\Property(property="user", ref="#/components/schemas/User")
   *         )
   *     ),
   *     @OA\Response(
   *         response=422,
   *         description="Credenciais inválidas",
   *         @OA\JsonContent(
   *             @OA\Property(property="message", type="string", example="The provided credentials are incorrect."),
   *             @OA\Property(property="errors", type="object",
   *                 @OA\Property(property="email", type="array", @OA\Items(type="string"))
   *             )
   *         )
   *     )
   * )
   */
  public function login(Request $request): JsonResponse
  {
    $request->validate([
      'email' => 'required|email',
      'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
      throw ValidationException::withMessages([
        'email' => ['The provided credentials are incorrect.'],
      ]);
    }

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
      'access_token' => $token,
      'token_type' => 'Bearer',
      'user' => $user
    ]);
  }

  /**
   * Handle an incoming registration request.
   * 
   * @OA\Post(
   *     path="/auth/register",
   *     summary="Registrar novo usuário",
   *     description="Cria uma nova conta de usuário",
   *     operationId="register",
   *     tags={"Authentication"},
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(
   *             required={"name", "email", "password", "password_confirmation"},
   *             @OA\Property(property="name", type="string", example="João Silva"),
   *             @OA\Property(property="email", type="string", format="email", example="joao@example.com"),
   *             @OA\Property(property="password", type="string", format="password", example="password123", minLength=8),
   *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
   *         )
   *     ),
   *     @OA\Response(
   *         response=201,
   *         description="Usuário registrado com sucesso",
   *         @OA\JsonContent(
   *             @OA\Property(property="access_token", type="string", example="1|token_example"),
   *             @OA\Property(property="token_type", type="string", example="Bearer"),
   *             @OA\Property(property="user", ref="#/components/schemas/User")
   *         )
   *     ),
   *     @OA\Response(
   *         response=422,
   *         description="Erro de validação",
   *         @OA\JsonContent(
   *             @OA\Property(property="message", type="string"),
   *             @OA\Property(property="errors", type="object")
   *         )
   *     )
   * )
   */
  public function register(Request $request): JsonResponse
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:8|confirmed',
    ]);

    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
      'user_type' => 'user', // Default to regular user
      'is_active' => true,
    ]);

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
      'access_token' => $token,
      'token_type' => 'Bearer',
      'user' => $user
    ], 201);
  }

  /**
   * Destroy an authenticated session (logout).
   * 
   * @OA\Post(
   *     path="/auth/logout",
   *     summary="Fazer logout",
   *     description="Revoga o token de acesso do usuário autenticado",
   *     operationId="logout",
   *     tags={"Authentication"},
   *     security={{"bearerAuth":{}}},
   *     @OA\Response(
   *         response=200,
   *         description="Logout realizado com sucesso",
   *         @OA\JsonContent(
   *             @OA\Property(property="message", type="string", example="Successfully logged out")
   *         )
   *     ),
   *     @OA\Response(
   *         response=401,
   *         description="Não autorizado",
   *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated"))
   *     )
   * )
   */
  public function logout(Request $request): JsonResponse
  {
    $request->user()->currentAccessToken()->delete();

    return response()->json([
      'message' => 'Successfully logged out'
    ]);
  }

  /**
   * Get the authenticated user.
   * 
   * @OA\Get(
   *     path="/auth/me",
   *     summary="Obter perfil do usuário",
   *     description="Retorna os dados do usuário autenticado",
   *     operationId="me",
   *     tags={"Authentication"},
   *     security={{"bearerAuth":{}}},
   *     @OA\Response(
   *         response=200,
   *         description="Dados do usuário recuperados com sucesso",
   *         @OA\JsonContent(ref="#/components/schemas/User")
   *     ),
   *     @OA\Response(
   *         response=401,
   *         description="Não autorizado",
   *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated"))
   *     )
   * )
   */
  public function me(Request $request): JsonResponse
  {
    return response()->json($request->user());
  }
}
