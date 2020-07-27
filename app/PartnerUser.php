<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartnerUser extends Model
{
    use SoftDeletes;
    
    protected $table = 'partner_users';

    protected $fillable = [
        'full_name',
        'address',
        'phone',
        'position',
        'other_information'
    ];

    protected $hidden = [
        'user_id'
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

    public function user()
    {
        return $this->belongsTo('App\User')->withTrashed();
    }

    public function partner()
    {
        return $this->belongsTo('App\Partner');
    }
}
