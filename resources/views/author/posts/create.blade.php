<x-app-layout>
    <x-slot name="title">New Post - Laravel Blog</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Post') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <a
                href="{{ route('author.posts.index') }}"
                class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800">
                &larr; Back to My Posts
            </a>

            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 sm:p-8">

                    @if ($errors->any())
                        <div class="mb-6 rounded-lg bg-red-50 border border-red-200 px-4 py-3">
                            <p class="text-sm font-semibold text-red-800">
                                {{ __('Please fix the following:') }}
                            </p>

                            <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form
                        action="{{ route('author.posts.store') }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="space-y-6">

                        @csrf

                        {{-- Title --}}
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Title <span class="text-red-600">*</span>
                            </label>

                            <input
                                type="text"
                                name="title"
                                id="title"
                                value="{{ old('title') }}"
                                required
                                autofocus
                                placeholder="Post title"
                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Category & Status --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Category <span class="text-red-600">*</span>
                                </label>

                                <select
                                    name="category_id"
                                    id="category_id"
                                    required
                                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                                    <option value="">
                                        {{ __('Select a category') }}
                                    </option>

                                    @foreach ($categories as $category)
                                        <option
                                            value="{{ $category->id }}"
                                            {{ (string) old('category_id') === (string) $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach

                                </select>

                                @error('category_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Status <span class="text-red-600">*</span>
                                </label>

                                <select
                                    name="status"
                                    id="status"
                                    required
                                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                                    @foreach (['draft' => 'Draft', 'published' => 'Published'] as $value => $label)
                                        <option
                                            value="{{ $value }}"
                                            {{ old('status', 'published') === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach

                                </select>

                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>

                        {{-- Tags --}}
                        <div>
                            <span class="block text-sm font-medium text-gray-700 mb-2">
                                Tags
                            </span>

                            @if ($tags->count() > 0)
                                <div class="flex flex-wrap gap-4 p-4 rounded-lg border border-gray-200 bg-gray-50">
                                    @foreach ($tags as $tag)
                                        <label for="tag-{{ $tag->id }}" class="inline-flex items-center gap-2 text-sm text-gray-700">
                                            <input
                                                type="checkbox"
                                                name="tags[]"
                                                id="tag-{{ $tag->id }}"
                                                value="{{ $tag->id }}"
                                                {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">

                                            {{ $tag->name }}
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500">
                                    {{ __('No tags available yet.') }}
                                </p>
                            @endif

                            @error('tags')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            @error('tags.*')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Featured Image --}}
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                                Featured Image
                            </label>

                            <input
                                type="file"
                                name="image"
                                id="image"
                                accept="image/jpeg,image/png,image/webp"
                                class="w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100">

                            <p class="mt-2 text-xs text-gray-500">
                                {{ __('JPG, PNG or WEBP. Max 2MB.') }}
                            </p>

                            @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Content --}}
                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                                Content <span class="text-red-600">*</span>
                            </label>

                            <textarea
                                name="content"
                                id="content"
                                rows="12"
                                required
                                placeholder="Write your post content here..."
                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('content') }}</textarea>

                            @error('content')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-4 pt-2 border-t border-gray-100">
                            <button
                                type="submit"
                                class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                {{ __('Publish Post') }}
                            </button>

                            <a
                                href="{{ route('author.posts.index') }}"
                                class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                                {{ __('Cancel') }}
                            </a>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

</x-app-layout>
