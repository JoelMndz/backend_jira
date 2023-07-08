<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sprint extends Model
{
    //use HasFactory;

    protected $fillable = ["start_date","end_date","status","project_id"];
}
