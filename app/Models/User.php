<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function conversions()
    {
        return $this->hasMany(Conversion::class);
    }

    public function getRemainingDailyConversionsAttribute()
    {
        // Free tier: 5 conversions per day
        $maxDailyConversions = 5;
        
        $todayConversions = $this->conversions()
            ->whereDate('created_at', now()->toDateString())
            ->count();
        
        return max(0, $maxDailyConversions - $todayConversions);
    }
}