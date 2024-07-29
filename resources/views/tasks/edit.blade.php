<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tasks / Edit
            </h2>
            <a href="{{ route('tasks.index') }}" class="bg-slate-700 text-sm rounded-md px-3 py-2 text-white">Back</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('tasks.update',$task->id) }}" method="POST">
                        @csrf
                        <div>
                            <label for="" class="text-lg font-medium">Title :</label>
                            <div class="my-3">
                                <input type="text" value="{{ old('title',$task->title) }}" name="title" placeholder="Enter Title" class="border-gray-300 shadow shadow-sm w-1/2 rounded-lg">
                                @error('title')
                                    <p class="text-red-400 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <label for="" class="text-lg font-medium">Description :</label>
                            <div class="my-3">
                            <textarea name="description" id="description" placeholder="Enter Description" class="border-gray-300 shadow shadow-sm w-1/2 rounded-lg" cols="30" rows="10">{{ old('description',$task->description) }}</textarea>                                
                                @error('description')
                                    <p class="text-red-400 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <label for="" class="text-lg font-medium">Task For :</label>
                            <div class="my-3">

                                <select name="assigned_to" id="assigned_to" class="border-gray-300 shadow shadow-sm w-1/2 rounded-lg">
                                    <option value="">Select a Employee</option>
                                    @if ($users->isNotEmpty())
                                        @foreach ($users as $user)
                                            <option {{ $user->id == old('assigned_to', $task->assigned_to) ? 'selected' : '' }} value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('assigned_to')
                                    <p class="text-red-400 font-medium">This field is required</p>
                                @enderror
                            </div>

                            <label for="" class="text-lg font-medium">Task Due Date :</label>
                            <div class="my-3">
                                <input type="date" class="border-gray-300 shadow shadow-sm w-1/2 rounded-lg" name="due_date" id="due_date" value="{{ old('due_date',$task->due_date) }}">
                                @error('due_date')
                                    <p class="text-red-400 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" class="bg-slate-700 text-sm rounded-md px-5 py-3 text-white">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>