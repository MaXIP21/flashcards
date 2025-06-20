<x-guest-layout>
    <div x-data="practiceSession({ flashcards: {{ json_encode($flashcards) }} })" x-init="init()" @keydown.window.arrow-right="nextCard()" @keydown.window.arrow-left="previousCard()" @keydown.window.space.prevent="flipCard()">
        <main class="container mx-auto p-4 md:p-8">
            <div class="max-w-4xl mx-auto">
                <!-- Header -->
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl md:text-2xl font-semibold text-gray-800">
                        {{ __('Practice') }}: {{ $flashcardSet->title }}
                    </h2>
                    <a href="{{ route('public.flashcard-set', $flashcardSet->unique_identifier) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Exit') }}
                    </a>
                </div>

                <!-- Progress Bar -->
                <div class="mb-4">
                    <div class="bg-gray-200 rounded-full h-2.5">
                        <div class="bg-blue-600 h-2.5 rounded-full" :style="`width: ${((currentPosition) / totalCards) * 100}%`"></div>
                    </div>
                    <p class="text-center text-sm text-gray-600 mt-2" x-text="`${currentPosition} / ${totalCards}`"></p>
                </div>

                <!-- Main Practice Area -->
                <div class="bg-white p-6 rounded-lg shadow-lg relative min-h-[300px]">
                    <template x-if="!completed">
                        <div class="flex flex-col items-center justify-center">
                            <!-- Card -->
                            <div class="relative perspective-1000 h-64 w-full max-w-md">
                                <div class="card-container w-full h-full" :class="{ 'flipped': isFlipped }" @click="flipCard()" style="transform-style: preserve-3d; transition: transform 0.8s cubic-bezier(0.25, 1, 0.5, 1);">
                                    <!-- Front -->
                                    <div class="card-face front absolute w-full h-full bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg flex items-center justify-center cursor-pointer" style="backface-visibility: hidden;">
                                        <p class="text-3xl font-semibold text-white p-4" x-text="currentCard?.source_word"></p>
                                    </div>
                                    <!-- Back -->
                                    <div class="card-face back absolute w-full h-full bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg flex items-center justify-center cursor-pointer" style="backface-visibility: hidden; transform: rotateY(180deg);">
                                        <p class="text-3xl font-semibold text-white p-4" x-text="currentCard?.target_word"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Navigation -->
                            <div class="mt-8 flex justify-between w-full max-w-md">
                                <button @click="previousCard()" :disabled="currentPosition <= 1" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg shadow hover:bg-gray-400 disabled:opacity-50 disabled:cursor-not-allowed">Previous</button>
                                <button @click="nextCard()" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg shadow hover:bg-gray-400">Next</button>
                            </div>
                        </div>
                    </template>

                    <!-- Completion Message -->
                    <template x-if="completed">
                        <div class="text-center py-10">
                            <h3 class="text-2xl font-bold text-green-600">Congratulations!</h3>
                            <p class="mt-2 text-gray-700">You have completed this set.</p>
                            <div class="mt-6 space-x-4">
                                <button @click="restartPractice()" class="px-6 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700">Practice Again</button>
                                <a href="{{ route('public.flashcard-set', $flashcardSet->unique_identifier) }}" class="px-6 py-2 bg-gray-600 text-white rounded-lg shadow hover:bg-gray-700">Back to Set</a>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </main>
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
                init() {
                    this.totalCards = this.flashcards.length;
                    this.updateCard();
                },
                updateCard() {
                    this.currentCard = this.flashcards[this.currentPosition - 1];
                    this.isFlipped = false;
                },
                flipCard() { this.isFlipped = !this.isFlipped; },
                nextCard() {
                    if (this.currentPosition >= this.totalCards) {
                        this.completed = true;
                        return;
                    }
                    this.currentPosition++;
                    this.updateCard();
                },
                previousCard() {
                    if (this.currentPosition <= 1) return;
                    this.currentPosition--;
                    this.completed = false;
                    this.updateCard();
                },
                restartPractice() {
                    this.currentPosition = 1;
                    this.completed = false;
                    this.updateCard();
                }
            }
        }
    </script>
    <style>
        .perspective-1000 { perspective: 1000px; }
        .card-container.flipped { transform: rotateY(180deg); }
        .card-face { backface-visibility: hidden; }
    </style>
</x-guest-layout> 