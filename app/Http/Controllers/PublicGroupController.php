<?php

namespace App\Http\Controllers;

use App\Group;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;

class PublicGroupController extends Controller
{
    /**
     * Join a public group
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function joinPublicGroup(Request $request)
    {
        $this->validate($request, [
            'group_id' => 'required',
        ]);
        $group_id = $request->group_id;
        // Unauthorized user should not be authorized for update operation
        if (Group::where('id', '=', $group_id)
            ->where('is_public_group', '=', 0)
            ->exists()
        ) {
            return response()->json(['status' => 401, 'message' => 'You are not authorized to join this group as this is not a public group'], 401);
        }
        $publicGroup = Group::find($group_id);
        $groupuser = $publicGroup->users()->wherePivot('user_id', auth()->user()->id)->exists();

        if ($groupuser) {
            return response()->json(['status' => 409, 'message' => 'User already exists.'], 409);
        }

        $publicGroup->users()->attach(auth()->user()->id, ['created_at' => Carbon::now()->timestamp, 'updated_at' => Carbon::now()->timestamp]);
        return response()->json(['status' => 200, 'message' => 'User added to the group successfully.'], 200);
    }

    /**
     * Leave a public group
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function leavePublicGroup(Request $request)
    {
        $this->validate($request, [
            'group_id' => 'required',
        ]);
        $group_id = $request->group_id;
        if (Group::where('id', '=', $group_id)
            ->where('is_public_group', '=', 0)
            ->exists()
        ) {
            return response()->json(['status' => 401, 'message' => 'You are not authorized to join this group as this is not a public group'], 401);
        }
        $publicGroup = Group::find($group_id);
        $groupuser = $publicGroup->users()->wherePivot('user_id', auth()->user()->id)->exists();
        if ($groupuser) {

            $publicGroup = Group::find($group_id);
            $publicGroup->users()->detach(auth()->user()->id);

            return response()->json(['status' => 200, 'message' => 'User left the group successfully.'], 200);
        } else {
            return response()->json(['status' => 409, 'message' => 'User does not exists in this public group.'], 409);
        }

    }

}
