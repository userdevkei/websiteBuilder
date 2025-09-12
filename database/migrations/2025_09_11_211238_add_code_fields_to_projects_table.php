<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCodeFieldsToProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('slug', 255)->nullable()->after('status');
            $table->longText('html_content')->nullable()->after('slug');
            $table->longText('css_content')->nullable()->after('html_content');
            $table->longText('js_content')->nullable()->after('css_content');
        });
    }

    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['slug', 'html_content', 'css_content', 'js_content']);
        });
    }
}
