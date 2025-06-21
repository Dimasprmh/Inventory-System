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

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            @include('layouts.navbar')

            <div class="container-fluid">
                <div id="flashMessageContainer" class="mb-4"></div>
                @yield('content')
            </div>
        </div>
    </div>
</div>

<!-- JS Dependencies -->
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

            sessionStorage.setItem('user', JSON.stringify(user));
            renderSidebar(user.menus);

            document.querySelectorAll('.sidebar-username').forEach(el => el.innerText = user.name);
            document.querySelectorAll('.navbar-username').forEach(el => el.innerText = user.name);

        } catch (error) {
            console.warn('Token expired atau tidak valid, mengarahkan ke login...');
            sessionStorage.clear();
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

            container.innerHTML = '';
            container.appendChild(alert);

            setTimeout(() => {
                $(alert).alert('close');
            }, 5000);
        }

        function renderSidebar(menus) {
            const sidebar = document.getElementById('accordionSidebar');

            let html = `
                <a class="sidebar-brand d-flex align-items-center justify-content-center">
                    <div class="sidebar-brand-text mx-3">Stok Gudang</div>
                </a>
                <hr class="sidebar-divider my-0">
            `;

            menus.forEach(menu => {
                if (menu.children) {
                    const id = 'menu-' + menu.title.toLowerCase().replace(/\s+/g, '-');
                    html += `
                        <li class="nav-item">
                            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#${id}"
                                aria-expanded="false" aria-controls="${id}">
                                <i class="${menu.icon}"></i>
                                <span>${menu.title}</span>
                            </a>
                            <div id="${id}" class="collapse" data-parent="#accordionSidebar">
                                <div class="bg-white py-2 collapse-inner rounded">
                                    ${menu.children.map(child => `<a class="collapse-item" href="${child.url}">${child.title}</a>`).join('')}
                                </div>
                            </div>
                        </li>
                        <hr class="sidebar-divider">
                    `;
                } else {
                    html += `
                        <li class="nav-item">
                            <a class="nav-link" href="${menu.url}">
                                <i class="${menu.icon}"></i>
                                <span>${menu.title}</span>
                            </a>
                        </li>
                        <hr class="sidebar-divider">
                    `;
                }
            });

            html += `
                <div class="text-center d-none d-md-inline">
                    <button class="rounded-circle border-0" id="sidebarToggle"></button>
                </div>
            `;

            sidebar.innerHTML = html;

            // Bind ulang tombol toggle agar sidebar bisa menyempit
            const toggleBtn = document.getElementById('sidebarToggle');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function () {
                    document.body.classList.toggle('sidebar-toggled');
                    document.querySelector('.sidebar').classList.toggle('toggled');
                });
            }
        }
    });
</script>

@stack("scripts")
</body>
</html>
