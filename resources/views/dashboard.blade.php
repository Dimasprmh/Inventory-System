@extends('layouts.app')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-white mb-0">
        <li class="breadcrumb-item">
            <a href="{{ url('/dashboard') }}" class="text-dark text-decoration-none">Inventory System</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            Dashboard
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
</div>

<div class="row">
    <!-- Products -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Products</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-products">-</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Items -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Items</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-items">-</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barang Masuk -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Barang Masuk</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-masuk">-</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barang Keluar -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Barang Keluar</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-keluar">-</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-comments fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Grafik Barang Masuk & Keluar -->
<div class="card shadow mb-4">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">Grafik Barang Masuk & Keluar per Bulan ({{ date('Y') }})</h6>
    </div>
    <div class="card-body">
        <canvas id="grafikBarangBulanan" height="100"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', async function () {
    const token = sessionStorage.getItem('token');

    const resStatistik = await fetch('/api/dashboard/statistik', {
        headers: { Authorization: `Bearer ${token}` }
    });
    const stat = await resStatistik.json();
    document.getElementById('stat-products').textContent = stat.products ?? 0;
    document.getElementById('stat-items').textContent = stat.items ?? 0;
    document.getElementById('stat-masuk').textContent = stat.masuk ?? 0;
    document.getElementById('stat-keluar').textContent = stat.keluar ?? 0;

    const res = await fetch('/api/dashboard/grafik-bulanan', {
        headers: { Authorization: `Bearer ${token}` }
    });
    const data = await res.json();
    const labels = data.map(item => item.bulan);
    const dataMasuk = data.map(item => item.masuk);
    const dataKeluar = data.map(item => item.keluar);

    const ctx = document.getElementById('grafikBarangBulanan').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Barang Masuk',
                    data: dataMasuk,
                    borderColor: 'green',
                    backgroundColor: 'transparent',
                    tension: 0.3
                },
                {
                    label: 'Barang Keluar',
                    data: dataKeluar,
                    borderColor: 'red',
                    backgroundColor: 'transparent',
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                title: { display: true, text: 'Grafik Barang Masuk & Keluar' }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
});
</script>
@endpush
