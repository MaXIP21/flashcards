<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('User Details') }}
            </h2>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Back to Users List
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- User Information -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">User Information</h3>
                            <dl class="mt-4 space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Role</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($user->role) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Registered At</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('F j, Y, g:i a') }}</dd>
                                </div>
                            </dl>
                        </div>
                        
                        <!-- Activation Status -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Activation Status</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1">
                                        @if ($user->is_activated)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending Activation</span>
                                        @endif
                                    </dd>
                                </div>

                                @if($user->isTeacher())
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Activated At</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $user->activated_at ? $user->activated_at->format('F j, Y, g:i a') : 'N/A' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Activated By</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            @if($user->activatedBy)
                                                Activated by: {{ $user->activatedBy->name }}
                                            @else
                                                N/A
                                            @endif
                                        </dd>
                                    </div>

                                    <div class="mt-6">
                                        @if($user->is_activated)
                                            <form action="{{ route('admin.users.deactivate', $user) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500" onclick="return confirm('Are you sure you want to deactivate this teacher?')">
                                                    Deactivate Teacher
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.users.activate', $user) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" onclick="return confirm('Are you sure you want to activate this teacher?')">
                                                    Activate Teacher
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Delete User Section -->
                    @if($user->id !== auth()->id())
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 text-red-600">Danger Zone</h3>
                            <p class="mt-2 text-sm text-gray-600">
                                Once you delete a user, there is no going back. Please be certain.
                            </p>
                            <div class="mt-4">
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" 
                                            onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone and will permanently remove all associated data including flashcard sets, assignments, and progress records.')">
                                        Delete User
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 