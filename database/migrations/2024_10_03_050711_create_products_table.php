<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->string('name');
            $table->text('description')->nullable();
            $table->float('price')->nullable();
            $table->float('weight')->nullable();
            $table->float('weight_for_features')->nullable();
            $table->float('calories')->nullable();
            $table->float('proteins')->nullable();
            $table->float('carbs')->nullable();
            $table->float('fats')->nullable();
            $table->boolean('is_alcohol')->default(false);
            $table->boolean('is_public')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
