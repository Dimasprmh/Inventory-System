@extends('layouts.app')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-white mb-0">
        <li class="breadcrumb-item">
            <a href="{{ url('/dashboard') }}" class="text-dark text-decoration-none">Inventory System</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            User Management
        </li>
    </ol>
</nav>
@endsection

@section('content')
<h1 class="h3 mb-4 text-gray-800">Karyawan Manajemen</h1>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Pengguna</h6>
        <button class="btn btn-sm btn-primary" id="btnAddUser">+ Tambah Pengguna</button>
    </div>
    <div class="card-body">
        <table class="table table-bordered" id="usersTable">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Modal Form User -->
<div class="modal fade" id="userModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Pengguna</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="userForm">
                    <input type="hidden" id="userId">
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" class="form-control" id="name" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" id="email" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" id="password">
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select id="role" class="form-control">
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" form="userForm" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const token = sessionStorage.getItem('token');
// const user = sessionStorage.getItem('user');
// if(user.role !== 'admin') window.location.href = '/403';

async function loadUsers() {
    const res = await fetch('/api/users', {
        headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
    });
    const result = await res.json();
    const tbody = document.querySelector('#usersTable tbody');
    tbody.innerHTML = '';
    result.data.forEach(user => {
        tbody.innerHTML += `
            <tr>
                <td>${user.name}</td>
                <td>${user.email}</td>
                <td>${user.role}</td>
                <td>${new Date(user.created_at).toLocaleString()}</td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick='editUser(${JSON.stringify(user)})'>Edit</button>
                    <button class="btn btn-sm btn-danger" onclick='deleteUser("${user.id}")'>Hapus</button>
                </td>
            </tr>`;
    });
}

function editUser(user) {

    document.getElementById('userId').value = user.id;
    document.getElementById('name').value = user.name;
    document.getElementById('email').value = user.email;
    document.getElementById('password').value = '';
    document.getElementById('role').value = user.role;
    $('#userModal').modal('show');
}

function deleteUser(id) {
    console.log("Delete User ID:", id);

    if (!confirm('Yakin ingin menghapus user ini?')) return;
    fetch(`/api/users/${id}`, {
        method: 'DELETE',
        headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
    }).then(loadUsers);
}

const form = document.getElementById('userForm');
form.addEventListener('submit', function (e) {
    e.preventDefault();
    const id = document.getElementById('userId').value;
    const method = id ? 'PUT' : 'POST';
    const url = id ? `/api/users/${id}` : '/api/users';
    const payload = {
        name: document.getElementById('name').value,
        email: document.getElementById('email').value,
        password: document.getElementById('password').value,
        role: document.getElementById('role').value,
    };

    console.log("Submit Data:", { method, url, payload });

    fetch(url, {
        method: method,
        headers: {
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    }).then(() => {
        $('#userModal').modal('hide');
        loadUsers();
    });
});

document.getElementById('btnAddUser').addEventListener('click', () => {
    form.reset();
    document.getElementById('userId').value = '';
    $('#userModal').modal('show');
});

loadUsers();
</script>
@endpush
