<?php

namespace App\Policies;

use App\Models\StoreSetting;
use App\Models\User;

class StoreSettingPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function update(User $user, StoreSetting $storeSetting): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }
}
