<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $flashcardSet->title }}
            </h2>
            <div class="space-x-4">
                 <a href="{{ route('teacher.flashcard-sets.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                    {{ __('Back to My Sets') }}
                </a>
                @if($flashcardSet->creator->id === auth()->id())
                    <a href="{{ route('teacher.flashcard-sets.edit', $flashcardSet) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                        {{ __('Edit Set') }}
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Shareable Link -->
            @if($flashcardSet->is_public)
                <div class="mb-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4" role="alert">
                    <p class="font-bold">Shareable Link</p>
                    <p class="text-sm">Anyone with this link can view and practice this set.</p>
                    <div class="mt-2">
                        <input type="text" readonly value="{{ route('public.flashcard-set', $flashcardSet->unique_identifier) }}" class="w-full bg-blue-50 border border-blue-300 rounded-md p-2 text-sm text-gray-700 focus:ring-0 focus:border-blue-500">
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-800">{{ __('Set Details') }}</h3>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $flashcardSet->description ?: 'No description provided.' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Languages</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $flashcardSet->source_language }} → {{ $flashcardSet->target_language }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Visibility</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $flashcardSet->is_public ? 'Public' : 'Private' }}</dd>
                        </div>
                         <div>
                            <dt class="text-sm font-medium text-gray-500">Shareable Link</dt>
                            <dd class="mt-1 text-sm text-indigo-600 hover:text-indigo-900">
                                <a href="{{ route('public.flashcard-set', $flashcardSet->unique_identifier) }}" target="_blank">
                                    {{ route('public.flashcard-set', $flashcardSet->unique_identifier) }}
                                </a>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created By</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $flashcardSet->creator->name }}</dd>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                     <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-700">{{ __("Flashcards in this Set") }}</h3>
                        <div class="space-x-2">
                            <a href="{{ route('practice.start', $flashcardSet) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Practice') }}
                            </a>
                            @if($flashcardSet->creator->id === auth()->id())
                                <a href="{{ route('teacher.flashcard-sets.flashcards.create', $flashcardSet) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Add New Card') }}
                                </a>
                                <a href="{{ route('teacher.flashcard-sets.flashcards.index', $flashcardSet) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                    {{ __('Manage Cards') }}
                                </a>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Placeholder for flashcards list --}}
                    <div class="overflow-x-auto bg-white rounded-lg shadow">
                         <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source Word</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target Word</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                                    @if($flashcardSet->creator->id === auth()->id())
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    @endif
                                </tr>
                            </thead>
                             <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($flashcardSet->flashcards as $card)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $card->source_word }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $card->target_word }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $card->position }}</td>
                                        @if($flashcardSet->creator->id === auth()->id())
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('teacher.flashcard-sets.flashcards.edit', [$flashcardSet, $card]) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                                                <form action="{{ route('teacher.flashcard-sets.flashcards.destroy', [$flashcardSet, $card]) }}" method="POST" class="inline-block ml-4" onsubmit="return confirm('Are you sure you want to delete this flashcard?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                                </form>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $flashcardSet->creator->id === auth()->id() ? '4' : '3' }}" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            This set has no flashcards yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($flashcardSet->creator->id === auth()->id())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" x-data="{ isUploading: false }">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Bulk Import Flashcards') }}</h3>
                        <form 
                            action="{{ route('teacher.flashcard-sets.flashcards.bulk-import', $flashcardSet) }}" 
                            method="POST" 
                            enctype="multipart/form-data"
                            @submit="isUploading = true"
                        >
                            @csrf
                            <div class="mb-4">
                                <x-input-label for="import_file" :value="__('Upload CSV or JSON File')" />
                                <x-text-input id="import_file" name="import_file" type="file" class="mt-1 block w-full" accept=".csv,.json" required />
                            </div>

                            <div class="flex items-center justify-end">
                                <x-primary-button x-bind:disabled="isUploading">
                                    <div x-show="isUploading" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                                    <span x-text="isUploading ? 'Uploading...' : 'Import'"></span>
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout> 