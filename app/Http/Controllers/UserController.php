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

  use League\Fractal\Manager;
  use League\Fractal\Resource\Collection;
  use League\Fractal\Resource\Item;
  use App\Transformers\UserTransformer;
  use Illuminate\Support\Facades\Redis;


  class UserController extends Controller{

    private $fractal;



  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
    public function index(Request $request){

      $userName = $request->get('name');
      //$seconds = 100000;

      if($userName){
          $user  = User::where('username', 'like', $userName."%")->get();

          $manager = new Manager();
          $resource = new Collection($user, new UserTransformer());

          $users = $manager->createData($resource)->toArray();
          return  $users;
     }else{

      if (app('redis')->exists('users_all')) {
        $users = app('redis')->get('users_all');
        return $users;
      }
      $user = User::all();

      $manager = new Manager();
      $resource = new Collection($user, new UserTransformer());
      $users = $manager->createData($resource)->toArray();
      app('redis')->set("users_all", json_encode($users));
      return $users;

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
      if(!$user) {
        return response()->json(['status' => 'User does not exists.'], 404);
      }
      $manager = new Manager();
      $resource = new Item($user, new UserTransformer());
      $user = $manager->createData($resource)->toArray();
      return $user;
    }

   /**
   * Show the form for creating a new resource,
   * Registering a new user,
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

       $manager = new Manager();
       $resource = new Item($user, new UserTransformer());
       $users = $manager->createData($resource)->toArray();
       return $users;

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
        return response()->json(['status' => 'User does not exists.'], 404);
      }

      if($user_id !=  Auth::user()->id){
        return response()->json(['error' => 'You are not authorized to update'], 401);
      }

      $this->validate($request, [
          'username'=>'required',
          'email' => ['required',Rule::unique('users')->ignore($user->id)],
          'mobile' => ['required',Rule::unique('users')->ignore($user->id)]
      ]);

      $user->username = $request->get('username');
      $user->email = $request->get('email');
      $user->mobile = $request->get('mobile');
      $user->address = $request->get('address');
      if($request->get('password')){
        $user->password = Hash::make($request->get('password'));
      }

      $user->update();

      $manager = new Manager();
      $resource = new Item($user, new UserTransformer());
      $users = $manager->createData($resource)->toArray();
      return $users;

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($user_id){
      $user  = User::find($user_id);
      if(!$user) {
        return response()->json(['status' => 404, 'message' => 'User does not exists.'], 404);
      }

      if($user_id !=  Auth::user()->id){
        return response()->json(['status' => 401, 'message' => 'You are not authorized to update'], 401);
      }

      $user->delete();
      if($user) {
        return response()->json(['status' => 410, 'message' =>  'User deleted successfully!'], 410);
      }else{
        return response()->json(['status' => 400, 'message' =>  'Failed to delete User!'], 400);
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
      if(!$user){
        return response()->json(['status' => 404,  'message' => 'User does not exists.']);
      }

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