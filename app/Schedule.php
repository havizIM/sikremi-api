<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    
    use SoftDeletes;
    
    protected $table = 'schedules';

    protected $fillable = [
        'date',
        'time',
        'estimate',
        'type',
        'shift',
        'submit'
    ];

    protected $hidden = [
        'building_id'
    ];

    public static function boot() {
        parent::boot();

        static::updating(function($table)  {
            $table->updated_by = auth()->user()->id;
        });

        static::deleting(function($table) {
            $table->deleted_by = auth()->user()->id;
            $table->save();
        });

        static::saving(function($table)  {
            $table->created_by = auth()->user()->id;
            $table->updated_by = auth()->user()->id;
        });
    }

    public function building()
    {
        return $this->belongsTo('App\Building')->withTrashed();
    }

    public function teams()
    {
        return $this->hasMany('App\Team');
    }

    public function preventives()
    {
        return $this->hasMany('App\PreventiveSchedule');
    }

    public function corrective()
    {
        return $this->hasMany('App\CorrectiveSchedule');
    }

    public function preventive_reports()
    {
        return $this->hasMany('App\PreventiveReport');
    }

    public function corrective_report()
    {
        return $this->hasMany('App\CorrectiveReport');
    }
}
