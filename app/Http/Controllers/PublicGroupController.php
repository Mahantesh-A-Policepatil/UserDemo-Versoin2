<?php
 
  namespace App\Http\Controllers;
   
  use App\Groups;
  use App\GroupUsers;
  use App\Http\Controllers\Controller;
  use Illuminate\Http\Request;
  use Response;
  use Auth;
  use Illuminate\Validation\Rule;
  use League\Fractal\Manager;
  use League\Fractal\Resource\Collection;
  use League\Fractal\Resource\Item;
  use App\Transformers\GroupUsersTransformer;

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
          return response()->json(['status' => 409, 'message' => 'User already exists.'],409);
        }

        $publicGroup = new GroupUsers([
          'group_id' => $group_id,
          'user_id' => Auth::user()->id
       ]);
       $publicGroup->save();
       //return response()->json($publicGroup);

       return response()->json(['status' => 200, 'message' => 'User added to the group successfully.']);
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
         )
      {
        $groupMember  = GroupUsers::where('group_id', '=', $group_id)
                                    ->where('user_id', '=', Auth::user()->id)
                                    ->first();
        $groupMember->delete();
        return response()->json(['status' => 200, 'message' => 'User left the group successfully.']);
      }else{
        return response()->json(['status' => 409, 'message' => 'User does not exists in this public group.']);
      }

   }

  }