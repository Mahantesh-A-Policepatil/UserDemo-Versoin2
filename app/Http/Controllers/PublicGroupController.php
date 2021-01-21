<?php

namespace App\Http\Controllers;

use App\Group;
use App\GroupUser;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Response;

class PublicGroupController extends Controller
{
    /**
     * Join a public group
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function joinPublicGroup(Request $request, $group_id)
    {

        if (Group::where('id', '=', $group_id)
            ->where('is_public_group', '=', 0)
            ->exists()
        ) {
            return response()->json(['status' => 401, 'message' => 'You are not authorized to join this group as this is not a public group'], 401);
        }

        if (GroupUser::where('group_id', '=', $group_id)
            ->where('user_id', '=', Auth::user()->id)
            ->exists()
        ) {
            // user already exists
            return response()->json(['status' => 409, 'message' => 'User already exists.'], 409);
        }

        $publicGroup = new GroupUser([
            'group_id' => $group_id,
            'user_id' => Auth::user()->id,
        ]);
        $publicGroup->save();
        //return response()->json($publicGroup);

        return response()->json(['status' => 200, 'message' => 'User added to the group successfully.'], 200);
    }

     /**
     * Leave a public group
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function leavePublicGroup(Request $request, $group_id)
    {

        if (Group::where('id', '=', $group_id)
            ->where('is_public_group', '=', 0)
            ->exists()
        ) {
            return response()->json(['status' => 401, 'message' => 'You are not authorized to join this group as this is not a public group'], 401);
        }

        if (GroupUser::where('group_id', '=', $group_id)
            ->where('user_id', '=', Auth::user()->id)
            ->exists()
        ) {
            $groupMember = GroupUser::where('group_id', '=', $group_id)
                ->where('user_id', '=', Auth::user()->id)
                ->first();
            $groupMember->delete();
            return response()->json(['status' => 200, 'message' => 'User left the group successfully.'], 200);
        } else {
            return response()->json(['status' => 409, 'message' => 'User does not exists in this public group.'], 409);
        }

    }

}
