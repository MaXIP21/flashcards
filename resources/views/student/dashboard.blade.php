<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Student Dashboard') }}
            </h2>
            <a href="{{ route('student.assignments.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition ease-in-out duration-150">
                {{ __('View All Assignments') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Assigned Sets</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['assigned_sets'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Completed Sets</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['completed_sets'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Public Sets Available</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['public_sets_available'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assigned Sets Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-700 mb-6">{{ __('My Assigned Sets') }}</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse ($assignedSets as $assignment)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="text-lg font-medium text-gray-900">{{ $assignment->flashcardSet->title }}</h4>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Assigned
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500 mb-3">{{ $assignment->flashcardSet->description }}</p>
                                <div class="flex justify-between items-center text-sm text-gray-500 mb-4">
                                    <span>{{ $assignment->flashcardSet->flashcards_count }} cards</span>
                                    <span>{{ $assignment->flashcardSet->source_language }} → {{ $assignment->flashcardSet->target_language }}</span>
                                </div>
                                <div class="text-sm text-gray-500 mb-4">
                                    <p>Assigned by: {{ $assignment->teacher->name }}</p>
                                    @if($assignment->due_date)
                                        <p>Due: {{ $assignment->due_date->format('M d, Y') }}</p>
                                    @endif
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('practice.start', $assignment->flashcardSet) }}" class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-green-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        Practice
                                    </a>
                                    <a href="{{ route('teacher.flashcard-sets.show', $assignment->flashcardSet) }}" class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-gray-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                        View
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-8 text-gray-500">
                                You don't have any assigned flashcard sets yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Recent Progress Section -->
            @if($recentProgress->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-700 mb-6">{{ __('Recent Progress') }}</h3>

                        <div class="space-y-4">
                            @foreach($recentProgress as $progress)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            @if($progress->completed)
                                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @else
                                                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-gray-900">{{ $progress->flashcardSet->title }}</p>
                                            <p class="text-sm text-gray-500">
                                                @if($progress->completed)
                                                    Completed
                                                @else
                                                    Last accessed: {{ $progress->last_accessed->diffForHumans() }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <a href="{{ route('practice.start', $progress->flashcardSet) }}" class="inline-flex items-center px-3 py-2 bg-blue-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        {{ $progress->completed ? 'Review' : 'Continue' }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Public Flashcard Sets Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-700 mb-6">{{ __('Public Flashcard Sets') }}</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse ($publicSets as $set)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="text-lg font-medium text-gray-900">{{ $set->title }}</h4>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Public
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500 mb-3">{{ $set->description }}</p>
                                <div class="flex justify-between items-center text-sm text-gray-500 mb-4">
                                    <span>{{ $set->flashcards_count }} cards</span>
                                    <span>{{ $set->source_language }} → {{ $set->target_language }}</span>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('practice.start', $set) }}" class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-green-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        Practice
                                    </a>
                                    <a href="{{ route('teacher.flashcard-sets.show', $set) }}" class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-gray-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                        View
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-8 text-gray-500">
                                No public flashcard sets available.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 