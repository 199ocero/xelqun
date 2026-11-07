<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, User $record): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, User $record): bool
    {
        return true;
    }

    /**
     * Nobody deletes the account they're signed in with.
     */
    public function delete(User $user, User $record): bool
    {
        return $user->isNot($record);
    }
}
