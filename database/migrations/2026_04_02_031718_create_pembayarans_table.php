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
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')
                ->constrained('invoices')
                ->cascadeOnDelete();

            $table->string('no_pembayaran')->unique();
            $table->date('tanggal_bayar');
            $table->decimal('nominal_bayar', 15, 2);
            $table->enum('metode_pembayaran', ['transfer', 'cash', 'giro', 'cek', 'lainnya'])->default('transfer');

            $table->string('nama_pengirim')->nullable();
            $table->string('bank_pengirim')->nullable();
            $table->string('no_referensi')->nullable();

            $table->string('bukti_pembayaran')->nullable();
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
