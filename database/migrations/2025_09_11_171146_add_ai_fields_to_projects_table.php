<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAiFieldsToProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->boolean('is_ai_generated')->default(false)->after('status');
            $table->text('ai_prompt')->nullable()->after('is_ai_generated');
            $table->json('ai_options')->nullable()->after('ai_prompt');
            $table->timestamp('ai_generated_at')->nullable()->after('ai_options');
        });
    }

    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['is_ai_generated', 'ai_prompt', 'ai_options', 'ai_generated_at']);
        });
    }
}
