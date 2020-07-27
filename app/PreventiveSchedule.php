<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PreventiveSchedule extends Model
{
    protected $table = 'preventive_schedules';

    public $timestamps = false;

    protected $hidden = [
        'schedule_id',
        'equipment_id',
    ];

    public function equipment()
    {
        return $this->belongsTo('App\Equipment')->withTrashed();
    }

    public function schedule()
    {
        return $this->belongsTo('App\Schedule')->where('type', '=', 'Preventive')->withTrashed();
    }
}
