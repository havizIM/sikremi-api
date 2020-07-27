<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkOrder extends Model
{
    use SoftDeletes;

    protected $table = 'work_orders';

    protected $fillable = [
        'wo_number',
        'description',
    ];

    protected $hidden = [
        'building_id', 'equipment_id'
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

    public function equipment()
    {
        return $this->belongsTo('App\Equipment')->withTrashed();
    }

    public function photos()
    {
        return $this->hasMany('App\WorkOrderPhoto');
    }

    public function schedule()
    {
        return $this->belongsToMany('App\Schedule', 'corrective_schedules');
    }
}
