<?php

namespace App\Services\User;

use App\Models\User;

class ShowUserService
{
  public function handle(User $user)
  {
    return $user->makeHidden(['password']);
  }
}
