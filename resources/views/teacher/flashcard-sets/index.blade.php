<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Flashcard Sets') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-col md:flex-row justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 md:mb-0">{{ __('Your Flashcard Sets') }}</h3>
                        <div class="flex items-center space-x-4">
                            <form method="GET" action="{{ route('teacher.flashcard-sets.index') }}">
                                <div class="flex">
                                    <label for="search" class="sr-only">Search</label>
                                    <x-text-input type="text" name="search" id="search" placeholder="Search sets..." value="{{ request('search') }}" class="w-full md:w-64"/>
                                    <x-primary-button type="submit" class="ml-2">Search</x-primary-button>
                                </div>
                            </form>
                            <a href="{{ route('teacher.flashcard-sets.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Create New Set') }}
                            </a>
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="mb-4 font-medium text-sm text-green-600 bg-green-100 border border-green-400 rounded-md p-3">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto bg-white rounded-lg shadow">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Languages</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($flashcardSets as $set)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <a href="{{ route('teacher.flashcard-sets.show', $set) }}" class="text-indigo-600 hover:text-indigo-900">{{ $set->title }}</a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $set->source_language }} → {{ $set->target_language }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if ($set->is_public)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Public
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Private
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $set->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('teacher.flashcard-sets.show', $set) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                            <a href="{{ route('teacher.flashcard-sets.edit', $set) }}" class="ml-4 text-yellow-600 hover:text-yellow-900">Edit</a>
                                            <form action="{{ route('teacher.flashcard-sets.destroy', $set) }}" method="POST" class="inline-block ml-4" onsubmit="return confirm('Are you sure you want to delete this flashcard set?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            You haven't created any flashcard sets yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $flashcardSets->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 