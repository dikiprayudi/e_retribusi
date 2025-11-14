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
        Schema::create('penerimaans', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('nama_pasien');
            $table->string('no_rm');
            $table->text('alamat');
            $table->enum('jenis_kelamin', ['L', 'P'])->default('l');
            $table->string('jenis_kunjungan');
            $table->string('status_pasien');
            $table->foreignId('poli_id')
                ->constrained()
                ->onDelete('cascade');
            $table->integer('total_tarif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penerimaans');
    }
};
