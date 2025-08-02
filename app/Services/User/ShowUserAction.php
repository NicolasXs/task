<?php

namespace App\Services\User;

use App\Models\User;

class ShowUserAction
{
  public function handle(User $user)
  {
    return $user->makeHidden(['password']);
  }
}
