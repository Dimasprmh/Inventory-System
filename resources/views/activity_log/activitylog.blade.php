@extends('layouts.app')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-white mb-0">
        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-dark text-decoration-none">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Log Aktivitas</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Log Aktivitas Saya</h1>
</div>

<div class="mb-3 row">
    <div class="col-md-4">
        <label>Filter Tipe</label>
        <select id="filterTipe" class="form-control">
            <option value="">Semua</option>
            <option value="masuk">Barang Masuk</option>
            <option value="keluar">Barang Keluar</option>
        </select>
    </div>
    <div class="col-md-4">
        <label>Dari Tanggal</label>
        <input type="date" id="filterDari" class="form-control">
    </div>
    <div class="col-md-4">
        <label>Sampai Tanggal</label>
        <input type="date" id="filterSampai" class="form-control">
    </div>
</div>

<div class="card shadow">
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>SKU</th>
                    <th>Merk</th>
                    <th>Tipe</th>
                    <th>Jumlah</th>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody id="logBody">
                <tr><td colspan="7" class="text-center">Loading...</td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const token = sessionStorage.getItem('token');

    const tipeSelect = document.getElementById('filterTipe');
    const dariInput = document.getElementById('filterDari');
    const sampaiInput = document.getElementById('filterSampai');
    const tbody = document.getElementById('logBody');

    function formatTanggal(rawDate) {
        const d = new Date(rawDate);
        return `${d.getDate()} ${d.toLocaleString('id-ID', { month: 'long' })} ${d.getFullYear()}, ${d.getHours().toString().padStart(2, '0')}.${d.getMinutes().toString().padStart(2, '0')} WIB`;
    }

    async function fetchLog() {
        const params = new URLSearchParams();
        if (tipeSelect.value) params.append('tipe', tipeSelect.value);
        if (dariInput.value) params.append('tanggal_dari', dariInput.value);
        if (sampaiInput.value) params.append('tanggal_sampai', sampaiInput.value);

        const res = await fetch(`/api/my-activity-log?${params.toString()}`, {
            headers: { Authorization: `Bearer ${token}` }
        });

        const { data } = await res.json();
        tbody.innerHTML = '';

        if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center">Tidak ada aktivitas.</td></tr>';
            return;
        }

        data.forEach((item, index) => {
            tbody.innerHTML += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.item.sku}</td>
                    <td>${item.item.merk}</td>
                    <td>${item.tipe}</td>
                    <td>${item.jumlah}</td>
                    <td>${formatTanggal(item.tanggal)}</td>
                    <td>${item.keterangan ?? '-'}</td>
                </tr>`;
        });
    }

    tipeSelect.addEventListener('change', fetchLog);
    dariInput.addEventListener('change', fetchLog);
    sampaiInput.addEventListener('change', fetchLog);

    fetchLog();
});
</script>
@endpush
