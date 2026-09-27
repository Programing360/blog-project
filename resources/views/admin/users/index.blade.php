<x-app-layout>
    <x-slot name="title">Users - Laravel Blog</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Users') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @include('admin.partials.flash')

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ $users->total() }} {{ Str::plural('user', $users->total()) }}
                    </h3>
                </div>

                @if ($users->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">User</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Role</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Posts</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Comments</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach ($users as $user)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 shrink-0 rounded-full bg-purple-100 flex items-center justify-center text-purple-700 font-semibold">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>

                                                <div class="min-w-0">
                                                    <p class="font-medium text-gray-900 truncate">
                                                        {{ $user->name }}

                                                        @if ($user->is(auth()->user()))
                                                            <span class="ml-1 text-xs font-normal text-gray-400">(you)</span>
                                                        @endif
                                                    </p>

                                                    <p class="text-xs text-gray-500 truncate">
                                                        {{ $user->email }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold
                                                {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-700' }}">
                                                {{ ucfirst($user->role ?? 'user') }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $user->posts_count }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $user->comments_count }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="inline-flex items-center gap-3">
                                                <a
                                                    href="{{ route('admin.users.edit', $user) }}"
                                                    class="text-indigo-600 hover:text-indigo-800">
                                                    {{ __('Edit Role') }}
                                                </a>

                                                @unless ($user->is(auth()->user()))
                                                    <form
                                                        action="{{ route('admin.users.destroy', $user) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Delete {{ $user->name }}? Their posts and comments will be removed too.');">
                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit" class="text-red-600 hover:text-red-800">
                                                            {{ __('Delete') }}
                                                        </button>
                                                    </form>
                                                @endunless
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $users->links() }}
                    </div>
                @else
                    <p class="p-10 text-center text-gray-600">
                        {{ __('No users found.') }}
                    </p>
                @endif

            </div>

        </div>
    </div>

</x-app-layout>
