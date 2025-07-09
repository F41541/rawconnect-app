<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\PratinjauItem;
use App\Models\ItemPaket;
use App\Models\PaketPengiriman;

class ResetTransactionsCommand extends Command
{
    protected $signature = 'app:reset-transaksi {--force : Lewati konfirmasi ya/tidak}';
    protected $description = 'HANYA mengosongkan tabel transaksi (paket pengiriman, item, pratinjau).';

    public function handle()
    {
        if (! $this->option('force') && ! $this->confirm('Yakin ingin menghapus semua histori pengiriman dan pratinjau? Data master tidak akan tersentuh.')) {
            return $this->comment('Proses dibatalkan.');
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        PratinjauItem::truncate();
        ItemPaket::truncate();
        PaketPengiriman::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->info('✓ Data transaksi berhasil direset.');
    }
}