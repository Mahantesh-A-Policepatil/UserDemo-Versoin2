<?php namespace App;
 
use Illuminate\Database\Eloquent\Model;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticableTrait;
 
class User extends Model
{ 
   use AuthenticableTrait;	
   protected $fillable = [
	   'id', 
	   'username', 
	   'password',
	   'email', 
	   'mobile', 
	   'address', 
	   'created_at', 
	   'updated_at'
   ];   

   protected $hidden = [
   		'password'
   ];
}
?>