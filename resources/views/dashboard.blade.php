<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Messages') }}
        </h2>
    </x-slot>

    <div class="flex h-[80vh] mt-4">
        <!-- Left: Friends List -->
        <div class="w-1/4 bg-gray-900 text-white p-4 overflow-y-auto rounded-l-xl">
            <h2 class="text-lg font-bold mb-3">Friends</h2>
            <ul class="space-y-2">
                @foreach ($friends as $friend)
                    <li>
                        <a href="{{ route('messages.with', $friend->id) }}"
                           class="flex items-center space-x-3 hover:bg-gray-700 p-2 rounded">
                            <img src="{{ $friend->avatar ? asset('avatar/' . basename($friend->avatar)) : asset('images/avatar-placeholder.png') }}"
                                 class="w-10 h-10 rounded-full border">
                            <span>{{ $friend->name }}</span>
                            @if (!empty($friend->hasUnread))
                                <span class="text-xs text-yellow-300 font-semibold">New message</span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Right: Messages Section -->
        <div class="w-3/4 p-6 bg-white dark:bg-gray-800 rounded-r-xl text-gray-800 dark:text-white flex flex-col justify-between">
            @if (isset($receiver))
                <div>
                    <h2 class="text-lg font-bold mb-4 border-b pb-2">Chat with {{ $receiver->name }}</h2>
                    <div class="space-y-2 overflow-y-auto max-h-[60vh]" id="message-box">
                        @foreach ($messages as $message)
                            <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                                <div class="p-2 rounded-lg max-w-md
                                    {{ $message->sender_id === auth()->id()
                                        ? ($message->read_at ? 'bg-blue-500 text-white' : 'bg-gray-400 text-white')
                                        : 'bg-gray-200 text-black' }}">
                                    {{ $message->body }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <form method="POST" action="{{ route('messages.store', $receiver->id) }}" class="mt-4">
                    @csrf
                    <div class="flex gap-2">
                        <input type="text" name="body" placeholder="Type a message..."
                               class="w-full border rounded p-2 text-black" autocomplete="off">
                        <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 rounded">
                            Send
                        </button>
                    </div>
                </form>
            @else
                <p>Select a friend to start chatting.</p>
            @endif
        </div>
    </div>

    <script>
        // Auto-scroll to bottom
        const box = document.getElementById('message-box');
        if (box) {
            box.scrollTop = box.scrollHeight;
        }
    </script>
</x-app-layout>
