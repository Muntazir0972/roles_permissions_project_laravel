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
use Illuminate\Support\Facades\Hash;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware():array{

        return [
            new Middleware('permission:view users', only:['index']),
            new Middleware('permission:edit users', only:['edit']),
            new Middleware('permission:create users', only:['create']),
            new Middleware('permission:delete users', only:['destroy']),
            new Middleware('permission:can change status', only:['changeStatus']),
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
        $roles = Role::orderBy('name','ASC')->get();
        return view('users.create',compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $data)
    {
        $validator = Validator::make($data->all(),[
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:5|same:confirm_password',
            'confirm_password' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('users.create')->withInput()->withErrors($validator);
        }

        $user = new User();
        $user->name = $data->name;
        $user->email = $data->email;
        $user->password = Hash::make($data->password);
        $user->save();

        $user->syncRoles($data->role);

        return redirect()->route('users.index')->with('success','User added successfully.');   
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
    public function destroy(Request $data)
    {
        $user = User::find($data->id);

        if ($user == null) {
            Session::flash('error','User not found');
            return response()->json([
                'status' => false
            ]);
        }

        $user->delete();
        Session::flash('success','User deleted successfully');
            return response()->json([
                'status' => true
            ]);
    }

    public function changeStatus($status,$id){

        $user = User::find($id);
        $user->status=$status;
        $user->save();

    return redirect()->route('users.index')->with('success', 'User status updated successfully.');

    }
}
