<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignedUser extends Model
{
    //use HasFactory;
    protected $fillable = ["isAdmin","user_id","project_id"];
}
