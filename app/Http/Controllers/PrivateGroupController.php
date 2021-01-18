<?php
 
  namespace App\Http\Controllers;
   
  use App\Groups;
  use App\GroupUsers;
  use App\Http\Controllers\Controller;
  use Illuminate\Http\Request;
  use Response;
  use Auth;
  use Illuminate\Validation\Rule;

  class PrivateGroupController extends Controller{

  	public function addUserToPrivateGroup(Request $request, $group_id){
  	
  		$this->validate($request, [
            'user_id'=>'required'
        ]);

        if(Groups::where('id', '=', $group_id)
          ->where('is_public_group','=',1)
          ->exists()
        ){
          return response()->json(['status' => 401, 'message' => 'You are not authorized to add users this group as this is not a private group'], 401);
        }

        if (GroupUsers::where('group_id', '=', $group_id)
                        ->where('user_id', '=',$request->get('user_id'))
                        ->exists()
            ){
          // user already exists
          return response()->json(['status' => 409, 'message' => 'User already exists.'], 409);
        }

        if(Groups::where('group_owner_id', '=', Auth::user()->id)
		          ->where('is_public_group','=',0)
		          ->exists()
        ){
            $privateGroup = new GroupUsers([
	          'group_id' => $group_id,
	          'user_id' => $request->get('user_id')
	        ]);
	        $privateGroup->save();
          //return response()->json($privateGroup);
          return response()->json(['status' => 200, 'message' => 'User added to the group successfully.'], 200);
        }
        else{
        	 return response()->json(['status' => 401, 'message' => 'You are not this group owner, So you can not add users to this group'], 401);
        }
  	}

  	public function removeUserFromPrivateGroup(Request $request, $group_id){
  	//print_r(Auth::user()->id); exit;
  		$this->validate($request, [
            'user_id'=>'required'
        ]);

        if(Groups::where('id', '=', $group_id)
          ->where('is_public_group','=',1)
          ->exists()
        ){
          return response()->json(['status' => 401, 'message' => 'You are not authorized to add users this group as this is not a private group'], 401);
        }

        $groupMember  = GroupUsers::where('group_id', '=', $group_id)
                                    ->where('user_id', '=', $request->get('user_id'))
                                    ->first();
        if(!$groupMember){
        	return response()->json(['status' => 404, 'message' => 'User does not exists in this private group.'], 404);
        }                            
        if(Groups::where('group_owner_id', '=', Auth::user()->id)
        	 	  ->where('is_public_group','=',0)
		          ->exists()
        ){
             
	         $groupMember->delete();
	         return response()->json(['status' => 200, 'message' => 'User has been deleted from the group successfully.']);
        }
       
  	}

  

  }