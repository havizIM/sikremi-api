<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Administrator extends Model
{
    use SoftDeletes;
    
    protected $table = 'administrators';

    protected $fillable = [
        'full_name',
        'address',
        'phone',
        'level',
        'other_information',
        'photo',
    ];

    protected $hidden = [
        'user_id', 'city_id', 'province_id'
    ];

    public static function boot() {
        parent::boot();

        static::updating(function($table)  {
            $table->updated_by = auth()->user()->id;
        });

        static::deleting(function($table) {
            $table->user()->delete();
            $table->deleted_by = auth()->user()->id;
            $table->save();

        });

        static::saving(function($table)  {
            $table->created_by = auth()->user()->id;
            $table->updated_by = auth()->user()->id;
        });
    }

    public function city()
    {
        return $this->belongsTo('App\City');
    }

    public function province()
    {
        return $this->belongsTo('App\Province');
    }

    public function user()
    {
        return $this->belongsTo('App\User')->withTrashed();
    }
}
