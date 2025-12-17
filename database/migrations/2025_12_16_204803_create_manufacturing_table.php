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
        Schema::create('manufacturing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('out_item_id')->constrained('items');
            $table->decimal('out_amount', 10, 2);
            $table->date('factory_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturing');
    }
};
