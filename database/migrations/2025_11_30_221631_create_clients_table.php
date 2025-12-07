<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel users (admin/sales yang menginput data)
            // onDelete('cascade') berarti jika User dihapus, data kliennya ikut terhapus
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            
            // Kolom Data Area & PIC
            $table->string('area')->nullable();      // Contoh: JakSel, Jatim
            $table->string('pic')->nullable();       // Contoh: Tuah, Ayu Diah
            
            // Kolom Data Utama Klien
            $table->string('nama_user');             // Contoh: Dr. Budi (User/Client)
            $table->string('nama_perusahaan');       // Contoh: RS Pondok Indah, PT. Maju
            
            // Kolom Kontak
            $table->string('email')->nullable();
            $table->string('no_telpon')->nullable();
            $table->text('alamat')->nullable();
            
            $table->timestamps(); // create_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};