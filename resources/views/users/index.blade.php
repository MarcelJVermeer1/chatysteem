<x-app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Find Friends') }}
        </h2>
    </x-slot>

    <div x-data="userSearch()" x-init="fetchUsers()" class="p-6">
        <!-- Search Input -->
        <input type="text"
               x-model="search"
               @input="fetchUsers"
               placeholder="Search users..."
               class="rounded px-3 py-2 w-full md:w-1/3 mb-4"
        >

        <!-- User List -->
        <ul class="space-y-3">
            <template x-for="user in users" :key="user.id">
                <li class="bg-gray-800 p-4 rounded shadow flex items-center justify-between w-full">
                    <!-- Left: Avatar + Name + Bio -->
                    <div class="flex items-center space-x-4">
                        <img
                            :src="user.avatar
                                ? '/avatar/' + user.avatar.split('/').pop()
                                : '/avatar/placeholder3.png'"
                            alt=""
                            class="w-12 h-12 rounded-full object-cover border"
                        >
                        <div>
                            <div class="text-white font-semibold" x-text="user.name"></div>
                            <div class="text-gray-300 text-sm" x-text="user.bio || 'No bio'"></div>
                        </div>
                    </div>

                    <!-- Right: Add Friend Button -->
                    <template x-if="!user.pending">
                        <form method="POST" :action="`/friendships/${user.id}`">
                            <input type="hidden" name="_token" :value="csrf">
                            <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                                Add Friend
                            </button>
                        </form>
                    </template>

                    <template x-if="user.pending">
                        <button disabled class="bg-gray-500 text-white px-4 py-2 rounded cursor-not-allowed">
                            Pending
                        </button>
                    </template>
                </li>
            </template>

            <template x-if="users.length === 0">
                <li class="text-gray-300">No users found.</li>
            </template>
        </ul>
    </div>

    <!-- Alpine Logic -->
    <script>
        function userSearch() {
            return {
                search: '',
                users: [],
                csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

                fetchUsers() {
                    fetch(`/users?search=${encodeURIComponent(this.search)}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            this.users = data;
                        });
                }
            };
        }
    </script>
</x-app-layout>
