<?php

namespace App\Http\Controllers;

use App\Group;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Response;

class PrivateGroupController extends Controller
{

    /**
     * Add user to a private group
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addMemberToPrivateGroup(Request $request, $group_id)
    {
        $this->validate($request, [
            'user_id' => 'required',
        ]);

        if (Group::where('id', '=', $group_id)->where('is_public_group', '=', 1)->exists()) {
            return response()->json(['status' => 401, 'message' => 'You are not authorized to add users this group as this is not a private group'], 401);
        }

        if (Group::where('id', '=', $group_id)->where('group_owner_id', '!=', Auth::user()->id)->exists()) {
            return response()->json(['status' => 401, 'message' => 'You are not this group owner, So you can not add users to this group'], 401);
        }

        $privateGroup = Group::find($group_id);
        $groupuser = $privateGroup->users()->wherePivot('user_id', $request->get('user_id'))->exists();
        if ($groupuser) {
            return response()->json(['status' => 409, 'message' => 'User already exists.'], 409);
        } else {
            $privateGroup->users()->attach($request->get('user_id'), ['created_at' => Carbon::now()->timestamp, 'updated_at' => Carbon::now()->timestamp]);
            return response()->json(['status' => 200, 'message' => 'User added to the group successfully.'], 200);
        }

    }

/**
 * Remove user from a private group
 *
 * @param  int  $id
 * @return \Illuminate\Http\Response
 */
    public function removeMemberFromPrivateGroup(Request $request, $group_id)
    {
        $this->validate($request, [
            'user_id' => 'required',
        ]);

        if (Group::where('id', '=', $group_id)->where('is_public_group', '=', 1)->exists()) {
            return response()->json(['status' => 401, 'message' => 'You are not authorized to add users this group as this is not a private group'], 401);
        }

        if (Group::where('id', '=', $group_id)->where('group_owner_id', '!=', Auth::user()->id)->exists()) {
            return response()->json(['status' => 401, 'message' => 'You are not this group owner, So you can not add users to this group'], 401);
        }

        $privateGroup = Group::find($group_id);
        $groupuser = $privateGroup->users()->wherePivot('user_id', $request->get('user_id'))->exists();
        //echo "Group Member".$request->get('user_id');
        //print_r($groupuser); exit;
        if (!$groupuser) {
            //echo "Control in if"; exit;
            return response()->json(['status' => 404, 'message' => 'User does not exists in this private group.'], 404);
        } else {
            //echo "Control in else"; exit;
            $privateGroup->users()->detach($request->get('user_id'));
            return response()->json(['status' => 200, 'message' => 'User deleted from the group successfully.'], 200);
        }

    }

}
