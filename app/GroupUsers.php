<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class GroupUsers extends Model
{
	protected $fillable = [
	   'id', 
	   'group_id', 
	   'user_id',
	   'created_at', 
	   'updated_at'
   ]; 
//    public static $timestamps = true;
//    public function getDateFormat()
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