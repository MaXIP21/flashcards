<x-guest-layout>
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 py-12">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <header class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $flashcardSet->title }}</h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ $flashcardSet->description }}</p>
                    <div class="mt-4 flex items-center text-sm text-gray-500 dark:text-gray-400">
                        <span>{{ $flashcardSet->source_language }}</span>
                        <svg class="w-4 h-4 mx-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        <span>{{ $flashcardSet->target_language }}</span>
                        <span class="mx-4">|</span>
                        <span>{{ $flashcardSet->flashcards_count }} {{ Str::plural('card', $flashcardSet->flashcards_count) }}</span>
                    </div>
                </header>

                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Flashcards</h2>
                    <a href="{{ route('public.practice', $flashcardSet->unique_identifier) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Practice This Set
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($flashcardSet->flashcards as $flashcard)
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-4 flex flex-col justify-between">
                            <div>
                                <p class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $flashcard->source_word }}</p>
                                <p class="text-md text-gray-600 dark:text-gray-400">{{ $flashcard->target_word }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12">
                            <p class="text-gray-500 dark:text-gray-400">This flashcard set is empty.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-guest-layout> 