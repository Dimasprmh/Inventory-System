@extends('layouts.app')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Barang Keluar</h1>
    <button class="btn btn-sm btn-danger shadow-sm" data-toggle="modal" data-target="#modalBarangKeluar">
        <i class="fas fa-minus fa-sm text-white-50"></i> Tambah Barang Keluar
    </button>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Histori Barang Keluar</h6>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="barangKeluarTable">
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
                    <tbody id="barangKeluarBody">
                        <tr><td colspan="6" class="text-center">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalBarangKeluar" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Barang Keluar</h5>
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
          <input type="date" id="tanggal" class="form-control" required>
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
        <button type="button" id="submitBarangKeluar" class="btn btn-danger">Simpan</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<!-- Toastify.js CDN -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<script>
document.addEventListener('DOMContentLoaded', async function () {
    const token = sessionStorage.getItem('token');

    const itemSelect = document.getElementById('item_id');
    const loadItems = async () => {
        const res = await fetch('/api/items', {
            headers: { Authorization: `Bearer ${token}` }
        });
        const items = await res.json();
        itemSelect.innerHTML = '';
        items.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = `${item.sku} - ${item.merk}`;
            itemSelect.appendChild(option);
        });
    };

    const fetchHistori = async () => {
        const res = await fetch('/api/barang-keluar', {
            headers: { Authorization: `Bearer ${token}` }
        });
        const data = await res.json();
        const tbody = document.getElementById('barangKeluarBody');
        tbody.innerHTML = '';
        data.forEach((item, index) => {
            tbody.innerHTML += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.item?.sku || '-'}</td>
                    <td>${item.item?.merk || '-'}</td>
                    <td>${item.jumlah}</td>
                    <td>${item.tanggal}</td>
                    <td>${item.keterangan || '-'}</td>
                </tr>`;
        });
    };

    document.getElementById('submitBarangKeluar').addEventListener('click', async () => {
        const payload = {
            item_id: document.getElementById('item_id').value,
            tanggal: document.getElementById('tanggal').value,
            jumlah: document.getElementById('jumlah').value,
            keterangan: document.getElementById('keterangan').value
        };

        const res = await fetch('/api/barang-keluar', {
            method: 'POST',
            headers: {
                Authorization: `Bearer ${token}`,
                'Content-Type': 'application/json',
                Accept: 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const result = await res.json();
        $('#modalBarangKeluar').modal('hide');
        fetchHistori();

        Toastify({
            text: result.message || "Barang keluar berhasil",
            duration: 5000,
            gravity: "bottom",
            position: "right",
            backgroundColor: "#dc3545",
            stopOnFocus: true
        }).showToast();
    });

    await loadItems();
    await fetchHistori();
});
</script>
@endpush
