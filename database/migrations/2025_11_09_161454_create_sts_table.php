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
        Schema::create('sts', function (Blueprint $table) {
            $table->id();
            $table->string('no_sts')->unique();
            $table->date('tanggal_setor');
            $table->string('bank');
            $table->string('no_rekening');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->decimal('total_disetor', 15, 2)->default(0);

            // Asumsi Anda akan mengisi ini dari form
            $table->string('pengguna_anggaran_nama');
            $table->string('pengguna_anggaran_nip');
            $table->string('bendahara_nama');
            $table->string('bendahara_nip');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sts');
    }
};
