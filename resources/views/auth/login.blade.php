@extends('auth.layouts.app')

@section('content')
<div class="container d-flex align-items-center justify-content-center min-vh-100">
    <div class="col-xl-6 col-lg-8 col-md-9">
        <div class="card o-hidden border-0 shadow-lg my-5 rounded-lg">
            <div class="card-body p-4">
                <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">Selamat Datang Kembali!</h1>
                </div>

                <div id="alert" class="alert alert-danger d-none" role="alert"></div>

                <form class="user" id="loginForm">
                    <div class="form-group">
                        <input type="email" class="form-control form-control-user"
                            id="email" name="email" aria-describedby="emailHelp"
                            placeholder="Masukkan Alamat Email..." required autofocus>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control form-control-user"
                            id="password" name="password" placeholder="Kata Sandi" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-user btn-block">
                        Masuk
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const form = document.getElementById('loginForm');
    const alertDiv = document.getElementById('alert');

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        alertDiv.classList.add('d-none');
        alertDiv.innerText = '';

        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        try {
            const response = await fetch('/api/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email, password })
            });

            const data = await response.json();

            if (response.ok) {
                sessionStorage.setItem('token', data.user.token);
                sessionStorage.setItem('user', JSON.stringify(data.user));
                window.location.href = '/dashboard';
            } else {
                alertDiv.innerText = data.message || 'Login gagal. Coba lagi.';
                alertDiv.classList.remove('d-none');
            }
        } catch (error) {
            alertDiv.innerText = 'Terjadi kesalahan saat login.';
            alertDiv.classList.remove('d-none');
        }
    });
</script>
@endpush
@endsection
