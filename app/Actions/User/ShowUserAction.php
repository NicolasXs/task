<?php

namespace App\Actions\User;

use App\Models\User;

class ShowUserAction
{
  public function handle(User $user)
  {
    return $user->makeHidden(['password']);
  }
}
