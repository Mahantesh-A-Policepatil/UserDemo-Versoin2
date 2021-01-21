<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'id',
        'group_name',
        'group_desc',
        'group_owner_id',
        'is_public_group',
        'created_at',
        'updated_at',
    ];

    public $timestamps = true;

    protected $dateFormat = 'U';

    protected $casts = [
        /* 'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'*/

        'created_at' => 'datetime:Y-m-d g:iA',
        'updated_at' => 'datetime:Y-m-d g:iA',
    ];

    public function users()
    {
        return $this->belongsToMany('App\User')->withTimestamps();
    }

}
