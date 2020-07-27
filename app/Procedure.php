<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Procedure extends Model
{
    use SoftDeletes;

    protected $table = 'procedures';

    protected $fillable = [
        'identifier_name',
        'type',
        'other_information',
    ];

    protected $hidden = [
        'partner_id'
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

    public function partner()
    {
        return $this->belongsTo('App\Partner')->withTrashed();
    }

    public function preventive_procedures()
    {
        return $this->hasMany('App\PreventiveProcedure');
    }

    public function checklist_procedure()
    {
        return $this->hasMany('App\ChecklistProcedure');
    }
}
