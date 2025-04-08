<?php

namespace App\Policies;

use App\Models\Conversion;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConversionPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Conversion $conversion)
    {
        return $user->id === $conversion->user_id;
    }

    public function create(User $user)
    {
        return $user->remaining_daily_conversions > 0;
    }
}