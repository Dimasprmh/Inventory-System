@extends('layouts.app')
@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Stok Barang</h1>
    <button class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#modalTambahProduk">
        <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Produk
    </button>
</div>

<div class="mb-3">
    <input type="text" class="form-control" id="searchInput" placeholder="Cari produk...">
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Produk</h6>
            </div>
            <div class="card-body">
                <div id="productTableWrapper">
                    <table class="table table-bordered" id="productTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>Nama</th>
                                <th width="150">Unit</th>
                                <th width="250">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="productTableBody">
                            <tr>
                                <td colspan="4" class="text-center">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                    <div id="paginationWrapper" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Produk -->
<div class="modal fade" id="modalTambahProduk" tabindex="-1" role="dialog" aria-labelledby="modalTambahProdukLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="formTambahProduk">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Produk</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <!-- Nama Produk -->
                    <div class="form-group">
                        <label for="namaProduk">Nama Produk</label>
                        <input type="text" class="form-control" id="namaProduk" name="name" required>
                    </div>

                    <!-- Unit -->
                    <div class="form-group">
                        <label for="unitProduk">Unit</label>
                        <input type="text" class="form-control" id="unitProduk" name="unit" required>
                    </div>

                    <!-- Item Attributes Dinamis -->
                    <div class="form-group">
                        <label>Item Attributes</label>
                        <div id="attributesWrapper"></div>
                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="addAttributeBtn">+ Tambah Atribut</button>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>



