<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    
    use SoftDeletes;
    
    protected $table = 'categories';

    protected $fillable = [
        'category_name',
        'other_information',
    ];

    protected $hidden = [
        
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

    public function equipments()
    {
        return $this->hasMany('App\Equipment');
    }
}
