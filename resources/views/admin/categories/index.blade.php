<x-app-layout>
    <x-slot name="title">Categories - Laravel Blog</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Categories') }}
            </h2>

            <a
                href="{{ route('admin.categories.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                {{ __('New Category') }}
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @include('admin.partials.flash')

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ $categories->total() }} {{ Str::plural('category', $categories->total()) }}
                    </h3>
                </div>

                @if ($categories->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Slug</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Posts</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach ($categories as $category)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4">
                                            <a
                                                href="{{ route('admin.categories.show', $category) }}"
                                                class="font-medium text-gray-900 hover:text-indigo-600 transition">
                                                {{ $category->name }}
                                            </a>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                                            {{ $category->slug }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $category->posts_count }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="inline-flex items-center gap-3">
                                                <a
                                                    href="{{ route('admin.categories.edit', $category) }}"
                                                    class="text-indigo-600 hover:text-indigo-800">
                                                    {{ __('Edit') }}
                                                </a>

                                                <form
                                                    action="{{ route('admin.categories.destroy', $category) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Delete the category {{ $category->name }}?');">
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
                        {{ $categories->links() }}
                    </div>
                @else
                    <div class="p-10 text-center">
                        <h4 class="text-lg font-semibold text-gray-900">
                            {{ __('No categories yet') }}
                        </h4>

                        <p class="mt-2 text-gray-600">
                            {{ __('Create your first category to organise posts.') }}
                        </p>
                    </div>
                @endif

            </div>

        </div>
    </div>

</x-app-layout>
