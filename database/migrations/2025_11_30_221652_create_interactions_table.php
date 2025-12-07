<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pastikan pakai Schema::create
        Schema::create('interactions', function (Blueprint $table) {
            $table->id();

            // Relasi ke Client (Wajib)
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');

            // Relasi ke User (Opsional: Siapa sales yang input data ini?)
            // Tambahkan ini jika Anda memang butuh mencatat siapa yang input
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 

            $table->string('nama_produk');       
            $table->string('jenis_kerjasama')->nullable(); 
            $table->decimal('nilai_kontribusi', 15, 2)->default(0); 
            $table->date('tanggal_interaksi'); 
            $table->text('catatan')->nullable(); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interactions');
    }
};