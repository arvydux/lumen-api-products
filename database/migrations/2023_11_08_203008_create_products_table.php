<?php

use App\Enum\VatRates;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->char('description', 140);
            $table->char('sku', 13);
            $table->decimal('cost_price',9,3);
            $table->decimal('selling_price',9,3);
            $table->boolean('vat');
            $table->enum('vat_rate', VatRates::values());
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
