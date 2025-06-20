<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Student Progress') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Progress on Assigned Sets') }}</h3>
                    
                    @if($assignments->isEmpty())
                        <div class="text-center py-8 text-gray-500">
                            <p>You haven't assigned any flashcard sets yet.</p>
                            <a href="{{ route('teacher.assignments.index') }}" class="mt-2 text-blue-600 hover:text-blue-900">Assign a set</a>
                        </div>
                    @else
                        <div class="space-y-8">
                            @forelse ($studentProgress as $userId => $progressGroup)
                                @php $student = $progressGroup->first()->user; @endphp
                                <div class="p-4 border border-gray-200 rounded-lg">
                                    <h4 class="text-md font-semibold text-gray-800 mb-3">{{ $student->name }} <span class="text-sm text-gray-500">({{ $student->email }})</span></h4>
                                    
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Flashcard Set</th>
                                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Accessed</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach ($progressGroup as $progress)
                                                    <tr>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $progress->flashcardSet->title }}</td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                            @php
                                                                $totalCards = $progress->flashcardSet->flashcards()->count();
                                                                $percentage = $totalCards > 0 ? ($progress->current_position + 1) / $totalCards * 100 : 0;
                                                                if($progress->completed) $percentage = 100;
                                                            @endphp
                                                            <div class="flex items-center">
                                                                <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                                                </div>
                                                                <span>{{ round($percentage) }}%</span>
                                                            </div>
                                                        </td>
                                                        <td class="px-4 py-3 whitespace-nowrap">
                                                            @if($progress->completed)
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                    Completed
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                                    In Progress
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $progress->last_accessed->diffForHumans() }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-gray-500">
                                    <p>No progress data available for your students yet.</p>
                                    <p class="text-sm">Progress will appear here once students start practicing the sets you've assigned.</p>
                                </div>
                            @endforelse
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 