<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Friend Requests') }}
        </h2>
    </x-slot>

    <div class="p-6">
        @forelse($pendingRequests as $request)
            <div class="bg-gray-800 text-white p-4 mb-3 rounded flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <img
                        src="{{ $request->sender->avatar
                        ? '/avatar/' . basename($request->sender->avatar)
                        :  asset('/avatar/placeholder3.png') }}"
                        alt="{{ $request->sender->name }}"
                        class="w-12 h-12 rounded-full object-cover border"
                    >
                    <div>
                        <div class="font-bold">{{ $request->sender->name }}</div>
                        <div class="text-sm text-gray-300">
                            {{ $request->sender->bio ?? 'No bio' }}
                        </div>
                    </div>
                </div>

                <div class="flex gap-2">
                    <!-- Accept -->
                    <form method="POST" action="{{ route('friendships.accept', $request->id) }}">
                        @csrf
                        <button class="bg-green-500 px-4 py-2 rounded">Accept</button>
                    </form>

                    <!-- Deny -->
                    <form method="POST" action="{{ route('friendships.deny', $request->id) }}">
                        @csrf
                        @method('DELETE')
                        <button class="bg-red-500 px-4 py-2 rounded">Deny</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-gray-300">No pending requests.</p>
        @endforelse
    </div>
</x-app-layout>
