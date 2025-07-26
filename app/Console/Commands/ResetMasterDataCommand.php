<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use App\Models\LayananPengiriman;
use App\Models\Toko;
use App\Models\Kategori;
use App\Models\JenisProduk;
use App\Models\Ekspedisi;
use App\Models\Merchant;
use App\Models\StockLog;


class ResetMasterDataCommand extends Command
{
    protected $signature = 'app:reset-master-data {--seed : Jalankan juga produk seeder setelah reset} {--force : Lewati konfirmasi ya/tidak}';
    protected $description = 'Menghapus SEMUA data master (Toko, Produk, dll) DAN semua data transaksi (kecuali user admin).';

    public function handle()
    {
        if (!$this->option('force') && !$this->confirm('PERINGATAN BESAR! Ini akan menghapus data Toko, Merchant, Ekspedisi, dll. Anda YAKIN?')) {
            return $this->comment('Proses dibatalkan.');
        }
        $this->info('Memulai proses reset data infrastruktur...');

        $this->call('app:reset-produk', ['--force' => true]);

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        LayananPengiriman::truncate();
        Kategori::truncate();
        Toko::truncate();
        JenisProduk::truncate();
        Ekspedisi::truncate();
        Merchant::truncate();
        StockLog::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->info('✓ Data infrastruktur berhasil direset.');

        if ($this->option('seed')) {
            $this->info('Menjalankan ulang ProdukSeeder...');
            $this->call('db:seed', ['--class' => 'ProdukSeeder', '--force' => true]);
        }
    }
}
