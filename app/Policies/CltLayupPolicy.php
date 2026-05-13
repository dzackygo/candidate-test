<?php

namespace App\Policies;

use App\Models\CltLayup;
use App\Models\Supplier;
use App\Models\User;

class CltLayupPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, CltLayup $layup): bool
    {
        return true;
    }

    public function create(User $user, Supplier $supplier): bool
    {
        return true;
    }

    public function update(User $user, CltLayup $layup): bool
    {
        return true;
    }

    public function delete(User $user, CltLayup $layup): bool
    {
        return true;
    }
}
