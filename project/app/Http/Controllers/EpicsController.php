<?php

namespace App\Http\Controllers;

use App\Models\AssignedUser;
use App\Models\Epics;
use App\Models\Project;
use Auth;
use Illuminate\Http\Request;

class EpicsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getAll(Project $project)
    {
        try {
            $projectValid = Project::with('users')
                ->where('id', $project->id)
                ->where('status', true)
                ->whereHas('users', function($query){
                    $query->where('users.id', Auth::id());
                })
                ->first()??false;

            if(!$projectValid){
                return response()->json([
                    'message'=>'El proyecto no existe',
                    'error'=>[]
                ], 404);
            }

            $epics = Epics::where('project_id', $project->id)
                ->get();

            return response()->json([
                'message'=>'Epics obtenidos correctamente',
                'data'=>$epics
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message'=>'Error al obtener las epics del proyecto especificado',
                'error'=>$e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function create(Request $request, Project $project)
    {
        $validate = \Validator::make($request->input(), [
            'name'=>'required|string'
        ]);

        if($validate->fails()){
            return response(['error'=>$validate->messages()], 400);
        }
        try {
            Project::with('users')
                ->where('id', $project->id)
                ->where('status', true)
                ->whereHas('users', function($query){
                    $query->where('users.id', Auth::id());
                })
                ->firstOrFail();
            
            try {                
                AssignedUser::where('user_id', Auth::id())
                    ->where('project_id', $project->id)
                    ->where('isAdmin', true)
                    ->firstOrFail();
                    
            } catch (\Throwable $e) {
                return response()->json([
                    'message' =>'El usuario no contiene los permisos para realizar esta acción',
                    'error'=>$e->getMessage()
                ]);
            }

            $newEpic = new Epics(array_merge($request->input(), [
                'state_id'=>1,
                'project_id'=>$project->id
            ]));

            try {
                $newEpic->save();
                return response()->json([
                    'message'=>'Epic guardado correctamente',
                    'data'=>$newEpic
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'No se pudo guardar el objeto Epics',
                    'error' => $e->getMessage()
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'No se pudo verificar el acceso al proyecto',
                'error' => $e->getMessage()
            ], 500);
        }       
    }

    /**
     * Display the specified resource.
     */
    public function show(Epics $epics)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Epics $epics)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Epics $epics)
    {
        //
    }
}
