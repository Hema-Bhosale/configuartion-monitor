<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonitoredFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monitored_files', function (Blueprint $table) {
            $table->id();

            $table->string('path')->unique();
            $table->string('file_type');

            $table->string('status')->default('active');

            $table->string('last_hash')->nullable();

            $table->json('last_snapshot')->nullable();

            $table->timestamp('last_checked_at')->nullable();
            $table->timestamp('last_changed_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monitored_files');
    }
}
