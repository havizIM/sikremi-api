<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CorrectiveReportPhoto extends Model
{
    protected $table = 'corrective_report_photos';

    public $timestamps = false;

    protected $fillable = [
        'photo'
    ];

    protected $hidden = [
        'corrective_report_id',
    ];

    public function corrective_report()
    {
        return $this->belongsTo('App\CorrectiveReport')->withTrashed();
    }
}
