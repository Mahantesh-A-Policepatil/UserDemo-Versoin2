<?php namespace App;
 
use Illuminate\Database\Eloquent\Model;
 
class User extends Model
{ 
   protected $fillable = [
	   'id', 
	   'name', 
	   'email', 
	   'mobile', 
	   'address', 
	   'created_at', 
	   'updated_at'
   ];   
}
?>