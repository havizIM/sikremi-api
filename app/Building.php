<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Building extends Model
{
    
    use SoftDeletes;
    
    protected $table = 'buildings';

    protected $fillable = [
        'building_code',
        'building_name',
        'type',
        'address',
        'phone',
        'fax',
        'email',
        'longitude',
        'latitude',
        'other_information',
    ];

    protected $hidden = [
        'partner_id', 'city_id', 'provice_id'
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

    public function city()
    {
        return $this->belongsTo('App\City');
    }

    public function province()
    {
        return $this->belongsTo('App\Province');
    }

    public function equipments()
    {
        return $this->hasMany('App\Equipment');
    }

    public function schedules()
    {
        return $this->hasMany('App\Schedule');
    }
}
