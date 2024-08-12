<x-app-layout>
    <x-slot name="header">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 text-gray-900 flex items-center justify-between">
                    <!-- {{ __("You're logged in!") }} -->

                        <h1 class="font-semibold text-red-700">Your Pending Tasks</h1>
                        <i class="fa-regular fa-clock text-red-700 text-xl"></i>
                </div>

            </div>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-2">
            @if(Auth::user()->tasks != null && $pendingTasks->isNotEmpty())
                @foreach ($pendingTasks as $pendingTask)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4 p-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <h2 class="font-semibold text-lg text-gray-800">{{ $pendingTask->title }}</h2>
                                <p class="text-gray-600">{{ $pendingTask->description }}</p>
                                <p class="text-gray-500">Due Date: {{ \Carbon\Carbon::parse($pendingTask->due_date)->format('M d, Y') }}</p>
                            </div>
        
                            @if(Auth::user()->id == $pendingTask->assigned_to)
                                <a href="{{ route('task.view', $pendingTask->id) }}" class="bg-blue-700 text-sm rounded-md px-3 py-2 text-white hover:bg-blue-600">View</a>
                            @else    
                                @can('view all tasks')
                                    <a href="{{ route('task.view', $pendingTask->id) }}" class="bg-blue-700 text-sm rounded-md px-3 py-2 text-white hover:bg-blue-600">View</a>
                                @endcan
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <p class="text-gray-600">No pending tasks.</p>
                </div>
            @endif
        </div>
        
        
    </div>
</x-app-layout>
