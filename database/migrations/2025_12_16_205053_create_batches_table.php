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
            Schema::create('batches', function (Blueprint $table) {
                $table->id();

                $table->foreignId('item_id')
                    ->constrained('items');

                

                $table->morphs('source');

                $table->decimal('initial_quantity', 10, 2);
                $table->decimal('remaining_quantity', 10, 2);

                $table->date('produced_at');
                $table->date('expired_date')->nullable();

                $table->timestamps();

            });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
