<!-- Navbar -->
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    
    <!-- Breadcrumb on the left -->
    <div class="d-none d-sm-inline-block">
        @yield('breadcrumb') <!-- Digunakan agar setiap halaman bisa mengisi breadcrumb-nya masing-masing -->
    </div>

    <!-- Spacer untuk mendorong profil ke kanan -->
    <div class="ml-auto d-flex align-items-center">
        <ul class="navbar-nav">
            
            <!-- Profil User (Dropdown) -->
            <li class="nav-item dropdown no-arrow">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="mr-2 d-none d-lg-inline text-gray-600 small" id="userNameDisplay">Welcome</span>

                    <img class="img-profile rounded-circle"
                         src="{{ asset('assets/img/undraw_profile.svg') }}" width="40" height="40">
                </a>

                <!-- Dropdown Menu Profil -->
                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                    <!-- Menu: Log Aktivitas -->
                    <a class="dropdown-item" href="{{ url('/activity-log') }}">
                        <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                        Activity Log
                    </a>

                    <div class="dropdown-divider"></div>

                    <!-- Tombol Logout (gunakan method POST untuk keamanan) -->
                    <form action="{{ route('logout') }}" method="POST" class="dropdown-item p-0 m-0">
                        @csrf
                        <div class="text-center py-2">
                            <button type="submit" class="btn btn-danger px-4">Logout</button>
                        </div>
                    </form>

                </div>
            </li>
        </ul>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', async function () {
    const token = sessionStorage.getItem('token');
    if (!token) return;

    try {
        const response = await fetch('/api/me', {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) throw new Error('Token tidak valid');

        const user = await response.json();
        const userName = user.name || user.email || 'User';
        document.getElementById('userNameDisplay').innerText = `Welcome, ${userName}`;
    } catch (error) {
        console.error('Gagal memuat data user:', error);
    }
});
</script>

