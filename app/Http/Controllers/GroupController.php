<?php
 
  namespace App\Http\Controllers;
   
  use App\Groups;
  use App\GroupUsers;
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
  use App\Transformers\GroupTransformer;
  use App\Transformers\GroupUsersTransformer;
  use Illuminate\Support\Facades\Redis;

  class GroupController extends Controller{

   /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  	public function index(Request $request){
   
      $groupName = $request->get('group_name');
        
      if($groupName){
        $groups  = Groups::where('group_name', 'like', $groupName."%")->get();
        $manager = new Manager();
        $resource = new Collection($groups, new GroupTransformer());
        $groups = $manager->createData($resource)->toArray();
        return  $groups;
      }else{
          if (app('redis')->exists('all_groups')) {
            $groups = app('redis')->get('all_groups');
            return $groups;
          } else {
            $groups  = Groups::all();
            $manager = new Manager();
            $resource = new Collection($groups, new GroupTransformer());
            $groups = $manager->createData($resource)->toArray();
            app('redis')->set("all_groups", json_encode($groups));
            return $groups;
        }
      }
        
    }

   /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
    public function show($group_id){
      $group  = Groups::find($group_id);
      //return response()->json($group);
      $manager = new Manager();
      $resource = new Item($group, new GroupTransformer());
      $group = $manager->createData($resource)->toArray();
      return $group;
    }

   /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
   public function store(Request $request){
   
      $this->validate($request, [
          'group_name'=>'required|unique:groups',
          'is_public_group'=>'required'
      ]);

	    $group_desc = '';
      if($request->get('group_desc')){ 
      	 $group_desc = $request->get('group_desc');
  	  }
      else{  
      	$group_desc = null;
      }
       $group = new Groups([
          'group_name' => $request->get('group_name'),
          'group_owner_id' => Auth::user()->id,
          'is_public_group' => $request->get('is_public_group'),
          'group_desc' => $group_desc
       ]);
       $group->save();
     
      //return response()->json($group);

      $manager = new Manager();
      $resource = new Item($group, new GroupTransformer());
      $group = $manager->createData($resource)->toArray();
      return $group;
   
    }
   
   /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
    public function update(Request $request, $group_id){

      $group  = Groups::find($group_id);
      if(!$group) {
        return response()->json(['status' => 'Group does not exists.']);
      }
      //echo "group_owner_id".$group->group_owner_id."Logged-In User".Auth::user()->id; exit;
      if($group->group_owner_id !=  Auth::user()->id){
        return response()->json(['error' => 'You are not authorized to update'], 401);
      }

      $this->validate($request, [
          'group_name' => ['required',Rule::unique('groups')->ignore($group->id)],
          'is_public_group'=>'required'
      ]);
      $group_desc = '';
      if($request->get('group_desc')){ 
      	 $group_desc = $request->get('group_desc');
  	  }
      else{  
      	$group_desc = null;
      }
      $group->group_name = $request->get('group_name');
      $group->group_owner_id = Auth::user()->id;
      $group->is_public_group = $request->get('is_public_group');
      $group->group_desc = $group_desc;
    
     
      $group->update();

      //return response()->json($group);

      $manager = new Manager();
      $resource = new Item($group, new GroupTransformer());
      $group = $manager->createData($resource)->toArray();
      return $group;
      
    }  

    /*
    public function destroy1($group_id){
        $group  = Groups::find($group_id);

        if($group){
          if($group->group_owner_id !=  Auth::user()->id){
            return response()->json(['error' => 'You are not authorized to delete'], 401);
          }
          $group->delete();
          return response()->json(['status' => 'Group Removed successfully.']);
        }else{
          return response()->json(['status' => 'Group does not exists.']);
        }
    }
*/
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($group_id){
      $group  = Groups::find($group_id);
      if($group){
        if($group->group_owner_id !=  Auth::user()->id){
          return response()->json(['error' => 'You are not authorized to delete'], 401);
        }
      }
      //Return error 404 response if product was not found
      if(!Groups::find($group_id)) return $this->errorResponse('Group not found!', 404);

      //Return 410(done) success response if delete was successful
      if(Groups::find($group_id)->delete()){
          return $this->customResponse('Group deleted successfully!', 410);
      }

      //Return error 400 response if delete was not successful
      return $this->errorResponse('Failed to delete Group!', 400);
    }

    public function customResponse($message = 'success', $status = 200)
    {
        return response(['status' =>  $status, 'message' => $message], $status);
    }

    public function getGroupMembers(Request $request){
      $group_name = $request->get('group_name');
      // if (app('redis')->exists('get_group_members')) {
      //   $group_members = app('redis')->get('get_group_members');
      //   return $group_members;
      // } else {
        $group_members['data'] = GroupUsers::select('users.id','users.username', 'group_users.created_at', 'group_users.updated_at')
                                            ->leftjoin('users','group_users.user_id','=','users.id')
                                            ->leftjoin('groups','group_users.group_id','=','groups.id')
                                            ->where('groups.group_name', $group_name)
                                            ->get();
        // app('redis')->set("get_group_members", json_encode($group_members));                                    
        return $group_members;
    //  }
   }

  }