<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Flashcard Set') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-700 mb-6">{{ __("Enter Details for the New Set") }}</h3>

                    <form action="{{ route('admin.flashcard-sets.store') }}" method="POST">
                        @csrf
                        <div class="space-y-6">
                            <!-- Title -->
                            <div>
                                <x-input-label for="title" :value="__('Title')" />
                                <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                                <x-input-error :messages="$errors->get('title')" class="mt-2" />
                            </div>

                            <!-- Description -->
                            <div>
                                <x-input-label for="description" :value="__('Description')" />
                                <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>

                            <!-- Source Language -->
                            <div>
                                <x-input-label for="source_language" :value="__('Source Language')" />
                                <x-text-input id="source_language" class="block mt-1 w-full" type="text" name="source_language" :value="old('source_language')" required />
                                <x-input-error :messages="$errors->get('source_language')" class="mt-2" />
                            </div>

                            <!-- Target Language -->
                            <div>
                                <x-input-label for="target_language" :value="__('Target Language')" />
                                <x-text-input id="target_language" class="block mt-1 w-full" type="text" name="target_language" :value="old('target_language')" required />
                                <x-input-error :messages="$errors->get('target_language')" class="mt-2" />
                            </div>

                            <!-- Is Public -->
                            <div class="block mt-4">
                                <label for="is_public" class="inline-flex items-center">
                                    <input id="is_public" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="is_public" value="1" {{ old('is_public') ? 'checked' : '' }}>
                                    <span class="ms-2 text-sm text-gray-600">{{ __('Make this set public') }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                             <a href="{{ route('admin.flashcard-sets.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline mr-4">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Create Set') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 