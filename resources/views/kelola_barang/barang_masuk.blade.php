@extends('layouts.app')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-white mb-0">
        <li class="breadcrumb-item">
            <a href="{{ url('/dashboard') }}" class="text-dark text-decoration-none">Inventory System</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            Barang Masuk
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Barang Masuk</h1>
    <button class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#modalBarangMasuk">
        <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Barang Masuk
    </button>
</div>

<!-- FILTER -->
<div class="mb-3">
    <div class="row">
        <div class="col-md-3">
            <label>Dari Tanggal</label>
            <input type="date" id="filter_dari" class="form-control">
        </div>
        <div class="col-md-3">
            <label>Sampai Tanggal</label>
            <input type="date" id="filter_sampai" class="form-control">
        </div>
        <div class="col-md-4">
            <label>SKU</label>
            <select id="filter_sku" class="form-control">
                <option value="">Semua SKU</option>
            </select>
        </div>
        <div class="col-md-2">
            <label>&nbsp;</label>
            <button class="btn btn-primary btn-block" id="applyFilter">Filter</button>
        </div>
    </div>
</div>

<!-- Show Entries & Search -->
<div class="row align-items-center mb-2">
    <div class="col-md-6 col-sm-12">
        <label>Show
            <select id="perPageSelect" class="custom-select custom-select-sm w-auto">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
            entries
        </label>
    </div>
    <div class="col-md-6 col-sm-12 text-md-right mt-2 mt-md-0">
        <input type="text" id="searchInput"
            class="form-control form-control-sm d-inline-block"
            style="height: 40px; width: 300px; border-radius: 0.35rem;"
            placeholder="Search">
    </div>
</div>

<!-- TABEL -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Histori Barang Masuk</h6>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="productTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>SKU</th>
                            <th>Merk</th>
                            <th>Jumlah</th>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="productTableBody">
                        <tr><td colspan="6" class="text-center">Loading...</td></tr>
                    </tbody>
                </table>
                <nav>
                    <ul class="pagination justify-content-center" id="paginationControls"></ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalBarangMasuk" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Barang Masuk</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>Kode Barang (SKU)</label>
          <select id="item_id" class="form-control" required>
            <option value="">Loading...</option>
          </select>
        </div>
        <div class="form-group">
          <label>Tanggal</label>
          <input type="datetime-local" id="tanggal" class="form-control" required>
        </div>
        <div class="form-group">
          <label>Jumlah</label>
          <input type="number" id="jumlah" class="form-control" required>
        </div>
        <div class="form-group">
          <label>Keterangan</label>
          <input type="text" id="keterangan" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" id="submitBarangMasuk" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const token = sessionStorage.getItem('token');
    const itemSelect = document.getElementById('item_id');
    const filterSku = document.getElementById('filter_sku');
    const perPageSelect = document.getElementById('perPageSelect');

    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('productTable');
    const tbody = table.querySelector('tbody');

    searchInput.addEventListener('input', function () {
        const keyword = this.value.toLowerCase();
        Array.from(tbody.rows).forEach(row => {
            const rowText = row.innerText.toLowerCase();
            row.style.display = rowText.includes(keyword) ? '' : 'none';
        });
    });

    let currentPage = 1;
    let perPage = parseInt(perPageSelect.value);

    perPageSelect.addEventListener('change', () => {
        perPage = parseInt(perPageSelect.value);
        currentPage = 1;
        fetchHistori();
    });

    document.getElementById('applyFilter').addEventListener('click', () => {
        currentPage = 1;
        fetchHistori();
    });

    document.getElementById('submitBarangMasuk').addEventListener('click', async () => {
        const payload = {
            item_id: document.getElementById('item_id').value,
            tanggal: document.getElementById('tanggal').value,
            jumlah: document.getElementById('jumlah').value,
            keterangan: document.getElementById('keterangan').value
        };

        const res = await fetch('/api/barang-masuk', {
            method: 'POST',
            headers: {
                Authorization: `Bearer ${token}`,
                'Content-Type': 'application/json',
                Accept: 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const result = await res.json();
        $('#modalBarangMasuk').modal('hide');
        fetchHistori();

        Toastify({
            text: result.message || "Barang masuk berhasil",
            duration: 5000,
            gravity: "bottom",
            position: "right",
            backgroundColor: "#28a745",
            stopOnFocus: true
        }).showToast();
    });

    async function loadItems() {
        const res = await fetch('/api/items', {
            headers: { Authorization: `Bearer ${token}` }
        });
        const items = await res.json();
        itemSelect.innerHTML = '';
        filterSku.innerHTML = '<option value="">Semua SKU</option>';
        items.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = `${item.sku} - ${item.merk}`;
            itemSelect.appendChild(option);
            filterSku.appendChild(option.cloneNode(true));
        });
    }

    async function fetchHistori() {
        const dari = document.getElementById('filter_dari').value;
        const sampai = document.getElementById('filter_sampai').value;
        const sku = document.getElementById('filter_sku').value;

        const query = new URLSearchParams();
        query.append('page', currentPage);
        query.append('per_page', perPage);
        if (dari) query.append('tanggal_dari', dari);
        if (sampai) query.append('tanggal_sampai', sampai);
        if (sku) query.append('sku', sku);

        const res = await fetch(`/api/barang-masuk?${query.toString()}`, {
            headers: { Authorization: `Bearer ${token}` }
        });

        const response = await res.json();
        const tbody = document.getElementById('productTableBody');
        tbody.innerHTML = '';
        response.data.forEach((item, index) => {
            const date = new Date(item.tanggal);
            const tanggalFormatted = `${date.getDate()} ${date.toLocaleString('id-ID', { month: 'long' })} ${date.getFullYear()}, ${date.getHours().toString().padStart(2, '0')}.${date.getMinutes().toString().padStart(2, '0')} WIB`;

            tbody.innerHTML += `
                <tr>
                    <td>${(response.from || 1) + index}</td>
                    <td>${item.item.sku}</td>
                    <td>${item.item.merk}</td>
                    <td>${item.jumlah}</td>
                    <td>${tanggalFormatted}</td>
                    <td>${item.keterangan || '-'}</td>
                </tr>`;
        });

        renderPagination(response);
    }

    function renderPagination(data) {
        const pagination = document.getElementById('paginationControls');
        pagination.innerHTML = '';

        for (let i = 1; i <= data.last_page; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${i === data.current_page ? 'active' : ''}`;
            li.innerHTML = `<button class="page-link">${i}</button>`;
            li.addEventListener('click', () => {
                currentPage = i;
                fetchHistori();
            });
            pagination.appendChild(li);
        }
    }

    loadItems();
    fetchHistori();
});
</script>
@endpush
