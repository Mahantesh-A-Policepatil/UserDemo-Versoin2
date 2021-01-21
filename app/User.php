<?php
namespace App;

use Illuminate\Auth\Authenticatable;

//use Illuminate\Contracts\Auth\Authenticatable;
//use Illuminate\Auth\Authenticatable as AuthenticableTrait;

use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    //use AuthenticableTrait;
    use Authenticatable, Authorizable;

    protected $fillable = [
        'id',
        'username',
        'password',
        'email',
        'mobile',
        'address',
        'api_key',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'password',
        'api_key',
    ];
    public $timestamps = true;

    protected $dateFormat = 'U';

    protected $casts = [
        /* 'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'*/

        'created_at' => 'datetime:Y-m-d g:iA',
        'updated_at' => 'datetime:Y-m-d g:iA',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    public function groups()
    {
        return $this->belongsToMany('App\Group');
    }

}
