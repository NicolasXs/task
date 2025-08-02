<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DeleteUserAction
{
  public function handle(User $user)
  {
    if ($user->id === Auth::id()) {
      return ['message' => 'You cannot delete your own account', 'forbidden' => true];
    }
    $user->delete();
    return ['message' => 'User deleted successfully'];
  }
}
