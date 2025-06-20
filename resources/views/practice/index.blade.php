<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Practice') }}: {{ $flashcardSet->title }}
            </h2>
            <form action="{{ route('practice.exit', $flashcardSet) }}" method="POST" class="inline-block">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Exit') }}
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-12" x-data="practiceSession({ flashcards: {{ json_encode($flashcards) }} })" x-init="init()" @keydown.window.arrow-right="nextCard()" @keydown.window.arrow-left="previousCard()" @keydown.window.space.prevent="flipCard()">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Practice Controls -->
            <div class="mb-6 flex justify-end space-x-2">
                <button @click="shuffleCards()" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Shuffle') }}
                </button>
                <button @click="restartPractice()" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-800 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Restart') }}
                </button>
            </div>

            <!-- Progress Bar -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Progress') }}</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400" x-text="`${currentPosition} / ${totalCards}`"></span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                         :style="`width: ${(currentPosition / totalCards) * 100}%`"></div>
                </div>
            </div>

            <!-- Completion Message -->
            <div x-show="completed" x-transition class="mb-8 bg-green-100 dark:bg-green-800 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 px-4 py-3 rounded">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">{{ __('Congratulations! You have completed this set.') }}</span>
                </div>
            </div>

            <!-- Flashcard and Navigation -->
            <div class="flex flex-col items-center">
                <!-- Flashcard -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-8 w-full max-w-md">
                    <div class="p-8">
                        <div class="flex justify-center">
                            <div class="w-full">
                                <!-- Card Container -->
                                <div class="relative perspective-1000 h-64">
                                    <div class="card-container w-full h-full"
                                         :class="{ 'flipped': isFlipped }"
                                         @click="flipCard()"
                                         style="transform-style: preserve-3d; transition: transform 0.8s cubic-bezier(0.25, 1, 0.5, 1);">

                                        <!-- Front of Card -->
                                        <div class="card-face front absolute w-full h-full bg-gradient-to-br from-indigo-600 to-indigo-700 rounded-lg shadow-lg flex items-center justify-center cursor-pointer"
                                             style="backface-visibility: hidden;">
                                            <div class="text-center text-white p-4">
                                                <h3 class="text-2xl font-bold mb-2">{{ $flashcardSet->source_language }}</h3>
                                                <p class="text-4xl font-bold" x-text="currentCard ? currentCard.source_word : 'Loading...'"></p>
                                                <p class="text-sm mt-4 opacity-75">Click to flip</p>
                                            </div>
                                        </div>

                                        <!-- Back of Card -->
                                        <div class="card-face back absolute w-full h-full bg-gradient-to-br from-purple-600 to-purple-700 rounded-lg shadow-lg flex items-center justify-center cursor-pointer"
                                             style="backface-visibility: hidden; transform: rotateY(180deg);">
                                            <div class="text-center text-white p-4">
                                                <h3 class="text-2xl font-bold mb-2">{{ $flashcardSet->target_language }}</h3>
                                                <p class="text-4xl font-bold" x-text="currentCard ? currentCard.target_word : 'Loading...'"></p>
                                                <p class="text-sm mt-4 opacity-75">Click to flip back</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Controls -->
                <div class="flex justify-center space-x-4">
                    <button @click="previousCard()"
                            :disabled="currentPosition <= 1"
                            class="inline-flex items-center px-6 py-3 bg-gray-600 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 dark:hover:bg-gray-600 focus:bg-gray-700 dark:focus:bg-gray-600 active:bg-gray-900 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 disabled:cursor-not-allowed transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        {{ __('Previous') }}
                    </button>

                    <button @click="nextCard()"
                            :disabled="completed"
                            class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 disabled:cursor-not-allowed transition ease-in-out duration-150">
                        {{ __('Next') }}
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Keyboard Navigation Info -->
            <div class="mt-8 text-center text-sm text-gray-500 dark:text-gray-400">
                <p>Use <kbd class="px-2 py-1 bg-gray-200 dark:bg-gray-600 rounded">←</kbd> and <kbd class="px-2 py-1 bg-gray-200 dark:bg-gray-600 rounded">→</kbd> arrow keys to navigate, <kbd class="px-2 py-1 bg-gray-200 dark:bg-gray-600 rounded">Space</kbd> to flip card</p>
            </div>
        </div>
    </div>

    <script>
        function practiceSession(data) {
            return {
                flashcards: data.flashcards,
                currentCard: null,
                currentPosition: 1,
                totalCards: 0,
                isFlipped: false,
                completed: false,
                flashcardSetId: {{ $flashcardSet->id }},

                init() {
                    this.totalCards = this.flashcards.length;
                    if (this.totalCards > 0) {
                        this.updateCard(); // Set initial card
                        this.loadCardFromProgress();
                    }
                },

                async loadCardFromProgress() {
                    try {
                        const response = await fetch("{{ route('practice.get-progress', $flashcardSet) }}");
                        if (!response.ok) {
                            throw new Error('No progress found');
                        }
                        const data = await response.json();
                        
                        this.currentPosition = data.current_position + 1;
                        this.completed = data.completed;
                        this.updateCard();

                    } catch (error) {
                        console.error('Error loading card from progress:', error);
                        this.currentPosition = 1;
                        this.updateCard();
                    }
                },

                updateCard() {
                    if (this.currentPosition > this.totalCards) {
                        this.currentPosition = this.totalCards;
                    }
                    if (this.currentPosition < 1) {
                        this.currentPosition = 1;
                    }
                    this.currentCard = this.flashcards[this.currentPosition - 1];
                    this.isFlipped = false;
                },

                flipCard() {
                    this.isFlipped = !this.isFlipped;
                },

                async nextCard() {
                    if (this.completed) return;
                    if (this.currentPosition >= this.totalCards) {
                        this.completed = true;
                        this.saveProgress();
                        return;
                    }

                    this.currentPosition++;
                    this.updateCard();
                    this.saveProgress();
                },

                async previousCard() {
                    if (this.currentPosition <= 1) return;
                    
                    this.currentPosition--;
                    this.completed = false; // Not completed if going back
                    this.updateCard();
                    this.saveProgress();
                },
                
                async saveProgress() {
                    try {
                        await fetch("{{ route('practice.save-progress', $flashcardSet) }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                current_position: this.currentPosition - 1,
                                completed: this.completed
                            })
                        });
                    } catch (error) {
                        console.error('Error saving progress:', error);
                    }
                },

                shuffleCards() {
                    for (let i = this.flashcards.length - 1; i > 0; i--) {
                        const j = Math.floor(Math.random() * (i + 1));
                        [this.flashcards[i], this.flashcards[j]] = [this.flashcards[j], this.flashcards[i]];
                    }
                    this.restartPractice(false); // Restart without confirmation
                    // Optionally, you can add a toast notification here
                },

                async restartPractice(confirmFirst = true) {
                    if (confirmFirst && !confirm('Are you sure you want to restart this practice session?')) {
                        return;
                    }

                    try {
                        await fetch("{{ route('practice.restart', $flashcardSet) }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json',
                            },
                        });
                        
                        this.completed = false;
                        this.currentPosition = 1;
                        this.updateCard();
                        this.saveProgress();
                    } catch (error) {
                        console.error('Error restarting practice:', error);
                    }
                }
            }
        }
    </script>

    <style>
        .perspective-1000 {
            perspective: 1000px;
        }
        
        .card-container.flipped {
            transform: rotateY(180deg);
        }
        
        .card-face {
            width: 100%;
            height: 100%;
        }
    </style>
</x-app-layout> 