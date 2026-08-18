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
    Schema::create('debts', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Nama pemutang/penghutang
        $table->enum('type', ['piutang', 'hutang']); // piutang = orang berhutang ke kita, hutang = kita berhutang
        $table->decimal('amount', 15, 2);
        $table->text('description')->nullable();
        $table->enum('status', ['unpaid', 'paid'])->default('unpaid');
        $table->date('due_date')->nullable();
        $table->timestamps();
    });
}
};
