<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProcessingAtColumnToModulesQueueMonitorJobsTable extends Migration
{
    public function up()
    {
        Schema::table('modules_queue_monitor_jobs', function (Blueprint $table) {
            $table->timestamp('processing_at')->nullable()->after('dispatched_at');
        });
    }
}
