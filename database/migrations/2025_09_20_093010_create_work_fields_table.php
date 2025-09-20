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
        Schema::create('work_fields', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type')->comment('Text / File');
            $table->unsignedBigInteger('work_step_group_id')->index();
            $table->unsignedBigInteger('work_step_id')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_fields');
    }
};
