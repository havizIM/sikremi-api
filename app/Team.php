<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $table = 'teams';

    public $timestamps = false;

    protected $hidden = [
        'schedule_id',
        'engineer_id',
    ];

    public function engineer()
    {
        return $this->belongsTo('App\Engineer')->withTrashed();
    }

    public function schedule()
    {
        return $this->belongsTo('App\Schedule')->withTrashed();
    }
}
