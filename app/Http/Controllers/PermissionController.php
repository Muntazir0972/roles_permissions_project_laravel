<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index(){

        $permissions = Permission::orderBy('created_at','DESC')->paginate(25);
        return view('permissions.list',compact('permissions'));
    }

    public function create(){
        return view('permissions.create');
    }

    public function store(Request $data){
        $validator = Validator::make($data->all(),[
            'name' =>' required|unique:permissions|min:3'
        ]);

        if ($validator->passes()) {
            Permission::create(['name' => $data->name]);
            return redirect()->route('permissions.index')->with('success','Permission added successfully.');
        } else{
            return redirect()->route('permissions.create')->withInput()->withErrors($validator);
        }
    }

    public function edit($id){
        $permission = Permission::findOrFail($id);
        return view('permissions.edit',compact('permission'));
    }

    public function update($id,Request $data){

        $permission = Permission::findOrFail($id);

        $validator = Validator::make($data->all(),[
            'name' =>' required|min:3|unique:permissions,name,'.$id.',id'
        ]);

        if ($validator->passes()) {
            // Permission::create(['name' => $data->name]);
            $permission->name =$data->name;
            $permission->save();

            return redirect()->route('permissions.index')->with('success','Permission updated successfully.');
        } else{
            return redirect()->route('permissions.edit',$id)->withInput()->withErrors($validator);
        }
    }

    public function destroy(Request $data){

        $id = $data->id;
        $permission = Permission::find($id);

        if ($permission === null) {
            session()->flash('error','Permission not found.');
            return response()->json([
                'status' => false
            ]);
        }

        $permission->delete();

        session()->flash('success','Permission deleted successfully.');
            return response()->json([
                'status' => true
            ]);
    }
}
