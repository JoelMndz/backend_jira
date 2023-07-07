<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStories extends Model
{
    //use HasFactory;
    protected $fillable = ["name","description","points","epic_id","state_id"];
}
