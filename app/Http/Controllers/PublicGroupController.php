<?php
 
  namespace App\Http\Controllers;
   
  use App\Groups;
  use App\GroupsMembers;
  use App\Http\Controllers\Controller;
  use Illuminate\Http\Request;
  use Response;
  use Auth;
  use Illuminate\Validation\Rule;

  class PublicGroupController extends Controller{

    public function joinPublicGroup(Request $request){

        $this->validate($request, [
            'group_id'=>'required',
            'group_member_id'=>'required'
        ]);

        if(Groups::where('id', '=', $request->get('group_id'))
          ->where('is_public_group','=',0)
          ->exists()
        ){
          return response()->json(['error' => 'You are not authorized to join this group as this is not a public group'], 401);
        }

        if (GroupsMembers::where('group_id', '=', $request->get('group_id'))
                        ->where('group_member_id', '=',$request->get('group_member_id'))
                        ->exists()
            ){
          // user already exists
          return response()->json(['status' => 'User already exists.']);
        }

        $publicGroup = new GroupsMembers([
          'group_id' => $request->get('group_id'),
          'group_member_id' => $request->get('group_member_id')
       ]);
       $publicGroup->save();
       return response()->json($publicGroup);
     }

    public function leavePublicGroup(Request $request){

      $this->validate($request, [
          'group_id'=>'required',
          'group_member_id'=>'required'
      ]);

      if(Groups::where('id', '=', $request->get('group_id'))
          ->where('is_public_group','=',0)
          ->exists()
        ){
          return response()->json(['error' => 'You are not authorized to join this group as this is not a public group'], 401);
        }

      if (GroupsMembers::where('group_id', '=', $request->get('group_id'))
                        ->where('group_member_id', '=',$request->get('group_member_id'))
                        ->exists()
         ){
        $groupMember  = GroupsMembers::where('group_id', '=', $request->get('group_id'))
                                    ->where('group_member_id', '=', $request->get('group_member_id'))
                                    ->first();
        $groupMember->delete();
        return response()->json(['status' => 'User left the group successfully.']);
      }else{
        return response()->json(['status' => 'User does not exists in this public group.']);
      }

   }

  }