<!-- Modal Edit Produk -->
<div class="modal fade" id="modalEditProduk" tabindex="-1" role="dialog" aria-labelledby="modalEditProdukLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="formEditProduk">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Produk</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editProductId">
                    <div class="form-group">
                        <label for="editNamaProduk">Nama Produk</label>
                        <input type="text" class="form-control" id="editNamaProduk" required>
                    </div>
                    <div class="form-group">
                        <label for="editUnitProduk">Unit</label>
                        <input type="text" class="form-control" id="editUnitProduk" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        loadProducts(1);
    });

    let attributeIndex = 0;

    document.getElementById('addAttributeBtn').addEventListener('click', function () {
        const wrapper = document.getElementById('attributesWrapper');

        const div = document.createElement('div');
        div.classList.add('form-row', 'mb-2');
        div.innerHTML = `
            <div class="col">
                <input type="text" class="form-control" name="attributes[${attributeIndex}][name]" placeholder="Nama Atribut (mis. warna)" required>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger btn-sm remove-attribute">&times;</button>
            </div>
        `;

        wrapper.appendChild(div);
        attributeIndex++;
    });

    document.getElementById('attributesWrapper').addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-attribute')) {
            e.target.closest('.form-row').remove();
        }
    });

    async function loadProducts(page = 1) {
        const token = sessionStorage.getItem('token');
        const tbody = document.getElementById('productTableBody');
        const paginationWrapper = document.getElementById('paginationWrapper');

        if (!token) return window.location.href = '/login';

        try {
            const response = await fetch(`/api/products?page=${page}`, {
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            });

            if (!response.ok) throw new Error('Unauthenticated');

            const result = await response.json();
            const products = result.data.data || [];
            tbody.innerHTML = '';

            if (products.length === 0) {
                tbody.innerHTML = `<tr><td colspan="4" class="text-center">Tidak ada produk</td></tr>`;
                paginationWrapper.innerHTML = '';
                return;
            }

            products.forEach((p, index) => {
                const nomor = (result.data.per_page * (result.data.current_page - 1)) + index + 1;
                const row = `
                    <tr>
                        <td>${nomor}</td>
                        <td>${p.name}</td>
                        <td>${p.unit}</td>
                        <td class="text-center">
                            <a href="/products/${p.id}/items?name=${encodeURIComponent(p.name)}" class="btn btn-info btn-sm">
                                <i class="fas fa-info-circle"></i> Info
                            </a>
                            <button class="btn btn-sm btn-warning mr-1" onclick="editProduk('${p.id}', '${p.name}', '${p.unit}')">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="hapusProduk('${p.id}')">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });

            renderPagination(result.data.current_page, result.data.last_page);
        } catch (error) {
            console.error(error);
            tbody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">Gagal mengambil data</td></tr>`;
        }
    }

    function renderPagination(currentPage, lastPage) {
        const paginationWrapper = document.getElementById('paginationWrapper');
        let html = '';
        if (currentPage > 1) html += `<li class="page-item"><a class="page-link" href="#" onclick="loadProducts(${currentPage - 1}); return false;">&laquo;</a></li>`;
        else html += `<li class="page-item disabled"><span class="page-link">&laquo;</span></li>`;

        for (let i = 1; i <= lastPage; i++) {
            html += (i === currentPage)
                ? `<li class="page-item active"><span class="page-link">${i}</span></li>`
                : `<li class="page-item"><a class="page-link" href="#" onclick="loadProducts(${i}); return false;">${i}</a></li>`;
        }

        if (currentPage < lastPage) html += `<li class="page-item"><a class="page-link" href="#" onclick="loadProducts(${currentPage + 1}); return false;">&raquo;</a></li>`;
        else html += `<li class="page-item disabled"><span class="page-link">&raquo;</span></li>`;

        paginationWrapper.innerHTML = `<ul class="pagination justify-content-center">${html}</ul>`;
    }

    document.getElementById('formTambahProduk').addEventListener('submit', async function (e) {
        e.preventDefault();
        const token = sessionStorage.getItem('token');
        const name = document.getElementById('namaProduk').value;
        const unit = document.getElementById('unitProduk').value;

        // Ambil semua atribut yang diinputkan
        const attributeInputs = document.querySelectorAll('#attributesWrapper input[name^="attributes"]');
        const attributes = [];
        attributeInputs.forEach(input => {
            attributes.push({ name: input.value });
        });

        try {
            const response = await fetch('/api/products', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ name, unit, attributes })
            });

            if (!response.ok) throw new Error('Gagal menyimpan produk');

            $('#modalTambahProduk').modal('hide');
            showFlashMessage('success', 'Produk berhasil ditambahkan!');
            loadProducts();
            this.reset();
            document.getElementById('attributesWrapper').innerHTML = '';
            attributeIndex = 0;
        } catch (error) {
            console.log(error)
            console.error(error);
            alert('Terjadi kesalahan saat menambah produk.');
        }
    });

    async function hapusProduk(id) {
        const token = sessionStorage.getItem('token');

        try {
            const response = await fetch(`/api/products/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Gagal menghapus produk');

            showFlashMessage('success', 'Produk berhasil dihapus!');
            loadProducts();
        } catch (error) {
            console.error(error);
            showFlashMessage('danger', 'Terjadi kesalahan saat menghapus produk.');
        }
    }

    function editProduk(id, name, unit) {
        document.getElementById('editProductId').value = id;
        document.getElementById('editNamaProduk').value = name;
        document.getElementById('editUnitProduk').value = unit;
        $('#modalEditProduk').modal('show');
    }

    document.getElementById('formEditProduk').addEventListener('submit', async function (e) {
        e.preventDefault();
        const token = sessionStorage.getItem('token');
        const id = document.getElementById('editProductId').value;
        const name = document.getElementById('editNamaProduk').value;
        const unit = document.getElementById('editUnitProduk').value;

        try {
            const response = await fetch(`/api/products/${id}`, {
                method: 'PUT',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ name, unit })
            });

            if (!response.ok) throw new Error('Gagal mengupdate produk');

            $('#modalEditProduk').modal('hide');
            showFlashMessage('success', 'Produk berhasil diperbarui!');
            loadProducts();
        } catch (error) {
            console.error(error);
            alert('Terjadi kesalahan saat mengupdate produk.');
        }
    });

    function showFlashMessage(type, message) {
        alert(message);
    }
</script>
@endpush