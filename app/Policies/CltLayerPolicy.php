<?php

namespace App\Policies;

use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\User;

class CltLayerPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, CltLayer $layer): bool
    {
        return true;
    }

    public function create(User $user, CltLayup $layup): bool
    {
        return true;
    }

    public function update(User $user, CltLayer $layer): bool
    {
        return true;
    }

    public function delete(User $user, CltLayer $layer): bool
    {
        return true;
    }
}
