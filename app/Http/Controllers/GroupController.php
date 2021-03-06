<?php

namespace App\Http\Controllers;

use App\Group;
use App\GroupUser;
use App\Http\Controllers\Controller;
use App\Transformers\GroupTransformer;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Response;

class GroupController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        /*
         * Check if group_name is there in the query string
         * If group_name is present then we need to filter the group list by group_name
         * Else return all the groups in the response
         */
        $groupName = $request->get('group_name');

        if ($groupName) {
            $groups = Group::where('group_name', 'like', $groupName . "%")->get();
            $manager = new Manager();
            $resource = new Collection($groups, new GroupTransformer());
            $groups = $manager->createData($resource)->toArray();
            return $groups;
        } else {
            if (app('redis')->exists('all_groups')) {
                $groups = app('redis')->get('all_groups');
                return $groups;
            } else {
                $groups = Group::all();
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
    public function show($group_id)
    {
        $group = Group::find($group_id);
        if (!$group) {
            return response()->json(['status' => 'Group does not exists.'], 404);
        }
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
    public function store(Request $request)
    {
        // validate the data
        $this->validate($request, [
            'group_name' => 'required|unique:groups',
            'is_public_group' => 'required',
        ]);

        $group_desc = '';
        if ($request->get('group_desc')) {
            $group_desc = $request->get('group_desc');
        } else {
            $group_desc = null;
        }
        // Create a new group
        $group = new Group([
            'group_name' => $request->get('group_name'),
            'group_owner_id' => auth()->user()->id,
            'is_public_group' => $request->get('is_public_group'),
            'group_desc' => $group_desc,
        ]);
        $group->save();
        // Group owner should be automatically added when the group is created.
        $group->users()->attach(auth()->user()->id, ['created_at' => Carbon::now()->timestamp, 'updated_at' => Carbon::now()->timestamp]);

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
    public function update(Request $request, $group_id)
    {
        // Check if the group exists.
        $group = Group::find($group_id);
        if (!$group) {
            return response()->json(['status' => 'Group does not exists.']);
        }
        // Only Group owner can update the group, unauthorized user should not be authorized for update operation
        if ($group->group_owner_id != auth()->user()->id) {
            return response()->json(['error' => 'You are not authorized to update'], 401);
        }
        // validate the data
        $this->validate($request, [
            'group_name' => ['required', Rule::unique('groups')->ignore($group->id)],
            'is_public_group' => 'required',
        ]);
        $group_desc = '';
        if ($request->get('group_desc')) {
            $group_desc = $request->get('group_desc');
        } else {
            $group_desc = null;
        }
        //Update group information.
        $group->group_name = $request->get('group_name');
        $group->group_owner_id = auth()->user()->id;
        $group->is_public_group = $request->get('is_public_group');
        $group->group_desc = $group_desc;

        $group->update();

        $manager = new Manager();
        $resource = new Item($group, new GroupTransformer());
        $group = $manager->createData($resource)->toArray();
        return $group;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($group_id)
    {
        //echo auth()->user()->id; exit;
        // Check if the group exists.
        $group = Group::find($group_id);
        if (!$group) {
            return response()->json(['status' => 404, 'message' => 'Group does not exists.'], 404);
        }
        // Only Group owner can delete the group, unauthorized user should not be authorized for delete operation
        if ($group->group_owner_id != auth()->user()->id) {
            return response()->json(['status' => 401, 'message' => 'You are not authorized to delete this group'], 401);
        }
        $group_users = collect($group->users())->pluck('id')->toArray();
        if (!empty($group_users)) {
            // Delete all group members attached to this group.
            $group->users()->detach();
        }
        // Delete the group.
        $group->delete();

        if ($group) {
            return response()->json(['status' => 410, 'message' => 'Group deleted successfully!'], 410);
        } else {
            return response()->json(['status' => 400, 'message' => 'Failed to delete group!'], 400);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getGroupMembers(Request $request)
    {
        /*
         * Check if group_name is there in the query string
         * If group_name is present then we need to filter the group_members list by group_name
         * Else return all the group_members in the response
         */
        $group_name = $request->get('group_name');
        if (app('redis')->exists("$group_name")) {
            $group_members = app('redis')->get("$group_name");
            return $group_members;
        } else {
            if ($group_name === '') {
                return response()->json(['status' => 422, 'message' => 'Please enter group name'], 422);
            }
            $group_members['data'] = GroupUser::select('users.id', 'users.username', 'group_user.created_at', 'group_user.updated_at')
                ->leftjoin('users', 'group_user.user_id', '=', 'users.id')
                ->leftjoin('groups', 'group_user.group_id', '=', 'groups.id')
                ->where('groups.group_name', $group_name)
                ->get();
            app('redis')->set("$group_name", json_encode($group_members));
            return $group_members;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getGroupUsers(Request $request)
    {
        /*
         * Check if group_name is there in the query string
         * If group_name is present then we need to filter the group_members list by group_name
         * Else return all the group_members in the response
         */
        $group_name = $request->get('group_name');
        $result = Group::where('group_name', $group_name)
        //where('group_owner_id', $this->user->id)
            ->with(['users'])
            ->get();

        return response()->json([
            'success' => true,
            'status' => 200,
            'data' => $result,
        ]);
    }

}
