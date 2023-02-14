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
        Schema::create('posts_commentaries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('commentary');
            $table->uuid('post_id')->constrained('posts')->onDelete('cascade');
            $table->uuid('user_id')->constrained('users')->onDelete('cascade');
            $table->uuid('commentary_id')->nullable()->constrained('posts_commentaries')->onDelete('cascade');
            $table->integer('likes')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    
    public function down(): void
    {
        Schema::dropIfExists('posts_commentaries');
    }
};
