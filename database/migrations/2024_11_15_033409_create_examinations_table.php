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
        Schema::create('examinations', function (Blueprint $table) {
            $table->id();
            $table->dateTime("exam_date");
            $table->foreignId("student_id")->constrained()->onDelete("cascade")->onUpdate("cascade");
            $table->foreignId("assessor_id")->constrained()->onDelete("cascade")->onUpdate("cascade");
            $table->foreignId("standard_id")->constrained("competency_standards")->onDelete("cascade")->onUpdate("cascade");
            $table->foreignId("element_id")->constrained("competency_elements")->onDelete("cascade")->onUpdate("cascade");
            $table->tinyInteger("status");
            $table->text("comments");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examinations');
    }
};
