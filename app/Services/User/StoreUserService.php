<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StoreUserService
{
  public function handle(Request $request)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:8',
      'user_type' => ['required', Rule::in(['admin', 'user'])],
    ]);

    $validated['password'] = Hash::make($validated['password']);
    $validated['email_verified_at'] = now();

    $user = User::create($validated);
    return $user;
  }
}
