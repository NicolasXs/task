<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Actions\User\ListUsersAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of users (Admin only)
     * 
     * @OA\Get(
     *     path="/users",
     *     summary="Listar usuários (Apenas Admin)",
     *     description="Retorna uma lista de todos os usuários do sistema",
     *     operationId="getUsers",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de usuários recuperada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/User"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acesso negado - Apenas administradores",
     *         @OA\JsonContent(@OA\Property(property="message", type="string"))
     *     )
     * )
     */
    public function index(ListUsersAction $listUsersAction)
    {
        Gate::authorize('manage-users');

        $users = $listUsersAction->handle();

        return response()->json([
            'data' => $users
        ]);
    }

    /**
     * Store a newly created user
     * 
     * @OA\Post(
     *     path="/users",
     *     summary="Criar um novo usuário (Apenas Admin)",
     *     description="Cria um novo usuário no sistema",
     *     operationId="createUser",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "role", "password"},
     *             @OA\Property(property="name", type="string", example="João Silva"),
     *             @OA\Property(property="email", type="string", format="email", example="joao@example.com"),
     *             @OA\Property(property="role", type="string", enum={"admin", "user"}, example="user"),
     *             @OA\Property(property="password", type="string", format="password", example="password123", minLength=8)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuário criado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acesso negado - Apenas administradores"
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
    public function store(Request $request)
    {
        Gate::authorize('manage-users');

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|in:admin,user',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'email_verified_at' => now(),
            'password' => Hash::make($request->password),
            'user_type' => $request->role,
            'is_active' => true,
        ]);

        return response()->json($user, 201);
    }

    /**
     * Display the specified user
     * 
     * @OA\Get(
     *     path="/users/{id}",
     *     summary="Obter um usuário específico (Apenas Admin)",
     *     description="Retorna os detalhes de um usuário específico",
     *     operationId="getUser",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do usuário",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário encontrado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acesso negado - Apenas administradores"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuário não encontrado"
     *     )
     * )
     */
    public function show(User $user)
    {
        Gate::authorize('manage-users');

        $user->load(['createdTasks', 'assignedTasks']);

        return response()->json($user);
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        Gate::authorize('manage-users');

        $request->validate([
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'in:admin,user',
        ]);

        $updateData = $request->only(['name', 'email']);

        if ($request->has('role')) {
            $updateData['user_type'] = $request->role;
        }

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8']);
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return response()->json($user);
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        Gate::authorize('manage-users');

        // Não permitir deletar o próprio usuário
        if ($user->id === Auth::id()) {
            return response()->json(['message' => 'You cannot delete yourself'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
