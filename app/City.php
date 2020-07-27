<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'cities';

    public $timestamps = false;

    protected $fillable = [
        'city', 'type', 'postal_code'
    ];

    protected $hidden = [
        'provice_id'
    ];

    public function province()
    {
        return $this->belongsTo('App\Province')->withTrashed();
    }
}
