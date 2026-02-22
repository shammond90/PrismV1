<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    @canany(['contacts.show','contacts.view'])
                        <x-nav-link :href="route('contacts.index')" :active="request()->routeIs('contacts.*')">
                            {{ __('Contacts') }}
                        </x-nav-link>
                    @endcanany
                    @canany(['companies.show','companies.view'])
                        <x-nav-link :href="route('companies.index')" :active="request()->routeIs('companies.*')">
                            {{ __('Companies') }}
                        </x-nav-link>
                    @endcanany
                    @can('shows.view')
                        <x-nav-link :href="route('shows.index')" :active="request()->routeIs('shows.*')">
                            {{ __('Shows') }}
                        </x-nav-link>
                    @endcan
                    @can('show_catalogues.view')
                        <x-nav-link :href="route('show_catalogues.index')" :active="request()->routeIs('show_catalogues.*')">
                            {{ __('Show Catalogue') }}
                        </x-nav-link>
                    @endcan
                    <x-nav-link :href="route('seasons.index')" :active="request()->routeIs('seasons.*')">
                        {{ __('Seasons') }}
                    </x-nav-link>
                    @canany(['venues.view','buildings.view','spaces.view'])
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                            <a href="#" onclick="event.preventDefault()" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700 transition duration-150 ease-in-out">
                                <div>{{ __('Locations') }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </a>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('locations.index')">{{ __('All') }}</x-dropdown-link>
                                <div class="border-t my-1"></div>
                                @can('venues.view')
                                    <x-dropdown-link :href="route('venues.index')">{{ __('Venues') }}</x-dropdown-link>
                                @endcan
                                @can('buildings.view')
                                    <x-dropdown-link :href="route('buildings.index')">{{ __('Buildings') }}</x-dropdown-link>
                                @endcan
                                @can('spaces.view')
                                    <x-dropdown-link :href="route('spaces.index')">{{ __('Spaces') }}</x-dropdown-link>
                                @endcan
                            </x-slot>
                        </x-dropdown>
                    @endcanany
                    @role('Admin')
                    <x-dropdown align="left" width="48">
                        <x-slot name="trigger">
                            <a href="#" onclick="event.preventDefault()" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700 transition duration-150 ease-in-out">
                                <div>{{ __('Admin') }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </a>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('admin.user-roles.index')">
                                {{ __('User Roles') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('admin.permissions.index')">
                                {{ __('Permissions') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('admin.roles.index')">
                                {{ __('Roles') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('admin.event_types.index')">
                                {{ __('Event Types') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('admin.departments.index')">
                                {{ __('Departments') }}
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                    @endrole
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
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
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
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
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            @canany(['contacts.show','contacts.view'])
                <x-responsive-nav-link :href="route('contacts.index')" :active="request()->routeIs('contacts.*')">
                    {{ __('Contacts') }}
                </x-responsive-nav-link>
            @endcanany
            @can('shows.view')
                <x-responsive-nav-link :href="route('shows.index')" :active="request()->routeIs('shows.*')">{{ __('Shows') }}</x-responsive-nav-link>
            @endcan
            @can('show_catalogues.view')
                <x-responsive-nav-link :href="route('show_catalogues.index')" :active="request()->routeIs('show_catalogues.*')">{{ __('Show Catalogue') }}</x-responsive-nav-link>
            @endcan
            <x-responsive-nav-link :href="route('seasons.index')" :active="request()->routeIs('seasons.*')">
                {{ __('Seasons') }}
            </x-responsive-nav-link>
            @canany(['companies.show','companies.view'])
                <x-responsive-nav-link :href="route('companies.index')" :active="request()->routeIs('companies.*')">
                    {{ __('Companies') }}
                </x-responsive-nav-link>
            @endcanany
                @canany(['venues.view','buildings.view','spaces.view'])
                    <x-responsive-nav-link :href="route('locations.index')" :active="request()->routeIs('locations')">{{ __('All Locations') }}</x-responsive-nav-link>
                    @can('venues.view')
                        <x-responsive-nav-link :href="route('venues.index')" :active="request()->routeIs('venues.*')">{{ __('Venues') }}</x-responsive-nav-link>
                    @endcan
                    @can('buildings.view')
                        <x-responsive-nav-link :href="route('buildings.index')" :active="request()->routeIs('buildings.*')">{{ __('Buildings') }}</x-responsive-nav-link>
                    @endcan
                    @can('spaces.view')
                        <x-responsive-nav-link :href="route('spaces.index')" :active="request()->routeIs('spaces.*')">{{ __('Spaces') }}</x-responsive-nav-link>
                    @endcan
                @endcanany
            @role('Admin')
            <x-responsive-nav-link :href="route('admin.user-roles.index')" :active="request()->routeIs('admin.user-roles.*')">
                {{ __('User Roles') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.permissions.index')" :active="request()->routeIs('admin.permissions.*')">
                {{ __('Permissions') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.roles.index')" :active="request()->routeIs('admin.roles.*')">
                {{ __('Roles') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.event_types.index')" :active="request()->routeIs('admin.event_types.*')">
                {{ __('Event Types') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.departments.index')" :active="request()->routeIs('admin.departments.*')">
                {{ __('Departments') }}
            </x-responsive-nav-link>
            @endrole
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
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
