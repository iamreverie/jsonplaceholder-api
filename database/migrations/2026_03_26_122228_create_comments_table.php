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
        Schema::create('jp_comments', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->unsignedInteger('post_id')->index();
            $table->foreign('post_id')->references('id')->on('jp_posts')->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->text('body');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jp_comments');
    }
};
