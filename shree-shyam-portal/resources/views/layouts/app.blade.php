<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') | Shree Shyam Hotel & Restorent</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('logo_round.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo_round.png') }}">
    
    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Flatpickr Datepicker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
    
    <!-- Custom Style -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="app-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <div class="brand-logo" style="background: transparent; box-shadow: none; animation: none; padding: 0;">
                    <img src="{{ asset('logo_round.png') }}" alt="Shree Shyam Logo" style="width: 100%; height: 100%; object-fit: contain;">
                </div>
                <div class="brand-name">
                    <h3>Shree Shyam</h3>
                    <span>Hotel & Restorent</span>
                </div>
            </div>
            
            <nav class="sidebar-menu">
                <a href="{{ route('dashboard') }}" class="{{ Route::is('dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line"></i> Dashboard
                </a>
                <a href="{{ route('checkin.form') }}" class="{{ Route::is('checkin.form') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-plus"></i> Guest Check-In
                </a>
                <a href="{{ route('bookings.index') }}" class="{{ Route::is('bookings.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-calendar-check"></i> Advance Bookings
                </a>
                <a href="{{ route('rooms.index') }}" class="{{ Route::is('rooms.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-door-open"></i> Room Management
                </a>
                <a href="{{ route('guests.index') }}" class="{{ Route::is('guests.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-address-book"></i> Guest Directory
                </a>
                <a href="{{ route('reports.monthly') }}" class="{{ Route::is('reports.monthly') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-invoice-dollar"></i> Monthly Report
                </a>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color: var(--danger-color);">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </nav>
            
            <div class="sidebar-footer">
                <p>&copy; {{ date('Y') }} Shree Shyam Portal</p>
                <small>v1.0.0 (Laravel 12)</small>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <header class="main-header">
                <div class="header-title">
                    <h1>@yield('header_title', 'Dashboard')</h1>
                    <p class="text-muted">@yield('header_subtitle', 'Welcome to Shree Shyam Hotel & Restorent Guest Portal')</p>
                </div>
                <div class="header-user">
                    <div class="current-time" id="live-clock">
                        <i class="fa-regular fa-clock"></i> <span></span>
                    </div>
                    <div class="user-avatar">
                        <span>A</span>
                    </div>
                </div>
            </header>

            <div class="content-body">
                <!-- SweetAlert2 notifications handled at the footer scripts -->

                @yield('content')
            </div>
        </main>
    </div>

    <!-- jQuery & jQuery Validation Plugins -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/additional-methods.min.js"></script>

    <!-- Live Clock Script -->
    <script>
        function updateClock() {
            const clockEl = document.querySelector('#live-clock span');
            if (clockEl) {
                const now = new Date();
                clockEl.textContent = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' | ' + now.toLocaleDateString([], { day: 'numeric', month: 'short', year: 'numeric' });
            }
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
    <!-- Flatpickr JS Plugin -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
    
    <!-- SweetAlert2 Plugin -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- SweetAlert2 Flash Messages -->
    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: "{{ session('success') }}",
                background: '#121826',
                color: '#fff',
                confirmButtonColor: '#10b981',
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: "{{ session('error') }}",
                background: '#121826',
                color: '#fff',
                confirmButtonColor: '#f43f5e'
            });
        @endif
    </script>
    
    @yield('scripts')
</body>
</html>
