<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\Task;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware():array{

        return [
            new Middleware('permission:view users', only:['index']),
            new Middleware('permission:edit users', only:['edit']),
            new Middleware('permission:create users', only:['create']),
            new Middleware('permission:delete users', only:['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('users.list',compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::orderBy('name','ASC')->get();
        $hasroles = $user->roles->pluck('id');

        return view('users.edit',compact('user','roles','hasroles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $data,$id)
    {
        $user = User::findOrFail($id);
        $validator = Validator::make($data->all(),[
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,'.$id.',id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('users.edit',$id)->withInput()->withErrors($validator);
        }

        $user->name = $data->name;
        $user->email = $data->email;
        $user->save();

        $user->syncRoles($data->role);

        return redirect()->route('users.index',$id)->with('success','User updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
