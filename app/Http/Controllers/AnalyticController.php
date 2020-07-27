<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

use App\PreventiveReport;
use App\CorrectiveReport;
use App\Schedule;

use App\Log;

class AnalyticController extends Controller
{
    
    public function performance($year)
    {
        if($year === ''){
            $year = date('Y');
        }

        switch(Auth::user()->roles){
            case 'ADMINISTRATOR' :
                $preventives = PreventiveReport::select(
                    DB::raw('COUNT(id) AS totalPreventiveKey'),
                    DB::raw('MONTH(date) AS monthKey'),
                )
                ->whereYear('date', '=', $year)
                ->groupBy('monthKey')
                ->get();

                $correctives = CorrectiveReport::select(
                    DB::raw('COUNT(id) AS totalCorrectiveKey'),
                    DB::raw('MONTH(date) AS monthKey'),
                )
                ->whereYear('date', '=', $year)
                ->groupBy('monthKey')
                ->get();
            break;

            case 'PARTNER' : 
                $auth = Auth::user()->load('partner_user.partner');

                $preventives = PreventiveReport::select(
                    DB::raw('COUNT(preventive_reports.id) AS totalPreventiveKey'),
                    DB::raw('MONTH(preventive_reports.date) AS monthKey'),
                )
                ->join('equipments', 'equipments.id' , '=', 'preventive_reports.equipment_id')
                ->join('buildings', 'buildings.id' , '=', 'equipments.building_id')
                ->join('partners', 'partners.id' , '=', 'buildings.partner_id')
                ->where('partners.id', '=', $auth->partner_user->partner->id)
                ->whereYear('preventive_reports.date', '=', $year)
                ->groupBy('monthKey')
                ->get();

                $correctives = CorrectiveReport::select(
                    DB::raw('COUNT(corrective_reports.id) AS totalCorrectiveKey'),
                    DB::raw('MONTH(corrective_reports.date) AS monthKey'),
                )
                ->join('equipments', 'equipments.id' , '=', 'corrective_reports.equipment_id')
                ->join('buildings', 'buildings.id' , '=', 'equipments.building_id')
                ->join('partners', 'partners.id' , '=', 'buildings.partner_id')
                ->where('partners.id', '=', $auth->partner_user->partner->id)
                ->whereYear('corrective_reports.date', '=', $year)
                ->groupBy('monthKey')
                ->get();
            break;
        }

        $month_preventive_array = ['Jan', 'Feb', 'Mar','Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $total_preventive_array = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

        foreach($preventives as $key){
            $index = $key->monthKey - 1;
            $total_preventive_array[$index] = $key->totalPreventiveKey;
        }

        $month_corrective_array = ['Jan', 'Feb', 'Mar','Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $total_corrective_array = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

        foreach($correctives as $key){
            $index = $key->monthKey - 1;
            $total_corrective_array[$index] = $key->totalCorrectiveKey;
        }

        $results['year'] = $year;
        $results['preventive']['month'] = $month_preventive_array;
        $results['preventive']['total'] = $total_preventive_array;

        $results['corrective']['month'] = $month_corrective_array;
        $results['corrective']['total'] = $total_corrective_array;

        return response()->json([
            'status' => true,
            'message' => 'Success fetch performance analytics',
            'results' => $results
        ]);
    }

    public function today_schedule()
    {
        $this->authorize('viewAny', Schedule::class);

        switch(Auth::user()->roles){
            case 'ADMINISTRATOR' :
            
            break;

            case 'PARTNER' : 

            break;

            case 'ENGINEER' : 
                $schedules = Schedule::whereHas('teams.engineer', function(Builder $query){
                    $auth = Auth::user()->load('engineer');
                    $query->where('id', '=', $auth->engineer->id);
                })->with(['building.partner'])->where('date', '=', date('Y-m-d'))->get();
            break;
        }
        
        return response()->json([
            'status' => true,
            'message' => 'Success fetch schedules',
            'results' => $schedules
        ]);
    }

}
