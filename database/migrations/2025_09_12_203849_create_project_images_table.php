<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('filename');
            $table->string('original_name');
            $table->string('path');
            $table->string('url');
            $table->unsignedBigInteger('size')->nullable(); // File size in bytes
            $table->string('mime_type')->nullable();
            $table->enum('source', ['upload', 'unsplash'])->default('upload');
            $table->string('source_id')->nullable(); // Unsplash image ID
            $table->string('source_author')->nullable(); // Unsplash author name
            $table->timestamps();
            $table->index(['project_id', 'created_at']);
            $table->index('source');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_images');
    }
}
