<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dasbor') }}
                    </x-nav-link>
                    <x-nav-link :href="route('guide')" :active="request()->routeIs('guide')">
                        {{ __('Panduan & Alur') }}
                    </x-nav-link>
                    <x-nav-link :href="route('documents.index')" :active="request()->routeIs('documents.*')">
                        {{ __('Standar & Dokumen') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Notification Bell & Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Notification Bell -->
                @if(Auth::user()->role === 'Administrator')
                <div class="relative mr-4" x-data="{
                    notifications: [],
                    unreadCount: 0,
                    show: false,
                    fetchNotifications() {
                        fetch('/notifications')
                            .then(res => res.json())
                            .then(data => {
                                this.notifications = data;
                                this.unreadCount = data.filter(n => !n.read_at).length;
                            });
                    },
                    markAsRead() {
                        fetch('/notifications/mark-as-read', { 
                            method: 'POST', 
                            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'} 
                        }).then(() => { this.unreadCount = 0; });
                    }
                }" x-init="fetchNotifications(); setInterval(() => fetchNotifications(), 15000)">
                    <button @click="show = !show; if(show && unreadCount > 0) markAsRead()" class="relative p-2 text-gray-400 hover:text-gray-500 transition duration-150 ease-in-out focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span x-show="unreadCount > 0" x-text="unreadCount" style="display: none;" class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-red-600 rounded-full"></span>
                    </button>
                    <!-- Dropdown Content -->
                    <div x-show="show" @click.away="show = false" x-transition class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg overflow-hidden z-50 ring-1 ring-black ring-opacity-5" style="display: none;">
                        <div class="px-4 py-3 bg-slate-50 border-b border-gray-100 flex justify-between items-center">
                            <span class="text-sm font-bold text-gray-700">Notifikasi</span>
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            <template x-for="notif in notifications" :key="notif.id">
                                <a :href="notif.data.url" class="block px-4 py-3 border-b border-gray-100 hover:bg-slate-50 transition-colors" :class="{'bg-blue-50/30': !notif.read_at}">
                                    <p class="text-sm font-medium text-gray-800" x-text="notif.data.title"></p>
                                    <p class="text-xs text-gray-500 mt-1" x-text="notif.data.message"></p>
                                    <p class="text-[10px] text-gray-400 mt-2" x-text="notif.created_at_human"></p>
                                </a>
                            </template>
                            <div x-show="notifications.length === 0" class="px-4 py-6 text-center text-sm text-gray-500">
                                Tidak ada notifikasi.
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Settings Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dasbor') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('guide')" :active="request()->routeIs('guide')">
                {{ __('Panduan & Alur') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('documents.index')" :active="request()->routeIs('documents.*')">
                {{ __('Standar & Dokumen') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4 flex items-center justify-between">
                <div>
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
                
                <!-- Notification Bell for Mobile/Responsive -->
                @if(Auth::user()->role === 'Administrator')
                <div class="relative" x-data="{
                    notifications: [],
                    unreadCount: 0,
                    show: false,
                    fetchNotifications() {
                        fetch('/notifications')
                            .then(res => res.json())
                            .then(data => {
                                this.notifications = data;
                                this.unreadCount = data.filter(n => !n.read_at).length;
                            });
                    },
                    markAsRead() {
                        fetch('/notifications/mark-as-read', { 
                            method: 'POST', 
                            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'} 
                        }).then(() => { this.unreadCount = 0; });
                    }
                }" x-init="fetchNotifications(); setInterval(() => fetchNotifications(), 15000)">
                    <button @click="show = !show; if(show && unreadCount > 0) markAsRead()" class="relative p-2 text-gray-400 hover:text-gray-500 transition duration-150 ease-in-out focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span x-show="unreadCount > 0" x-text="unreadCount" style="display: none;" class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-red-600 rounded-full"></span>
                    </button>
                    <!-- Dropdown Content Mobile -->
                    <div x-show="show" @click.away="show = false" x-transition class="absolute right-0 mt-2 w-72 bg-white rounded-md shadow-lg overflow-hidden z-50 ring-1 ring-black ring-opacity-5" style="display: none;">
                        <div class="px-4 py-3 bg-slate-50 border-b border-gray-100">
                            <span class="text-sm font-bold text-gray-700">Notifikasi</span>
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            <template x-for="notif in notifications" :key="notif.id">
                                <a :href="notif.data.url" class="block px-4 py-3 border-b border-gray-100 hover:bg-slate-50 transition-colors" :class="{'bg-blue-50/30': !notif.read_at}">
                                    <p class="text-sm font-medium text-gray-800" x-text="notif.data.title"></p>
                                    <p class="text-xs text-gray-500 mt-1" x-text="notif.data.message"></p>
                                    <p class="text-[10px] text-gray-400 mt-2" x-text="notif.created_at_human"></p>
                                </a>
                            </template>
                            <div x-show="notifications.length === 0" class="px-4 py-6 text-center text-sm text-gray-500">
                                Tidak ada notifikasi.
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
