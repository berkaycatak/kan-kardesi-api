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
        Schema::create('blood_compatibility', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_blood_type_id')->constrained('blood_types')->onDelete('cascade');
            $table->foreignId('recipient_blood_type_id')->constrained('blood_types')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_compatibility');
    }
};
