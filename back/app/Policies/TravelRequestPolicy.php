<?php

namespace App\Policies;

use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TravelRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, TravelRequest $travelRequest): bool
    {
        return $user->is_admin || $user->id === $travelRequest->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function updateStatus(User $user, TravelRequest $travelRequest): bool
    {
        return $user->is_admin;
    }
}