<?php

namespace App\Policies;

use App\Models\Petani;
use App\Models\User;

class PetaniPolicy
{
    public function create(User $user): bool
    {
        return $user->tokenCan('petani:write');
    }

    public function update(User $user, Petani $petani): bool
    {
        return $user->tokenCan('petani:write');
    }

    public function delete(User $user, Petani $petani): bool
    {
        return $user->tokenCan('petani:write');
    }
}
