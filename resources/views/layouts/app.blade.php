<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="referrer" content="no-referrer">
    <title>@yield('title', 'Dashboard') · {{ config('app.name') }}</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/bali_kami_logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    @if (auth()->check())
        <div class="shell">
            <aside class="sidebar">
                <a href="{{ route('dashboard') }}" class="brand brand-link" aria-label="Bali Kami TMS">
                    <img src="{{ asset('images/bali_kami_icon_white.png') }}" alt="Bali Kami Logo">
                    <span class="brand-copy">
                        <span class="brand-name">BALI KAMI <b>TMS</b></span>
                        <span class="brand-caption">TRANSPORT MANAGEMENT</span>
                    </span>
                </a>

                <div class="sidebar-section-label">WORKSPACE</div>
                <nav class="nav">
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        @if (request()->routeIs('dashboard')) aria-current="page" @endif title="Dashboard">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <rect x="3" y="3" width="8" height="8" rx="2" stroke="currentColor"
                                stroke-width="1.8" />
                            <rect x="13" y="3" width="8" height="5" rx="2" stroke="currentColor"
                                stroke-width="1.8" />
                            <rect x="13" y="10" width="8" height="11" rx="2" stroke="currentColor"
                                stroke-width="1.8" />
                            <rect x="3" y="13" width="8" height="8" rx="2" stroke="currentColor"
                                stroke-width="1.8" />
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ auth()->user()->role === 'reservation' ? route('reservations.index') : route('admin.reservations.index') }}"
                        class="{{ request()->routeIs('reservations.*') || request()->routeIs('admin.reservations.*') ? 'active' : '' }}"
                        title="Reservation Requests">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 4h14v16H5zM8 8h8M8 12h8M8 16h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>{{ auth()->user()->role === 'reservation' ? 'My Requests' : 'Reservation Review' }}</span>
                    </a>
                    @if(in_array(strtolower((string) auth()->user()->role), ['admin','owner','manager'], true))
                    <a href="{{ route('services.index') }}"
                        class="{{ request()->routeIs('services.*') ? 'active' : '' }}"
                        @if (request()->routeIs('services.*')) aria-current="page" @endif title="Transport Services / SPK">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <rect x="3" y="7" width="18" height="13" rx="3" stroke="currentColor"
                                stroke-width="1.8" />
                            <path d="M7 7V5a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v2M3 12h18m-11 0v2h4v-2" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>Transport Services / SPK</span>
                    </a>

                    <a href="{{ route('drivers.index') }}" class="{{ request()->routeIs('drivers.*') ? 'active' : '' }}"
                        @if (request()->routeIs('drivers.*')) aria-current="page" @endif title="Drivers">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="1.8" />
                            <path d="M4 21v-2a8 8 0 0 1 16 0v2" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" />
                        </svg>
                        <span>Drivers</span>
                    </a>

                    <a href="{{ route('vehicles.index') }}"
                        class="{{ request()->routeIs('vehicles.*') ? 'active' : '' }}"
                        @if (request()->routeIs('vehicles.*')) aria-current="page" @endif title="Vehicles">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M5 11 7 5h10l2 6M3 11h18v8H3z" stroke="currentColor" stroke-width="1.8"
                                stroke-linejoin="round" />
                            <path d="M6 19v2m12-2v2M6 15h2m8 0h2" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" />
                        </svg>
                        <span>Vehicles</span>
                    </a>

                    <a href="{{ route('types.index') }}" class="{{ request()->routeIs('types.*') ? 'active' : '' }}"
                        @if (request()->routeIs('types.*')) aria-current="page" @endif title="Service Types">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4 5h16v14H4z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                            <path d="M8 9h8M8 13h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                        </svg>
                        <span>Service Types</span>
                    </a>

                    <a href="{{ route('contacts.index') }}"
                        class="{{ request()->routeIs('contacts.*') ? 'active' : '' }}"
                        @if (request()->routeIs('contacts.*')) aria-current="page" @endif title="Emergency Contacts">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M7 3h10a2 2 0 0 1 2 2v14H5V5a2 2 0 0 1 2-2Z" stroke="currentColor"
                                stroke-width="1.8" stroke-linejoin="round" />
                            <path d="M12 8v6m-3-3h6" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" />
                        </svg>
                        <span>Emergency Contacts</span>
                    </a>
                    @endif

                </nav>

                <div class="sidebar-bottom">
                    <div class="sidebar-bottom-line"></div>
                    <div class="sidebar-version">
                        <span class="sidebar-version-dot"></span>
                        <span>Transport Operations</span>
                    </div>
                </div>
            </aside>

            <section class="main">
                <header class="topbar">
                    <div class="topbar-left">
                        <button type="button" class="sidebar-toggle" data-sidebar-toggle
                            aria-label="Perlebar atau persempit navigasi" aria-expanded="true"
                            title="Toggle sidebar">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" />
                            </svg>
                        </button>

                        <div class="topbar-page">
                            <strong>@yield('title', 'Dashboard')</strong>
                            <span>Management Console</span>
                        </div>
                    </div>

                    <div class="topbar-right">
                        <div class="topbar-status">
                            <span class="status-dot"></span>
                            System Online
                        </div>

                        <div class="topbar-divider"></div>

                        <form method="post" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn secondary topbar-logout" type="submit">
                                Logout
                            </button>
                        </form>
                    </div>
                </header>
                <main class="content">
    @else
                <main>
    @endif
    @if (session('success'))
        <div class="alert">{{ session('success') }} @if (session('new_spk_url'))
                <br><strong>Tautan SPK (simpan sekarang):</strong> <a href="{{ session('new_spk_url') }}"
                    target="_blank" rel="noreferrer">{{ session('new_spk_url') }}</a>
            @endif
        </div>
    @endif
    @if ($errors->any())
        <div class="alert error"><strong>Periksa input:</strong>
            <ul style="margin:5px 0 0;padding-left:20px">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @yield('content')
    <footer class="footer">
        <span>Bali Kami TMS</span>
        <span>Transport Management System</span>
    </footer>
    @if (auth()->check())
        </main>
        </section>
    </div>@else</main>
    @endif
</body>

</html>
