@php
    $settings = $appSettings ?? \App\Models\Setting::DEFAULTS;
    $siteName = $settings['site_name'] ?? config('app.name');
    $primaryColor = $settings['theme_color'] ?? '#ED701D';
    $logo = ! empty($settings['site_logo']) ? asset('storage/'.$settings['site_logo']) : asset('images/call-guru-logo-primary.png');
    $favicon = ! empty($settings['site_favicon']) ? asset('storage/'.$settings['site_favicon']) : asset('images/call-guru-logo-primary.png');
    $user = auth()->user();
    $avatarPath = $user ? data_get($user, 'profile_photo_path') ?? data_get($user, 'profile_image') ?? data_get($user, 'avatar') : null;
    $avatar = $avatarPath ? (str_starts_with($avatarPath, 'http') ? $avatarPath : asset('storage/'.$avatarPath)) : null;
    $breadcrumbs ??= match (true) {
        request()->routeIs('dashboard') => [
            ['label' => 'Dashboard'],
        ],
        request()->routeIs('listeners.*') => [
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Listeners'],
        ],
        request()->routeIs('gurujis.*') => [
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Gurujis'],
        ],
        request()->routeIs('categories.*') => [
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Categories'],
        ],
        request()->routeIs('terms-and-conditions.*') => [
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Terms and Conditions'],
        ],
        request()->routeIs('faqs.*') => [
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'FAQ'],
        ],
        request()->routeIs('settings.*') => [
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Settings'],
        ],
        default => [
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => trim($__env->yieldContent('page-title', 'Page'))],
        ],
    };
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ $siteName }}</title>
    <link rel="icon" href="{{ $favicon }}">
    <script>
        (() => {
            const defaultTheme = 'light';
            const savedTheme = localStorage.getItem('dashboard-theme');
            const theme = ['light', 'dark'].includes(savedTheme) ? savedTheme : defaultTheme;

            document.documentElement.dataset.theme = theme;
        })();
    </script>
    <style>
        :root { --primary: {{ $primaryColor }}; }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="dashboard-body">
    <div class="dashboard-shell" data-dashboard-shell>
        <aside class="dashboard-sidebar" data-sidebar>
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <a href="{{ route('dashboard') }}" class="brand-mark">
                        @if ($logo)
                            <img src="{{ $logo }}" alt="{{ $siteName }}">
                        @else
                            <span>{{ strtoupper(substr($siteName, 0, 1)) }}</span>
                        @endif
                    </a>
                    <a href="{{ route('dashboard') }}" class="brand-copy">
                        <strong>{{ $siteName }}</strong>
                    </a>
                </div>
            </div>

            <nav class="sidebar-nav" aria-label="Dashboard navigation">
                <x-dashboard.nav-link :href="route('dashboard')" icon="dashboard" :active="request()->routeIs('dashboard')">
                    Dashboard
                </x-dashboard.nav-link>

                <x-dashboard.nav-link :href="route('listeners.index')" icon="listeners" :active="request()->routeIs('listeners.*')">
                    Listeners
                </x-dashboard.nav-link>

                <x-dashboard.nav-link :href="route('gurujis.index')" icon="guruji" :active="request()->routeIs('gurujis.*')">
                    Gurujis
                </x-dashboard.nav-link>

                <x-dashboard.nav-link :href="route('categories.index')" icon="categories" :active="request()->routeIs('categories.*')">
                    Categories
                </x-dashboard.nav-link>

                <x-dashboard.nav-link :href="route('terms-and-conditions.index')" icon="terms" :active="request()->routeIs('terms-and-conditions.*')">
                    Terms and Conditions
                </x-dashboard.nav-link>

                <x-dashboard.nav-link :href="route('faqs.index')" icon="faq" :active="request()->routeIs('faqs.*')">
                    FAQ
                </x-dashboard.nav-link>
            </nav>
        </aside>

        <div class="mobile-backdrop" data-sidebar-backdrop></div>

        <div class="dashboard-main">
            <header class="dashboard-topbar">
                <div class="topbar-left">
                    <button type="button" class="icon-button" data-sidebar-toggle aria-label="Toggle sidebar">
                        <x-dashboard.icon name="menu" />
                    </button>
                </div>

                <div class="topbar-actions">
                    <div class="topbar-context">
                        <span>Admin Workspace</span>
                        <strong>{{ now('Asia/Kolkata')->format('M d, Y') }}</strong>
                    </div>

                    <div class="topbar-user-cluster">
                        <button type="button" class="theme-switch" data-theme-toggle aria-label="Toggle color theme">
                            <x-dashboard.icon name="sun" class="theme-sun" />
                            <x-dashboard.icon name="moon" class="theme-moon" />
                        </button>

                        <div class="user-menu" data-dropdown>
                            <button type="button" class="user-button" data-dropdown-toggle aria-expanded="false">
                                @if ($avatar)
                                    <img class="user-avatar" src="{{ $avatar }}" alt="{{ $user?->name ?? 'User' }}">
                                @else
                                    <span class="user-avatar user-avatar-fallback"><x-dashboard.icon name="user" /></span>
                                @endif
                                <span class="user-meta">
                                    <strong>{{ $user?->name ?? 'Admin' }}</strong>
                                    <small>{{ $user?->mobile_number ? trim(($user->country_code ?? '').' '.$user->mobile_number) : 'Administrator' }}</small>
                                </span>
                                <x-dashboard.icon name="chevron" class="user-chevron" />
                            </button>

                            <div class="dropdown-panel" data-dropdown-panel>
                                <div class="dropdown-user">
                                    @if ($avatar)
                                        <img src="{{ $avatar }}" alt="{{ $user?->name ?? 'User' }}">
                                    @else
                                        <span><x-dashboard.icon name="user" /></span>
                                    @endif
                                    <div>
                                        <strong>{{ $user?->name ?? 'Admin' }}</strong>
                                        <small>{{ $user?->mobile_number ? trim(($user->country_code ?? '').' '.$user->mobile_number) : 'Administrator' }}</small>
                                    </div>
                                </div>
                                <a href="{{ route('profile.show') }}">
                                    <x-dashboard.icon name="user" />
                                    Profile
                                    <x-dashboard.icon name="chevron-right" class="dropdown-arrow" />
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit">
                                        <x-dashboard.icon name="logout" />
                                        Logout
                                        <x-dashboard.icon name="chevron-right" class="dropdown-arrow" />
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="dashboard-content">
                @yield('content')
            </main>
        </div>
    </div>

    <x-dashboard.toasts />
</body>
</html>
