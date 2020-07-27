<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

use App\WorkOrder;
use App\WorkOrderPhoto;
use App\Log;


use Help;

class WorkOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', WorkOrder::class);

        switch(Auth::user()->roles){
            case 'ADMINISTRATOR' :
                $work_order = WorkOrder::with(['building', 'equipment', 'photos', 'schedule.corrective_report'])->get();
            break;

            case 'PARTNER' : 
                $work_order = WorkOrder::whereHas('building.partner', function(Builder $query){
                    $auth = Auth::user()->load('partner_user.partner');
                    $query->where('id', '=', $auth->partner_user->partner->id);
                })->with(['building', 'equipment', 'photos', 'schedule.corrective_report'])->get();
            break;
        }
        
        
        return response()->json([
            'status' => true,
            'message' => 'Success fetch work order',
            'results' => $work_order
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
        $this->authorize('create', WorkOrder::class);

        $validator = Validator::make($request->all(), [
            'building_id' => 'required|exists:buildings,id',
            'date' => 'required|date_format:Y-m-d',
            'description' => 'required|string',
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
            $work_order = new WorkOrder;
            $work_order->building_id = $request->building_id;
            $work_order->equipment_id = $request->equipment_id;
            $work_order->date = $request->date;
            $work_order->wo_number = Help::dateCode('WO', 'work_orders', 'wo_number');
            $work_order->description = $request->description;
            $work_order->save();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed add work order',
                'error' => $e->getMessage()
            ], 500);
        }
        
        $photo = $request->file('photo');

        foreach($request['photo'] as $key => $val){
            try {
                
                $name       = $photo[$key]->getClientOriginalName();
                $filename   = pathinfo($name, PATHINFO_FILENAME);
                $extension  = $photo[$key]->getClientOriginalExtension();
                $store_as   = $filename.'_'.time().'.'.$extension;
                $photo[$key]->storeAs('public/work_order/'.$work_order->id.'/', $store_as);

                $work_order_photo = new WorkOrderPhoto;
                $work_order_photo->work_order_id = $work_order->id;
                $work_order_photo->photo = $store_as;
                $work_order_photo->save();
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' => 'Failed add work order photo',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        try {
            $log = new Log;
            $log->user_id = Auth::id();
            $log->description = 'Add Work Order #'.$work_order->id;
            $log->reference_id = $work_order->id;
            $log->url = '#/work_order/'.$work_order->id;

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
            'message' => 'Success add work order',
            'results' => $work_order,
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
                $work_order = WorkOrder::with(['building.partner', 'equipment', 'photos', 'schedule.corrective_report'])->findOrFail($id);
            break;

            case 'PARTNER' : 
                $work_order = WorkOrder::whereHas('building.partner', function(Builder $query){
                    $auth = Auth::user()->load('partner_user.partner');
                    $query->where('id', '=', $auth->partner_user->partner->id);
                })->with(['building.partner', 'equipment', 'photos', 'schedule.corrective_report'])->findOrFail($id);
            break;

             case 'ENGINEER' :
                $work_order = WorkOrder::with(['building.partner', 'equipment', 'photos', 'schedule.corrective_report'])->findOrFail($id);
            break;
        }

        $this->authorize('view', $work_order);

        return response()->json([
            'status' => true,
            'message' => 'Success fetch specific work order',
            'results' => $work_order
        ], 200);
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
        $work_order = WorkOrder::findOrFail($id);

        $this->authorize('delete', $work_order);

        DB::beginTransaction();

            try {
                $work_order->delete();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed delete work_order',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Delete Work Order #'.$work_order->id;
                $log->reference_id = $work_order->id;
                $log->url = '#/work_order';

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
            'message' => 'Success archive work order',
            'results' => $work_order,
        ], 200);
    }

    public function picture($id, $filename)
    {
        $path = base_path().'/storage/app/public/work_order/'.$id.'/'.$filename;
        return Response::download($path); 
    }
}
