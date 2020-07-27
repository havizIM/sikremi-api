<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

use App\Equipment;
use App\Log;

class EquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Equipment::class);

        $search = $request->search;
        $limit = $request->limit;
        $building = $request->building;

        switch(Auth::user()->roles){
            case 'ADMINISTRATOR' :
                $equipment = Equipment::with(['building', 'category', 'procedure'])->get();
            break;

            case 'PARTNER' : 
                if($limit || $search || $building){

                    $equipment = Equipment::whereHas('building.partner', function(Builder $query){
                        $auth = Auth::user()->load('partner_user.partner');
                        $query->where('id', '=', $auth->partner_user->partner->id);
                    })->with(['building', 'category', 'procedure'])->where('building_id', '=', $building)->where(function($query) use($search){
                        $query->where('sku', 'like', '%'.$search.'%')
                              ->orWhere('equipment_name', 'like', '%'.$search.'%');
                    })->limit($limit)->get();
                } else {
                    $equipment = Equipment::whereHas('building.partner', function(Builder $query){
                        $auth = Auth::user()->load('partner_user.partner');
                        $query->where('id', '=', $auth->partner_user->partner->id);
                    })->with(['building', 'category', 'procedure'])->get();
                }
            break;
        }
        
        return response()->json([
            'status' => true,
            'message' => 'Success fetch equipment',
            'results' => $equipment
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
        $this->authorize('create', Equipment::class);

        $validator = Validator::make($request->all(), [
            'sku' => 'required|string',
            'equipment_name' => 'required|string',
            'building_id' => 'required|exists:buildings,id',
            'category_id' => 'required|exists:categories,id',
            'procedure_id' => 'required|exists:procedures,id',
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

            $photo = $request->file('photo');
        
            if($photo){
                $name       = $photo->getClientOriginalName();
                $filename   = pathinfo($name, PATHINFO_FILENAME);
                $extension  = $photo->getClientOriginalExtension();

                $store_as   = $filename.'_'.time().'.'.$extension;

                $photo->storeAs('public/equipment/', $store_as);
            } else {
                $store_as = NULL;
            }

            $equipment = new Equipment;
            $equipment->sku = $request->sku;
            $equipment->equipment_name = $request->equipment_name;
            $equipment->category_id = $request->category_id;
            $equipment->building_id = $request->building_id;
            $equipment->procedure_id = $request->procedure_id;
            $equipment->brand = $request->brand;
            $equipment->type = $request->type;
            $equipment->location = $request->location;
            $equipment->other_information = $request->other_information;
            $equipment->photo = $store_as;
            $equipment->save();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed add equipment',
                'error' => $e->getMessage()
            ], 500);
        }

        try {
            $log = new Log;
            $log->user_id = Auth::id();
            $log->description = 'Add Equipment #'.$equipment->id;
            $log->reference_id = $equipment->id;
            $log->url = '#/partner/equipment/'.$equipment->id;

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
            'message' => 'Success add equipment',
            'results' => $equipment,
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
                $equipment = Equipment::with(['building.partner', 'category', 'procedure', 'preventive_reports', 'corrective_reports'])->findOrFail($id);
            break;

            case 'PARTNER' : 
                $equipment = Equipment::whereHas('building.partner', function(Builder $query){
                    $auth = Auth::user()->load('partner_user.partner');
                    $query->where('id', '=', $auth->partner_user->partner->id);
                })->with(['building', 'category', 'procedure', 'preventive_reports', 'corrective_reports'])->findOrFail($id);
            break;

            case 'ENGINEER' : 
                $equipment = Equipment::with(['building', 'category', 'procedure', 'preventive_reports', 'corrective_reports'])->findOrFail($id);
            break;
        }

        $this->authorize('view', $equipment);
        
        return response()->json([
            'status' => true,
            'message' => 'Success fetch equipment',
            'results' => $equipment
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
        $equipment = Equipment::findOrFail($id);

        $this->authorize('delete', $equipment);

        DB::beginTransaction();

            try {
                $equipment->delete();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed delete equipment',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Delete equipment #'.$equipment->id;
                $log->reference_id = $equipment->id;
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
            'message' => 'Success archive equipment',
            'results' => $equipment,
        ], 200);
    }

    public function picture($filename)
    {
        $path = base_path().'/storage/app/public/equipment/'.$filename;
        return Response::download($path);  
    }
}
