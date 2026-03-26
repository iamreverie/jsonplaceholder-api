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
        Schema::create('jp_todos', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->unsignedInteger('user_id')->index();
            $table->foreign('user_id')->references('id')->on('jp_users')->cascadeOnDelete();
            $table->string('title');
            $table->boolean('completed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jp_todos');
    }
};
