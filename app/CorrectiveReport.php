<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CorrectiveReport extends Model
{
    use SoftDeletes;
    
    protected $table = 'corrective_reports';

    protected $fillable = [
        'report_number',
        'date',
        'description',
        'note',
        'signature',
        'approved_by',
    ];

    protected $hidden = [
        'schedule_id'
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

    public function schedule()
    {
        return $this->belongsTo('App\Schedule')->where('type', '=', 'Corrective')->withTrashed();
    }

    public function equipment()
    {
        return $this->belongsTo('App\Equipment')->withTrashed();
    }

    public function photos()
    {
        return $this->hasMany('App\CorrectiveReportPhoto');
    }
}
