<?php

namespace App\Policies;

use App\Models\State;
use App\Models\User;

class StatePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, State $state): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, State $state): bool
    {
        return true;
    }

    public function delete(User $user, State $state): bool
    {
        return true;
    }
}
