<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

use App\Procedure;
use App\PreventiveProcedure;
use App\Log;

class ProcedureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', Procedure::class);
        
        switch(Auth::user()->roles){
            case 'ADMINISTRATOR' :

            break;

            case 'PARTNER' : 
                $procedure = Procedure::whereHas('partner', function(Builder $query){
                    $auth = Auth::user()->load('partner_user.partner');
                    $query->where('id', '=', $auth->partner_user->partner->id);
                })->withCount(['preventive_procedures'])->get();
            break;
        }
        
        return response()->json([
            'status' => true,
            'message' => 'Success fetch procedure',
            'results' => $procedure
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Procedure::class);

        $validator = Validator::make($request->all(), [
            'identifier_name' => 'required|string',
            'partner_id' => 'required|exists:partners,id',
            'description' => 'required|array',
            'description.*' => 'required|string',
            'periode' => 'required|array',
            'periode.*' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Fields Required',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $procedure = new Procedure;
            $procedure->partner_id = $request->partner_id;
            $procedure->identifier_name = $request->identifier_name;
            $procedure->type = $request->type;
            $procedure->other_information = $request->other_information;
            $procedure->save();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed add procedure',
                'error' => $e->getMessage()
            ], 500);
        }

        foreach($request['description'] as $key => $val){
            try {
                $preventive_procedure = new PreventiveProcedure;
                $preventive_procedure->procedure_id = $procedure->id;
                $preventive_procedure->description = $request['description'][$key];
                $preventive_procedure->periode = $request['periode'][$key];
                $preventive_procedure->tools = $request['tools'][$key];
                $preventive_procedure->save();
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' => 'Failed add preventive procedure',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        try {
            $log = new Log;
            $log->user_id = Auth::id();
            $log->description = 'Add Procedure #'.$procedure->id;
            $log->reference_id = $procedure->id;
            $log->url = '#/partner/procedure/'.$procedure->id;

            $log->save();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed add log',
                'error' => $e->getMessage()
            ], 500);
        }

        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'Success add procedure',
            'results' => $procedure,
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        switch(Auth::user()->roles){
            case 'ADMINISTRATOR' :
                $procedure = Procedure::with(['partner', 'preventive_procedures'])->findOrFail($id);
            break;

            case 'PARTNER' : 
                $procedure = Procedure::whereHas('partner', function(Builder $query){
                    $auth = Auth::user()->load('partner_user.partner');
                    $query->where('id', '=', $auth->partner_user->partner->id);
                })->with(['preventive_procedures'])->findOrFail($id);
            break;

            case 'ENGINEER' : 
                $procedure = Procedure::with(['preventive_procedures'])->findOrFail($id);
            break;
        }

        $this->authorize('view', $procedure);
        
        return response()->json([
            'status' => true,
            'message' => 'Success fetch procedure',
            'results' => $procedure
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $procedure = Procedure::findOrFail($id);

        $this->authorize('delete', $procedure);

        DB::beginTransaction();

            try {
                $procedure->delete();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed delete procedure',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Delete Procedure #'.$procedure->id;
                $log->reference_id = $procedure->id;
                $log->url = '#/partner';

                $log->save();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed add log',
                    'error' => $e->getMessage()
                ], 500);
            }
        
        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'Success archive procedure',
            'results' => $procedure,
        ], 200);
    }
}
