<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\Task;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(){

        $tasks = Task::latest()->with('user')->paginate(25);
        return view('tasks.list',compact('tasks'));
    }

    public function create(){

        $users = User::all();
        return view('tasks.create',compact('users'));
    }

    public function store(Request $data){

        $validator = Validator::make($data->all(),[
            'title' => 'required|min:5',
            'assigned_to' => 'required'
        ]);

        if ($validator->passes()) {
            $task = new Task();
            $task->title = $data->title;
            $task->description = $data->description;
            $task->assigned_to = $data->assigned_to;
            $task->due_date = $data->due_date;
            $task->save();

            return redirect()->route('tasks.index')->with('success','Task assigned successfully.');

        } else {
            return redirect()->route('tasks.create')->withInput()->withErrors($validator);
        }
    }
}
