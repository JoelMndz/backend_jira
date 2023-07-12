<?php

namespace App\Http\Controllers;

use App\Models\AssignedUser;
use App\Models\Project;
use App\Models\Sprint;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SprintController extends Controller
{
    // Obtener sprints de proyecto por codigo
    public function getAll( $code )
    {
        try {
            $haveAccess = Project::with('users')
                ->where('code', $code)
                ->whereHas('users', function ($query) {
                    $query->where('users.id', Auth::id());
                })
                ->where('status', true)
                ->firstOrFail();

            $sprints = Sprint::where('project_id', $haveAccess->id)
                ->where('is_delete', false)
                ->get();

            return response()->json([
                'message'=>'Sprints obtenidos correctamente',
                'sprints'=> $sprints,
            ], 200);
        } catch (\Exception $e) {
            return response([
                'message'=>'Proyecto no encontrado',
                'sprints'=>[]
            ], 404);
        }
    }
    
    /**
     * Crear nuevo sprint
     */
    public function create(Request $request)
    {        
        $rules = [
            'project_id' => 'required|integer',
            'name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date'
        ];

        $validate = \Validator::make($request->input(), $rules);

        if($validate->fails()){
            return Response(['errors'=>$validate->messages()], 500);
        }
        $newSprint = new Sprint(array_merge($request->input(), ['status' => 3]));
        try{
            $newSprint->save();
            return response()->json([
                'message'=> 'Sprint creado correctamente',
                'sprint'=> $newSprint
            ], 200);
        }catch(\Exception $e){
            Log::error('Error al crear un nuevo sprint: '. $e );
            return response()->json([
                "message" => "Ha ocurrido un error al guardar el sprint",
                'error'=> $e
            ], 422);    
        }                       
    }

    /**
     * Actualizar sprint (no incluye status)
     */
    public function update(Request $request, Sprint $sprint)
    {
        $rules = [
            'project_id' => 'required|integer',
            'name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ];

        $validate = \Validator::make($request->input(), $rules);

        if($validate->fails()){
            return Response(['errors'=>$validate->messages()], 500);
        }

        $isAdmin = AssignedUser::where('user_id', Auth::id())
            ->where('project_id', $request->project_id)
            ->where('isAdmin', true)
            ->first()?? false;

        if(!$isAdmin){
            return response()->json([
                "message" => "No tienes permisos para realizar esta acción."
            ], 401);
        }

        $sprint->update([
            'project_id'=>$request->project_id,
            'name'=>$request->name,
            'start_date'=>$request->start_date,
            'end_date'=>$request->end_date,
        ]);
        return response()->json([
            'message'=>"Cambios aplicados correctamente",
            "sprint"=>$sprint
        ]);
    }

    public function updateStatus(Request $request, Sprint $sprint){
        $rules = [
            'status'=>'required',
            'project_id'=>'required|integer',
        ];
        
        $validate = \Validator::make($request->input(), $rules);

        if($validate->fails()){
            return Response(['errors'=>$validate->messages()], 500);
        }

        $isAdmin = AssignedUser::where('user_id', Auth::id())
            ->where('project_id', $request->project_id)
            ->where('isAdmin', true)
            ->first()?? false;

        if(!$isAdmin){
            return response()->json([
                "message" => "No tienes permisos para realizar esta acción."
            ], 401);
        }

        $sprint->update([
            'status'=>$request->status
        ]);
        return response()->json([
            'message'=>"Cambios aplicados correctamente",
            "sprint"=>$sprint
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sprint $sprint)
    {
        $isAdmin = AssignedUser::where('user_id', Auth::id())
            ->where('project_id', $sprint->project_id)
            ->where('isAdmin', true)
            ->first()?? false;

        if(!$isAdmin){
            return response()->json([
                "message" => "No tienes permisos para realizar esta acción"
            ], 401);
        }

        $sprint->update([
            'is_delete'=>true
        ]);
        return response()->json([
            'message'=>"Sprint eliminado correctamente",
            "sprint"=>$sprint
        ]);
    }
}
