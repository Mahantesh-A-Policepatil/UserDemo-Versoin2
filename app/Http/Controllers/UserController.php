<?php
 
namespace App\Http\Controllers;
 
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
 
class UserController extends Controller{

/**
 * Display a listing of the resource.
 *
 * @return \Illuminate\Http\Response
 */
  public function index(Request $request){
 
    $userName = $request->get('name');
      
    if($userName){
      $user  = User::where('username', 'like',$userName."%")->get();
      return response()->json($user);
    }else{
      $user  = User::all();
      return response()->json($user);
    }
      
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
        'username'=>'required',
        'email'=>'required|email|unique:users',
        'mobile'=>'required',
        'password'=>'required'
    ]);

     $user = new User([
        'username' => $request->get('username'),
        'email' => $request->get('email'),
        'mobile' => $request->get('mobile'),
        'address' => $request->get('address'),
        'password' => Hash::make($request->get('password'))
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
        'username'=>'required',
        'email'=>'required|email|unique:users',
        'mobile'=>'required',
        'password'=>'required'
    ]);

    $user  = User::find($id);

    $user->username = $request->get('username');
    $user->email = $request->get('email');
    $user->mobile = $request->get('mobile');
    $user->address = $request->get('address');
    $user->password = Hash::make($request->get('password'));
   
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

  //
  public function authenticate(Request $request){

    $this->validate($request, [
       'email' => 'required',
       'password' => 'required'
    ]);
    $user = User::where('email', $request->get('email'))->first();

    if(Hash::check($request->get('password'), $user->password)){
      $apikey = base64_encode(Str::random(40));
      User::where('email', $request->get('email'))->update(['api_key' => "$apikey"]);;
      return response()->json(['status' => 'success','api_key' => $apikey]);
    }else{
      return response()->json(['status' => 'fail'],401);
    }

  }
   

  //

  
}
?>