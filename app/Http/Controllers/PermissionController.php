<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index(){
        return view('permissions.list');
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

    public function edit(){

    }

    public function update(){

    }

    public function destroy(){

    }
}
