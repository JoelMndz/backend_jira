<?php

namespace App\Http\Controllers;

use App\Models\AssignedUser;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function getAll()
    {
        $user_id = Auth::id();
        $data = Project::with('users')
        ->whereHas('users', function ($query) use ($user_id) {
            $query->where('users.id', $user_id);
        })
        ->where("status",true)
        ->get();
        return response()->json($data);
    }

    public function create(Request $request)
    {
        $rules = [
            "name" => "required|string|min:1|max:100",
            "code" => "required|string|min:1|max:100|unique:projects"
        ];
        $validator = \Validator::make($request->input(), $rules);

        if ($validator->fails()) {
            return response()->json([
                "error" => $validator->errors()->all()
            ], 400);
        }
        $project = new Project($request->input());
        $project->save();
        $assignedUser = new AssignedUser([
            "isAdmin"=> true,
            "user_id"=> Auth::id(),
            "project_id"=> $project->id
        ]);
        $assignedUser->save();
        return response()->json($project);
    }

    public function getById(string $id)
    {
        $data = Project::where('id', $id)->get();
        return response()->json($data);
    }

    public function update(Request $request, Project $project)
    {
        $rules = [
            "name" => "required|string|min:1|max:100"
        ];
        if($project->code != $request->code){
            $rules["code"] = "required|string|min:1|max:100|unique:projects";
        }
        $validator = \Validator::make($request->input(), $rules);

        if ($validator->fails()) {
            return response()->json([
                "error" => $validator->errors()->all()
            ], 400);
        }
        $userValid = AssignedUser::
            where("user_id", Auth::id())
            ->where("project_id", $project->id)
            ->where("isAdmin", true)
            ->first();
        if (!$userValid) {
            return response()->json([
                "error" => "Acción no permitida"
            ], 400);
        }
        $project->update([
            "id"=> $project->id,
            "name"=> $request->name,
            "code"=> $request->code
        ]);
        return response()->json($project);
    }

    public function delete(Project $project)
    {
        $project->update([
            "id"=> $project->id,
            "status" => false
        ]);
        return response()->json($project);
    }
}
