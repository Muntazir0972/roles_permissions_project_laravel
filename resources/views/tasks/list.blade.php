<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">

            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tasks') }}
            </h2>

            @can('create tasks')
            <a href="{{ route('tasks.create') }}" class="bg-slate-700 text-sm rounded-md px-3 py-2 text-white">Create</a>
            @endcan

        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <x-message />

            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="border-b">
                        <th class="px-6 py-3 text-left" width="60">#</th>
                        <th class="px-6 py-3 text-left" width="150">Title</th>
                        <th class="px-6 py-3 text-left" width="150">Assigned To</th>
                        <th class="px-6 py-3 text-left" width="150">Created</th>
                        <th class="px-6 py-3 text-left" width="150">Due Date</th>
                        <th class="px-6 py-3 text-left" width="150">Status</th>
                        <th class="px-6 py-3 text-center" width="280">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white">

                    @if ($tasks->isNotempty())
                        @foreach ($tasks as $task)    
                        <tr class="border-b">
                            <td class="px-6 py-3 text-left">{{ $task->id }}</td>
                            <td class="px-6 py-3 text-left">{{ $task->title }}</td>
                            <td class="px-6 py-3 text-left">{{ $task->user->name }}</td>
                            <td class="px-6 py-3 text-left">{{ \Carbon\Carbon::parse($task->created_at)->format('d M,Y') }}</td>
                            <td class="px-6 py-3 text-left">{{ \Carbon\Carbon::parse($task->due_date)->format('d M,Y') }}</td>

                            <td class="px-6 py-3 text-left">
                                @if (Auth::user()->id == $task->assigned_to)
                                    <select onchange="updateStatus(this, {{ $task->id }})" style="width: 130px" class="bg-white border border-gray-300 rounded px-2 py-1">
                                        <option value="todo" {{ $task->status == 'todo' ? 'selected' : '' }}>Todo</option>
                                        <option value="in progress" {{ $task->status == 'in progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                @else
                                    <span>{{ ucfirst($task->status) }}</span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-3 text-center">
                                
                                @if(Auth::user()->id == $task->assigned_to)
                                    <a href="{{ route('task.view', $task->id) }}" class="bg-blue-700 text-sm rounded-md px-3 py-2 text-white hover:bg-blue-600">View</a>
                                @else    
                                @can('view all tasks')
                                <a href="{{ route('task.view', $task->id) }}" class="bg-blue-700 text-sm rounded-md px-3 py-2 text-white hover:bg-blue-600">View</a>
                                @endcan
                                @endif



                                @can('edit tasks')
                                <a href="{{ route('tasks.edit',$task->id) }}" class="bg-slate-700 text-sm rounded-md px-3 py-2 text-white hover:bg-slate-600">Edit</a>
                                @endcan

                                @can('delete tasks')
                                <a href="javascript:void(0)" onclick="deleteTask({{ $task->id }})" class="bg-red-700 text-sm rounded-md px-3 py-2 text-white hover:bg-red-600">Delete</a>
                                @endcan


                            </td>
                        </tr>
                        @endforeach
                    @endif

                </tbody>
            </table>

            <div class="my-3">
                {{ $tasks->links() }}
            </div>

        </div>
    </div>
    <x-slot name="script">
        <script type="text/javascript">
            function deleteTask(id){
                if (confirm("Are You sure you want to delete?")) {
                    $.ajax({
                        url:'{{ route("tasks.destroy") }}',
                        type: 'delete',
                        data:{id:id},
                        dataType:'json',
                        headers:{
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'                        
                        },
                        success:function(response){
                            window.location.href = "{{ route('tasks.index') }}";
                        }
                    });
                }
            }

    function updateStatus(selectElement, taskId) {
    const status = selectElement.value;

    fetch(`/tasks/update-status/${taskId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Redirect to the tasks index page after updating the status
            window.location.href = "{{ route('tasks.index') }}";
        } else {
            alert('Failed to update status.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred.');
    });
}

        </script>
    </x-slot>
</x-app-layout>