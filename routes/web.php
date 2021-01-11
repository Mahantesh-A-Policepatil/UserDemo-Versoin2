<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api/v1', 'middleware' => 'auth'], function () use ($router) {

	 //the route below is for viewing all users
	 $router->get('users', 'UserController@index');

	 //the route below is for viewing one user
	 $router->get('user/{id}', 'UserController@show');

	 //the route below is for creating a new user
	 $router->post('users/add', 'UserController@store');

	 //the route below is for updating the information of a user
	 $router->post('users/update/{id}', 'UserController@update');

	 //the route below is for deleting a user from the database
	 $router->delete('users/delete/{id}', 'UserController@destroy');

	  //the route below is for logout
	 $router->get('users/logout', 'UserController@logout');

	 //the route below is for viewing all users
	 $router->get('groups', 'GroupController@index');

	 //the route below is for viewing one user
	 $router->get('groups/{id}', 'GroupController@show');

	 //the route below is for viewing all users
	 $router->post('groups/add', 'GroupController@store');

	 //the route below is for updating the information of a user
	 $router->post('groups/update/{id}', 'GroupController@update');

	 //the route below is for deleting a group from the database
	 $router->delete('groups/delete/{id}', 'GroupController@destroy');

	 //the route below is for logged-in user to join a public group
	 $router->post('groups/joinPublicGroup', 'PublicGroupController@joinPublicGroup');

	 //the route below is for logged-in user to leave a public group
	 $router->post('groups/leavePublicGroup', 'PublicGroupController@leavePublicGroup');

	 //the route below is for group-owner to add users into his private group
	 $router->post('groups/addUserToPrivateGroup', 'PrivateGroupController@addUserToPrivateGroup');

	  //the route below is for group-owner to delete users from his private group
	 $router->post('groups/removeUserFromPrivateGroup', 'PrivateGroupController@removeUserFromPrivateGroup');

});

$router->group(['prefix' => 'api/v1'], function () use ($router) {

	 //the route below is for creating/regsitering a new user
	 //$router->post('users/register', 'UserController@store');

	 //the route below is for login
	 $router->post('users/login', 'UserController@authenticate');
});
