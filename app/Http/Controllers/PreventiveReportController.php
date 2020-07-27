<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Support\Facades\Storage; 

use App\PreventiveReport;
use App\PreventiveReportDetail;
use App\PreventiveReportPhoto;
use App\Log;

use Help;

class PreventiveReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', PreventiveReport::class);

        switch(Auth::user()->roles){
            case 'ADMINISTRATOR' :
                 $report = PreventiveReport::with(['schedule', 'equipment'])->get();
            break;

            case 'PARTNER' : 
                $report = PreventiveReport::whereHas('schedule.building.partner', function(Builder $query){
                    $auth = Auth::user()->load('partner_user.partner');
                    $query->where('id', '=', $auth->partner_user->partner->id);
                })->with(['schedule', 'equipment'])->get();
            break;

            case 'ENGINEER' : 
                $report = PreventiveReport::whereHas('schedule.teams.engineer', function(Builder $query){
                    $auth = Auth::user()->load('engineer');
                    $query->where('id', '=', $auth->engineer->id);
                })->with(['schedule', 'equipment'])->get();
            break;
        }
        
        return response()->json([
            'status' => true,
            'message' => 'Success fetch preventive report',
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
        $this->authorize('create', PreventiveReport::class);

        $validator = Validator::make($request->all(), [
            'schedule_id' => 'required|exists:schedules,id',
            'equipment_id' => 'required|exists:equipments,id',
            'date' => 'required|date_format:Y-m-d',
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
            $preventive_report = new PreventiveReport;
            $preventive_report->schedule_id = $request->schedule_id;
            $preventive_report->equipment_id = $request->equipment_id;
            $preventive_report->date = $request->date;
            $preventive_report->report_number = Help::dateCode('CR', 'corrective_reports', 'report_number');
            $preventive_report->note = $request->note;
            $preventive_report->save();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed add preventive report',
                'error' => $e->getMessage()
            ], 500);
        }

        foreach($request['description'] as $key => $val){
            try {
                $details = new PreventiveReportDetail;
                $details->preventive_report_id = $preventive_report->id;
                $details->description = $request['description'][$key];
                $details->periode = $request['periode'][$key];
                $details->tools = $request['tools'][$key];
                $details->check = isset($request['check'][$key]) ? $request['check'][$key] : 'N';
                $details->save();
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' => 'Failed add Report Details',
                    'error' => $e->getMessage()
                ], 500);
            }
        }
        
        $photo = $request->file('photo');

        foreach($request['photo'] as $key => $val){
            try {
                
                $name       = $photo[$key]->getClientOriginalName();
                $filename   = pathinfo($name, PATHINFO_FILENAME);
                $extension  = $photo[$key]->getClientOriginalExtension();
                $store_as   = $filename.'_'.time().'.'.$extension;
                $photo[$key]->storeAs('public/preventive_report/'.$preventive_report->id.'/', $store_as);

                $preventive_report_photo = new PreventiveReportPhoto;
                $preventive_report_photo->preventive_report_id = $preventive_report->id;
                $preventive_report_photo->photo = $store_as;
                $preventive_report_photo->save();
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' => 'Failed add preventive report photo',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        try {
            $log = new Log;
            $log->user_id = Auth::id();
            $log->description = 'Add Preventive Report #'.$preventive_report->id;
            $log->reference_id = $preventive_report->id;
            $log->url = '#/preventive_report/'.$preventive_report->id;

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
            'message' => 'Success add Preventive Report',
            'results' => $preventive_report,
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
                 $report = PreventiveReport::with(['schedule.building.partner', 'equipment', 'photos', 'details'])->findOrFail($id);
            break;

            case 'PARTNER' : 
                $report = PreventiveReport::whereHas('schedule.building.partner', function(Builder $query){
                    $auth = Auth::user()->load('partner_user.partner');
                    $query->where('id', '=', $auth->partner_user->partner->id);
                })->with(['schedule.building.partner', 'equipment', 'details', 'photos'])->findOrFail($id);
            break;

            case 'ENGINEER' : 
                $report = PreventiveReport::whereHas('schedule.teams.engineer', function(Builder $query){
                    $auth = Auth::user()->load('engineer');
                    $query->where('id', '=', $auth->engineer->id);
                })->with(['schedule.building.partner', 'equipment', 'details', 'photos'])->findOrFail($id);
            break;
        }

        $this->authorize('view', $report);
        
        return response()->json([
            'status' => true,
            'message' => 'Success fetch preventive report',
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
        $report = PreventiveReport::findOrFail($id);

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

                Storage::disk('preventive_signature')->put($report->id.'.png', $photo);

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
                $log->description = 'Approve Preventive Report #'.$id;
                $log->reference_id = $id;
                $log->url = '#/preventive_report/'.$id;

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
        $path = base_path().'/storage/app/public/preventive_report/'.$id.'/'.$filename;
        return Response::download($path); 
    }

    public function signature($filename)
    {
        $path = base_path().'/storage/app/public/preventive_signature/'.$filename;
        return Response::download($path);  
    }
}
