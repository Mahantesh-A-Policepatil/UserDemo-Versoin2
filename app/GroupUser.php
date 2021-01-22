<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupUser extends Model
{
    //group_users
    protected $table = 'group_user';
    protected $fillable = [
        'id',
        'group_id',
        'user_id',
        'created_at',
        'updated_at',
    ];

    public $timestamps = true;

    protected $dateFormat = 'U';

}
