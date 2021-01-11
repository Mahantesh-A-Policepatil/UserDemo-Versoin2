<?php
 
  namespace App\Http\Controllers;
   
  use App\PublicGroups;
  use App\Http\Controllers\Controller;
  use Illuminate\Http\Request;
  use Response;
  use Illuminate\Support\Facades\Hash;
  use Illuminate\Support\Str;
  use Auth;
  use Illuminate\Validation\Rule;

  class PublicGroupController extends Controller{

   /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  	public function index(Request $request){
   
      $groupName = $request->get('group_name');
        
      if($groupName){
        $publicGroups  = PublicGroups::where('group_name', 'like', $userName."%")->get();
        return response()->json($publicGroups);
      }else{
        $publicGroups  = PublicGroups::all();
        return response()->json($publicGroups);
      }
        
    }

    /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
    public function show($id){

      $publicGroup  = PublicGroups::find($id);
      return response()->json($publicGroup);
    }

     /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   
    public function store(Request $request){
   
      $this->validate($request, [
          'group_name'=>'required',
          'group_member_id'=>'required'
      ]);

       $publicGroup = new User([
          'group_name' => $request->get('group_name'),
          'group_member_id' => $request->get('group_member_id'),
          'group_desc' => isset($request->get('group_desc')) ? $request->get('group_desc') : null;
       ]);
       $publicGroup->save();
     
      return response()->json($publicGroup);
   
    }
   */
    /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
    public function update(Request $request, $id){

      $publicGroup  = PublicGroups::find($id);
      if(!$publicGroup) {
        return response()->json(['status' => 'Group does not exists.']);
      }

      $this->validate($request, [
          'group_name'=>'required',
          'group_member_id'=>'required'
      ]);

      $publicGroup->group_name = $request->get('group_name');
      $publicGroup->group_member_id = $request->get('group_member_id');
      $publicGroup->group_desc = isset($request->get('group_desc')) ? $request->get('group_desc') : null;
    
     
      $publicGroup->update();

      return response()->json($publicGroup);
      
    }  

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        $publicGroup  = PublicGroups::find($id);
        if($publicGroup){
          $publicGroup->delete();
          return response()->json(['status' => 'Group Removed successfully.']);
        }else{
          return response()->json(['status' => 'Group does not exists.']);
        }
    }



  }