<?php
 
  namespace App\Http\Controllers;
   
  use App\Groups;
  use App\Http\Controllers\Controller;
  use Illuminate\Http\Request;
  use Response;
  use Illuminate\Support\Facades\Hash;
  use Illuminate\Support\Str;
  use Auth;
  use Illuminate\Validation\Rule;

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
        return response()->json($groups);
      }else{
        if(app('redis')->exists("all_groups"))
        {
          return json_decode(app('redis')->get("all_groups"));
        }else{
          $groups  = Groups::all();
          app('redis')->set("all_groups", $groups);
          return json_decode(app('redis')->get("all_groups"));
        }
      }
        
    }

   /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
    public function show($id){
      $group  = Groups::find($id);
      return response()->json($group);
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
     
      return response()->json($group);
   
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

      return response()->json($group);
      
    }  

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
          $group->delete();
          return response()->json(['status' => 'Group Removed successfully.']);
        }else{
          return response()->json(['status' => 'Group does not exists.']);
        }
    }



  }