<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('transactions')) {
            Schema::create('transactions', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->decimal('amount', 10, 2);
                $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
                $table->date('date');
                $table->timestamps();
                $table->index(['date', 'category_id']);
            });
        } else {
            // Table exists, try to add the index (will fail silently if it already exists)
            try {
                Schema::table('transactions', function (Blueprint $table) {
                    $table->index(['date', 'category_id']);
                });
            } catch (\Exception $e) {
                // Index might already exist, that's okay
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};


