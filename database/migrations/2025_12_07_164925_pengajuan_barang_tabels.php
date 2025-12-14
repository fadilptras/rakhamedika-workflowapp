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
        Schema::create('pengajuan_barang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('judul_pengajuan');
            $table->string('divisi');
            $table->json('rincian_barang'); // Array of items with deskripsi and jumlah
            $table->enum('status', ['diajukan', 'diproses', 'selesai', 'ditolak'])->default('diajukan');
            $table->enum('status_atasan', ['menunggu', 'disetujui', 'ditolak', 'skipped'])->default('menunggu');
            $table->text('catatan_atasan')->nullable();
            $table->enum('status_finance', ['menunggu', 'disetujui', 'ditolak', 'skipped'])->default('menunggu');
            $table->text('catatan_finance')->nullable();
            $table->enum('status_direktur', ['menunggu', 'disetujui', 'ditolak', 'skipped'])->default('skipped');
            $table->text('catatan_direktur')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->json('lampiran')->nullable(); // Array of file paths
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_barang');
    }
};
