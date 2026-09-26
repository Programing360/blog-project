<x-app-layout>
    <x-slot name="title">Home - Laravel Blog</x-slot>

    <x-slot name="header">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900">
            Latest Posts
        </h1>

        <p class="mt-2 text-gray-600">
            Explore our latest articles and tutorials.
        </p>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- Search & Category Filter --}}
        <div class="bg-white rounded-xl shadow-sm p-5 mb-8">

            <form action="{{ route('posts.index') }}" method="GET">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    {{-- Search --}}
                    <div class="md:col-span-2">

                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                            Search Posts
                        </label>

                        <input
                            type="text"
                            name="search"
                            id="search"
                            value="{{ request('search') }}"
                            placeholder="Search by title or content..."
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                    </div>

                    {{-- Category --}}
                    <div>

                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                            Category
                        </label>

                        <select
                            name="category"
                            id="category"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                            <option value="">
                                All Categories
                            </option>

                            @foreach($categories as $category)

                            <option
                                value="{{ $category->slug }}"
                                {{ request('category') == $category->slug ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>

                            @endforeach

                        </select>

                    </div>

                </div>

                {{-- Buttons --}}
                <div class="flex gap-3 mt-5">

                    <button
                        type="submit"
                        class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        Search
                    </button>

                    <a
                        href="{{ route('posts.index') }}"
                        class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Clear
                    </a>

                </div>

            </form>

        </div>

        {{-- Posts --}}
        @if($posts->count() > 0)

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            @foreach($posts as $post)

            <article class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition">

                {{-- Post Content --}}
                <div class="p-6">

                    {{-- Category --}}
                    @if($post->category)

                    <span class="inline-block px-3 py-1 text-xs font-semibold text-indigo-700 bg-indigo-100 rounded-full">
                        {{ $post->category->name }}
                    </span>

                    @endif

                    {{-- Title --}}
                    <h2 class="mt-4 text-xl font-bold text-gray-900">

                        <a
                            href="{{ route('posts.show', $post->slug) }}"
                            class="hover:text-indigo-600 transition">
                            {{ $post->title }}
                        </a>

                    </h2>

                    {{-- Excerpt --}}
                    <p class="mt-3 text-gray-600 leading-relaxed">
                        {{ Str::limit(strip_tags($post->content), 150) }}
                    </p>

                    {{-- Author & Date --}}
                    <div class="mt-5 pt-5 border-t border-gray-100 flex items-center justify-between">

                        <div>

                            @if($post->user)

                            <p class="text-sm font-medium text-gray-900">
                                {{ $post->user->name }}
                            </p>

                            @endif

                            <p class="text-xs text-gray-500 mt-1">
                                {{ $post->created_at->format('M d, Y') }}
                            </p>

                        </div>

                        {{-- Read More --}}
                        <a
                            href="{{ route('posts.show', $post->slug) }}"
                            class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                            Read More &rarr;
                        </a>

                    </div>

                </div>

            </article>

            @endforeach

        </div>

        {{-- Pagination --}}
        <div class="mt-10">
            {{ $posts->links() }}
        </div>

        @else

        {{-- No Posts --}}
        <div class="bg-white rounded-xl shadow-sm p-10 text-center">

            <h2 class="text-2xl font-bold text-gray-900">
                No Posts Found
            </h2>

            <p class="mt-2 text-gray-600">
                We couldn't find any posts matching your search.
            </p>

            <a
                href="{{ route('posts.index') }}"
                class="inline-block mt-5 px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                View All Posts
            </a>

        </div>

        @endif

    </div>

</x-app-layout>
