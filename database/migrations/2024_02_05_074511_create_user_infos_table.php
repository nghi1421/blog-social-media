<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_infos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('description', 1000)->nullable();
            $table->string('phone_number', 15)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('email')->unique();
            $table->datetime('dob')->nullable();
            $table->string('id_number', 20)->nullable();
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_infos');
    }
};
