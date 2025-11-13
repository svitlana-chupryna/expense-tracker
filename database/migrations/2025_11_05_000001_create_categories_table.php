<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->timestamps();
            });
        } else {
            // Table exists, just add unique constraint if it doesn't exist
            Schema::table('categories', function (Blueprint $table) {
                $table->unique('name');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};


