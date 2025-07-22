import './bootstrap';
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;
import jQuery from 'jquery';
window.$ = window.jQuery = jQuery;
import toastr from 'toastr';
window.toastr = toastr;
import Chart from 'chart.js/auto';
import './script.js';
import Alpine from 'alpinejs';

// --- LOGIKA UTAMA SETELAH HALAMAN DIMUAT ---
document.addEventListener('DOMContentLoaded', () => {

    const pageDataEl = document.getElementById('page-data');

    // Jika elemen jembatan data tidak ada, jangan lakukan apa-apa
    if (!pageDataEl) return;

    // --- Logika 1: Notifikasi (Hanya berjalan jika ada pesan sesi) ---
    const sessionMessage = pageDataEl.dataset.sessionMessage;
    if (sessionMessage) {
        const sessionStatus = pageDataEl.dataset.sessionStatus;
        toastr.options = {
            "closeButton": true, "progressBar": true, "positionClass": "toast-top-right", "timeOut": "4000",
        };
        if (sessionStatus === 'success') toastr.success(sessionMessage);
        if (sessionStatus === 'error') toastr.error(sessionMessage);
    }

    // --- Logika 2: Grafik Penjualan (Hanya berjalan jika ada kanvas dan datanya) ---
    const salesChartCanvas = document.getElementById('salesChart');
    if (salesChartCanvas && pageDataEl.dataset.salesChartLabels) {
        const labels = JSON.parse(pageDataEl.dataset.salesChartLabels);
        const values = JSON.parse(pageDataEl.dataset.salesChartValues);
        new Chart(salesChartCanvas, {
            type: 'line', data: { labels: labels, datasets: [{ label: 'Item Terjual', data: values, fill: true, backgroundColor: 'rgba(13, 110, 253, 0.1)', borderColor: 'rgba(13, 110, 253, 1)', tension: 0 }] },
            options: { scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }, responsive: true, maintainAspectRatio: false, plugins: {legend: {display: false}}
        }
        });
    }

    // --- Logika 3: Grafik Stok (Hanya berjalan jika ada kanvas dan datanya) ---
    const stockChartCanvas = document.getElementById('stockMovementChart');
    if (stockChartCanvas && pageDataEl.dataset.stockChartLabels) {
        const labels = JSON.parse(pageDataEl.dataset.stockChartLabels);
        const stockMasuk = JSON.parse(pageDataEl.dataset.stockChartMasuk);
        const stockKeluar = JSON.parse(pageDataEl.dataset.stockChartKeluar);
        new Chart(stockChartCanvas, {
            type: 'bar', data: { labels: labels, datasets: [ { label: 'Stok Masuk', data: stockMasuk, backgroundColor: 'rgba(25, 135, 84, 0.7)' }, { label: 'Stok Keluar', data: stockKeluar, backgroundColor: 'rgba(220, 53, 69, 0.7)' } ] },
            options: { scales: { x: { stacked: false }, y: { stacked: false, beginAtZero: true, ticks: { precision: 0 } } }, responsive: true, maintainAspectRatio: false }
        });
    }

    // --- Logika 4: Grafik Merchant (Hanya berjalan jika ada kanvas dan datanya) ---
    const merchantChartCanvas = document.getElementById('merchantPieChart');
    if (merchantChartCanvas && pageDataEl.dataset.merchantChartLabels) {
        const labels = JSON.parse(pageDataEl.dataset.merchantChartLabels);
        const data = JSON.parse(pageDataEl.dataset.merchantChartData);
        new Chart(merchantChartCanvas, {
            type: 'doughnut', data: { labels: labels, datasets: [{ label: 'Jumlah Paket', data: data, backgroundColor: ['rgba(13, 110, 253, 0.7)', 'rgba(25, 135, 84, 0.7)', 'rgba(255, 193, 7, 0.7)', 'rgba(220, 53, 69, 0.7)', 'rgba(108, 117, 125, 0.7)'], hoverOffset: 4 }] },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }
});

// Jalankan Alpine.js di akhir
window.Alpine = Alpine;
Alpine.start();