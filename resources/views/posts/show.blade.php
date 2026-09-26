<x-app-layout>
    <x-slot name="title">{{ $post->title }} - Laravel Blog</x-slot>

    <x-slot name="header">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900">
            {{ $post->title }}
        </h1>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- Back to Posts --}}
        <div class="mb-6">
            <a
                href="{{ route('posts.index') }}"
                class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800">
                &larr; Back to Posts
            </a>
        </div>

        {{-- Post --}}
        <article class="bg-white rounded-2xl shadow-sm overflow-hidden">

            <div class="p-6 sm:p-8 md:p-10">

                {{-- Category --}}
                @if($post->category)

                <a
                    href="{{ route('posts.index', ['category' => $post->category->slug]) }}"
                    class="inline-block px-3 py-1 text-sm font-semibold text-indigo-700 bg-indigo-100 rounded-full hover:bg-indigo-200">
                    {{ $post->category->name }}
                </a>

                @endif

                {{-- Author & Date --}}
                <div class="mt-6 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-gray-500">

                    @if($post->user)

                    <div class="flex items-center gap-2">

                        <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-semibold">
                            {{ strtoupper(substr($post->user->name, 0, 1)) }}
                        </div>

                        <div>
                            <p class="font-medium text-gray-900">
                                {{ $post->user->name }}
                            </p>

                            <p class="text-xs text-gray-500">
                                Author
                            </p>
                        </div>

                    </div>

                    @endif

                    <div>
                        <span>Published</span>

                        <span class="mx-1">&bull;</span>

                        <span>
                            {{ $post->created_at->format('M d, Y') }}
                        </span>
                    </div>

                </div>

                {{-- Divider --}}
                <div class="border-t border-gray-200 my-8"></div>

                {{-- Post Content --}}
                <div class="prose prose-lg max-w-none text-gray-800 leading-relaxed">
                    {!! nl2br(e($post->content)) !!}
                </div>

                {{-- Tags --}}
                @if($post->tags->count() > 0)

                <div class="mt-10 pt-6 border-t border-gray-200">

                    <h3 class="text-sm font-semibold text-gray-900 mb-3">
                        Tags
                    </h3>

                    <div class="flex flex-wrap gap-2">

                        @foreach($post->tags as $tag)

                        <span class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-full">
                            #{{ $tag->name }}
                        </span>

                        @endforeach

                    </div>

                </div>

                @endif

            </div>

        </article>

        {{-- Comments Section --}}
        <section class="mt-10">

            <div class="bg-white rounded-2xl shadow-sm p-6 sm:p-8">

                <h2 class="text-2xl font-bold text-gray-900">
                    Comments
                </h2>

                {{-- Existing Comments --}}
                @if($post->comments->count() > 0)

                <div class="mt-6 space-y-6">

                    @foreach($post->comments as $comment)

                    <div class="border-b border-gray-100 pb-6 last:border-b-0">

                        <div class="flex items-start gap-3">

                            {{-- Avatar --}}
                            <div class="w-10 h-10 shrink-0 rounded-full bg-gray-200 flex items-center justify-center text-gray-700 font-semibold">
                                {{ strtoupper(substr($comment->user?->name ?? '?', 0, 1)) }}
                            </div>

                            <div class="flex-1">

                                {{-- Comment Author --}}
                                <div class="flex flex-wrap items-center gap-2">

                                    <p class="font-semibold text-gray-900">
                                        {{ $comment->user?->name ?? 'Anonymous' }}
                                    </p>

                                    <span class="text-xs text-gray-400">
                                        &bull;
                                    </span>

                                    <span class="text-xs text-gray-500">
                                        {{ $comment->created_at->format('M d, Y') }}
                                    </span>

                                </div>

                                {{-- Comment --}}
                                <p class="mt-2 text-gray-700 leading-relaxed">
                                    {{ $comment->body }}
                                </p>

                            </div>

                        </div>

                    </div>

                    @endforeach

                </div>

                @else

                <div class="mt-6 py-8 text-center bg-gray-50 rounded-xl">

                    <p class="text-gray-500">
                        No comments yet.
                    </p>

                    <p class="text-sm text-gray-400 mt-1">
                        Be the first one to comment.
                    </p>

                </div>

                @endif

                {{-- Login Message --}}
                @guest

                <div class="mt-8 p-4 bg-indigo-50 border border-indigo-100 rounded-xl">

                    <p class="text-sm text-indigo-800">
                        Please
                        <a
                            href="{{ route('login') }}"
                            class="font-semibold underline hover:text-indigo-600">
                            login
                        </a>
                        to leave a comment.
                    </p>

                </div>

                @endguest

                {{-- Comment Form --}}
                @auth

                <div class="mt-8 pt-8 border-t border-gray-200">

                    <h3 class="text-lg font-semibold text-gray-900">
                        Leave a Comment
                    </h3>

                    <form
                        action="{{ route('comments.store', $post) }}"
                        method="POST"
                        class="mt-4">

                        @csrf

                        <div>

                            <label for="body" class="block text-sm font-medium text-gray-700 mb-2">
                                Your Comment
                            </label>

                            <textarea
                                name="body"
                                id="body"
                                rows="5"
                                required
                                placeholder="Write your comment..."
                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('body') }}</textarea>

                            @error('body')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                            @enderror

                        </div>

                        <button
                            type="submit"
                            class="mt-4 px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                            Post Comment
                        </button>

                    </form>

                </div>

                @endauth

            </div>

        </section>

    </div>

</x-app-layout>
