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
        Schema::create('bpjs', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal'); // Tanggal penerimaan
            $table->string('no_sts'); // Kolom untuk menyimpan Enum kategori
            $table->string('kategori'); // Kolom untuk menyimpan Enum kategori
            $table->string('uraian'); // Kolom untuk menyimpan Enum kategori
            $table->string('bank'); // Kolom untuk menyimpan Enum kategori
            $table->string('no_rekening'); // Kolom untuk menyimpan Enum kategori
            $table->integer('jumlah'); // Jumlah uang (15 digit total, 2 di belakang koma)
            $table->text('keterangan')->nullable(); // Keterangan opsional
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bpjs');
    }
};
