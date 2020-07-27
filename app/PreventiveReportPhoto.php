<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PreventiveReportPhoto extends Model
{
    protected $table = 'preventive_report_photos';

    public $timestamps = false;

    protected $fillable = [
        'photo'
    ];

    protected $hidden = [
        'preventive_report_id',
    ];

    public function preventive_report()
    {
        return $this->belongsTo('App\PreventiveReport')->withTrashed();
    }
}
