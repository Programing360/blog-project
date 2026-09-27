<x-app-layout>
    <x-slot name="title">New Category - Laravel Blog</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Category') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <a
                href="{{ route('admin.categories.index') }}"
                class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800">
                &larr; Back to Categories
            </a>

            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 sm:p-8">

                    @if ($errors->any())
                        <div class="mb-6 rounded-lg bg-red-50 border border-red-200 px-4 py-3">
                            <ul class="list-disc list-inside text-sm text-red-700">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form
                        action="{{ route('admin.categories.store') }}"
                        method="POST"
                        class="space-y-6">

                        @csrf

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Name') }} <span class="text-red-600">*</span>
                            </label>

                            <input
                                type="text"
                                name="name"
                                id="name"
                                value="{{ old('name') }}"
                                required
                                autofocus
                                placeholder="e.g. Laravel"
                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <p class="text-xs text-gray-500">
                            {{ __('The slug is generated automatically from the name.') }}
                        </p>

                        <div class="flex items-center gap-4 pt-2 border-t border-gray-100">
                            <button
                                type="submit"
                                class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                {{ __('Create Category') }}
                            </button>

                            <a
                                href="{{ route('admin.categories.index') }}"
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
