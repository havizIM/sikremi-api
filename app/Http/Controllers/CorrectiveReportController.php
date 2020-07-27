<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Support\Facades\Storage; 

use App\CorrectiveReport;
use App\CorrectiveReportPhoto;
use App\Log;

use Help;

class CorrectiveReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', CorrectiveReport::class);

        switch(Auth::user()->roles){
            case 'ADMINISTRATOR' :
                $report = CorrectiveReport::with(['schedule', 'equipment'])->get();
            break;

            case 'PARTNER' : 
                $report = CorrectiveReport::whereHas('schedule.building.partner', function(Builder $query){
                    $auth = Auth::user()->load('partner_user.partner');
                    $query->where('id', '=', $auth->partner_user->partner->id);
                })->with(['schedule', 'equipment'])->get();
            break;

            case 'ENGINEER' : 
                $report = CorrectiveReport::whereHas('schedule.teams.engineer', function(Builder $query){
                    $auth = Auth::user()->load('engineer');
                    $query->where('id', '=', $auth->engineer->id);
                })->with(['schedule', 'equipment'])->get();
            break;
        }
        
        return response()->json([
            'status' => true,
            'message' => 'Success fetch corrective report',
            'results' => $report
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
        $this->authorize('create', CorrectiveReport::class);

        $validator = Validator::make($request->all(), [
            'schedule_id' => 'required|exists:schedules,id',
            'equipment_id' => 'required|exists:equipments,id',
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
            $corrective_report = new CorrectiveReport;
            $corrective_report->schedule_id = $request->schedule_id;
            $corrective_report->equipment_id = $request->equipment_id;
            $corrective_report->date = $request->date;
            $corrective_report->report_number = Help::dateCode('CR', 'corrective_reports', 'report_number');
            $corrective_report->description = $request->description;
            $corrective_report->note = $request->note;
            $corrective_report->save();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed add corrective report',
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
                $photo[$key]->storeAs('public/corrective_report/'.$corrective_report->id.'/', $store_as);

                $corrective_report_photo = new CorrectiveReportPhoto;
                $corrective_report_photo->corrective_report_id = $corrective_report->id;
                $corrective_report_photo->photo = $store_as;
                $corrective_report_photo->save();
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' => 'Failed add corrective report photo',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        try {
            $log = new Log;
            $log->user_id = Auth::id();
            $log->description = 'Add Corrective Report #'.$corrective_report->id;
            $log->reference_id = $corrective_report->id;
            $log->url = '#/corrective_report/'.$corrective_report->id;

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
            'message' => 'Success add Corrective Report',
            'results' => $corrective_report,
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
                $report = CorrectiveReport::with(['schedule.building.partner', 'schedule.corrective.work_order', 'equipment', 'photos'])->findOrFail($id);
            break;

            case 'PARTNER' : 
                $report = CorrectiveReport::whereHas('schedule.building.partner', function(Builder $query){
                    $auth = Auth::user()->load('partner_user.partner');
                    $query->where('id', '=', $auth->partner_user->partner->id);
                })->with(['schedule.building.partner', 'schedule.corrective.work_order', 'equipment', 'photos'])->findOrFail($id);
            break;

            case 'ENGINEER' : 
                $report = CorrectiveReport::whereHas('schedule.teams.engineer', function(Builder $query){
                    $auth = Auth::user()->load('engineer');
                    $query->where('id', '=', $auth->engineer->id);
                })->with(['schedule.building.partner', 'schedule.corrective.work_order', 'equipment', 'photos'])->findOrFail($id);
            break;
        }

        $this->authorize('view', $report);
        
        return response()->json([
            'status' => true,
            'message' => 'Success fetch corrective report',
            'results' => $report
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
        //
    }

    public function approve(Request $request, $id)
    {
        $report = CorrectiveReport::findOrFail($id);

        $this->authorize('update', $report);
        
        $validator = Validator::make($request->all(), [
            'signature' => 'required|string',
            'approved_by' => 'required|string',
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
                $photo      = base64_decode($request->signature);

                Storage::disk('corrective_signature')->put($report->id.'.png', $photo);

                $report->signature = $report->id.'.png';
                $report->approved_by = $request->approved_by;
                $report->update();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed update report',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Approve Corrective Report #'.$id;
                $log->reference_id = $id;
                $log->url = '#/corrective_report/'.$id;

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
            'message' => 'Success update report',
            'results' => $report,
        ], 200);
    }

    public function picture($id, $filename)
    {
        $path = base_path().'/storage/app/public/corrective_report/'.$id.'/'.$filename;
        return Response::download($path); 
    }

    public function signature($filename)
    {
        $path = base_path().'/storage/app/public/corrective_signature/'.$filename;
        return Response::download($path);  
    }
}
