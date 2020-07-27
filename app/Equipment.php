<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    use SoftDeletes;
    
    protected $table = 'equipments';

    protected $fillable = [
        'sku',
        'equipment_name',
        'brand',
        'type',
        'location',
        'other_information',
        'photo',
    ];

    protected $hidden = [
        'building_id', 'category_id', 'procedure_id'
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

    public function category()
    {
        return $this->belongsTo('App\Category')->withTrashed();
    }

    public function procedure()
    {
        return $this->belongsTo('App\Procedure')->withTrashed();
    }

    public function corrective_reports()
    {
        return $this->hasMany('App\CorrectiveReport');
    }

    public function preventive_reports()
    {
        return $this->hasMany('App\PreventiveReport');
    }
}
