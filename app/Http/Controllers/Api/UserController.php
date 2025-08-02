<?php

namespace App\Http\Controllers\Api;

use App\Services\User\DeleteUserAction;
use App\Services\User\ListUsersAction;
use App\Services\User\ShowUserAction;
use App\Services\User\StoreUserAction;
use App\Services\User\UpdateUserAction;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
  use AuthorizesRequests;

  /**
   * Display a listing of the resource.
   */
  public function index(): JsonResponse
  {
    $users = app(ListUsersAction::class)->handle();
    return response()->json($users);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request): JsonResponse
  {
    $user = app(StoreUserAction::class)->handle($request);
    return response()->json($user, 201);
  }

  /**
   * Display the specified resource.
   */
  public function show(User $user): JsonResponse
  {
    $user = app(ShowUserAction::class)->handle($user);
    return response()->json($user);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, User $user): JsonResponse
  {
    $user = app(UpdateUserAction::class)->handle($request, $user);
    return response()->json($user);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(User $user): JsonResponse
  {
    $result = app(DeleteUserAction::class)->handle($user);
    if (!empty($result['forbidden'])) {
      return response()->json(['message' => $result['message']], 403);
    }
    return response()->json(['message' => $result['message']]);
  }
}
