<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CorrectiveSchedule extends Model
{
    protected $table = 'corrective_schedules';

    public $timestamps = false;

    protected $hidden = [
        'schedule_id',
        'work_order_id',
    ];

    public function work_order()
    {
        return $this->belongsTo('App\WorkOrder')->withTrashed();
    }

    public function schedule()
    {
        return $this->belongsTo('App\Schedule')->where('type', '=', 'Corrective')->withTrashed();
    }
}
