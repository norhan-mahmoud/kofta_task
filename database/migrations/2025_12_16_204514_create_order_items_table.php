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
        Schema::create('order_items', function (Blueprint $table) {
             $table->id();
             $table->foreignId('order_id')->constrained()->onDelete('cascade');
             $table->foreignId('item_id')->constrained()->onDelete('cascade');
             $table->decimal('quantity', 10, 2);
             $table->decimal('unit_price', 10, 2);
        });
       Schema::create('order_item_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('batches')->onDelete('cascade');
            $table->foreignId('order_item_id')->constrained('order_items')->onDelete('cascade');
            $table->timestamps(); // لو حابب
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('order_item_batches');

      
    }
};
