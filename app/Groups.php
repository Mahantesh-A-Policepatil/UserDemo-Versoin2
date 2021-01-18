<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;


class Groups extends Model
{
	protected $fillable = [
	   'id', 
	   'group_name', 
	   'group_desc',
	   'group_owner_id', 
	   'is_public_group',
	   'created_at', 
	   'updated_at'
   ];   
//    public static $timestamps = true;
//    protected function getDateFormat()
//    {
// 		return 'U';
//    }

	
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