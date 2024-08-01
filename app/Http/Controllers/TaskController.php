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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TaskAssignEmail;

class TaskController extends Controller implements HasMiddleware
{
    public static function middleware():array{

        return [
            new Middleware('permission:view tasks', only:['index']),
            new Middleware('permission:edit tasks', only:['edit']),
            new Middleware('permission:create tasks', only:['create']),
            new Middleware('permission:delete tasks', only:['destroy']),
        ];
    }

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

            $assignedUser = User::find($task->assigned_to);

            if ($assignedUser) {
                $userEmail = $assignedUser->email;
                // dd($userEmail); 

                $mailData = [
                    'userEmail' => $userEmail,
                    'task' => $task
                ];

                Mail::to($assignedUser->email)->send(new TaskAssignEmail($mailData));
            }

            return redirect()->route('tasks.index')->with('success','Task assigned successfully.');

        } else {
            return redirect()->route('tasks.create')->withInput()->withErrors($validator);
        }
    }

    public function edit($id){
        
        $task = Task::findOrFail($id);
        $users = User::all();
        return view('tasks.edit',compact('task','users'));
    }

    public function update(Request $data,$id){

        $task = Task::findOrFail($id);

        $validator = Validator::make($data->all(),[
            'title' => 'required|min:5',
            'assigned_to' => 'required'
        ]);

        if ($validator->passes()) {

            $task->title = $data->title;
            $task->description = $data->description;
            $task->assigned_to = $data->assigned_to;
            $task->due_date = $data->due_date;
            $task->save();

            return redirect()->route('tasks.index')->with('success','Task updated successfully.');

        } else {
            return redirect()->route('tasks.edit',$id)->withInput()->withErrors($validator);
        }
    }

    public function destroy(Request $data){

        $task = Task::find($data->id);

        if ($task == null) {
            Session::flash('error','Task not found.');
            return response()->json([
                'status' => false
            ]);
        }

        $task->delete();
        Session::flash('success','Task deleted sucessfully.');
        return response()->json([
            'status' => true
        ]);
    }

    public function updateStatus(Request $data, $id)
    {
        $task = Task::find($id);
        
        if (!$task) {
            Session::flash('error', 'Task not found');
            return response()->json(['success' => false]);
        }
    
        if (Auth::user()->id == $task->assigned_to) {
            $task->status = $data->input('status');
            $task->save();
            
            Session::flash('success', 'Status updated successfully');
            return response()->json(['success' => true]);
        }
    
        Session::flash('error', 'You are not authorized to update this task status');
        return response()->json(['success' => false]);
    }
    
    

}
