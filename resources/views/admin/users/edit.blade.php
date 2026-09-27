<x-app-layout>
    <x-slot name="title">Edit {{ $user->name }} - Laravel Blog</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit User Role') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <a
                href="{{ route('admin.users.index') }}"
                class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800">
                &larr; Back to Users
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

                    <div class="flex items-center gap-3 pb-6 border-b border-gray-100">
                        <div class="w-12 h-12 shrink-0 rounded-full bg-purple-100 flex items-center justify-center text-purple-700 text-lg font-semibold">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>

                        <div class="min-w-0">
                            <p class="font-medium text-gray-900 truncate">
                                {{ $user->name }}

                                @if ($user->is(auth()->user()))
                                    <span class="ml-1 text-xs font-normal text-gray-400">(you)</span>
                                @endif
                            </p>

                            <p class="text-sm text-gray-500 truncate">
                                {{ $user->email }}
                            </p>
                        </div>
                    </div>

                    <form
                        action="{{ route('admin.users.update', $user) }}"
                        method="POST"
                        class="mt-6 space-y-6">

                        @csrf
                        @method('PUT')

                        <fieldset>
                            <legend class="block text-sm font-medium text-gray-700 mb-3">
                                {{ __('Role') }}
                            </legend>

                            <div class="space-y-3">
                                @foreach ($roles as $role)
                                    <label
                                        for="role_{{ $role }}"
                                        class="flex items-start gap-3 p-4 border rounded-lg cursor-pointer transition
                                            {{ old('role', $user->role) === $role ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300' }}">

                                        <input
                                            type="radio"
                                            name="role"
                                            id="role_{{ $role }}"
                                            value="{{ $role }}"
                                            class="mt-1 text-indigo-600 focus:ring-indigo-500"
                                            @checked(old('role', $user->role) === $role)>

                                        <span class="text-sm">
                                            <span class="font-medium text-gray-900">{{ ucfirst($role) }}</span>

                                            <span class="block text-gray-500">
                                                @switch($role)
                                                    @case('admin')
                                                        Full access to the admin panel, categories and users.
                                                        @break

                                                    @case('author')
                                                        Can create, edit and delete their own posts.
                                                        @break

                                                    @default
                                                        Can read the blog and comment on posts.
                                                @endswitch
                                            </span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>

                            @error('role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </fieldset>

                        @if ($user->is(auth()->user()))
                            <p class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-4 py-3">
                                {{ __('You are editing your own account, so you cannot change your own role or delete yourself.') }}
                            </p>
                        @endif

                        <div class="flex items-center gap-4 pt-2 border-t border-gray-100">
                            <button
                                type="submit"
                                class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                {{ __('Save Changes') }}
                            </button>

                            <a
                                href="{{ route('admin.users.index') }}"
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
