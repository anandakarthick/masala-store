<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public static function getAdminRole(): ?self
    {
        return self::where('slug', 'admin')->first();
    }

    public static function getStaffRole(): ?self
    {
        return self::where('slug', 'staff')->first();
    }

    public static function getCustomerRole(): ?self
    {
        return self::where('slug', 'customer')->first();
    }
}
