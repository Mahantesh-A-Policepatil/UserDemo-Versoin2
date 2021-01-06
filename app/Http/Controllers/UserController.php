<?php
 
namespace App\Http\Controllers;
 
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Response;
 
class UserController extends Controller{

/**
 * Display a listing of the resource.
 *
 * @return \Illuminate\Http\Response
 */
  public function index(){

      $user  = User::all();
      return response()->json($user);
  }

/**
 * Display the specified resource.
 *
 * @param  int  $id
 * @return \Illuminate\Http\Response
 */
  public function show($id){

      $user  = User::find($id);
      return response()->json($user);
  }

 /**
 * Show the form for creating a new resource.
 *
 * @return \Illuminate\Http\Response
 */
  public function store(Request $request){
 
    $this->validate($request, [
        'name'=>'required',
        'email'=>'required|email|unique:users',
        'mobile'=>'required'
    ]);

     $user = new User([
        'name' => $request->get('name'),
        'email' => $request->get('email'),
        'mobile' => $request->get('mobile'),
        'address' => $request->get('address')
     ]);
     $user->save();
   
    return response()->json($user);
 
  }
 
 /**
 * Update the specified resource in storage.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  int  $id
 * @return \Illuminate\Http\Response
 */
  public function update(Request $request, $id){

    $this->validate($request, [
        'name'=>'required',
        'email'=>'required|email|unique:users',
        'mobile'=>'required'
    ]);

    $user  = User::find($id);

    $user->name = $request->get('name');
    $user->email = $request->get('email');
    $user->mobile = $request->get('mobile');
    $user->address = $request->get('address');
   
    $user->update();

    return response()->json($user);
  }  

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id){
      $user  = User::find($id);
      if($user) {
        $user->delete();
        return response()->json('User Removed successfully.');
      }else{
        return response()->json('User does not exists.');
      }
  }

  
}
?>