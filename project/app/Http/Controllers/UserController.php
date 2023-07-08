<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
  
  public function getAll()
  {
    $data = User::all();
    return response()->json($data);
  }
}
