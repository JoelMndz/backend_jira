<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    protected $fillable = ["name","code"];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class,'assigned_users')->withPivot('isAdmin');
    }
}
