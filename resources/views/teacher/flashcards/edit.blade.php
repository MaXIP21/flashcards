<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Flashcard in') }} "{{ $flashcardSet->title }}"
            </h2>
            <a href="{{ route('teacher.flashcard-sets.flashcards.index', $flashcardSet) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Back to Cards') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('teacher.flashcard-sets.flashcards.update', [$flashcardSet, $flashcard]) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="source_word" :value="__('Source Word')" />
                            <x-text-input id="source_word" name="source_word" type="text" class="mt-1 block w-full" :value="old('source_word', $flashcard->source_word)" required autofocus />
                            <x-input-error :messages="$errors->get('source_word')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="target_word" :value="__('Target Word')" />
                            <x-text-input id="target_word" name="target_word" type="text" class="mt-1 block w-full" :value="old('target_word', $flashcard->target_word)" required />
                            <x-input-error :messages="$errors->get('target_word')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="position" :value="__('Position')" />
                            <x-text-input id="position" name="position" type="number" class="mt-1 block w-full" :value="old('position', $flashcard->position)" min="0" required />
                            <x-input-error :messages="$errors->get('position')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Update Flashcard') }}</x-primary-button>
                            <a href="{{ route('teacher.flashcard-sets.flashcards.index', $flashcardSet) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 