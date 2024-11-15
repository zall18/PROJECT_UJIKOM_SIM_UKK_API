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
        Schema::create('competency_standards', function (Blueprint $table) {
            $table->id();
            $table->string("unit_code", 32);
            $table->string("unit_title", 64);
            $table->text("unit_description");
            $table->foreignId("major_id")->constrained()->onDelete("cascade")->onUpdate("cascade");
            $table->foreignId("assessor_id")->constrained()->onDelete("cascade")->onUpdate("cascade");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competency_standards');
    }
};
