<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Epics extends Model
{
    //use HasFactory;
    protected $fillable = ["name","description","state_id","project_id"];
}
