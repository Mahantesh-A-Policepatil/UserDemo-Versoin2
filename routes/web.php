<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of The routes for an application.
| It is a breeze. Simply tell Lumen The URIs it should respond to
| and give it The Closure to call when that URI is requested.
|
 */

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api/v1'], function () use ($router) {

    //The following route is for sign-up/regsitering a new user
    $router->post('users/register', 'UserController@store');

    //The following route is for login
    $router->post('users/login', 'AuthController@login');
});

// Following routes uses JWT Auth Tokens
$router->group(['prefix' => 'api/v1', 'middleware' => 'auth'], function () use ($router) {

    //The following route is for logout
    $router->get('users/logout', 'AuthController@logout');

    //The following route is for viewing all users
    $router->get('users', 'UserController@index');

    //The following route is for viewing a user
    $router->get('users/{user_id}', 'UserController@show');

    //The following route is for updating The information of a user
    $router->put('users/update/{user_id}', 'UserController@update');

    //The following route is for deleting a user from The database
    $router->delete('users/delete/{user_id}', 'UserController@destroy');

    //The following route is for viewing all groups
    $router->get('groups', 'GroupController@index');

    //The following route is for viewing one group
    $router->get('groups/{group_id}', 'GroupController@show');

    //The following route is for creating a new group
    $router->post('groups', 'GroupController@store');

    //The following route is for updating The information of a group
    $router->put('groups/update/{group_id}', 'GroupController@update');

    //The following route is for deleting a group from The database
    $router->delete('groups/delete/{group_id}', 'GroupController@destroy');

    //The following route is for viewing one group
    $router->get('groupMembers', 'GroupController@getGroupMembers');

    //The following route is for viewing one group
    $router->get('groupUsers', 'GroupController@getGroupUsers');

    //The following route is for logged-in user to join a public group
    $router->post('groups/{group_id}/join', 'PublicGroupController@joinPublicGroup');

    //The following route is for logged-in user to leave a public group
    $router->post('groups/{group_id}/leave', 'PublicGroupController@leavePublicGroup');

    //The following route is for group-owner to add users into his private group
    $router->post('groups/{group_id}/add', 'PrivateGroupController@addUserToPrivateGroup');

    //The following route is for group-owner to delete users from his private group
    $router->post('groups/{group_id}/remove', 'PrivateGroupController@removeUserFromPrivateGroup');

});
