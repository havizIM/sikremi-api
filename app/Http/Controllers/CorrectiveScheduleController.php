<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

use App\Schedule;
use App\Team;
use App\CorrectiveSchedule;
use App\Log;

use Illuminate\Support\Facades\DB;

class CorrectiveScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', Schedule::class);

        switch(Auth::user()->roles){
            case 'ADMINISTRATOR' :
                $schedules = Schedule::with(['building.partner', 'corrective.work_order', 'corrective_report'])->where('type', '=', 'Corrective')->get();
            break;

            case 'PARTNER' : 
                $schedules = Schedule::whereHas('building.partner', function(Builder $query){
                    $auth = Auth::user()->load('partner_user.partner');
                    $query->where('id', '=', $auth->partner_user->partner->id);
                })->with(['building', 'corrective.work_order'])->where('type', '=', 'Corrective')->get();
            break;

            case 'ENGINEER' : 
                $schedules = Schedule::whereHas('teams.engineer', function(Builder $query){
                    $auth = Auth::user()->load('engineer');
                    $query->where('id', '=', $auth->engineer->id);
                })->with(['building.partner', 'corrective.work_order', 'corrective_report'])->where('type', '=', 'Corrective')->get();
            break;
        }
        
        return response()->json([
            'status' => true,
            'message' => 'Success fetch corrective schedules',
            'results' => $schedules
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
        $this->authorize('create', Schedule::class);

        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d',
            'building_id' => 'required|exists:buildings,id',
            'work_order_id' => 'required|exists:work_orders,id',
            'estimate' => 'required|string',
            'engineer' => 'required|array',
            'engineer.*' => 'required|string',
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
            $schedule = new Schedule;
            $schedule->building_id = $request->building_id;
            $schedule->date = $request->date;
            $schedule->time = $request->time;
            $schedule->estimate = $request->estimate;
            $schedule->shift = $request->shift;
            $schedule->description = $request->description;
            $schedule->type = 'Corrective';
            $schedule->submit = 'Y';
            $schedule->save();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed add corrective schedule',
                'error' => $e->getMessage()
            ], 500);
        }

        try {
            $corrective_schedule = new CorrectiveSchedule;
            $corrective_schedule->schedule_id = $schedule->id;
            $corrective_schedule->work_order_id = $request->work_order_id;
            $corrective_schedule->save();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed add corrective schedule',
                'error' => $e->getMessage()
            ], 500);
        }

        foreach($request->engineer as $key => $val){
            try {
                $team = new Team;
                $team->schedule_id = $schedule->id;
                $team->engineer_id = $val;
                $team->save();
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' => 'Failed add teams',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        try {
            $log = new Log;
            $log->user_id = Auth::id();
            $log->description = 'Add Corrective Schedule #'.$schedule->id;
            $log->reference_id = $schedule->id;
            $log->url = '#/corrective_schedule/'.$schedule->id;

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
            'message' => 'Success add schedule',
            'results' => $schedule,
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
                $schedules = Schedule::with(['building.partner', 'corrective.work_order.equipment.category', 'teams.engineer' , 'corrective_report'])->where('type', '=', 'Corrective')->findOrFail($id);
            break;

            case 'PARTNER' : 
                $schedules = Schedule::whereHas('building.partner', function(Builder $query){
                    $auth = Auth::user()->load('partner_user.partner');
                    $query->where('id', '=', $auth->partner_user->partner->id);
                })->with(['building', 'corrective.work_order.equipment.category', 'teams.engineer'])->where('type', '=', 'Corrective')->findOrFail($id);
            break;

            case 'ENGINEER' : 
                $schedules = Schedule::whereHas('teams.engineer', function(Builder $query){
                    $auth = Auth::user()->load('engineer');
                    $query->where('id', '=', $auth->engineer->id);
                })->with(['building.partner', 'corrective.work_order.equipment.category', 'teams.engineer', 'corrective_report'])->where('type', '=', 'Corrective')->findOrFail($id);
            break;
        }

        $this->authorize('view', $schedules);
        
        return response()->json([
            'status' => true,
            'message' => 'Success fetch corrective schedules',
            'results' => $schedules
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
        $corrective_schedule = Schedule::findOrFail($id);

        $this->authorize('delete', $corrective_schedule);

        DB::beginTransaction();

            try {
                $corrective_schedule->delete();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed delete corrective schedule',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Delete Corrective Schedule #'.$corrective_schedule->id;
                $log->reference_id = $corrective_schedule->id;
                $log->url = '#/corrective_schedule';

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
            'message' => 'Success archive corrective schedule',
            'results' => $corrective_schedule,
        ], 200);
    }
}
