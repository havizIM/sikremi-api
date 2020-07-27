<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkOrderPhoto extends Model
{
    protected $table = 'work_order_photos';

    public $timestamps = false;

    protected $fillable = [
        'photo',
    ];

    protected $hidden = [
        'work_order_id',
    ];

    public function work_order()
    {
        return $this->belongsTo('App\WorkOrder')->withTrashed();
    }
}
