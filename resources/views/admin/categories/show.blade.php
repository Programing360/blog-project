<x-app-layout>
    <x-slot name="title">{{ $category->name }} - Laravel Blog</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $category->name }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <a
                href="{{ route('admin.categories.index') }}"
                class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800">
                &larr; Back to Categories
            </a>

            <div class="mt-6 flex items-center gap-3">
                <a
                    href="{{ route('admin.categories.edit', $category) }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                    {{ __('Edit') }}
                </a>

                <a
                    href="{{ route('posts.index', ['category' => $category->slug]) }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
                    {{ __('View on Blog') }}
                </a>
            </div>

            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
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
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Author</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Created</th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach ($posts as $post)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4">
                                            <a
                                                href="{{ route('posts.show', $post->slug) }}"
                                                class="font-medium text-gray-900 hover:text-indigo-600 transition">
                                                {{ $post->title }}
                                            </a>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $post->user?->name ?? 'Unknown' }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $post->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                                                {{ ucfirst($post->status) }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $post->created_at->format('M d, Y') }}
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
                    <p class="p-10 text-center text-gray-600">
                        {{ __('No posts in this category yet.') }}
                    </p>
                @endif

            </div>

        </div>
    </div>

</x-app-layout>
