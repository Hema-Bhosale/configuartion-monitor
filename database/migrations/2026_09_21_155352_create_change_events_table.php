<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChangeEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('change_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitored_file_id')
                ->constrained('monitored_files')
                ->cascadeOnDelete();

            $table->string('file_path');
            $table->string('event_type');
            $table->json('changes')->nullable();

            $table->timestamp('detected_at');

            $table->string('webhook_status')->default('pending');
            $table->timestamp('webhook_sent_at')->nullable();
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
        Schema::dropIfExists('change_events');
    }
}
