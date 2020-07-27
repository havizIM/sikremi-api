<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

use App\Schedule;
use App\Team;
use App\PreventiveSchedule;
use App\Log;

use Illuminate\Support\Facades\DB;

class PreventiveScheduleController extends Controller
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
                $schedules = Schedule::with(['building', 'preventives.equipment.category', 'preventive_reports'])->where('type', '=', 'Preventive')->get();
            break;

            case 'PARTNER' : 
                $schedules = Schedule::whereHas('building.partner', function(Builder $query){
                    $auth = Auth::user()->load('partner_user.partner');
                    $query->where('id', '=', $auth->partner_user->partner->id);
                })->with(['building'])->where('type', '=', 'Preventive')->get();
            break;

            case 'ENGINEER' : 
                $schedules = Schedule::whereHas('teams.engineer', function(Builder $query){
                    $auth = Auth::user()->load('engineer');
                    $query->where('id', '=', $auth->engineer->id);
                })->with(['building'])->where('type', '=', 'Preventive')->get();
            break;
        }
        
        
        
        return response()->json([
            'status' => true,
            'message' => 'Success fetch preventive schedules',
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
            'estimate' => 'required|string',
            'engineer' => 'required|array',
            'engineer.*' => 'required|string',
            'equipment' => 'required|array',
            'equipment.*' => 'required|string',
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
            $schedule->type = 'Preventive';
            $schedule->submit = 'Y';
            $schedule->save();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed add corrective schedule',
                'error' => $e->getMessage()
            ], 500);
        }

        foreach($request->equipment as $key => $val){
            try {
                $preventive_schedule = new PreventiveSchedule;
                $preventive_schedule->schedule_id = $schedule->id;
                $preventive_schedule->equipment_id = $val;
                $preventive_schedule->save();
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' => 'Failed add preventive schedule',
                    'error' => $e->getMessage()
                ], 500);
            }
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
            $log->description = 'Add Preventive Schedule #'.$schedule->id;
            $log->reference_id = $schedule->id;
            $log->url = '#/preventive_schedule/'.$schedule->id;

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
                $schedules = Schedule::with(['building.partner', 'preventives.equipment.category', 'teams.engineer', 'preventive_reports'])->where('type', '=', 'Preventive')->findOrFail($id);
            break;

            case 'PARTNER' : 
                $schedules = Schedule::whereHas('building.partner', function(Builder $query){
                    $auth = Auth::user()->load('partner_user.partner');
                    $query->where('id', '=', $auth->partner_user->partner->id);
                })->with(['building', 'preventives.equipment.category', 'teams.engineer'])->where('type', '=', 'Preventive')->findOrFail($id);
            break;

            case 'ENGINEER' : 
                $schedules = Schedule::whereHas('teams.engineer', function(Builder $query){
                    $auth = Auth::user()->load('engineer');
                    $query->where('id', '=', $auth->engineer->id);
                })->with(['building.partner', 'preventives.equipment.category', 'preventives.equipment.procedure.preventive_procedures', 'teams.engineer', 'preventive_reports'])->where('type', '=', 'Preventive')->findOrFail($id);
            break;
        }
        
        $this->authorize('view', $schedules);
        
        return response()->json([
            'status' => true,
            'message' => 'Success fetch preventive schedules',
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
        $preventive_schedule = Schedule::findOrFail($id);

        $this->authorize('delete', $preventive_schedule);

        DB::beginTransaction();

            try {
                $preventive_schedule->delete();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed delete preventive schedule',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Delete Preventive Schedule #'.$preventive_schedule->id;
                $log->reference_id = $preventive_schedule->id;
                $log->url = '#/preventive_schedule';

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
            'message' => 'Success archive preventive schedule',
            'results' => $preventive_schedule,
        ], 200);
    }
}
