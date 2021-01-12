<?php
 
  namespace App\Http\Controllers;
   
  use App\User;
  use App\Http\Controllers\Controller;
  use Illuminate\Http\Request;
  use Response;
  use Illuminate\Support\Facades\Hash;
  use Illuminate\Support\Str;
  use Auth;
  use Illuminate\Validation\Rule;
   
  class UserController extends Controller{

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
    public function index(Request $request){
   
      $userName = $request->get('name');
        
      if($userName){
        $user  = User::where('username', 'like', $userName."%")->get();
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
    public function show($user_id){

      $user  = User::find($user_id);
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
          'mobile'=>'required|unique:users',
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
    public function update(Request $request, $user_id){

      $user  = User::find($user_id);
      if(!$user) {
        return response()->json(['status' => 'User does not exists.']);
      }
      
      if($user_id !=  Auth::user()->id){
        return response()->json(['error' => 'You are not authorized to update'], 401);
      }
      //print_r($request->get('password')); exit;

      
      $this->validate($request, [
          'username'=>'required',
          'email' => ['required',Rule::unique('users')->ignore($user->id)],
          'mobile' => ['required',Rule::unique('users')->ignore($user->id)],
          //'password'=>'required'
      ]);

      $user->username = $request->get('username');
      $user->email = $request->get('email');
      $user->mobile = $request->get('mobile');
      $user->address = $request->get('address');
      if($request->get('password')){
        $user->password = Hash::make($request->get('password'));
      }
     
      $user->update();

      return response()->json($user);
      
    }  

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($user_id){
        $user  = User::find($user_id);
        if($user){
          if($user_id !=  Auth::user()->id){
            return response()->json(['error' => 'You are not authorized to delete'], 401);
          }
          $user->delete();
          return response()->json(['status' => 'User Removed successfully.']);
        }else{
          return response()->json(['status' => 'User does not exists.']);
        }
    }

    /**
     * Login:Authenticate a user
     *
     * @param  string  $email
     * @param  string  $password
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request){

      $this->validate($request, [
         'email' => 'required|email',
         'password' => 'required'
      ]);
      $user = User::where('email', $request->get('email'))->first();

      if(Hash::check($request->get('password'), $user->password)){
        $apikey = base64_encode(Str::random(40));
        User::where('email', $request->get('email'))->update(['api_key' => "$apikey"]);;
        return response()->json(['status' => 'success','api_key' => $apikey]);
      }else{
        return response()->json(['status' => 'Login Failed!, Incorrect User name or password'],401);
      }

    }

    /*
    * This function will get the authenticated user
    * unset and save the api token
    */
    public function logout() {
      $user = Auth::user();
      $user->api_key = null;
      $user->save();
      return response()->json(['status' => 'Successfully Logged Out']);
    }
     
    
  }

?>