<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Task Detail
            </h2>
            <a href="{{ route('tasks.index') }}" class="bg-slate-700 text-sm rounded-md px-3 py-2 text-white">Back</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 style="font-size: 30px" class="text-xlg font-semibold mb-4">{{ $taskInfo->title }}</h1>

                    <div class="mb-4">
                        <strong>Description:</strong>
                        <p>{{ $taskInfo->description }}</p>
                    </div>

                    <div class="mb-4">
                        <strong>Assigned To:</strong>
                        <p>{{ $taskInfo->user->name }}</p>
                    </div>


                    @if ($taskInfo->file_path)
                    <div class="mb-4">
                        <strong>Task Document:</strong>
                        <p>
                            <a style="color:blue" href="{{ asset('task_files/' . $taskInfo->file_path) }}" download>{{ $taskInfo->file_path }}</a>
                        </p>
                    </div>
                    @endif


                    <div class="mb-4">
                        <strong>Assign Date:</strong>
                        <p>{{ \Carbon\Carbon::parse($taskInfo->created_at)->format('d M, Y') }}</p>
                    </div>

                    <div class="mb-4">
                        <strong>Due Date:</strong>
                        <p>{{ \Carbon\Carbon::parse($taskInfo->due_date)->format('d M, Y') }}</p>
                    </div>

                    <div class="mb-4">
                        <strong>Status:</strong>
                        <p>{{ ucfirst($taskInfo->status) }}</p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>