<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

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
}
