<?php
 
  namespace App\Http\Controllers;
   
  use App\Groups;
  use App\GroupUsers;
  use App\Http\Controllers\Controller;
  use Illuminate\Http\Request;
  use Response;
  use Auth;
  use Illuminate\Validation\Rule;

  class PublicGroupController extends Controller{

    public function joinPublicGroup(Request $request, $group_id){

        // $this->validate($request, [
        //     'group_id'=>'required',
        //     'user_id'=>'required'
        // ]);

        if(Groups::where('id', '=', $group_id)
          ->where('is_public_group','=',0)
          ->exists()
        ){
          return response()->json(['error' => 'You are not authorized to join this group as this is not a public group'], 401);
        }

        if (GroupUsers::where('group_id', '=', $group_id)
                        ->where('user_id', '=',Auth::user()->id)
                        ->exists()
            ){
          // user already exists
          return response()->json(['status' => 'User already exists.']);
        }

        $publicGroup = new GroupUsers([
          'group_id' => $group_id,
          'user_id' => Auth::user()->id
       ]);
       $publicGroup->save();
       return response()->json($publicGroup);
     }

    public function leavePublicGroup(Request $request, $group_id){

      // $this->validate($request, [
      //     'group_id'=>'required',
      //     'user_id'=>'required'
      // ]);

      if(Groups::where('id', '=', $group_id)
          ->where('is_public_group','=',0)
          ->exists()
        ){
          return response()->json(['error' => 'You are not authorized to join this group as this is not a public group'], 401);
        }

      if (GroupUsers::where('group_id', '=', $group_id)
                        ->where('user_id', '=',Auth::user()->id)
                        ->exists()
         ){
        $groupMember  = GroupUsers::where('group_id', '=', $group_id)
                                    ->where('user_id', '=', Auth::user()->id)
                                    ->first();
        $groupMember->delete();
        return response()->json(['status' => 'User left the group successfully.']);
      }else{
        return response()->json(['status' => 'User does not exists in this public group.']);
      }

   }

  }