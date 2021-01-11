<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class PublicGroups extends Model
{
	protected $fillable = [
	   'id', 
	   'group_name', 
	   'group_desc',
	   'group_member_id', 
	   'created_at', 
	   'updated_at'
   ];   

   protected $dates = [
       'created_at', 
	   'updated_at'
   ];

   protected $casts = [
	  /* 'created_at' => 'datetime:Y-m-d H:i:s',
	   'updated_at' => 'datetime:Y-m-d H:i:s'*/

	   'created_at' => 'datetime:Y-m-d g:iA',
	   'updated_at' => 'datetime:Y-m-d g:iA'
   ];
}