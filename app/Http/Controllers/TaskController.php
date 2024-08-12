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
            // new Middleware('permission:view own tasks', only:['viewTask']),
            new Middleware('permission:edit tasks', only:['edit']),
            new Middleware('permission:create tasks', only:['create']),
            new Middleware('permission:delete tasks', only:['destroy']),
        ];
    }

    public function index(){

        $tasks = Task::latest()->with('user')->paginate(25);
        return view('tasks.list',compact('tasks'));
    }

    public function viewTask($id){

        $taskInfo = Task::where('id',$id)->first();

        if (Auth::user()->id != $taskInfo->assigned_to) {
            abort(404);
        }

        return view('tasks.singleTask',compact('taskInfo'));

    }

    public function create(){

        $users = User::all();
        return view('tasks.create',compact('users'));
    }

    public function store(Request $data){

        $rules = [

            'title' => 'required|min:5',
            'assigned_to' => 'required'
        ];

        if (!empty($data->task_file)) {
            $rules['task_file'] = 'nullable|mimes:pdf,doc,docx,txt,csv|max:5120';
        }

        $validator = Validator::make($data->all(),$rules);


        if ($validator->passes()) {
            $task = new Task();
            $task->title = $data->title;
            $task->description = $data->description;
            $task->assigned_to = $data->assigned_to;
            $task->due_date = $data->due_date;
            $task->save();


            //here we will upload file
            if (!empty($data->task_file)) {

            $file = $data->task_file;
            $fileName = $file->getClientOriginalName();
            $file->move(public_path('task_files'),$fileName);
            $task->file_path = $fileName;
            $task->save();

            }

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
            'assigned_to' => 'required',
            'task_file' => 'nullable|mimes:pdf,doc,docx,txt|max:5120'
        ]);

        if ($validator->passes()) {

            $task->title = $data->title;
            $task->description = $data->description;
            $task->assigned_to = $data->assigned_to;
            $task->due_date = $data->due_date;

            // Check if a new file is uploaded
            if ($data->hasFile('task_file')) {
                // Delete the old file if it exists
                if ($task->file_path && file_exists(public_path('task_files/' . $task->file_path))) {
                    unlink(public_path('task_files/' . $task->file_path));
                }

                // Upload the new file
                $file = $data->file('task_file');
                $fileName = $file->getClientOriginalName();
                $file->move(public_path('task_files'), $fileName);

                // Update the task with the new file path
                $task->file_path = $fileName;
            }

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


        // Check if the task has an associated file and delete it
        if ($task->file_path) {
            $filePath = public_path('task_files/' . $task->file_path);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Delete the task from the database
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
    
    public function pendingTasks(){

        $user = Auth::user();
        $pendingTasks = $user->tasks()->where('status',['todo','in progress'])->get();
        
        return view('dashboard',compact('pendingTasks'));
    }

}
