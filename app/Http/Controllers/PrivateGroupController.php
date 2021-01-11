<?php
 
  namespace App\Http\Controllers;
   
  use App\Groups;
  use App\GroupsMembers;
  use App\Http\Controllers\Controller;
  use Illuminate\Http\Request;
  use Response;
  use Auth;
  use Illuminate\Validation\Rule;

  class PrivateGroupController extends Controller{

  	public function addUserToPrivateGroup(Request $request){
  	
  		$this->validate($request, [
            'group_id'=>'required',
            'group_member_id'=>'required'
        ]);

        if(Groups::where('id', '=', $request->get('group_id'))
          ->where('is_public_group','=',1)
          ->exists()
        ){
          return response()->json(['error' => 'You are not authorized to add users this group as this is not a private group'], 401);
        }

        if (GroupsMembers::where('group_id', '=', $request->get('group_id'))
                        ->where('group_member_id', '=',$request->get('group_member_id'))
                        ->exists()
            ){
          // user already exists
          return response()->json(['status' => 'User already exists.']);
        }

        if(Groups::where('group_owner_id', '=', Auth::user()->id)
		          ->where('is_public_group','=',0)
		          ->exists()
        ){
            $privateGroup = new GroupsMembers([
	          'group_id' => $request->get('group_id'),
	          'group_member_id' => $request->get('group_member_id')
	        ]);
	        $privateGroup->save();
	        return response()->json($privateGroup);
        }
        else{
        	 return response()->json(['error' => 'You are not this group owner, So you can not add users to this group'], 401);
        }
  	}

  	public function removeUserFromPrivateGroup(Request $request){
  	//print_r(Auth::user()->id); exit;
  		$this->validate($request, [
            'group_id'=>'required',
            'group_member_id'=>'required'
        ]);

        if(Groups::where('id', '=', $request->get('group_id'))
          ->where('is_public_group','=',1)
          ->exists()
        ){
          return response()->json(['error' => 'You are not authorized to add users this group as this is not a private group'], 401);
        }

        $groupMember  = GroupsMembers::where('group_id', '=', $request->get('group_id'))
                                    ->where('group_member_id', '=', $request->get('group_member_id'))
                                    ->first();
        if(!$groupMember){
        	return response()->json(['status' => 'User does not exists in this private group.']);
        }                            
        if(Groups::where('group_owner_id', '=', Auth::user()->id)
        	 	  ->where('is_public_group','=',0)
		          ->exists()
        ){
             
	         $groupMember->delete();
	         return response()->json(['status' => 'User has been deleted from the group successfully.']);
        }
       
  	}

  

  }