<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Toko;
use App\Models\JenisProduk;
use App\Models\Ekspedisi;
use App\Models\Merchant;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\LayananPengiriman;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Memulai proses seeding data contoh...');

        $this->command->comment('Tahap 1: Membuat data master...');

        $kategoriProdukJual = Kategori::firstOrCreate(['name' => 'Produk Jual']);
        $kategoriProdukJadi = Kategori::firstOrCreate(['name' => 'Produk Jadi']);
        $kategoriBahanBaku = Kategori::firstOrCreate(['name' => 'Bahan Baku']);
        $kategoriPouchSticker = Kategori::firstOrCreate(['name' => 'Pouch Printing & Sticker']);
        $kategoriKemasan = Kategori::firstOrCreate(['name' => 'Kemasan Dll']);

        $tokoRawTisane = Toko::firstOrCreate(['name' => 'RAW TISANE']);
        $tokoTeaHouse = Toko::firstOrCreate(['name' => 'TEA HOUSE']);

        $merchantShopee = Merchant::firstOrCreate(['name' => 'SHOPEE']);
        $merchantTokopedia = Merchant::firstOrCreate(['name' => 'TOKOPEDIA']);
        $merchantLazada = Merchant::firstOrCreate(['name' => 'LAZADA']);

        $ekspedisiJNE = Ekspedisi::firstOrCreate(['name' => 'JNE']);
        $ekspedisiJNT = Ekspedisi::firstOrCreate(['name' => 'JNT']);
        $ekspedisiSPX = Ekspedisi::firstOrCreate(['name' => 'SPX']);
        
        $this->command->comment('Tahap 2: Membuat jenis produk dan relasinya...');

        $jp10bag = JenisProduk::firstOrCreate(['name' => '10 Tea Bag']);
        $jp10bag->kategoris()->syncWithoutDetaching([$kategoriProdukJual->id, $kategoriProdukJadi->id]);
        
        $jp20bag = JenisProduk::firstOrCreate(['name' => '20 Tea Bag']);
        $jp20bag->kategoris()->syncWithoutDetaching([$kategoriProdukJual->id, $kategoriProdukJadi->id]);
        
        $jpSingle = JenisProduk::firstOrCreate(['name' => 'Single Tea Bag']);
        $jpSingle->kategoris()->syncWithoutDetaching([$kategoriProdukJual->id, $kategoriProdukJadi->id]);
        
        $jpMix = JenisProduk::firstOrCreate(['name' => 'Mix Tea Bag']);
        $jpMix->kategoris()->syncWithoutDetaching([$kategoriProdukJual->id, $kategoriProdukJadi->id]);

        $jpDriedFruit = JenisProduk::firstOrCreate(['name' => 'Dried Fruit']);
        $jpDriedFruit->kategoris()->syncWithoutDetaching([$kategoriProdukJual->id, $kategoriBahanBaku->id]);

        $jpDriedFlower = JenisProduk::firstOrCreate(['name' => 'Dried Flower']);
        $jpDriedFlower->kategoris()->syncWithoutDetaching([$kategoriProdukJual->id, $kategoriBahanBaku->id]);
        
        $jpHerbSpice = JenisProduk::firstOrCreate(['name' => 'Herb & Spice']);
        $jpHerbSpice->kategoris()->syncWithoutDetaching([$kategoriProdukJual->id, $kategoriBahanBaku->id]);

        $jpPowder = JenisProduk::firstOrCreate(['name' => 'Bahan Baku Powder']);
        $jpPowder->kategoris()->syncWithoutDetaching([$kategoriBahanBaku->id]);
        
        $jpPouchPrinting = JenisProduk::firstOrCreate(['name' => 'Pouch Printing']);
        $jpPouchPrinting->kategoris()->syncWithoutDetaching([$kategoriPouchSticker->id]);

        $jpSticker = JenisProduk::firstOrCreate(['name' => 'Sticker']);
        $jpSticker->kategoris()->syncWithoutDetaching([$kategoriPouchSticker->id]);
        
        $jpKemasanLain = JenisProduk::firstOrCreate(['name' => 'Kemasan']);
        $jpKemasanLain->kategoris()->syncWithoutDetaching([$kategoriKemasan->id]);
        
        $jpAksesoris = JenisProduk::firstOrCreate(['name' => 'Aksesoris']);
        $jpAksesoris->kategoris()->syncWithoutDetaching([$kategoriProdukJual->id, $kategoriKemasan]);

        $this->command->comment('Tahap 3: Membuat aturan layanan pengiriman...');
        LayananPengiriman::firstOrCreate(['toko_id' => $tokoRawTisane->id, 'merchant_id' => $merchantShopee->id, 'ekspedisi_id' => $ekspedisiJNE->id]);
        LayananPengiriman::firstOrCreate(['toko_id' => $tokoRawTisane->id, 'merchant_id' => $merchantShopee->id, 'ekspedisi_id' => $ekspedisiJNT->id]);
        LayananPengiriman::firstOrCreate(['toko_id' => $tokoRawTisane->id, 'merchant_id' => $merchantTokopedia->id, 'ekspedisi_id' => $ekspedisiJNT->id]);
        LayananPengiriman::firstOrCreate(['toko_id' => $tokoTeaHouse->id, 'merchant_id' => $merchantLazada->id, 'ekspedisi_id' => $ekspedisiSPX->id]);

        $this->command->comment('Tahap 4: Membuat contoh produk...');
        Produk::firstOrCreate(
            ['nama' => 'Teh Daun Mint', 'toko_id' => $tokoTeaHouse->id, 'jenis_produk_id' => $jp10bag->id],
            ['stok' => 50, 'satuan' => 'pouch']
        );
        Produk::firstOrCreate(
            ['nama' => 'Chamomile Flower', 'toko_id' => $tokoRawTisane->id, 'jenis_produk_id' => $jpDriedFlower->id],
            ['stok' => 5000, 'satuan' => 'gram']
        );
         Produk::firstOrCreate(
            ['nama' => 'Betel Leaf Tea', 'toko_id' => $tokoRawTisane->id, 'jenis_produk_id' => $jpSticker->id],
            ['stok' => 33, 'satuan' => 'pcs']
        );
        Produk::firstOrCreate(
            ['nama' => 'Pinset A5', 'toko_id' => $tokoTeaHouse->id, 'jenis_produk_id' => $jpAksesoris->id],
            ['stok' => 10, 'satuan' => 'pcs']
        );

        $this->command->info('✓ Proses seeding data contoh selesai.');
    }
}