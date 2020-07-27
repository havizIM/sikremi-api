<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChecklistProcedure extends Model
{
    protected $table = 'checklist_procedures';

    public $timestamps = false;

    protected $hidden = [
        'procedure_id',
    ];

    public function procedure()
    {
        return $this->belongsTo('App\Procedure')->withTrashed();
    }
}
