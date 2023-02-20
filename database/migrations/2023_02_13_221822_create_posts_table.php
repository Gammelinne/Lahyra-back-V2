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
        Schema::create('posts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title', 100);
            $table->string('body', 1500);
            $table->integer('reposts')->default(0);
            $table->boolean('is_private')->default(false);
            $table->uuid('user_id')->constrained('users')->onDelete('cascade');
            $table->uuid('post_id')->nullable()->constrained('posts')->onDelete('cascade'); //for reposts
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
