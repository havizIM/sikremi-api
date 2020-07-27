<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use SoftDeletes;
    
    protected $table = 'users';

    protected $fillable = [
        'username',
        'email',
        'roles',
        'active',
    ];

    protected $hidden = [
        'password', 'created_by', 'updated_by', 'deleted_by', 'category_id',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function logs()
    {
        return $this->hasMany('App\Log')->orderBy('created_at', 'desc')->limit(15);
    }

    public function engineer()
    {
        return $this->hasOne('App\Engineer');
    }

    public function administrator()
    {
        return $this->hasOne('App\Administrator');
    }

    public function partner_user()
    {
        return $this->hasOne('App\PartnerUser');
    }

    public function partners()
    {
        return $this->belongsToMany('App\Partner', 'partner_users')->select('partner_name', 'partners.address', 'partners.phone', 'partners.fax', 'handphone', 'partners.email')->withPivot('id', 'full_name', 'address', 'phone', 'position');
    }


}
