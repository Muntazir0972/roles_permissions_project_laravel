<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;


class RoleController extends Controller
{
    public function index(){

        $roles= Role::orderBy('name','ASC')->paginate(25);
        return view('roles.list',compact('roles'));
    }

    public function create(){

        $permissions = Permission::orderBy('name','ASC')->get();
        return view('roles.create',compact('permissions'));
    }

    public function store(Request $data){
        
        $validator = Validator::make($data->all(),[
            'name' =>' required|unique:roles|min:3'
        ]);

        if ($validator->passes()) {
           $role = Role::create(['name' => $data->name]);

            if (!empty($data->permission)) {
                foreach ($data->permission as $name) {
                    $role->givePermissionTo($name);
                }
            }

            return redirect()->route('roles.index')->with('success','Role added successfully.');
        } else{
            return redirect()->route('roles.create')->withInput()->withErrors($validator);
        }
    }

    public function edit($id){

        $role = Role::findOrFail($id);
        $hasPermissions = $role->permissions->pluck('name');
        $permissions = Permission::orderBy('name','ASC')->get();

        return view('roles.edit',compact('hasPermissions','permissions','role'));
    }

    public function update($id,Request $data){

        $role = Role::findOrFail($id);
        $validator = Validator::make($data->all(),[
            'name' =>' required|unique:roles,name,'.$id.',id'
        ]);

        if ($validator->passes()) {

        $role->name = $data->name;
        $role->save();

            if (!empty($data->permission)) {
                $role->syncPermissions($data->permission);
            } else {
                $role->syncPermissions([]);
            }

            return redirect()->route('roles.index')->with('success','Role updated successfully.');
        } else{
            return redirect()->route('roles.edit',$id)->withInput()->withErrors($validator);
        }

    }

    public function destroy(Request $data){

        $id = $data->id; 
        $role = Role::find($id);
        if ($role == null) {
        Session::flash('error','Role not found.');
            return response()->json([
                'status' => false,
            ]);
        }

        $role->delete();
        Session::flash('success','Role Deleted Successfully.');
        return response()->json([
            'status' => true,
        ]);
        
    }

}
