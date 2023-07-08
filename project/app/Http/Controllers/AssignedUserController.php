<?php

namespace App\Http\Controllers;

use App\Models\AssignedUser;
use Illuminate\Http\Request;

class AssignedUserController extends Controller
{
    public function create(Request $request)
    {
        $rules = [
            "user_id" => "required|integer",
            "project_id" => "required|integer",
            "isAdmin" => "required|boolean"
        ];
        $validator = \Validator::make($request->input(), $rules);

        if ($validator->fails()) {
            return response()->json([
                "error" => $validator->errors()->all()
            ], 400);
        }

        $exists = AssignedUser::where([
            "user_id" => $request->user_id,
            "project_id" => $request->project_id
        ])->exists();

        if ($exists) {
            return response()->json([
                "error" => "El usuario ya está asignado a este proyecto"
            ], 400);
        }

        $assignedUser = new AssignedUser([
            "user_id" => $request->user_id,
            "project_id" => $request->project_id,
            "isAdmin" => $request->isAdmin
        ]);
        $assignedUser->save();
        
        return response()->json($assignedUser);
    }

}
