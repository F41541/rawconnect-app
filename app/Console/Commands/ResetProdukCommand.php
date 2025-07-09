<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use App\Models\PratinjauItem;
use App\Models\ItemPaket;
use App\Models\PaketPengiriman;
use App\Models\Produk;

class ResetProdukCommand extends Command
{
    protected $signature = 'app:reset-produk {--force : Lewati konfirmasi ya/tidak}';
    protected $description = 'Menghapus semua produk DAN data transaksi terkait.';

    public function handle()
    {
        if (!$this->option('force') && !$this->confirm('PERINGATAN! Ini akan menghapus SEMUA produk dan data pengiriman. Anda yakin?')) {
            return $this->comment('Proses dibatalkan.');
        }
        $this->info('Memulai proses reset data produk dan transaksi...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        PratinjauItem::truncate();
        ItemPaket::truncate();
        PaketPengiriman::truncate();
        Produk::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->info('✓ Data produk dan transaksi berhasil direset.');

    }
}