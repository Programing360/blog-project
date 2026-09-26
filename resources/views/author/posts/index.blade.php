<x-app-layout>
    <x-slot name="title">My Posts - Laravel Blog</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Posts') }}
            </h2>

            <a
                href="{{ route('author.posts.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                {{ __('New Post') }}
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
            <div class="mb-6 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ $posts->total() }} {{ Str::plural('post', $posts->total()) }}
                    </h3>
                </div>

                @if ($posts->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Title</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Category</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Created</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach ($posts as $post)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if ($post->image)
                                        <img
                                            src="{{ Storage::url($post->image) }}"
                                            alt="{{ $post->title }}"
                                            class="h-10 w-16 shrink-0 rounded object-cover">
                                        @endif

                                        <div>
                                            <a
                                                href="{{ route('posts.show', $post->slug) }}"
                                                class="font-medium text-gray-900 hover:text-indigo-600 transition">
                                                {{ $post->title }}
                                            </a>

                                            @if ($post->tags->count() > 0)
                                            <div class="mt-1 flex flex-wrap gap-1">
                                                @foreach ($post->tags as $tag)
                                                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-600">
                                                    #{{ $tag->name }}
                                                </span>
                                                @endforeach
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $post->category?->name ?? '—' }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($post->status === 'published')
                                    <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">
                                        {{ ucfirst($post->status) }}
                                    </span>
                                    @else
                                    <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">
                                        {{ ucfirst($post->status) }}
                                    </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $post->created_at->format('M d, Y') }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="inline-flex items-center gap-3">
                                        <a
                                            href="{{ route('author.posts.edit', $post) }}"
                                            class="text-indigo-600 hover:text-indigo-800">
                                            {{ __('Edit') }}
                                        </a>

                                        <form
                                            action="{{ route('author.posts.destroy', $post) }}"
                                            method="POST"
                                            onsubmit="return confirm('Delete this post? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="text-red-600 hover:text-red-800">
                                                {{ __('Delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $posts->links() }}
                </div>
                @else
                <div class="p-10 text-center">
                    <h4 class="text-lg font-semibold text-gray-900">
                        {{ __('No posts yet') }}
                    </h4>

                    <p class="mt-2 text-gray-600">
                        {{ __('You have not written anything so far.') }}
                    </p>

                    <a
                        href="{{ route('author.posts.create') }}"
                        class="inline-block mt-5 px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        {{ __('Write your first post') }}
                    </a>
                </div>
                @endif

            </div>

        </div>
    </div>

</x-app-layout>