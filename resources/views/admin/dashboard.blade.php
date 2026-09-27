<x-app-layout>
    <x-slot name="title">Admin Dashboard - Laravel Blog</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Admin Dashboard') }}
            </h2>

            <span class="inline-flex items-center px-3 py-1 rounded-full bg-purple-100 text-purple-800 text-xs font-semibold uppercase tracking-wider">
                {{ __('Administrator') }}
            </span>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-6 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Summary Stats --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                @foreach ([
                    ['label' => 'Total Posts', 'value' => $totalPosts, 'href' => route('admin.dashboard'), 'accent' => 'bg-indigo-500'],
                    ['label' => 'Total Users', 'value' => $totalUsers, 'href' => route('admin.users.index'), 'accent' => 'bg-purple-500'],
                    ['label' => 'Total Comments', 'value' => $totalComments, 'href' => route('admin.dashboard'), 'accent' => 'bg-emerald-500'],
                    ['label' => 'Total Categories', 'value' => $totalCategories, 'href' => route('admin.categories.index'), 'accent' => 'bg-amber-500'],
                ] as $stat)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <p class="text-sm font-medium text-gray-500">
                                {{ $stat['label'] }}
                            </p>

                            <p class="mt-2 text-3xl font-bold text-gray-900">
                                {{ $stat['value'] }}
                            </p>
                        </div>

                        <div class="{{ $stat['accent'] }} h-1.5"></div>
                    </div>
                @endforeach

            </div>

            {{-- Quick Actions --}}
            <div class="mt-6 flex flex-wrap gap-3">
                <a
                    href="{{ route('admin.categories.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                    {{ __('New Category') }}
                </a>

                <a
                    href="{{ route('admin.users.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
                    {{ __('Manage Users') }}
                </a>
            </div>

            <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Recent Posts --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-base font-semibold text-gray-900">
                            {{ __('Recent Posts') }}
                        </h3>

                        <a href="{{ route('posts.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                            {{ __('View Blog') }}
                        </a>
                    </div>

                    @if ($recentPosts->count() > 0)
                        <ul class="divide-y divide-gray-100">
                            @foreach ($recentPosts as $post)
                                <li class="px-6 py-4 flex items-center justify-between gap-4">
                                    <div class="min-w-0">
                                        <a
                                            href="{{ route('posts.show', $post->slug) }}"
                                            class="block truncate font-medium text-gray-900 hover:text-indigo-600 transition">
                                            {{ $post->title }}
                                        </a>

                                        <p class="mt-1 text-xs text-gray-500">
                                            {{ $post->user?->name ?? 'Unknown' }}
                                            &bull;
                                            {{ $post->category?->name ?? 'Uncategorised' }}
                                        </p>
                                    </div>

                                    <span class="shrink-0 inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $post->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ ucfirst($post->status) }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="px-6 py-8 text-center text-sm text-gray-500">
                            {{ __('No posts yet.') }}
                        </p>
                    @endif
                </div>

                {{-- Recent Users --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-base font-semibold text-gray-900">
                            {{ __('Recent Users') }}
                        </h3>

                        <a href="{{ route('admin.users.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                            {{ __('Manage') }}
                        </a>
                    </div>

                    @if ($recentUsers->count() > 0)
                        <ul class="divide-y divide-gray-100">
                            @foreach ($recentUsers as $user)
                                <li class="px-6 py-4 flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-9 h-9 shrink-0 rounded-full bg-purple-100 flex items-center justify-center text-purple-700 font-semibold">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>

                                        <div class="min-w-0">
                                            <p class="truncate font-medium text-gray-900">
                                                {{ $user->name }}
                                            </p>

                                            <p class="text-xs text-gray-500">
                                                {{ $user->posts_count }} {{ Str::plural('post', $user->posts_count) }}
                                            </p>
                                        </div>
                                    </div>

                                    <span class="shrink-0 inline-flex rounded-full px-3 py-1 text-xs font-semibold
                                        {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-700' }}">
                                        {{ ucfirst($user->role ?? 'user') }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="px-6 py-8 text-center text-sm text-gray-500">
                            {{ __('No users yet.') }}
                        </p>
                    @endif
                </div>

            </div>

        </div>
    </div>

</x-app-layout>
