<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stok Gudang</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">
</head>
<body id="page-top">
<div id="wrapper">
    @include('layouts.sidebar')
    <div id="content-wrapper" class="d-flex flex-colum  n">
        <div id="content">
            @include('layouts.navbar')
            <div class="container-fluid">
                <div id="flashMessageContainer" class="mb-4"></div>
                @yield('content')
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{ asset('assets/js/sb-admin-2.min.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', async function () {
        const token = sessionStorage.getItem('token');
        if (!token) {
            window.location.href = '/login';
            return;
        }

        try {
            const response = await fetch('/api/me', {
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Token invalid');
            }

            const user = await response.json();

            document.querySelectorAll('.sidebar-username').forEach(el => el.innerText = user.name);
            document.querySelectorAll('.navbar-username').forEach(el => el.innerText = user.name);
        } catch (error) {
            console.warn('Token expired atau tidak valid, mengarahkan ke login...');
            sessionStorage.removeItem('token');
            sessionStorage.removeItem('user');
            window.location.href = '/login';
        }

        window.showFlashMessage = (type, message) => {
            const container = document.getElementById('flashMessageContainer');
            const alert = document.createElement('div');

            alert.className = `alert alert-${type} alert-dismissible shadow-sm fade show`;
            alert.role = 'alert';
            alert.innerHTML = `
                <div class="d-flex align-items-center justify-content-between">
                    <div><i class="fas fa-info-circle mr-2"></i> ${message}</div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `;

            container.innerHTML = ''; // bersihkan pesan sebelumnya
            container.appendChild(alert);

            // Hapus otomatis setelah 5 detik
            setTimeout(() => {
                $(alert).alert('close');
            }, 5000);
        }

    });
</script>
@stack("scripts")
</body>
</html>
