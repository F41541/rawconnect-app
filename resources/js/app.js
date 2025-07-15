import './bootstrap';
import * as bootstrap from 'bootstrap';
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
    if (!pageDataEl) return; // Jika tidak ada data, hentikan

    // 1. Logika Notifikasi Toastr
    const sessionStatus = pageDataEl.dataset.sessionStatus;
    const sessionMessage = pageDataEl.dataset.sessionMessage;
    if (sessionMessage) {
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "4000",
        };
        if (sessionStatus === 'success') toastr.success(sessionMessage);
        if (sessionStatus === 'error') toastr.error(sessionMessage);
    }

    // 2. Logika Grafik Penjualan
    const salesChartCanvas = document.getElementById('salesChart');
    if (salesChartCanvas) {
        const labels = JSON.parse(pageDataEl.dataset.salesChartLabels);
        const values = JSON.parse(pageDataEl.dataset.salesChartValues);
        new Chart(salesChartCanvas, {
            type: 'line', data: { labels: labels, datasets: [{ label: 'Item Terjual', data: values, fill: true, backgroundColor: 'rgba(13, 110, 253, 0.1)', borderColor: 'rgba(13, 110, 253, 1)', tension: 0.3 }] },
            options: { scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }, responsive: true, maintainAspectRatio: false }
        });
    }

    // 3. Logika Grafik Stok Masuk vs Keluar
    const stockChartCanvas = document.getElementById('stockMovementChart');
    if (stockChartCanvas) {
        const labels = JSON.parse(pageDataEl.dataset.stockChartLabels);
        const stockMasuk = JSON.parse(pageDataEl.dataset.stockChartMasuk);
        const stockKeluar = JSON.parse(pageDataEl.dataset.stockChartKeluar);
        new Chart(stockChartCanvas, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    { 
                        label: 'Stok Masuk', 
                        data: stockMasuk, 
                        backgroundColor: 'rgba(25, 135, 84, 0.7)', 
                        borderColor: 'rgba(13, 150, 0, 1)', 
                        borderWidth: 1 
                    },
                    { 
                        label: 'Stok Keluar', 
                        data: stockKeluar, 
                        backgroundColor: 'rgba(220, 53, 69, 0.7)', 
                        borderColor: 'rgba(255, 0, 25, 1)', 
                        borderWidth: 1 
                    }
                ]
            },
            options: { 
                scales: { 
                    x: { stacked: false }, 
                    y: { beginAtZero: true, ticks: { precision: 0 } } 
                }, 
                responsive: true, 
                maintainAspectRatio: false 
            }
        });
    }

    // ===========================================
    // == LOGIKA BARU UNTUK GRAFIK MERCHANT ==
    // ===========================================
    const merchantChartCanvas = document.getElementById('merchantPieChart');
    if (merchantChartCanvas) {
        const labels = JSON.parse(pageDataEl.dataset.merchantChartLabels);
        const data = JSON.parse(pageDataEl.dataset.merchantChartData);

        new Chart(merchantChartCanvas, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Paket',
                    data: data,
                    backgroundColor: [
                        'rgba(13, 110, 253, 0.7)',  // Biru
                        'rgba(25, 135, 84, 0.7)',   // Hijau
                        'rgba(255, 193, 7, 0.7)',  // Kuning
                        'rgba(220, 53, 69, 0.7)',   // Merah
                        'rgba(108, 117, 125, 0.7)'  // Abu-abu
                    ],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
            }
        });
    }
});

// Jalankan Alpine.js di akhir
window.Alpine = Alpine;
Alpine.start();