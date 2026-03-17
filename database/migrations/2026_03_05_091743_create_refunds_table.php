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
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_reservation');
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('account_name');
            $table->text('cancel_reason')->nullable();
            $table->integer('amount')->default(0);
            $table->string('status')->default('pending'); // pending, completed
            $table->timestamps();

            // foreign key (opsional, jika tipe datanya pas)
            // $table->foreign('id_reservation')->references('id_reservation')->on('reservations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
