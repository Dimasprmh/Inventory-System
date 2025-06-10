@extends('layouts.app')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Detail Produk: <span id="productName"></span></h1>
    <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Item</h6>
        <button class="btn btn-sm btn-primary" id="addItemBtn">+ Tambah Item</button>
    </div>
    <div class="card-body">
        <table class="table table-bordered" id="itemTable" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>SKU</th>
                    <th>Merk</th>
                    <th>Ukuran</th>
                    <th>Stock</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="itemTableBody">
                <tr>
                    <td colspan="6" class="text-center">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah/Edit Item -->
<div class="modal fade" id="itemModal" tabindex="-1" role="dialog" aria-labelledby="itemModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="itemForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="itemModalLabel">Tambah Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="itemId">
                    <div class="form-group">
                        <label for="sku">SKU</label>
                        <input type="text" class="form-control" id="sku" required>
                    </div>
                    <div class="form-group">
                        <label for="merk">Merk</label>
                        <input type="text" class="form-control" id="merk" required>
                    </div>
                    <div class="form-group">
                        <label for="ukuran">Ukuran</label>
                        <input type="number" class="form-control" id="ukuran" required>
                    </div>
                    <div class="form-group">
                        <label for="stock">Stock</label>
                        <input type="number" class="form-control" id="stock" required>
                    </div>
                    <div id="attributeFields"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
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
    const productId = window.location.pathname.split('/')[2];
    const productName = new URLSearchParams(window.location.search).get('name');
    const token = sessionStorage.getItem('token');
    let attributes = [];

    document.getElementById('productName').innerText = productName;

    async function loadItems() {
        try {
            const response = await fetch(`/api/products/${productId}/items`, {
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            });

            const result = await response.json();
            const tbody = document.getElementById('itemTableBody');
            const theadRow = document.querySelector('#itemTable thead tr');

            tbody.innerHTML = '';
            attributes = result.data.attributes;

            attributes.forEach(attr => {
                if (!theadRow.querySelector(`[data-attr='${attr.name}']`)) {
                    const th = document.createElement('th');
                    th.textContent = attr.name;
                    th.setAttribute('data-attr', attr.name);
                    theadRow.insertBefore(th, theadRow.lastElementChild);
                }
            });

            const items = result.data.items;
            if (!items || items.length === 0) {
                tbody.innerHTML = `<tr><td colspan="${theadRow.children.length}" class="text-center">Tidak ada item</td></tr>`;
                return;
            }

            items.forEach((item, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${item.sku}</td>
                    <td>${item.merk}</td>
                    <td>${item.ukuran}</td>
                    <td>${item.stock}</td>
                `;

                attributes.forEach(attr => {
                    const found = item.attribute_values.find(val => val.product_attribute_id === attr.id);
                    row.innerHTML += `<td>${found ? found.value : '-'}</td>`;
                });

                row.innerHTML += `
                    <td>
                        <button class='btn btn-sm btn-warning mr-1' onclick='editItem(${JSON.stringify(item)})'>Edit</button>
                        <button class='btn btn-sm btn-danger' onclick='deleteItem("${item.id}")'>Hapus</button>
                    </td>`;

                tbody.appendChild(row);
            });
        } catch (err) {
            console.error('Fetch error:', err);
            document.getElementById('itemTableBody').innerHTML =
                `<tr><td colspan="6" class="text-center text-danger">Gagal memuat data item</td></tr>`;
        }
    }

    document.getElementById('addItemBtn').addEventListener('click', () => {
        document.getElementById('itemForm').reset();
        document.getElementById('itemId').value = '';
        document.getElementById('attributeFields').innerHTML = '';
        attributes.forEach(attr => {
            document.getElementById('attributeFields').innerHTML += `
                <div class="form-group">
                    <label>${attr.name}</label>
                    <input type="text" class="form-control" name="attribute_${attr.id}" placeholder="${attr.name}">
                </div>`;
        });
        $('#itemModal').modal('show');
    });

    window.editItem = function (item) {
        document.getElementById('itemForm').reset();
        document.getElementById('itemId').value = item.id;
        document.getElementById('sku').value = item.sku;
        document.getElementById('merk').value = item.merk;
        document.getElementById('ukuran').value = item.ukuran;
        document.getElementById('stock').value = item.stock;

        document.getElementById('attributeFields').innerHTML = '';
        attributes.forEach(attr => {
            const val = item.attribute_values.find(v => v.product_attribute_id === attr.id);
            document.getElementById('attributeFields').innerHTML += `
                <div class="form-group">
                    <label>${attr.name}</label>
                    <input type="text" class="form-control" name="attribute_${attr.id}" placeholder="${attr.name}" value="${val ? val.value : ''}">
                </div>`;
        });

        $('#itemModal').modal('show');
    }

    document.getElementById('itemForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        const itemId = document.getElementById('itemId').value;
        const payload = {
            sku: document.getElementById('sku').value,
            merk: document.getElementById('merk').value,
            ukuran: parseInt(document.getElementById('ukuran').value),
            stock: parseInt(document.getElementById('stock').value),
            attribute_values: attributes.map(attr => ({
                product_attribute_id: attr.id,
                value: document.querySelector(`[name='attribute_${attr.id}']`).value || ''
            }))
        };

        const url = itemId
            ? `/api/products/${productId}/items/${itemId}`
            : `/api/products/${productId}/items`;

        const method = itemId ? 'PUT' : 'POST';

        try {
            await fetch(url, {
                method,
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            $('#itemModal').modal('hide');
            loadItems();
        } catch (error) {
            console.error('Gagal simpan item:', error);
        }
    });

    window.deleteItem = async function (id) {
        if (!confirm('Yakin ingin menghapus item ini?')) return;
        try {
            await fetch(`/api/products/${productId}/items/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });
            loadItems();
        } catch (error) {
            alert('Gagal menghapus item.');
        }
    }

    loadItems();
});
</script>
@endpush