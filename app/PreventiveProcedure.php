<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PreventiveProcedure extends Model
{
    protected $table = 'preventive_procedures';

    public $timestamps = false;

    protected $hidden = [
        'procedure_id',
    ];

    public function procedure()
    {
        return $this->belongsTo('App\Procedure')->withTrashed();
    }
}
