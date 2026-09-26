
<nav x-data="{ open: false }"
    class="sticky top-0 z-50 bg-purple-700 border-b border-purple-600 shadow-lg">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between h-16">

            {{-- =========================
                Logo
            ========================== --}}
            <div class="flex items-center">

                <a href="{{ route('posts.index') }}"
                    class="flex items-center gap-3 group">

                    {{-- Logo Icon --}}
                    <div
                        class="w-10 h-10 rounded-xl bg-white/15 backdrop-blur-sm flex items-center justify-center border border-white/20 group-hover:bg-white/25 transition">
                        <svg
                            class="w-6 h-6 text-white"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />

                        </svg>
                    </div>

                    <div class="hidden sm:block">
                        <span class="text-xl font-bold text-white tracking-tight">
                            Laravel
                        </span>

                        <span class="text-xl font-light text-purple-200">
                            Blog
                        </span>
                    </div>

                </a>


                {{-- =========================
                    Desktop Navigation
                ========================== --}}
                <div class="hidden sm:flex items-center ml-10 gap-2">

                    {{-- Blog --}}
                    <a href="{{ route('posts.index') }}"
                        class="relative px-4 py-2 rounded-lg text-sm font-medium transition
                        {{ request()->routeIs('posts.*')
                            ? 'bg-white/15 text-white'
                            : 'text-purple-100 hover:bg-white/10 hover:text-white' }}">

                        <span class="flex items-center gap-2">

                            <svg class="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2" />

                            </svg>

                            Blog

                        </span>

                    </a>


                    {{-- Dashboard --}}
                    @auth

                    <a href="{{ route('dashboard') }}"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition
                            {{ request()->routeIs('dashboard')
                                ? 'bg-white/15 text-white'
                                : 'text-purple-100 hover:bg-white/10 hover:text-white' }}">

                        <span class="flex items-center gap-2">

                            <svg class="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4" />

                            </svg>

                            Dashboard

                        </span>

                    </a>

                    @endauth


                    {{-- My Posts --}}
                    @auth

                    @if(Route::has('author.posts.index'))

                    <a href="{{ route('author.posts.index') }}"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition
                                {{ request()->routeIs('author.*')
                                    ? 'bg-white/15 text-white'
                                    : 'text-purple-100 hover:bg-white/10 hover:text-white' }}">

                        <span class="flex items-center gap-2">

                            <svg class="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 20h9M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z" />

                            </svg>

                            My Posts

                        </span>

                    </a>

                    @endif

                    @endauth

                </div>

            </div>


            {{-- =========================
                Right Side
            ========================== --}}
            <div class="hidden sm:flex items-center gap-3">

                @guest

                {{-- Login --}}
                <a href="{{ route('login') }}"
                    class="px-4 py-2 text-sm font-medium text-purple-100 hover:text-white transition">

                    Log in

                </a>


                {{-- Register --}}
                <a href="{{ route('register') }}"
                    class="inline-flex items-center px-5 py-2.5 bg-white text-purple-700 rounded-lg text-sm font-semibold shadow-sm hover:bg-purple-50 hover:shadow-md transition">

                    Get Started

                </a>

                @endguest


                @auth

                {{-- User Dropdown --}}
                <x-dropdown align="right" width="48">

                    <x-slot name="trigger">

                        <button
                            class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-white hover:bg-white/10 transition">

                            {{-- Avatar --}}
                            <div
                                class="w-9 h-9 rounded-full bg-white text-purple-700 flex items-center justify-center font-bold">

                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}

                            </div>


                            <div class="hidden md:block text-left">

                                <div class="font-semibold text-white">
                                    {{ Auth::user()->name }}
                                </div>

                                <div class="text-xs text-purple-200">
                                    {{ Auth::user()->email }}
                                </div>

                            </div>


                            <svg
                                class="w-4 h-4 text-purple-200"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 9l-7 7-7-7" />

                            </svg>

                        </button>

                    </x-slot>


                    <x-slot name="content">

                        {{-- Profile --}}
                        <x-dropdown-link :href="route('profile.edit')">

                            <div class="flex items-center gap-2">

                                <svg
                                    class="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />

                                </svg>

                                Profile

                            </div>

                        </x-dropdown-link>


                        {{-- Logout --}}
                        <form method="POST" action="{{ route('logout') }}">

                            @csrf

                            <x-dropdown-link
                                :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">

                                <div class="flex items-center gap-2">

                                    <svg
                                        class="w-4 h-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24">

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />

                                    </svg>

                                    Log Out

                                </div>

                            </x-dropdown-link>

                        </form>

                    </x-slot>

                </x-dropdown>

                @endauth

            </div>


            {{-- =========================
                Mobile Menu Button
            ========================== --}}
            <div class="flex sm:hidden">

                <button
                    @click="open = !open"
                    class="inline-flex items-center justify-center w-10 h-10 rounded-lg text-white hover:bg-white/10 focus:outline-none transition">

                    {{-- Menu Icon --}}
                    <svg
                        x-show="!open"
                        class="w-6 h-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />

                    </svg>


                    {{-- Close Icon --}}
                    <svg
                        x-show="open"
                        class="w-6 h-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />

                    </svg>

                </button>

            </div>

        </div>

    </div>


    {{-- =========================
        Mobile Navigation
    ========================== --}}
    <div
        x-show="open"
        x-transition
        class="sm:hidden border-t border-purple-600 bg-purple-800">

        <div class="px-4 py-4 space-y-2">

            {{-- Blog --}}
            <a
                href="{{ route('posts.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition
                {{ request()->routeIs('posts.*')
                    ? 'bg-white/15 text-white'
                    : 'text-purple-100 hover:bg-white/10 hover:text-white' }}">

                Blog

            </a>


            @auth

            {{-- Dashboard --}}
            <a
                href="{{ route('dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium text-purple-100 hover:bg-white/10 hover:text-white transition">

                Dashboard

            </a>


            {{-- My Posts --}}
            @if(Route::has('author.posts.index'))

            <a
                href="{{ route('author.posts.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium text-purple-100 hover:bg-white/10 hover:text-white transition">

                My Posts

            </a>

            @endif


            <div class="border-t border-purple-600 my-3"></div>


            {{-- User --}}
            <div class="px-4 py-3">

                <p class="text-sm font-semibold text-white">
                    {{ Auth::user()->name }}
                </p>

                <p class="text-xs text-purple-200 mt-1">
                    {{ Auth::user()->email }}
                </p>

            </div>


            {{-- Profile --}}
            <a
                href="{{ route('profile.edit') }}"
                class="block px-4 py-3 rounded-lg text-sm text-purple-100 hover:bg-white/10 hover:text-white transition">

                Profile

            </a>


            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">

                @csrf

                <button
                    type="submit"
                    class="w-full text-left px-4 py-3 rounded-lg text-sm text-purple-100 hover:bg-white/10 hover:text-white transition">

                    Log Out

                </button>

            </form>

            @endauth


            @guest

            <div class="border-t border-purple-600 my-3"></div>


            {{-- Login --}}
            <a
                href="{{ route('login') }}"
                class="block px-4 py-3 rounded-lg text-sm font-medium text-purple-100 hover:bg-white/10 hover:text-white transition">

                Log in

            </a>


            {{-- Register --}}
            <a
                href="{{ route('register') }}"
                class="block text-center px-4 py-3 bg-white text-purple-700 rounded-lg text-sm font-semibold hover:bg-purple-50 transition">

                Get Started

            </a>

            @endguest

        </div>

    </div>

</nav>