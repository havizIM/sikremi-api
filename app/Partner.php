<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partner extends Model
{
    use SoftDeletes;
    
    protected $table = 'partners';

    protected $fillable = [
        'full_name',
        'address',
        'phone',
        'level',
        'other_information',
        'photo',
    ];

    protected $hidden = [
        'city_id', 'province_id'
    ];

    public static function boot() {
        parent::boot();

        static::updating(function($table)  {
            $table->updated_by = auth()->user()->id;
        });

        static::deleting(function($table) {
            foreach($table->partner_users as $partner_users){
                $partner_users->delete();
            }

            $table->buildings()->delete();
            $table->categories()->delete();
            $table->equipments()->delete();
            $table->procedures()->delete();
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

    public function buildings()
    {
        return $this->hasMany('App\Building');
    }

    public function categories()
    {
        return $this->hasMany('App\Category');
    }

    public function equipments()
    {
        return $this->hasManyThrough('App\Equipment', 'App\Building');
    }

    public function procedures()
    {
        return $this->hasMany('App\Procedure');
    }

    public function partner_users()
    {
        return $this->hasMany('App\PartnerUser');
    }
}
