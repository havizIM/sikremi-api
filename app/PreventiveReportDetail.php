<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PreventiveReportDetail extends Model
{
    protected $table = 'preventive_report_details';

    public $timestamps = false;

    protected $fillable = [
        'description', 'periode', 'tools', 'check'
    ];

    protected $hidden = [
        'preventive_report_id',
    ];

    public function preventive_report()
    {
        return $this->belongsTo('App\PreventiveReport')->withTrashed();
    }
}
